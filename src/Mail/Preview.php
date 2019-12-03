<?php

namespace Sebastienheyd\BoilerplateEmailEditor\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Preview extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($content)
    {
        $html = new \DOMDocument('1.0', 'utf-8');
        @$html->loadHTML($content);

        $tags = $html->getElementsByTagName('img');

        if (!empty($tags)) {
            foreach ($tags as $tag) {
                $src = $tag->getAttribute('src');
                if (preg_match('`^/`', $src)) {
                    $tag->setAttribute('src', config('app.url') . $src);
                    $html->saveHTML($tag);
                }
            }
        }

        $this->content = $html->saveHTML();
    }

    public function subject($subject)
    {
        $subject = '[PREVIEW] ' . $subject;

        return parent::subject($subject);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->html($this->content);
    }
}
