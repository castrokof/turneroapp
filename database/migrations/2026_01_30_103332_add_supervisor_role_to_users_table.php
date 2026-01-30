<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddSupervisorRoleToUsersTable extends Migration
{
    public function up()
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'supervisor', 'agent', 'viewer') DEFAULT 'agent'");
    }

    public function down()
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'agent', 'viewer') DEFAULT 'agent'");
    }
}
