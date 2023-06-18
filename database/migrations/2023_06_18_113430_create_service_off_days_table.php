<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceOffDaysTable extends Migration
{
    public function up()
    {
        Schema::create('service_off_days', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_id');
            $table->date('off_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            // Add any other columns you need for the service off days
            $table->timestamps();

            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('service_off_days');
    }
}
