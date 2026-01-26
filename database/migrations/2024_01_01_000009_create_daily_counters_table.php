<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDailyCountersTable extends Migration
{
    public function up()
    {
        Schema::create('daily_counters', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->unsignedBigInteger('service_type_id');
            $table->integer('last_number')->default(0);
            $table->integer('total_generated')->default(0);
            $table->timestamps();

            $table->foreign('service_type_id')->references('id')->on('service_types')->onDelete('cascade');
            $table->unique(['date', 'service_type_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('daily_counters');
    }
}
