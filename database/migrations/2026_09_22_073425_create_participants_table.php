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
        Schema::create('participants', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lengkap');
            $table->string('email')->unique();
            $table->string('no_hp', 20);
            $table->text('alamat');
            $table->string('sekolah');
            $table->boolean('sudah_cg')->default(false);
            $table->string('no_cg')->nullable();
            $table->enum('coach', ['Nichmatt', 'Yoyo', 'Stefani'])->nullable();
            $table->string('session_token')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participants');
    }
};
