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
        Schema::create('conteudo', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->foreignId('materia_id')->nullable()->constrained('materias');
            $table->text('descricao')->nullable();
            $table->enum('tipo', ['pdf', 'slide', 'document'])->default('null');
            $table->binary('arquivo')->nullable();
            $table->string('nome_arquivo')->nullable();
            $table->string('extensao_arquivo')->nullable();
            $table->string('url')->nullable();
            $table->boolean('situacao')->default(true)->comment('1 = ativo, 0 = inativo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conteudo');
    }
};
