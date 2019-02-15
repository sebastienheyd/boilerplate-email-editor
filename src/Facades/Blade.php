<?php

namespace Sebastienheyd\BoilerplateEmailEditor\Facades;

class Blade extends \Illuminate\Support\Facades\Blade
{
    public static function get($string, $data, $emptyVariableError = true)
    {
        $php = parent::compileString($string);

        $obLevel = ob_get_level();
        ob_start();

        extract($data, EXTR_SKIP);

        try {
            eval('?>'.$php);
        } catch(\Exception $e) {
            if(!$emptyVariableError && preg_match("#^Undefined variable: (.*)#", $e->getMessage(), $m)) {
                while(ob_get_level() > $obLevel) ob_end_clean();
                return self::get($string, $data + [$m[1] => '$'.$m[1]], $emptyVariableError);
            } else {
                while(ob_get_level() > $obLevel) ob_end_clean();
                throw $e;
            }
        }

        return ob_get_clean();
    }
}