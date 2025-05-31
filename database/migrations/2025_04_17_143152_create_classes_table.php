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
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            // this is a pivot table , it enusers cases where the same module 
            // could be tought to diffrent groups by diffrent teachers

            $table->foreignId("group_id")->constrained()->onDelete("cascade");
            $table->foreignId("module_id")->constrained()->onDelete("cascade");
            $table->foreignId("teacher_id")->constrained()->onDelete("cascade");
            $table->enum('class_type', ['TP', 'TD']);

            //  // 1. Prevents two teachers from teaching the same group+module+class_type
            //  $table->unique(["group_id", "module_id", "class_type"]);

            //  // 2. Prevents the same teacher from teaching two modules to the same group
            //  $table->unique(["group_id", "teacher_id"]);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
