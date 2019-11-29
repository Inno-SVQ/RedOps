<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWebTechnologiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('web_technologies', function (Blueprint $table) {
            $table->uuid('id');
            $table->uuid('service_id');
            $table->string('name');
            $table->string('icon');
            $table->uuid('from_job_id');
            $table->unique(['name', 'service_id']);
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
        Schema::dropIfExists('web_technologies');
    }
}
