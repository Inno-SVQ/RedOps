<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->uuid('id');
            $table->uuid('host_id');
            $table->enum('protocol', array('TCP', 'UDP'));
            $table->integer('port');
            $table->string('product');
            $table->string('version');
            $table->string('application_protocol');
            $table->uuid('from_job_id');
            $table->foreign('host_id')->references('id')->on('hosts')->onDelete('cascade');
            $table->unique(['port', 'host_id', 'protocol']);
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
        Schema::dropIfExists('services');
    }
}
