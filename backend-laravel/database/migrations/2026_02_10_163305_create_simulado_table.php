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
        Schema::create('simulado', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sala_aula_id')->constrained('sala_aula')->onDelete('cascade');
            $table->integer('questao_correta');
            $table->text('questao_a')->nullable();
            $table->text('questao_b')->nullable();
            $table->text('questao_c')->nullable();
            $table->text('questao_d')->nullable();
            $table->text('questao_e')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('simulado');
    }
};
