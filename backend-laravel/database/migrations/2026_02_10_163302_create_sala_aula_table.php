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
            // $table->enum('status', ['pending', 'active', 'completed', 'archived'])->default('pending');
            $table->dateTime('data_hora_inicio')->nullable();
            $table->dateTime('data_hora_fim')->nullable();
            $table->string('materia');
            $table->foreignId('material_id')->nullable()->constrained('material');
            $table->integer('qtd_alunos');
            $table->string('url');
            $table->float('avaliacao');
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
