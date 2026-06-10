<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    public static function send(User $user, string $type, string $title, string $message, array $data = [], ?string $url = null): Notification
    {
        return Notification::create([
            'user_id' => $user->id,
            'type'    => $type,
            'title'   => $title,
            'message' => $message,
            'data'    => $data,
            'url'     => $url,
        ]);
    }

    public static function sendToAdmins(string $type, string $title, string $message, array $data = [], ?string $url = null): void
    {
        User::where('role','admin')->each(function ($admin) use ($type,$title,$message,$data,$url) {
            static::send($admin, $type, $title, $message, $data, $url);
        });
    }

    public static function invoiceCreated(\App\Models\Invoice $invoice): void
    {
        $tenant = $invoice->tenant;
        if ($tenant->user) {
            static::send(
                $tenant->user,
                'invoice_created',
                'Tagihan Baru',
                "Tagihan {$invoice->invoice_number} sebesar {$invoice->formatted_amount} telah dibuat.",
                ['invoice_id' => $invoice->id],
                route('invoices.show', $invoice)
            );
        }
    }

    public static function paymentSubmitted(\App\Models\Payment $payment): void
    {
        static::sendToAdmins(
            'payment_submitted',
            'Pembayaran Baru',
            "Bukti pembayaran untuk invoice {$payment->invoice->invoice_number} telah diunggah.",
            ['payment_id' => $payment->id],
            route('payments.show', $payment)
        );
    }

    public static function paymentVerified(\App\Models\Payment $payment): void
    {
        $tenant = $payment->invoice->tenant;
        if ($tenant->user) {
            static::send(
                $tenant->user,
                'payment_verified',
                'Pembayaran Disetujui',
                "Pembayaran Anda untuk invoice {$payment->invoice->invoice_number} telah disetujui.",
                ['payment_id' => $payment->id],
                route('invoices.show', $payment->invoice)
            );
        }
    }

    public static function paymentRejected(\App\Models\Payment $payment): void
    {
        $tenant = $payment->invoice->tenant;
        if ($tenant->user) {
            static::send(
                $tenant->user,
                'payment_rejected',
                'Pembayaran Ditolak',
                "Pembayaran Anda untuk invoice {$payment->invoice->invoice_number} ditolak. Alasan: {$payment->rejection_reason}",
                ['payment_id' => $payment->id],
                route('invoices.show', $payment->invoice)
            );
        }
    }

    public static function complaintCreated(\App\Models\Complaint $complaint): void
    {
        static::sendToAdmins(
            'complaint_created',
            'Keluhan Baru',
            "Keluhan baru dari {$complaint->tenant->name}: {$complaint->title}",
            ['complaint_id' => $complaint->id],
            route('complaints.show', $complaint)
        );
    }
}
