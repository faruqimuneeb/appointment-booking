<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceBreaksTable extends Migration
{
    public function up()
    {
        Schema::create('service_breaks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_id');
            $table->string('name')->nullable();
            $table->string('description')->nullable();
            $table->string('day_of_week');
            $table->time('start_time');
            $table->time('end_time');
            // Add any other columns you need for the service breaks
            $table->timestamps();

            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('service_breaks');
    }
}
