<?php

namespace App\Mailers;

use App\Generators\TicketGenerator;
use App\Models\Order;
use App\Services\Order as OrderService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrderMailer
{
    public function sendOrderNotification(Order $order)
    {
        $orderService = new OrderService($order->amount, $order->organiser_booking_fee, $order->event);
        $orderService->calculateFinalCosts();

        $data = [
            'order'        => $order,
            'orderService' => $orderService
        ];

        Mail::send('Emails.OrderNotification', $data, function ($message) use ($order) {
            $message->to($order->account->email);
            $message->subject(trans("Controllers.new_order_received",
                ["event" => $order->event->title, "order" => $order->order_reference]));
        });

    }

    public function sendOrderTickets(Order $order)
    {
        $orderService = new OrderService($order->amount, $order->organiser_booking_fee, $order->event);
        $orderService->calculateFinalCosts();

        Log::info("Sending ticket to: " . $order->email);
        $data = [
            'order'        => $order,
            'orderService' => $orderService
        ];

        // Generate PDF filename and path
        $pdf_file = TicketGenerator::generateFileName($order->order_reference);

        if (!file_exists($pdf_file['fullpath'])) {
            Log::error("Cannot send actual ticket to : " . $order->email . " as ticket file does not exist on disk");
            return;
        }

        Mail::send('Mailers.TicketMailer.SendOrderTickets', $data, function ($message) use ($order, $pdf_file) {
            $message->to($order->email);
            $message->subject(trans("Controllers.tickets_for_event", ["event" => $order->event->title]));
            $message->attach($pdf_file['fullpath']);
        });

    }

}
