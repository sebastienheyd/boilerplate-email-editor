<?php

namespace Sebastienheyd\BoilerplateEmailEditor\Models;

use Illuminate\Database\Eloquent\Model;
use Mail;
use Sebastienheyd\BoilerplateEmailEditor\Facades\Blade;
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
        'label',
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

    public function render($data = [], $emptyVariableError = true)
    {
        $data['sender_name'] = $data['sender_name'] ?? $this->sender_name ?? config('mail.from.name');
        $data['sender_email'] = $data['sender_email'] ?? $this->sender_email ?? config('mail.from.address');

        $content = Blade::get($this->content, $data, $emptyVariableError);

        $layout = $this->layout;
        if (isset($data['layout_id'])) {
            $layout = EmailLayout::find($data['layout_id']);
        }

        if ($layout !== null) {
            $data['content'] = $content;

            return $layout->render($data, $emptyVariableError)->getContent();
        }

        return response($content, 200)->header('Content-Type', 'text/html');
    }

    public function send($to, $data = [])
    {
        $mail = new EmailToSend($this->id, $data);
        Mail::to($to)->send($mail);
    }
}
