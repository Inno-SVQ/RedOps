<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWebScreenshotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('web_screenshots', function (Blueprint $table) {
            $table->uuid('id');
            $table->string('service_id');
            $table->uuid('image_name');
            $table->unique(['service_id', 'image_name']);
            $table->uuid('from_job_id');
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
        Schema::dropIfExists('web_screenshots');
    }
}
