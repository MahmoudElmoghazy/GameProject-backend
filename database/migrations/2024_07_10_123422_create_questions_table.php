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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('type',['match','mcq','true_false','range']);
            $table->unsignedBigInteger('right_answer_id');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('difficulty_id');
            $table->string('image')->nullable();
            $table->string('start_range')->nullable();
            $table->string('end_range')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
