<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hts_posts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 100);
            $table->text('content')->nullable();
            $table->dateTime('created');
            $table->dateTime('modified')->nullable();
            $table->string('picture')->nullable();
            //$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('hts_posts');
    }
}
