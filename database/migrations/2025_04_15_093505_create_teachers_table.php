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
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId("department_id")->constrained()->onDelete("cascade");
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('email')->unique();
            $table->string('phone_number');
            $table->string('password');
            $table->string('first_name');
            $table->string('last_name');
            $table->enum('grade', ['Maître Assistant B', 'Maître Assistant A', 'Maître de Conférences A', 'Maître de Conférences B', 'Professeur']);
            $table->string('address');
            $table->string('img_url')->nullable();
    
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
