<?php

namespace Sebastienheyd\BoilerplateEmailEditor\Models;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmailLayout
{
    /**
     * Get e-mail layouts array.
     *
     * @return array
     */
    public static function getList()
    {
        $layouts = collect(Storage::disk('email-layout')->files())->filter(function ($v, $k) {
            return preg_match('`^(.*?)\.blade\.php$`', $v) != false;
        })->toArray();

        $result = [];

        foreach ($layouts as $layout) {
            $lines = file(Storage::disk('email-layout')->path($layout));
            $layout = preg_replace('`\.blade\.php$`', '', $layout);

            if (preg_match('`^{{--(.*?)--}}$`', trim($lines[0]), $m)) {
                $layoutName = trim($m[1]);
            } else {
                $layoutName = ucfirst($layout);
            }

            $result['email-layout.' . $layout] = $layoutName;
        }

        if (!isset($result['email-layout.default'])) {
            $result['boilerplate-email-editor::layout.default'] = 'HTML';
        }

        ksort($result);

        return $result;
    }



}
