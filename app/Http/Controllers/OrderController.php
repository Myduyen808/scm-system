<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class OrderController extends Controller
{
    public function createOrder(Request $request)
    {
        // Tạo order (giả sử bạn đã lưu $order)
        $order = Order::create([
            'user_id' => $request->user()->id,
            'total' => $request->input('total'),
        ]);

        // Gửi message RabbitMQ
        $connection = $this->getRabbitMQConnection();
        $channel = $connection->channel();
        $channel->queue_declare('inventory_deduct', false, true, false, false);

        $msg = new AMQPMessage(json_encode([
            'order_id' => $order->id,
            'action' => 'deduct_stock'
        ]));

        $channel->basic_publish($msg, '', 'inventory_deduct');

        $channel->close();
        $connection->close();

        return response()->json(['success' => true, 'order_id' => $order->id]);
    }

    protected function getRabbitMQConnection()
    {
        return new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
    }
}
