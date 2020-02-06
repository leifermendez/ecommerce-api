<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ShoppingCartReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $subject = 'Recordatorio de compra';
    public $address = 'from@example.com';
    public $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($correo)
    {
        $this->data = $correo;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('email.shoppingCartReminder')
                    ->from($this->address)
                    ->subject($this->subject)
                    ->with(['user' => $this->data['user'], 'products' => $this->data['products'] ]);
    }
}
