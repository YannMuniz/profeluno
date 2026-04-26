<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Escolaridade extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'escolaridade';

    protected $fillable = [
        'nome_escolaridade',
    ];

    protected $hidden = [
        'remember_token',
    ];
}