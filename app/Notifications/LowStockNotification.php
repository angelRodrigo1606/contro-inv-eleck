<?php

namespace App\Notifications;

use App\Dtos\Data\ProductData;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public ProductData $product) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Alerta de stock bajo: '.$this->product->name)
            ->line('El producto '.$this->product->name.' (SKU: '.$this->product->sku.') tiene stock bajo.')
            ->line('Stock actual: '.$this->product->quantity)
            ->line('Stock mínimo: '.$this->product->minStock)
            ->action('Ver producto', url('/products/'.$this->product->id));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'quantity' => $this->product->quantity,
            'min_stock' => $this->product->minStock,
        ];
    }
}
