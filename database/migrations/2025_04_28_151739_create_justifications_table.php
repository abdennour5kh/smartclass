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
        Schema::create('justifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('session_id')->constrained()->onDelete('cascade');

            // we need to get the admin/teacher decision
            // if both 1 then status approved
            // else then status refused
            // default to pending

            $table->enum('admin_decision', ['1', '0', '2'])->default('2'); // 2 = pending review
            $table->enum('teacher_decision', ['1', '0', '2'])->default('2');
            $table->enum('status', ['pending', 'approved', 'refused'])->default('pending');
            
            $table->text('message')->nullable();

            $table->timestamps();

            $table->unique(['student_id', 'session_id']); // student can only justifiy session once
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('justifications');
    }
};
