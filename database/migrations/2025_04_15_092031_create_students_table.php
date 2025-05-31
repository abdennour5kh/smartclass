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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('registration_num')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->unsignedBigInteger('group_id');
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone_number');
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->string('img_url')->nullable();
            $table->string('address')->nullable();
            $table->date('birth_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
