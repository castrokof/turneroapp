<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQueuesTable extends Migration
{
    public function up()
    {
        Schema::create('queues', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number', 20);
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('service_type_id');
            $table->unsignedBigInteger('service_window_id')->nullable();
            $table->unsignedBigInteger('agent_id')->nullable();
            $table->enum('priority', ['emergency', 'priority', 'scheduled', 'normal'])->default('normal');
            $table->enum('status', [
                'pending',      // En espera
                'called',       // Llamado
                'in_progress',  // En atención
                'completed',    // Finalizado
                'absent',       // Ausente
                'transferred',  // Trasladado
                'cancelled'     // Cancelado
            ])->default('pending');
            $table->boolean('is_express')->default(false);
            $table->text('notes')->nullable();
            $table->timestamp('called_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('wait_time')->nullable(); // segundos de espera
            $table->integer('service_time')->nullable(); // segundos de atención
            $table->date('queue_date');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('client_id')->references('id')->on('clients')->onDelete('set null');
            $table->foreign('service_type_id')->references('id')->on('service_types')->onDelete('cascade');
            $table->foreign('service_window_id')->references('id')->on('service_windows')->onDelete('set null');
            $table->foreign('agent_id')->references('id')->on('users')->onDelete('set null');

            $table->index(['queue_date', 'status']);
            $table->index(['service_type_id', 'status']);
            $table->index('ticket_number');
        });
    }

    public function down()
    {
        Schema::dropIfExists('queues');
    }
}
