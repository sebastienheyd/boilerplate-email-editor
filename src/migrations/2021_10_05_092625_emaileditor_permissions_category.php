<?php

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Illuminate\Database\Migrations\Migration;

class EmaileditorPermissionsCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $id = DB::table('permissions_categories')->insertGetId([
            'name'         => 'emaileditor',
            'display_name' => 'boilerplate-email-editor::permissions.category',
        ]);

        DB::table('permissions')->where('name', 'like', 'emaileditor_%')
            ->update(['category_id' => $id]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('permissions')->where('name', 'like', 'emaileditor_')->update(['category_id' => null]);
        DB::table('permissions_categories')->where('name', 'emaileditor')->delete();
    }
}
