<?php

namespace App\Notifications;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WithdrawalRequestStatusUpdated extends Notification implements ShouldQueue
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
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $status = $this->transaction->status;
        $amount = $this->transaction->amount;
        return (new MailMessage)
            ->subject('Withdrawal Request Status Updated')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line("Your withdrawal request for {$amount} EGP has been {$status}.")
            ->action('View Request', url('/admin/withdrawal-requests/' . $this->transaction->id))
            ->line('Thank you for using our application!');
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
            'admin_name' => $this->transaction->wallet->owner->name,
            'status' => $this->transaction->status,
            'created_at' => $this->transaction->created_at,
        ];
    }
}
