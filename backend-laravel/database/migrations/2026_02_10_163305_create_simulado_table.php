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

            // ── Dados do simulado ──────────────────────────────────────────
            $table->string('titulo', 255);
            $table->text('descricao')->nullable();
            $table->boolean('situacao')->default(true)->comment('1 = ativo, 0 = inativo');

            // ── Vínculo com sala de aula ───────────────────────────────────
            $table->unsignedBigInteger('sala_aula_id')->nullable();
            $table->foreign('sala_aula_id')
                  ->references('id')
                  ->on('sala_aula')
                  ->nullOnDelete();

            // ── Vínculo com professor ──────────────────────────────────────
            $table->unsignedBigInteger('professor_id')->nullable();
            $table->foreign('professor_id')
                  ->references('id')
                  ->on('users')
                  ->nullOnDelete();

            $table->timestamps();
        });

        // ── Tabela de questões (separada do simulado) ──────────────────────
        Schema::create('simulado_questao', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('simulado_id');
            $table->foreign('simulado_id')
                  ->references('id')
                  ->on('simulado')
                  ->cascadeOnDelete();

            $table->integer('ordem')->default(1)->comment('Posição da questão no simulado');
            $table->text('enunciado');

            $table->text('questao_a');
            $table->text('questao_b');
            $table->text('questao_c');
            $table->text('questao_d');
            $table->text('questao_e')->nullable()->comment('Alternativa E é opcional');

            // 1 = A, 2 = B, 3 = C, 4 = D, 5 = E
            $table->tinyInteger('questao_correta');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('simulado_questao');
        Schema::dropIfExists('simulado');
    }
};