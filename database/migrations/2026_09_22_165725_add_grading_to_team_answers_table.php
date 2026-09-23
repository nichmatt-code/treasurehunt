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
        Schema::table('team_answers', function (Blueprint $table) {
            $table->string('image_path')->nullable()->after('answer_text');
            $table->enum('status', ['pending', 'correct', 'incorrect'])->default('pending')->after('image_path');
            $table->foreignId('graded_by')->nullable()->after('status')->constrained('participants')->nullOnDelete();
            $table->timestamp('graded_at')->nullable()->after('graded_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('team_answers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('graded_by');
            $table->dropColumn(['image_path', 'status', 'graded_at']);
        });
    }
};
