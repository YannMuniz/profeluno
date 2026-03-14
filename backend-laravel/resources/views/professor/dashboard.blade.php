{{-- resources/views/professor/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard - Professor')

@section('content')

<div class="stats-row">
    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-icon primary">
                <i class="fas fa-users"></i>
            </div>
        </div>
        <div class="stat-value">
            <h3>{{ $classrooms->sum(fn($c) => $c->students()->count()) }}</h3>
            <p class="stat-label">Total de Alunos</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-icon success">
                <i class="fas fa-chalkboard"></i>
            </div>
        </div>
        <div class="stat-value">
            <h3>{{ $totalClasses }}</h3>
            <p class="stat-label">Total de Aulas</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-icon warning">
                <i class="fas fa-play-circle"></i>
            </div>
        </div>
        <div class="stat-value">
            <h3>{{ $activeClasses }}</h3>
            <p class="stat-label">Aulas Ativas</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-icon info">
                <i class="fas fa-book-open"></i>
            </div>
        </div>
        <div class="stat-value">
            <h3>{{ $classrooms->where('status', 'completed')->count() }}</h3>
            <p class="stat-label">Aulas Concluídas</p>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="quick-actions">
    <h2 class="section-title">Ações Rápidas</h2>
    <div class="action-cards">
        <a href="{{ route('professor.sala-aula.create') }}" class="action-card">
            <div class="action-icon">
                <i class="fas fa-plus"></i>
            </div>
            <h3>Criar Nova Aula</h3>
            <p>Crie uma nova sala de aula virtual para seus alunos</p>
        </a>

        <a href="{{ route('professor.sala-aula') }}" class="action-card">
            <div class="action-icon">
                <i class="fas fa-chalkboard-user"></i>
            </div>
            <h3>Minhas Salas</h3>
            <p>Gerencie e acompanhe todas as suas salas de aula</p>
        </a>

        <a href="{{ route('professor.relatorios') }}" class="action-card">
            <div class="action-icon">
                <i class="fas fa-chart-bar"></i>
            </div>
            <h3>Relatórios</h3>
            <p>Veja estatísticas e relatórios de desempenho</p>
        </a>
    </div>
</div>

<!-- Recent Classes -->
<div class="recent-activity">
    <h2 class="section-title">Aulas Recentes</h2>

    @if($classrooms->count() > 0)
        @foreach($classrooms as $classroom)
            <div class="activity-item">
                <div class="activity-icon" style="background: rgba(115, 103, 240, 0.15); color: var(--primary-color);">
                    <i class="fas fa-chalkboard-user"></i>
                </div>
                <div class="activity-content" style="flex: 1;">
                    <h4>{{ $classroom->title }}</h4>
                    <p>
                        {{ $classroom->subject ?? 'Sem matéria' }} &mdash;
                        {{ $classroom->students()->count() }}/{{ $classroom->max_students }} alunos
                    </p>
                </div>
                <div style="margin-left: auto; display: flex; align-items: center; gap: 12px;">
                    @if($classroom->status === 'completed')
                        <span class="badge-status badge-completed">
                            <i class="fas fa-check"></i> Concluída
                        </span>
                    @elseif($classroom->status === 'active')
                        <span class="badge-status badge-active">
                            <i class="fas fa-play"></i> Ativa
                        </span>
                    @else
                        <span class="badge-status badge-pending">
                            <i class="fas fa-clock"></i> Pendente
                        </span>
                    @endif

                    <a href="{{ route('aluno.show', $classroom->id) }}" class="view-btn">
                        <i class="fas fa-eye"></i>
                    </a>
                </div>
            </div>
        @endforeach
    @else
        <div class="empty-state">
            <i class="fas fa-inbox" style="font-size: 48px; color: var(--text-secondary); margin-bottom: 15px;"></i>
            <h3 style="color: var(--text-primary); margin-bottom: 8px;">Nenhuma aula criada</h3>
            <p style="color: var(--text-secondary);">Você ainda não criou nenhuma aula.</p>
        </div>
    @endif
</div>

@endsection