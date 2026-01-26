<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceTypesTable extends Migration
{
    public function up()
    {
        Schema::create('service_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('prefix', 5);
            $table->string('color', 7)->default('#007bff');
            $table->integer('estimated_time')->default(15); // minutos
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('requires_appointment')->default(false);
            $table->integer('daily_limit')->nullable();
            $table->integer('display_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('service_types');
    }
}
