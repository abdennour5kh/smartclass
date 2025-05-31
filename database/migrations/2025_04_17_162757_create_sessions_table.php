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
        Schema::create('sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId("classe_id")->constrained()->onDelete("cascade");
            $table->date("session_date");
            $table->time('start_time');
            $table->time('end_time');
            $table->string("location", 20);
            $table->string("notes")->nullable();
            $table->enum('status', ['scheduled', 'completed', 'canceled', 'rescheduled'])->default("scheduled");
            $table->string("type", 3);

            $table->unique(['classe_id', 'session_date', 'start_time', 'end_time'], 'unique_session_per_slot');
            $table->unique(['session_date', 'start_time', 'end_time', 'location'], 'unique_location_booking');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};
