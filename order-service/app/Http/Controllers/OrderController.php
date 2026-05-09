<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\RabbitMqService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        return response()->json(
            Order::with('items.product')->orderByDesc('id')->get()
        );
    }

    public function store(Request $request, RabbitMqService $broker)
    {
        $data = $request->validate([
            'user_id' => 'required|integer',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            $order = DB::transaction(function () use ($data) {
                $total = 0;

                $order = Order::create([
                    'user_id' => $data['user_id'],
                    'total_price' => 0,
                    'status' => 'pending',
                ]);

                foreach ($data['items'] as $item) {
                    $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                    if ($product->stock < $item['quantity']) {
                        throw new \Exception("Insufficient stock for product ID {$product->id}");
                    }

                    $subtotal = $product->price * $item['quantity'];
                    $total += $subtotal;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $item['quantity'],
                        'price' => $product->price,
                    ]);

                    $product->stock -= $item['quantity'];
                    $product->save();
                }

                $order->update([
                    'total_price' => $total,
                ]);

                return $order->load('items.product');
            });

            $broker->publish(env('RABBITMQ_QUEUE', 'order_events'), [
                'event' => 'order.created',
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'total_price' => $order->total_price,
                'items' => $order->items->map(function ($item) {
                    return [
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                    ];
                })->values(),
            ]);

            return response()->json([
                'message' => 'Order created',
                'order' => $order,
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Failed to create order',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}