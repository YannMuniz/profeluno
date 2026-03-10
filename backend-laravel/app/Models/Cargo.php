<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Cargo extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nome_cargo',
    ];

    protected $hidden = [
        'remember_token',
    ];
}