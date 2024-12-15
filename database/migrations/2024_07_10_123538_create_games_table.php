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
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->integer('no_of_questions');
            $table->integer('no_of_players');
            $table->integer('no_of_joined_players')->default(0);
            $table->string('code')->nullable();
            $table->unsignedBigInteger('difficulty_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('category_id');
            $table->integer('time_for_each_question');
            $table->unsignedInteger('current_question')->nullable();
            $table->dateTime('started_at')->nullable();
            $table->enum('status',['pending','started','finished'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
