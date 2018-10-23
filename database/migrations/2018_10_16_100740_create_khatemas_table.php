<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKhatemasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('khatemas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('completed_pages')->nullable();
            $table->integer('taken_hours')->nullable();;
            $table->integer('completed')->nullable();;
            $table->integer('remaining_hours')->nullable();;
            $table->json('pages');
            $table->timestamp('completed_at')->nullable()->index();
            $table->timestamp('created_at')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('khatemas');
    }
}
