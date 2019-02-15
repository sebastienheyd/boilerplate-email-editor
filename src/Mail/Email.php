<?php

namespace Sebastienheyd\BoilerplateEmailEditor\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Sebastienheyd\BoilerplateEmailEditor\Models\Email as EmailModel;

class Email extends Mailable
{
    use Queueable, SerializesModels;

    private $id;
    private $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($id, $data = [])
    {
        $this->id = $id;
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $email = EmailModel::findOrFail($this->id);

        $this->html($email->render($this->data))
            ->subject($email->subject)
            ->from($email->sender_email ?? config('mail.from.address'), $email->sender_name ?? config('mail.from.name'));

        return $this;
    }
}
