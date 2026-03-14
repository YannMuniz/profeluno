<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\UserController;

// Rota de início - redireciona para login (evita erro de rota [home] não definida nos views)
Route::get('/', function () {
    return Auth::check()
        ? redirect(session('cargo_id') === 1 ? '/aluno/dashboard' : '/professor/dashboard')
        : redirect('/login');
})->name('home');

// Autenticação - Rotas públicas
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'autenticar'])->name('autenticar');
    Route::get('/registro', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/registro', [AuthController::class, 'registrar'])->name('registrar');
    Route::post('/configuracoes', [AuthController::class, 'configurar'])->name('configuracoes');

    // Rota de teste/integração com a API .NET para validar usuário (email + senha)
    Route::post('/dotnet/verify-user', [AuthController::class, 'verifyUser']);
});

// Logout deve ser feito via POST para maior segurança, mas aceitamos GET para simplificar cliques em link.
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Rotas Protegidas - Aluno
Route::middleware(['auth'])->prefix('aluno')->name('aluno.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'studentDashboard'])->name('dashboard');
    Route::get('/buscar-sala', [ClassroomController::class, 'studentBrowse'])->name('buscar-sala');
    Route::get('/minhas-aulas', [ClassroomController::class, 'studentBrowse'])->name('minhas-aulas');
    Route::get('/historico', [ClassroomController::class, 'studentBrowse'])->name('historico');
    Route::get('/sala/{id}', [ClassroomController::class, 'showClassroom'])->name('show');
    Route::post('/sala/{id}/entrar', [ClassroomController::class, 'join'])->name('join');
});

// Rotas Protegidas - Professor
Route::middleware(['auth'])->prefix('professor')->name('professor.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'teacherDashboard'])->name('dashboard');
    Route::get('/salas', [ClassroomController::class, 'teacherClassrooms'])->name('sala-aula');
    Route::get('/salas/create', [ClassroomController::class, 'teacherClassrooms'])->name('sala-aula.create');
    Route::get('/conteudos', [ClassroomController::class, 'teacherContents'])->name('conteudos');
    Route::get('/avaliacoes', [ClassroomController::class, 'teacherEvaluations'])->name('avaliacoes');
    Route::get('/relatorios', [ClassroomController::class, 'teacherReports'])->name('relatorios');
});

// Rotas Protegidas - Admin
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('dashboard');
    Route::get('/usuarios', [UserController::class, 'usuarios'])->name('usuarios');
});
