<?php

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Illuminate\Database\Migrations\Migration;

class EmailsPermissions extends Migration
{
    private $permissions = [
        [
            'name'         => 'emaileditor_email_edition',
            'display_name' => 'boilerplate-email-editor::permissions.email_edition.display_name',
            'description'  => 'boilerplate-email-editor::permissions.email_edition.description',
        ],
        [
            'name'         => 'emaileditor_email_dev',
            'display_name' => 'boilerplate-email-editor::permissions.email_dev.display_name',
            'description'  => 'boilerplate-email-editor::permissions.email_dev.description',
        ],
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Insert default permissions
        foreach ($this->permissions as $permission) {
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
            DB::table('permissions')->where('name', $permission['name'])->delete();
        }
    }
}
