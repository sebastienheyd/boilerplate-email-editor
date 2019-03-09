<?php // phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Emails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('emails', function (Blueprint $table) {
            $table->increments('id');
            $table->string('label');
            $table->string('sender_name')->nullable();
            $table->string('sender_email')->nullable();
            $table->integer('layout_id')->unsigned()->index()->nullable();
            $table->string('description')->nullable();
            $table->string('subject');
            $table->longText('content');
            $table->softDeletes();

            $table->foreign('layout_id')->references('id')->on('emails_layouts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('emails');
    }
}
