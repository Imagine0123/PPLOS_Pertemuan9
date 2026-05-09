<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GatewayController extends Controller
{
    // AUTH SERVICE
    public function register(Request $request)
    {
        $response = Http::post(
            env('AUTH_SERVICE_URL') . '/api/register',
            $request->all()
        );

        return response()->json(
            $response->json(),
            $response->status()
        );
    }

    public function login(Request $request)
    {
        $response = Http::post(
            env('AUTH_SERVICE_URL') . '/api/login',
            $request->all()
        );

        return response()->json(
            $response->json(),
            $response->status()
        );
    }

    public function profile(Request $request)
    {
        $response = Http::withHeaders([
            'Authorization' => $request->header('Authorization')
        ])->get(
            env('AUTH_SERVICE_URL') . '/api/profile'
        );

        return response()->json(
            $response->json(),
            $response->status()
        );
    }

    // PRODUCT SERVICE
    public function products()
    {
        $response = Http::get(
            env('ORDER_SERVICE_URL') . '/api/products'
        );

        return response()->json(
            $response->json(),
            $response->status()
        );
    }

    public function product($id)
    {
        $response = Http::get(
            env('ORDER_SERVICE_URL') . "/api/products/{$id}"
        );

        return response()->json(
            $response->json(),
            $response->status()
        );
    }

    public function createProduct(Request $request)
    {
        $response = Http::withHeaders([
            'Authorization' => $request->header('Authorization')
        ])->post(
            env('ORDER_SERVICE_URL') . '/api/products',
            $request->all()
        );

        return response()->json(
            $response->json(),
            $response->status()
        );
    }

    public function updateProduct(Request $request, $id)
    {
        $response = Http::withHeaders([
            'Authorization' => $request->header('Authorization')
        ])->put(
            env('ORDER_SERVICE_URL') . "/api/products/{$id}",
            $request->all()
        );

        return response()->json(
            $response->json(),
            $response->status()
        );
    }

    public function deleteProduct(Request $request, $id)
    {
        $response = Http::withHeaders([
            'Authorization' => $request->header('Authorization')
        ])->delete(
            env('ORDER_SERVICE_URL') . "/api/products/{$id}"
        );

        return response()->json(
            $response->json(),
            $response->status()
        );
    }

    // ORDER SERVICE
    public function orders(Request $request)
    {
        $response = Http::withHeaders([
            'Authorization' => $request->header('Authorization')
        ])->get(
            env('ORDER_SERVICE_URL') . '/api/orders'
        );

        return response()->json(
            $response->json(),
            $response->status()
        );
    }

    public function createOrder(Request $request)
    {
        $response = Http::withHeaders([
            'Authorization' => $request->header('Authorization')
        ])->post(
            env('ORDER_SERVICE_URL') . '/api/orders',
            $request->all()
        );

        return response()->json(
            $response->json(),
            $response->status()
        );
    }
}