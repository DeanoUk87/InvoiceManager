<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
class CreateHtsuploadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hts_uploads', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('relatedId');
            $table->string('filename', 100);
            $table->string('tablekey',100);
            $table->string('upload_title',150);
            $table->text('upload_description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hts_uploads');
    }
}
