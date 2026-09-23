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
        Schema::create('quiz_players', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_game_id')->constrained()->cascadeOnDelete();
            $table->string('nickname');
            $table->unsignedInteger('score')->default(0);
            $table->string('player_token')->unique();
            $table->timestamp('joined_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_players');
    }
};
