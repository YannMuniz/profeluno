<?php

namespace App\Models;

use App\Models\Cargo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class SalaAula extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'titulo',
        'descricao',
        'user_id',
        'data_hora_inicio',
        'data_hora_fim',
        'materia',
        'material_id',
        'qtd_alunos',
        'url',
        'avaliacao',
        'status',
    ];
}