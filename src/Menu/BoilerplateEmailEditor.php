<?php

namespace Sebastienheyd\BoilerplateEmailEditor\Menu;

use Sebastienheyd\Boilerplate\Menu\Builder;

class BoilerplateEmailEditor
{
    public function make(Builder $menu)
    {
        $menu->add(__('boilerplate-email-editor::editor.title'), [
                'permission' => 'emaileditor_email_edition,emaileditor_email_dev',
                'route'      => 'emaileditor.email.index',
                'icon'       => 'mail-bulk', ])
            ->id('emaileditor')
            ->activeIfRoute('emaileditor.*')
            ->order(900);
    }
}
