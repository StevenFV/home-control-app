<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devices.lights', function (Blueprint $table) {
            $table->id();
            $table->string('ieee_address', 18)->unique();
            $table->string('friendly_name', 70)->unique();
            $table->integer('brightness')->nullable();
            $table->float('energy')->nullable();
            $table->integer('linkquality')->nullable();
            $table->float('power')->nullable();
            $table->text('state')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devices.lights');
    }
};
