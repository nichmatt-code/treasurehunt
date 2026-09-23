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
        Schema::create('quiz_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_game_id')->constrained()->cascadeOnDelete();
            $table->foreignId('quiz_player_id')->constrained()->cascadeOnDelete();
            $table->foreignId('quiz_question_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('selected_option_index')->nullable();
            $table->boolean('is_correct')->default(false);
            $table->unsignedInteger('response_time_ms')->default(0);
            $table->unsignedInteger('score_awarded')->default(0);
            $table->timestamp('answered_at');
            $table->timestamps();

            $table->unique(['quiz_player_id', 'quiz_question_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_answers');
    }
};
