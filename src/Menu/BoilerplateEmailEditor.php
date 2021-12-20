<?php

namespace Sebastienheyd\BoilerplateEmailEditor\Menu;

use Sebastienheyd\Boilerplate\Menu\Builder;

class BoilerplateEmailEditor
{
    public function make(Builder $menu)
    {
        $menu->add(__('boilerplate-email-editor::editor.title'), [
            'permission' => 'emaileditor_email_edition,emaileditor_email_dev',
            'icon'       => 'mail-bulk',
            'active'     => 'emaileditor.*',
        ])->id('emaileditor')->order(900);

        $menu->addTo('emaileditor', __('boilerplate-email-editor::email.list'), [
            'permission' => 'emaileditor_email_edition,emaileditor_email_dev',
            'route'      => 'emaileditor.email.index',
            'active'     => 'emaileditor.email.index',
        ]);

        $menu->addTo('emaileditor', __('boilerplate-email-editor::email.add'), [
            'permission' => 'emaileditor_email_edition,emaileditor_email_dev',
            'route'      => 'emaileditor.email.create',
            'active'     => 'emaileditor.email.create',
        ]);
    }
}
