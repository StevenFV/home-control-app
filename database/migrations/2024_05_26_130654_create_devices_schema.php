<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateDevicesSchema extends Migration
{
    public function up(): void
    {
        DB::statement('CREATE SCHEMA IF NOT EXISTS devices');
    }

    public function down(): void
    {
        DB::statement('DROP SCHEMA IF EXISTS devices');
    }
}
