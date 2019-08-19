<?php

namespace App\Notifications;

use App\User;
use Illuminate\Support\HtmlString;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class _MailMarketing extends Notification implements ShouldQueue
{
    use Queueable;
    private $user;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
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
        return (new MailMessage)
                    ->subject("¡Tienda Alterhome!")
                    ->line(new HtmlString('Hola! <b>'.$this->user->name.'</b>'))
                    ->line('')
                    ->line(new HtmlString("Me gustaría avisarte que hemos abierto nuestra tienda online de viajeros, “Alterhome Shop” con la que podrás obtener hasta <b>70%</b> de descuentos en nuestros productos."))
                    ->line('')
                    ->line(new HtmlString("Tienes una oferta <b>limitada válida durante 6 días</b>, por ser un miembro valioso del equipo Alterhome."))
                    ->line('')
                    ->line(new HtmlString("<img src='https://storage.googleapis.com/media-ecommerce-alterhome/public/upload/products/medium_V0ad9hEfU9L7CEKEbFI7mU6p5w9yp9D9cGC.jpg' />"))
                    ->line(new HtmlString("<br>"))
                    ->line('Lo único que necesitas hacer es iniciar sesión en nuestra página y automáticamente se activa la promoción.')
                    ->line('')
                    ->action('Activar Promoción', url('http://tienda.alterhome.es?ref='.$this->user->email));
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
