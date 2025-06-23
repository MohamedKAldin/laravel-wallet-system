<?php

namespace App\Notifications;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewTopUpRequest extends Notification implements ShouldQueue
{
    use Queueable;

    public $transaction;

    /**
     * Create a new notification instance.
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'transaction_id' => $this->transaction->id,
            'amount' => $this->transaction->amount,
            'user_name' => $this->transaction->wallet->owner->name,
            'status' => $this->transaction->status,
            'created_at' => $this->transaction->created_at,
        ];
    }

    public function toMail(object $notifiable)
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('New Top-Up Request')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('A new top-up request has been created.')
            ->line('User: ' . $this->transaction->wallet->owner->name)
            ->line('Amount: ' . $this->transaction->amount . ' EGP')
            ->action('View Request', url('/admin/top-up-requests/' . $this->transaction->id))
            ->line('Thank you for using our application!');
    }
}
