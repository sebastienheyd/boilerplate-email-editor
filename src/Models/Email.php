<?php

namespace Sebastienheyd\BoilerplateEmailEditor\Models;

use Illuminate\Database\Eloquent\Model;
use Mail;
use Sebastienheyd\BoilerplateEmailEditor\Mail\Email as EmailToSend;

/**
 * Class Email.
 *
 * @property EmailLayout $layout
 */
class Email extends Model
{
    protected $table = 'emails';
    protected $fillable = [
        'slug',
        'layout',
        'description',
        'subject',
        'content',
        'sender_name',
        'sender_email',
    ];
    public $timestamps = false;

    public static function findBySlug($slug)
    {
        return self::whereSlug($slug)->firstOrFail();
    }

    public function getMceContentAttribute()
    {
        $content = $this->getAttribute('content');
        $content = preg_replace('`\[([a-zA-Z0-9_-]*)]`', '<variable contenteditable="false">[$1]</variable>', $content);
        return trim($content);
    }

    public function render($data = [])
    {
        $data = [
            'sender_name'  => $data['sender_name'] ?? $this->getAttribute('sender_name') ?? config('mail.from.name'),
            'sender_email' => $data['sender_email'] ?? $this->getAttribute('sender_email') ?? config(
                    'mail.from.address'
                ),
        ];

        $content = $this->getAttribute('content');

        foreach ($data as $k => $v) {
            $content = str_replace("[$k]", $v, $content);
        }


        if (!empty($this->getAttribute('layout'))) {
            $data['content'] = $content;
            $content = (string)view($this->getAttribute('layout'), $data);
        }

        return response($this->minify($content), 200)->header('Content-Type', 'text/html');
    }

    private function minify($content)
    {
        $replace = [
            '/\>[^\S ]+/s'      => '>',
            '/[^\S ]+\</'       => '<',
            '/(\s)+/s'          => '\\1',
            '/<!--(.|\s)*?-->/' => '',
        ];

        return preg_replace(array_keys($replace), array_values($replace), $content);
    }

    public function send($to, $data = [])
    {
        $mail = new EmailToSend($this->id, $data);
        Mail::to($to)->send($mail);
    }
}
