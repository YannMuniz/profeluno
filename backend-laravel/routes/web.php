<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AlunoController;
use App\Http\Controllers\ProfessorController;
use App\Http\Controllers\SalaController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistema de Aulas Virtuais
|--------------------------------------------------------------------------
*/

// Rotas Públicas
Route::get('/', function () {
    return view('index');
})->name('home');

// Autenticação
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/registro', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/registro', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Rotas Protegidas - Aluno
Route::middleware(['auth', 'role:aluno'])->prefix('aluno')->name('aluno.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AlunoController::class, 'dashboard'])->name('dashboard');
    
    // Buscar Sala
    Route::get('/buscar-sala', [AlunoController::class, 'buscarSala'])->name('buscar-sala');
    
    // Sala de Aula
    Route::get('/sala/{id}', [SalaController::class, 'entrarAluno'])->name('sala.entrar');
    Route::post('/sala/{id}/entrar', [SalaController::class, 'entrar'])->name('sala.join');
    
    // Simulados
    Route::get('/simulados', [AlunoController::class, 'simulados'])->name('simulados');
    Route::get('/simulado/{id}', [AlunoController::class, 'verSimulado'])->name('simulado.ver');
    Route::post('/simulado/{id}/responder', [AlunoController::class, 'responderSimulado'])->name('simulado.responder');
    
    // Conteúdos
    Route::get('/conteudos', [AlunoController::class, 'conteudos'])->name('conteudos');
    Route::get('/conteudo/{id}', [AlunoController::class, 'verConteudo'])->name('conteudo.ver');
    
    // Professor Details
    Route::get('/professor/{id}', [AlunoController::class, 'verProfessor'])->name('professor.detalhes');
    
    // Configurações
    Route::get('/configuracoes', [AlunoController::class, 'configuracoes'])->name('configuracoes');
    Route::put('/configuracoes', [AlunoController::class, 'atualizarConfiguracoes'])->name('configuracoes.atualizar');
    
    // Perfil
    Route::get('/perfil', [AlunoController::class, 'perfil'])->name('perfil');
    Route::put('/perfil', [AlunoController::class, 'atualizarPerfil'])->name('perfil.atualizar');
});

// Rotas Protegidas - Professor
Route::middleware(['auth', 'role:professor'])->prefix('professor')->name('professor.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [ProfessorController::class, 'dashboard'])->name('dashboard');
    
    // Salas de Aula
    Route::get('/salas', [ProfessorController::class, 'salas'])->name('salas');
    Route::get('/sala/criar', [ProfessorController::class, 'criarSala'])->name('sala.criar');
    Route::post('/sala', [ProfessorController::class, 'salvarSala'])->name('sala.salvar');
    Route::get('/sala/{id}', [SalaController::class, 'entrarProfessor'])->name('sala.entrar');
    Route::get('/sala/{id}/editar', [ProfessorController::class, 'editarSala'])->name('sala.editar');
    Route::put('/sala/{id}', [ProfessorController::class, 'atualizarSala'])->name('sala.atualizar');
    Route::delete('/sala/{id}', [ProfessorController::class, 'deletarSala'])->name('sala.deletar');
    
    // Simulados
    Route::get('/simulados', [ProfessorController::class, 'simulados'])->name('simulados');
    Route::get('/simulado/criar', [ProfessorController::class, 'criarSimulado'])->name('simulado.criar');
    Route::post('/simulado', [ProfessorController::class, 'salvarSimulado'])->name('simulado.salvar');
    Route::get('/simulado/{id}/editar', [ProfessorController::class, 'editarSimulado'])->name('simulado.editar');
    Route::put('/simulado/{id}', [ProfessorController::class, 'atualizarSimulado'])->name('simulado.atualizar');
    Route::delete('/simulado/{id}', [ProfessorController::class, 'deletarSimulado'])->name('simulado.deletar');
    
    // Conteúdos
    Route::get('/conteudos', [ProfessorController::class, 'conteudos'])->name('conteudos');
    Route::get('/conteudo/criar', [ProfessorController::class, 'criarConteudo'])->name('conteudo.criar');
    Route::post('/conteudo', [ProfessorController::class, 'salvarConteudo'])->name('conteudo.salvar');
    Route::get('/conteudo/{id}/editar', [ProfessorController::class, 'editarConteudo'])->name('conteudo.editar');
    Route::put('/conteudo/{id}', [ProfessorController::class, 'atualizarConteudo'])->name('conteudo.atualizar');
    Route::delete('/conteudo/{id}', [ProfessorController::class, 'deletarConteudo'])->name('conteudo.deletar');
    
    // Alunos
    Route::get('/alunos', [ProfessorController::class, 'alunos'])->name('alunos');
    Route::get('/aluno/{id}', [ProfessorController::class, 'verAluno'])->name('aluno.ver');
    
    // Configurações
    Route::get('/configuracoes', [ProfessorController::class, 'configuracoes'])->name('configuracoes');
    Route::put('/configuracoes', [ProfessorController::class, 'atualizarConfiguracoes'])->name('configuracoes.atualizar');
    
    // Perfil
    Route::get('/perfil', [ProfessorController::class, 'perfil'])->name('perfil');
    Route::put('/perfil', [ProfessorController::class, 'atualizarPerfil'])->name('perfil.atualizar');
});

// Rotas de API para Sala de Aula (WebRTC, Chat, etc)
Route::middleware('auth')->prefix('api')->name('api.')->group(function () {
    // Chat
    Route::post('/sala/{id}/mensagem', [SalaController::class, 'enviarMensagem'])->name('sala.mensagem');
    Route::get('/sala/{id}/mensagens', [SalaController::class, 'buscarMensagens'])->name('sala.mensagens');
    
    // Participantes
    Route::get('/sala/{id}/participantes', [SalaController::class, 'listarParticipantes'])->name('sala.participantes');
    Route::post('/sala/{id}/levantar-mao', [SalaController::class, 'levantarMao'])->name('sala.levantar-mao');
    
    // Notificações
    Route::get('/notificacoes', [AlunoController::class, 'notificacoes'])->name('notificacoes');
    Route::put('/notificacao/{id}/ler', [AlunoController::class, 'marcarComoLida'])->name('notificacao.ler');
    
    // Busca
    Route::get('/buscar-professores', [AlunoController::class, 'buscarProfessoresApi'])->name('buscar-professores');
});