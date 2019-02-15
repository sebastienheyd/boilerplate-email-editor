<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Permissions extends Migration
{
    private $permissions = [
        [
            'name'         => 'emaileditor_layout_crud',
            'display_name' => 'boilerplate-email-editor::permissions.layout_crud.display_name',
            'description'  => 'boilerplate-email-editor::permissions.layout_crud.description'
        ],
        [
            'name'         => 'emaileditor_email_crud',
            'display_name' => 'boilerplate-email-editor::permissions.email_crud.display_name',
            'description'  => 'boilerplate-email-editor::permissions.email_crud.description'
        ]
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Insert default permissions
        foreach($this->permissions as $permission) {
            $permission['created_at'] = date('Y-m-d H:i:s');
            $permission['updated_at'] = date('Y-m-d H:i:s');
            DB::table('permissions')->insert($permission);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach ($this->permissions as $permission) {
            DB::table('permissions')->where('name', $permission[ 'name' ])->delete();
        }
    }
}
