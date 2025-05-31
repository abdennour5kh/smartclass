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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId("student_id")->constrained()->onDelete("cascade");
            $table->foreignId("session_id")->constrained()->onDelete("cascade");
            $table->enum('status', ['present', 'absent', 'late', 'excused', 'justified'])->default('absent');
            $table->text("notes")->nullable();

            $table->unique(["student_id", "session_id"]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
