{{-- resources/views/professor/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard - Professor')

@section('content')
<div class="stats-row">
    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-icon primary">
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
            <div class="stat-icon success">
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
            <div class="stat-icon warning">
                <i class="fas fa-play-circle"></i>
            </div>
        </div>
        <div class="stat-value">
            <h3>{{ $activeClasses }}</h3>
            <p class="stat-label">Aulas Ativas</p>
        </div>
    </div>
</div>

<div class="quick-actions">
    <h2 class="section-title">
        <i class="fas fa-lightning-bolt"></i>
        Ações Rápidas
    </h2>
    <div class="action-cards">
        <a href="#" class="action-card">
            <div class="action-icon">
                <i class="fas fa-plus"></i>
            </div>
            <h3>Criar Nova Aula</h3>
            <p>Crie uma nova sala de aula</p>
        </a>
        <a href="{{ route('professor.classrooms') }}" class="action-card">
            <div class="action-icon">
                <i class="fas fa-list"></i>
            </div>
            <h3>Minhas Aulas</h3>
            <p>Gerencie suas salas de aula</p>
        </a>
        <a href="#" class="action-card">
            <div class="action-icon">
                <i class="fas fa-chart-bar"></i>
            </div>
            <h3>Relatórios</h3>
            <p>Veja estatísticas e relatórios</p>
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
                            <p><strong>Matéria:</strong> {{ $classroom->subject ?? 'Não informado' }}</p>
                            <p><strong>Alunos:</strong> {{ $classroom->students()->count() }} / {{ $classroom->max_students }}</p>
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
                        @else
                            <span class="class-status">
                                <i class="fas fa-clock"></i> Pendente
                            </span>
                        @endif
                    </div>
                    <a href="{{ route('aluno.show', $classroom->id) }}" class="view-btn">
                        <i class="fas fa-eye"></i>
                    </a>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <h3>Nenhuma aula criada</h3>
            <p>Você ainda não criou nenhuma aula. <a href="#">Criar nova aula</a></p>
        </div>
    @endif
</div>
@endsection
