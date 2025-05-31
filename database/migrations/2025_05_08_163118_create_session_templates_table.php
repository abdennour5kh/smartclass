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
        Schema::create('session_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classe_id')->constrained()->onDelete("cascade");
            $table->enum('weekday', ['0','1','2','3','4','5','6']); // Sunday = 0
            $table->time('start_time');
            $table->time('end_time');
            $table->string('location', 20);
            $table->string('notes')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->string('type', 3);
            $table->timestamps();

            $table->unique(['classe_id', 'weekday', 'start_time', 'end_time'], 'unique_session_per_time');
            $table->unique(['weekday', 'start_time', 'end_time', 'location'], 'unique_location_per_session');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('session_templates');
    }
};
