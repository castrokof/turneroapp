<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'agent', 'viewer'])->default('agent')->after('email');
            $table->boolean('is_active')->default(true)->after('role');
            $table->unsignedBigInteger('service_window_id')->nullable()->after('is_active');
            $table->string('avatar')->nullable()->after('service_window_id');
            $table->timestamp('last_login_at')->nullable()->after('avatar');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'is_active', 'service_window_id', 'avatar', 'last_login_at']);
        });
    }
}
