<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceWindowsTable extends Migration
{
    public function up()
    {
        Schema::create('service_windows', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 10)->unique();
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive', 'paused'])->default('inactive');
            $table->unsignedBigInteger('current_agent_id')->nullable();
            $table->string('location')->nullable();
            $table->integer('display_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('current_agent_id')->references('id')->on('users')->onDelete('set null');
        });

        // Tabla pivot para servicios que atiende cada ventanilla
        Schema::create('service_type_service_window', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_type_id');
            $table->unsignedBigInteger('service_window_id');
            $table->timestamps();

            $table->foreign('service_type_id')->references('id')->on('service_types')->onDelete('cascade');
            $table->foreign('service_window_id')->references('id')->on('service_windows')->onDelete('cascade');
            $table->unique(['service_type_id', 'service_window_id'], 'service_window_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('service_type_service_window');
        Schema::dropIfExists('service_windows');
    }
}
