<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class resetPassword extends Mailable
{
    use Queueable, SerializesModels;

    public $subject = 'Restablecimiento de contraseña';
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
        
        return $this->view('email.resetPassword')
                    ->from($this->address)
                    ->subject($this->subject)
                    ->with(['name' => $this->data['name'], 'url' => $this->data['url']]);
    }
}
