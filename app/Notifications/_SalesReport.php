<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class _SalesReport extends Notification
{
    use Queueable;

    public $PDF;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($datos)
    {
        $this->PDF = $datos;
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
        $path = env('APP_URL').$this->PDF;
        return (new MailMessage)
            ->subject("Reporte de ventas")
            ->attach($path, [
                'as' => 'ventas.pdf',
                'mime' => 'application/pdf'])
            ->markdown("vendor.notifications.alterhome.sales_resport");

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
