<?php

namespace Sebastienheyd\BoilerplateEmailEditor\Models;

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
        $layouts = collect(Storage::disk('email-layouts')->files())->filter(
            function ($v) {
                return preg_match('`^(.*?)\.blade\.php$`', $v) != false;
            }
        )->toArray();

        $result = [];

        foreach ($layouts as $layout) {
            $lines = file(Storage::disk('email-layouts')->path($layout));
            $layout = preg_replace('`\.blade\.php$`', '', $layout);

            if (preg_match('`^{{--(.*?)--}}$`', trim($lines[0]), $m)) {
                $layoutName = trim($m[1]);
            } else {
                $layoutName = ucfirst($layout);
            }

            $result['email-layouts.'.$layout] = $layoutName;
        }

        if (!isset($result['email-template.default'])) {
            $result['boilerplate-email-editor::layout.default'] = 'HTML';
        }

        ksort($result);

        return $result;
    }
}
