<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClassroomProfessorController extends Controller
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('DOTNET_API_URL', 'http://profeluno_dotnet:9000');
    }

    public function create() {
        // Lógica para criar uma nova sala de aula para o professor
    }

    public function index() {
        // Lógica para listar as salas de aula do professor
    }
}
