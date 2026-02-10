<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pendukungs', function (Blueprint $table) {
            $table->id();
            $table->string('nik')->unique();
            $table->foreign('nik')
                ->references('nik')->on('penduduks')
                ->onDelete('cascade');
            $table->string('nama');
            $table->string('alamat')->nullable();
            $table->string('rt');
            $table->string('rw');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->foreignId('koordinator_id')
                ->constrained('koordinators')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pendukungs');
    }
};
