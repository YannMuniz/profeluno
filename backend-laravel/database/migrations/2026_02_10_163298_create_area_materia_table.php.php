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
        Schema::create('area_materia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('area_id')->nullable()->constrained('area');
            $table->foreignId('materia_id')->nullable()->constrained('materias');
            $table->integer('situacao_area_materia')->default(1); // 1 para ativa, 0 para inativa
            $table->primary(['area_id', 'materia_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('area_materia');
    }
};
