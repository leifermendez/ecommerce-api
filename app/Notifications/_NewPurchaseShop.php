<?php

namespace App\Notifications;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class _NewPurchaseShop extends Notification
{
    use Queueable;
    private $pay;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $pay)
    {
        $this->pay = $pay;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $site = env('APP_SITE_MAIL', '');
        return (new MailMessage)
            ->subject("¡Nuevo Pago exitoso!")
            ->markdown("vendor.notifications.".$site.".pay", ['pay' => $this->pay]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
