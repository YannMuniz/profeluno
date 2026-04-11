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
        Schema::create('sala_aula', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->dateTime('data_hora_inicio')->nullable();
            $table->dateTime('data_hora_fim')->nullable();
            $table->foreignId('materia_id')->nullable()->constrained('materias')->nullOnDelete();
            $table->foreignId('conteudo_id')->nullable()->constrained('conteudo')->nullOnDelete();
            $table->foreignId('simulado_id')->nullable()->constrained('simulado')->nullOnDelete();
            $table->integer('max_alunos')->default(30); 
            $table->string('url')->nullable(); // verificar se precisa excluir ou não
            $table->string('room_name')->unique()->nullable(); // sala Jitsi
            $table->float('avaliacao')->nullable()->default(null);
            $table->enum('status', ['active', 'completed', 'pending'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sala_aula');
    }
};
