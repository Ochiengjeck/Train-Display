<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('device_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('station_name');
            $table->string('device_serial_number');
            $table->string('device_model');
            $table->string('station_id')->default('1');
            $table->text('api_token')->nullable();
            $table->timestamps();
        });

        Schema::create('device_registration_history', function (Blueprint $table) {
            $table->id();
            $table->string('station_name');
            $table->string('device_serial_number');
            $table->string('device_model');
            $table->string('station_id')->default('1');
            $table->text('api_token')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_registration');
    }
};
