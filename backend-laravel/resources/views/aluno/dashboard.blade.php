{{-- resources/views/aluno/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard - Aluno')

@section('content')
<div class="stats-row">
    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-icon primary">
                <i class="fas fa-book"></i>
            </div>
        </div>
        <div class="stat-value">
            <h3>{{ $totalClasses }}</h3>
            <p class="stat-label">Total de Aulas</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-icon success">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
        <div class="stat-value">
            <h3>{{ $completedClasses }}</h3>
            <p class="stat-label">Aulas Concluídas</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-icon info">
                <i class="fas fa-calendar-check"></i>
            </div>
        </div>
        <div class="stat-value">
            <h3>{{ now()->format('d/m') }}</h3>
            <p class="stat-label">Data de Hoje</p>
        </div>
    </div>
</div>

<div class="quick-actions">
    <h2 class="section-title">
        <i class="fas fa-lightning-bolt"></i>
        Ações Rápidas
    </h2>
    <div class="action-cards">
        <a href="{{ route('aluno.salas.index') }}" class="action-card">
            <div class="action-icon">
                <i class="fas fa-search"></i>
            </div>
            <h3>Buscar Salas</h3>
            <p>Encontre novas aulas para participar</p>
        </a>
        <a href="#" class="action-card">
            <div class="action-icon">
                <i class="fas fa-file-pdf"></i>
            </div>
            <h3>Materiais</h3>
            <p>Acesse conteúdos e materiais</p>
        </a>
        <a href="#" class="action-card">
            <div class="action-icon">
                <i class="fas fa-chart-bar"></i>
            </div>
            <h3>Desempenho</h3>
            <p>Verifique seu progresso</p>
        </a>
    </div>
</div>

<div class="recent-classes">
    <h2 class="section-title">
        <i class="fas fa-history"></i>
        Aulas Recentes
    </h2>

    @if($classrooms->count() > 0)
        <div class="classes-list">
            @foreach($classrooms as $classroom)
                <div class="class-item">
                    <div class="class-info">
                        <div class="class-icon">
                            <i class="fas fa-chalkboard-user"></i>
                        </div>
                        <div class="class-details">
                            <h4>{{ $classroom->title }}</h4>
                            <p><strong>Professor:</strong> {{ $classroom->teacher->name }}</p>
                            <p><strong>Matéria:</strong> {{ $classroom->subject ?? 'Não informado' }}</p>
                        </div>
                    </div>
                    <div class="class-meta">
                        @if($classroom->status === 'completed')
                            <span class="class-status status-completed">
                                <i class="fas fa-check"></i> Concluída
                            </span>
                        @elseif($classroom->status === 'active')
                            <span class="class-status status-pending">
                                <i class="fas fa-play"></i> Em andamento
                            </span>
                        @endif
                    </div>
                    <a href="{{ route('aluno.salas.show', $classroom->id) }}" class="view-btn">
                        <i class="fas fa-eye"></i>
                    </a>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <h3>Nenhuma aula ainda</h3>
            <p>Você ainda não participa de nenhuma aula. <a href="{{ route('aluno.salas.index') }}">Buscar salas</a></p>
        </div>
    @endif
</div>
@endsection