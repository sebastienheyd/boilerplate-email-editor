<?php

namespace Sebastienheyd\BoilerplateEmailEditor\Menu;

use Sebastienheyd\Boilerplate\Menu\Builder;

class BoilerplateEmailEditor
{
    public function make(Builder $menu)
    {
        $menu->add(__('boilerplate-email-editor::editor.title'), [
                'permission' => 'emaileditor_email_crud,emaileditor_layout_crud',
                'icon'       => 'envelope-o', ])
            ->id('emaileditor')
            ->activeIfRoute('emaileditor.*')
            ->order(900);

        $menu->addTo('emaileditor', __('boilerplate-email-editor::email.title'), [
                'route'      => 'emaileditor.email.index',
                'permission' => 'emaileditor_email_crud', ])
            ->activeIfRoute(['emaileditor.email.*']);

        $menu->addTo('emaileditor', __('boilerplate-email-editor::layout.title'), [
            'route'      => 'emaileditor.layout.index',
            'permission' => 'emaileditor_layout_crud', ])
            ->activeIfRoute(['emaileditor.layout.*']);
    }
}
