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
        Schema::create('accommodation_rooms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('accommodation_id')->constrained('accommodations')->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('room_number', 10)->unique();
            $table->decimal('price_per_night', 15, 2);
            $table->integer('capacity');
            $table->enum('status', ['available', 'booked', 'maintenance', 'unavailable'])->default('available');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accommodation_rooms');
    }
};
