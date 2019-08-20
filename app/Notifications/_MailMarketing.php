<?php

namespace App\Notifications;

use App\User;
use Illuminate\Support\HtmlString;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class _MailMarketing extends Notification
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
                    ->from($address = 'rrhh@alterhome.es', $name = 'RRHH')
                    ->subject("¡Tienda Alterhome!")
                    ->line(new HtmlString('Hola!! <b>'.$this->user->name.'</b>'))
                    ->line('')
                    ->line(new HtmlString("Te presentamos “Alterhome Shop”, nuestra tienda online donde los viajeros pueden disfrutar de hasta un <b>70%</b> de descuento en cientos de productos."))
                    ->line('')
                    ->line(new HtmlString("Antes de lanzarla al público queremos que la pruebes, examines y nos des tu opinión, si todo funciona bien, si detectas algún error ortográfico o de otro tipo, qué cambiarías, si la página es práctica e intuitiva…etc."))
                    ->line(new HtmlString("Y por esta ayudita y por ser un miembro valioso del equipo de Alterhome tienes un descuento del <b>30% durante este mes de Agosto!!</b>"))
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
