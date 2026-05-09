<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class ConsumeOrders extends Command
{
    protected $signature = 'broker:consume-orders';
    protected $description = 'Consume order events from RabbitMQ';

    public function handle(): int
    {
        $connection = new AMQPStreamConnection(
            env('RABBITMQ_HOST', '127.0.0.1'),
            (int) env('RABBITMQ_PORT', 5672),
            env('RABBITMQ_USER', 'guest'),
            env('RABBITMQ_PASSWORD', 'guest')
        );

        $channel = $connection->channel();
        $queue = env('RABBITMQ_QUEUE', 'order_events');

        $channel->queue_declare($queue, false, true, false, false);

        $this->info("Waiting for messages on queue: {$queue}");

        $callback = function ($msg) {
            $payload = json_decode($msg->body, true);

            logger()->info('Order event received', $payload);

            $this->info('Received event: ' . $msg->body);
        };

        $channel->basic_consume($queue, '', false, true, false, false, $callback);

        while ($channel->is_consuming()) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();

        return self::SUCCESS;
    }
}