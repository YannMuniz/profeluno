{{-- resources/views/aluno/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard - Aluno')

@section('content')
<!-- Stats Cards -->
<div class="stats-row">
    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-icon primary">
                <i class="fas fa-chalkboard"></i>
            </div>
        </div>
        <div class="stat-value">
            <h3>{{ $stats['aulasEmAndamento'] ?? 8 }}</h3>
            <p class="stat-label">Aulas em Andamento</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-icon success">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
        <div class="stat-value">
            <h3>{{ $stats['simuladosConcluidos'] ?? 24 }}</h3>
            <p class="stat-label">Simulados Concluídos</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-icon warning">
                <i class="fas fa-clock"></i>
            </div>
        </div>
        <div class="stat-value">
            <h3>{{ $stats['simuladosPendentes'] ?? 5 }}</h3>
            <p class="stat-label">Simulados Pendentes</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-icon info">
                <i class="fas fa-trophy"></i>
            </div>
        </div>
        <div class="stat-value">
            <h3>{{ $stats['taxaAproveitamento'] ?? 87 }}%</h3>
            <p class="stat-label">Taxa de Aproveitamento</p>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="quick-actions">
    <h2 class="section-title">Acesso Rápido</h2>
    <div class="action-cards">
        <div class="action-card" onclick="window.location.href='{{ route('aluno.buscar-sala') }}'">
            <div class="action-icon">
                <i class="fas fa-search"></i>
            </div>
            <h3>Buscar Sala de Aula</h3>
            <p>Encontre professores disponíveis nas matérias do seu interesse</p>
        </div>

        <div class="action-card" onclick="window.location.href='{{ route('aluno.simulados') }}'">
            <div class="action-icon">
                <i class="fas fa-file-alt"></i>
            </div>
            <h3>Meus Simulados</h3>
            <p>Veja seus simulados realizados e pendentes</p>
        </div>

        <div class="action-card" onclick="window.location.href='{{ route('aluno.conteudos') }}'">
            <div class="action-icon">
                <i class="fas fa-book-open"></i>
            </div>
            <h3>Conteúdo das Aulas</h3>
            <p>Acesse materiais e conteúdos das aulas anteriores</p>
        </div>
    </div>
</div>

<!-- Recent Classes -->
<div class="recent-classes">
    <h2 class="section-title">Aulas Recentes</h2>
    
    @forelse($aulasRecentes ?? [] as $aula)
    <div class="class-item">
        <div class="class-info">
            <div class="class-icon" style="background: {{ $aula['iconBg'] }}; color: {{ $aula['iconColor'] }};">
                <i class="{{ $aula['icon'] }}"></i>
            </div>
            <div class="class-details">
                <h4>{{ $aula['titulo'] }}</h4>
                <p>{{ $aula['professor'] }} - {{ $aula['topico'] }}</p>
            </div>
        </div>
        <div class="class-meta">
            <span class="class-status {{ $aula['statusClass'] }}">{{ $aula['statusLabel'] }}</span>
            <button class="view-btn" onclick="window.location.href='{{ $aula['url'] }}'">
                {{ $aula['actionLabel'] }}
            </button>
        </div>
    </div>
    @empty
    {{-- Dados padrão caso não haja aulas --}}
    <div class="class-item">
        <div class="class-info">
            <div class="class-icon" style="background: rgba(115, 103, 240, 0.15); color: var(--primary-color);">
                <i class="fas fa-calculator"></i>
            </div>
            <div class="class-details">
                <h4>Matemática Avançada</h4>
                <p>Prof. João Silva - Trigonometria e Funções</p>
            </div>
        </div>
        <div class="class-meta">
            <span class="class-status status-completed">Concluída</span>
            <button class="view-btn">Ver Conteúdo</button>
        </div>
    </div>
    @endforelse
</div>
@endsection