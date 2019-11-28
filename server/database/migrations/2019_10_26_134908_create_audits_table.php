<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateAuditsTable
 *
 * class Audit{
 *  Id
 *  Name
 *  StartDate
 *  EndDate
 *  Type
 *  Owner
 * }
 *
 * Audit "*" --- "1..*" AuditorAudit: > composedBy
 */
class CreateAuditsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audits', function (Blueprint $table) {
            $table->uuid('id');
            $table->string('name');
            $table->timestamp('startDate');
            $table->timestamp('endDate');
            $table->enum('type', ['red_team', 'penetration_test', 'web_audit']);
            $table->integer('owner')->unsigned();
            $table->foreign('owner')->references('id')->on('users')->onDelete('cascade');
            $table->primary('id');
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
        Schema::dropIfExists('audits');
    }
}
