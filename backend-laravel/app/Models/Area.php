<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Area extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'area';

    protected $fillable = [
        'nome_area',
        'situacao_area',
    ];

    protected $hidden = [
        'remember_token',
    ];
}