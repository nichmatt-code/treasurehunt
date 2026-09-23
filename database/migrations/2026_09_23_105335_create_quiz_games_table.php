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
        Schema::create('quiz_games', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->cascadeOnDelete();
            $table->foreignId('host_participant_id')->constrained('participants')->cascadeOnDelete();
            $table->string('room_code', 6)->unique();
            $table->enum('status', ['lobby', 'question', 'result', 'leaderboard', 'finished'])->default('lobby');
            $table->unsignedInteger('current_question_index')->default(0);
            $table->timestamp('current_question_started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_games');
    }
};
