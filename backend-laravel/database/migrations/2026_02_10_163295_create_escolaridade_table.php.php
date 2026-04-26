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
        Schema::create('escolaridade', function (Blueprint $table) {
            $table->id();
            $table->string('nome_escolaridade')->unique();
            $table->integer('situacao_escolaridade')->default(1); // 1 para ativa, 0 para inativa
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('escolaridade');
    }
};
