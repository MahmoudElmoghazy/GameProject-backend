<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ActivationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $activationUrl;

    /**
     * Create a new message instance.
     *
     * @param string $activationUrl
     * @return void
     */
    public function __construct($activationUrl)
    {
        $this->activationUrl = $activationUrl;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('email.activation')
            ->with([
                'activationUrl' => $this->activationUrl,
            ])
            ->subject('Activate Your Account');
    }
}
