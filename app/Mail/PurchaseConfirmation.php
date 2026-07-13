<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PurchaseConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order,
        public bool $isAdminCopy = false,
    ) {
        $this->order->loadMissing([
            'user',
            'items.product',
        ]);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->isAdminCopy
                ? 'New Al Reem Expo Order #'.$this->order->id
                : 'Order Confirmation #'.$this->order->id.' - Al Reem Expo',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.purchase_confirmation',
            with: [
                'order' => $this->order,
                'isAdminCopy' => $this->isAdminCopy,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
