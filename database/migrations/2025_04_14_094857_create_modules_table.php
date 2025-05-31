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
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId("semester_id")->constrained()->onDelete("cascade");
            $table->string("name");
            $table->string("color", 7); // hex color difrentiate each module
            $table->string("img_url")->nullable();
            $table->timestamps();

            $table->unique(["semester_id", "name"]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
