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
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->cascadeOnDelete();
            $table->string('question');
            $table->string('image_path')->nullable();
            $table->json('options');
            $table->unsignedTinyInteger('correct_option_index');
            $table->unsignedSmallInteger('time_limit_seconds')->default(10);
            $table->unsignedInteger('points')->default(1000);
            $table->unsignedInteger('order_no')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_questions');
    }
};
