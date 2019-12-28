<?php

namespace Sebastienheyd\BoilerplateEmailEditor\Models;

use DOMDocument;
use DOMElement;
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

    /**
     * Get email by his slug.
     *
     * @param $slug
     *
     * @return mixed
     */
    public static function findBySlug($slug)
    {
        return self::whereSlug($slug)->firstOrFail();
    }

    /**
     * Get email content for TinyMCE edition.
     *
     * @return string
     */
    public function getMceContentAttribute()
    {
        $content = $this->getAttribute('content');
        $html = new DOMDocument();
        @$html->loadHTML('<?xml encoding="UTF-8">'.$content,
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOENT);

        $this->domTextReplace('`\[([a-zA-Z0-9_-]*)]`', '<variable contenteditable="false">[$1]</variable>', $html);
        $content = (string) $html->saveHTML();
        $content = html_entity_decode($content);
        $content = preg_replace('`%5B(([a-zA-Z0-9_-]*))%5D`', '[$1]', $content);

        return trim($content);
    }

    /**
     * Replace text only in text nodes, not in attributes.
     *
     * @param string                 $search
     * @param string                 $replace
     * @param DOMDocument|DOMElement $domNode
     */
    private function domTextReplace($search, $replace, &$domNode)
    {
        if ($domNode->hasChildNodes()) {
            $children = [];
            foreach ($domNode->childNodes as $child) {
                $children[] = $child;
            }
            foreach ($children as $child) {
                if ($child->nodeType == XML_PI_NODE) {
                    $domNode->removeChild($child);
                } elseif ($child->nodeType === XML_TEXT_NODE) {
                    $oldText = $child->wholeText;
                    $newText = preg_replace($search, $replace, $oldText);
                    $newTextNode = $domNode->ownerDocument->createTextNode($newText);
                    $domNode->replaceChild($newTextNode, $child);
                } else {
                    $this->domTextReplace($search, $replace, $child);
                }
            }
        }
    }

    /**
     * Render email content.
     *
     * @param array $data
     *
     * @return string
     */
    public function render($data = [])
    {
        $data = $data + [
            'sender_name'  => $data['sender_name'] ?? $this->getAttribute('sender_name') ?? config('mail.from.name'),
            'sender_email' => $data['sender_email'] ?? $this->getAttribute('sender_email') ?? config(
                'mail.from.address'
            ),
        ];

        $content = $this->getAttribute('content');
        if (!is_string($content) || empty($content)) {
            return '';
        }

        foreach ($data as $k => $v) {
            $content = str_replace("[$k]", $v, $content);
        }

        if (!empty($this->getAttribute('layout'))) {
            $data['content'] = $content;
            $content = (string) view($this->getAttribute('layout'), $data);
        }

        $this->parseImg($content);

        return $this->minify($content);
    }

    private function parseImg(& $content)
    {
        $html = new \DOMDocument('1.0', 'utf-8');
        @$html->loadHTML($content);

        $tags = $html->getElementsByTagName('img');

        if (!empty($tags)) {
            foreach ($tags as $tag) {
                $src = $tag->getAttribute('src');

                if (preg_match('`^/`', $src)) {
                    $width = $tag->getAttribute('width');
                    $height = $tag->getAttribute('height');
                    if(!empty($width) && !empty($height)) {
                        $src = img_url($src, $width, $height, 'resize');
                    }
                    $tag->setAttribute('src', config('app.url').$src);
                    $html->saveHTML($tag);
                }
            }
        }

        $content = $html->saveHTML();
    }

    /**
     * Minify HTML content.
     *
     * @param $content
     *
     * @return string
     */
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

    /**
     * Send current email.
     *
     * @param string $to
     * @param array  $data
     */
    public function send($to, $data = [])
    {
        $mail = new EmailToSend($this->id, $data);
        Mail::to($to)->send($mail);
    }
}
