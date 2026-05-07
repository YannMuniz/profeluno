<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SalaAulaAlunoController;
use App\Http\Controllers\SalaAulaProfessorController;
use App\Http\Controllers\ConteudoController;
use App\Http\Controllers\SimuladoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\EscolaridadeController;
use App\Http\Controllers\CargoController;
use App\Http\Controllers\MateriaController;
use App\Http\Controllers\ProfileController;

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

// ─── Perfil (todos os usuários autenticados) ──────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/perfil', [ProfileController::class, 'edit'])->name('perfil.edit');
    Route::put('/perfil', [ProfileController::class, 'update'])->name('perfil.update');
});

// ─── Aluno ────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:aluno'])->prefix('aluno')->name('aluno.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'DashboardAluno'])->name('dashboard');

    // Salas de Aula
    Route::resource('salas', SalaAulaAlunoController::class, ['only' => ['index', 'show']]);
    Route::post('salas/{id}/join', [SalaAulaAlunoController::class, 'join'])->name('salas.join');
    Route::get('salas/{id}/aguardando', [SalaAulaAlunoController::class, 'aguardando'])->name('salas.aguardando');
    Route::get('salas/{id}/video', [SalaAulaAlunoController::class, 'video'])->name('salas.video');
    Route::post('salas/{id}/leave', [SalaAulaAlunoController::class, 'leave'])->name('salas.leave');
    Route::post('salas/{id}/rating', [SalaAulaAlunoController::class, 'rating'])->name('salas.rating');
    Route::get('salas/{id}/check-liberada', [SalaAulaAlunoController::class, 'checkLiberada'])->name('salas.checkLiberada');

    // Histórico de Aulas
    Route::get('historico', [SalaAulaAlunoController::class, 'historico'])->name('historico');
    Route::get('historico/{id}', [SalaAulaAlunoController::class, 'historicoShow'])->name('historico.show');

    // Simulados
    Route::get('simulados', [SalaAulaAlunoController::class, 'simulados'])->name('simulados');
    Route::get('simulados/{id}', [SalaAulaAlunoController::class, 'simuladoShow'])->name('simulados.show');
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
//   professor.conteudo.index   GET  /professor/conteudo
//   professor.conteudo.create  GET  /professor/conteudo/create
//   professor.conteudo.store   POST /professor/conteudo
//   professor.conteudo.show    GET  /professor/conteudo/{conteudo}
//   professor.conteudo.edit    GET  /professor/conteudo/{conteudo}/edit
//   professor.conteudo.update  PUT  /professor/conteudo/{conteudo}
//   professor.conteudo.destroy DELETE /professor/conteudo/{conteudo}
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
    Route::resource('salas', SalaAulaProfessorController::class);
    Route::patch('salas/{sala}/iniciar',        [SalaAulaProfessorController::class, 'iniciar'])->name('salas.iniciar');
    Route::patch('salas/{sala}/encerrar',       [SalaAulaProfessorController::class, 'encerrar'])->name('salas.encerrar');
    Route::get('salas/{sala}/video-aula',       [SalaAulaProfessorController::class, 'videoAula'])->name('salas.video-aula');
    Route::post('salas/{sala}/liberar',         [SalaAulaProfessorController::class, 'liberarAlunos'])->name('salas.liberar');
    Route::get('salas/{sala}/contagem-alunos',  [SalaAulaProfessorController::class, 'contagemAlunos'])->name('salas.contagemAlunos');

    // Conteúdo
    Route::resource('conteudo', ConteudoController::class);
    Route::patch('conteudo/{conteudo}/toggle', [ConteudoController::class, 'toggle'])->name('conteudo.toggle');
    Route::get('conteudo/{conteudo}/download', [ConteudoController::class, 'download'])->name('conteudo.download');
 
    // Simulados
    Route::resource('simulados', SimuladoController::class);
 
    // Outras seções
    Route::get('/avaliacoes', [SalaAulaProfessorController::class, 'teacherEvaluations'])->name('avaliacoes');
    Route::get('/relatorios', [SalaAulaProfessorController::class, 'teacherReports'])->name('relatorios');
});

// ─── Admin ────────────────────────────────────────────────────────────────────
// O grupo já tem name('admin.'), o resource herda e gera automaticamente:
//   admin.usuarios.index  | .create | .store | .edit | .update | .destroy
//   admin.materias.index  | .create | .store | .edit | .update | .destroy
//   admin.materias.toggle
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'DashboardAdmin'])->name('dashboard');

    Route::resource('usuarios', UserController::class);
    Route::resource('materias', MateriaController::class);
    Route::patch('materias/{materia}/toggle', [MateriaController::class, 'toggle'])->name('materias.toggle');

    Route::resource('cargos', CargoController::class);
    Route::resource('areas', AreaController::class);
    Route::patch('areas/{area}/toggle', [AreaController::class, 'toggle'])->name('areas.toggle');
    Route::resource('escolaridades', EscolaridadeController::class);
    Route::patch('escolaridades/{escolaridade}/toggle', [EscolaridadeController::class, 'toggle'])->name('escolaridades.toggle');
});