<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQueueTransfersTable extends Migration
{
    public function up()
    {
        Schema::create('queue_transfers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('queue_id');
            $table->unsignedBigInteger('from_window_id')->nullable();
            $table->unsignedBigInteger('to_window_id');
            $table->unsignedBigInteger('from_agent_id')->nullable();
            $table->unsignedBigInteger('to_agent_id')->nullable();
            $table->text('reason')->nullable();
            $table->timestamps();

            $table->foreign('queue_id')->references('id')->on('queues')->onDelete('cascade');
            $table->foreign('from_window_id')->references('id')->on('service_windows')->onDelete('set null');
            $table->foreign('to_window_id')->references('id')->on('service_windows')->onDelete('cascade');
            $table->foreign('from_agent_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('to_agent_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('queue_transfers');
    }
}
