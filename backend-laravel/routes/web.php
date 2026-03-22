<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClassroomController;
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
    Route::get('/buscar-sala',       [ClassroomController::class, 'BuscarSalaAluno'])->name('buscar-sala');
    Route::get('/historico-aulas',   [ClassroomController::class, 'HistoricoAulasAluno'])->name('minhas-aulas');
    Route::get('/simulados',         [ClassroomController::class, 'SimuladosAluno'])->name('simulados');
    Route::get('/sala/{id}',         [ClassroomController::class, 'showClassroom'])->name('show');
    Route::post('/sala/{id}/entrar', [ClassroomController::class, 'join'])->name('join');
});

// ─── Professor ────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:professor'])->prefix('professor')->name('professor.')->group(function () {
    Route::get('/dashboard',    [DashboardController::class, 'DashboardProfessor'])->name('dashboard');
    Route::get('/salas',        [ClassroomController::class, 'teacherClassrooms'])->name('sala-aula');
    Route::get('/salas/create', [ClassroomController::class, 'teacherClassrooms'])->name('sala-aula.create');
    Route::get('/conteudos',    [ClassroomController::class, 'teacherContents'])->name('conteudos');
    Route::get('/avaliacoes',   [ClassroomController::class, 'teacherEvaluations'])->name('avaliacoes');
    Route::get('/relatorios',   [ClassroomController::class, 'teacherReports'])->name('relatorios');
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