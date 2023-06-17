<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lighting.lights', function (Blueprint $table) {
            $table->id();
            $table->string('IEEE_address')->unique();
            $table->string('friendly_name')->unique();
            $table->string('room');
            $table->json('current_state');
            $table->json('energy_kwh');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lighting.lights');
    }
};
