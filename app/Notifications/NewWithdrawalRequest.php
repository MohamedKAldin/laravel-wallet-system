<?php

namespace App\Notifications;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NewWithdrawalRequest extends Notification implements ShouldQueue
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
        $wallet = $this->transaction->wallet;
        $owner = $wallet->user ?? $wallet->admin;
        $ownerType = $wallet->user ? 'user' : 'admin';
        
        return [
            'transaction_id' => $this->transaction->id,
            'amount' => $this->transaction->amount,
            'requester_name' => $owner->name,
            'requester_type' => $ownerType,
            'created_at' => $this->transaction->created_at,
        ];
    }

    public function toMail(object $notifiable)
    {
        $wallet = $this->transaction->wallet;
        $owner = $wallet->user ?? $wallet->admin;
        $ownerType = $wallet->user ? 'User' : 'Admin';
        
        return (new MailMessage)
            ->subject('New Withdrawal Request')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('A new withdrawal request has been created.')
            ->line($ownerType . ': ' . $owner->name)
            ->line('Amount: ' . $this->transaction->amount . ' EGP')
            ->action('View Request', url('/admin/withdrawal-requests/' . $this->transaction->id))
            ->line('Thank you for using our application!');
    }
}
