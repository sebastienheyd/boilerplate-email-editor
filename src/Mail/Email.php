<?php

namespace Sebastienheyd\BoilerplateEmailEditor\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Sebastienheyd\BoilerplateEmailEditor\Models\Email as EmailModel;

class Email extends Mailable
{
    use Queueable;
    use SerializesModels;

    private $id;
    private $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($id, $data = [], $subjectData = [])
    {
        $this->id = $id;
        $this->data = $data;
        $this->subjectData = $subjectData;
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
            ->subject($email->renderSubject($email->subject, $this->subjectData))
            ->from(
                $email->sender_email ?? config('boilerplate.email-editor.from.address'),
                $email->sender_name ?? config('boilerplate.email-editor.from.name')
            );

        return $this;
    }
}
