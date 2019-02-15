<?php

namespace Sebastienheyd\BoilerplateEmailEditor\Models;

use Illuminate\Database\Eloquent\Model;
use Sebastienheyd\BoilerplateEmailEditor\Facades\Blade;

class EmailLayout extends Model
{
    protected $table = 'emails_layouts';
    protected $fillable = ['label', 'content'];
    public $timestamps = false;

    public function render($data = [], $emptyVariableError = true)
    {
        $content = Blade::get($this->content, $data, $emptyVariableError);
        return response($content, 200)->header('Content-Type', 'text/html');
    }
}
