<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWebUrlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('web_urls', function (Blueprint $table) {
            $table->uuid('id');
            $table->uuid('service_id');
            $table->string('path');
            $table->string('file_type');
            $table->string('word_length');
            $table->string('char_length');
            $table->integer('statusCode');
            $table->unique(['service_id', 'path']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('web_urls');
    }
}
