<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClassroomAlunoController;
use App\Http\Controllers\ClassroomProfessorController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\SimuladoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MateriaController;
use App\Http\Controllers\CargoController;

// ─── Raiz ────────────────────────────────────────────────────────────────────
Route::get('/', function () {
    if (!Auth::check()) {
        return redirect('/login');
    }

    return match (session('user_cargo')) {
        'professor' => redirect('/professor/dashboard'),
        'admin'     => redirect('/admin/dashboard'),
        default     => redirect('/aluno/dashboard'),
    };
})->name('home');

// ─── Autenticação (públicas) ──────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',     [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',    [AuthController::class, 'autenticar'])->name('autenticar');
    Route::get('/registro',  [AuthController::class, 'showRegister'])->name('register');
    Route::post('/registro', [AuthController::class, 'registrar'])->name('registrar');
});

Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ─── Aluno ────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:aluno'])->prefix('aluno')->name('aluno.')->group(function () {
    Route::get('/dashboard',         [DashboardController::class, 'DashboardAluno'])->name('dashboard');
    Route::get('/buscar-sala',       [ClassroomAlunoController::class, 'BuscarSalaAluno'])->name('buscar-sala');
    Route::get('/historico-aulas',   [ClassroomAlunoController::class, 'HistoricoAulasAluno'])->name('minhas-aulas');
    Route::get('/simulados',         [ClassroomAlunoController::class, 'SimuladosAluno'])->name('simulados');
    Route::get('/sala/{id}',         [ClassroomAlunoController::class, 'showClassroom'])->name('show');
    Route::post('/sala/{id}/entrar', [ClassroomAlunoController::class, 'join'])->name('join');
});

// ─── Professor ────────────────────────────────────────────────────────────────
// Rotas geradas automaticamente pelo resource:
//   professor.salas.index   GET  /professor/salas
//   professor.salas.create  GET  /professor/salas/create
//   professor.salas.store   POST /professor/salas
//   professor.salas.show    GET  /professor/salas/{sala}
//   professor.salas.edit    GET  /professor/salas/{sala}/edit
//   professor.salas.update  PUT  /professor/salas/{sala}
//   professor.salas.destroy DELETE /professor/salas/{sala}
//
//   professor.materiais.index   GET  /professor/materiais
//   professor.materiais.create  GET  /professor/materiais/create
//   professor.materiais.store   POST /professor/materiais
//   professor.materiais.show    GET  /professor/materiais/{material}
//   professor.materiais.edit    GET  /professor/materiais/{material}/edit
//   professor.materiais.update  PUT  /professor/materiais/{material}
//   professor.materiais.destroy DELETE /professor/materiais/{material}
//
//   professor.simulados.index   GET  /professor/simulados
//   professor.simulados.create  GET  /professor/simulados/create
//   professor.simulados.store   POST /professor/simulados
//   professor.simulados.show    GET  /professor/simulados/{simulado}
//   professor.simulados.edit    GET  /professor/simulados/{simulado}/edit
//   professor.simulados.update  PUT  /professor/simulados/{simulado}
//   professor.simulados.destroy DELETE /professor/simulados/{simulado}
Route::middleware(['auth', 'role:professor'])->prefix('professor')->name('professor.')->group(function () {
 
    Route::get('/dashboard', [DashboardController::class, 'DashboardProfessor'])->name('dashboard');
 
    // Salas de Aula
    Route::resource('salas', ClassroomProfessorController::class);
    Route::patch('salas/{sala}/iniciar',  [ClassroomProfessorController::class, 'iniciar'])->name('salas.iniciar');
    Route::patch('salas/{sala}/encerrar', [ClassroomProfessorController::class, 'encerrar'])->name('salas.encerrar');
 
    // Materiais (views em resources/views/material/create.blade.php etc.)
    Route::resource('materiais', MaterialController::class);
 
    // Simulados (views em resources/views/simulado/create.blade.php etc.)
    Route::resource('simulados', SimuladoController::class);
 
    // Outras seções
    Route::get('/conteudos',  [ClassroomProfessorController::class, 'teacherContents'])->name('conteudos');
    Route::get('/avaliacoes', [ClassroomProfessorController::class, 'teacherEvaluations'])->name('avaliacoes');
    Route::get('/relatorios', [ClassroomProfessorController::class, 'teacherReports'])->name('relatorios');
});

// ─── Admin ────────────────────────────────────────────────────────────────────
// O grupo já tem name('admin.'), o resource herda e gera automaticamente:
//   admin.usuarios.index  | .create | .store | .edit | .update | .destroy
//   admin.materias.index  | .create | .store | .edit | .update | .destroy
//   admin.materias.toggle
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'DashboardAdmin'])->name('dashboard');

    Route::resource('usuarios', UserController::class);

    Route::resource('cargos', CargoController::class);
    Route::resource('materias', MateriaController::class);
    Route::patch('materias/{materia}/toggle', [MateriaController::class, 'toggle'])->name('materias.toggle');
});