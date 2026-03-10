{{-- resources/views/professor/sala-buscar.blade.php --}}
@extends('layouts.app')

@section('title', 'Minhas Salas de Aula')

@section('content')
<div class="current-class-section">
    <h2 class="section-title">
        <i class="fas fa-play-circle"></i>
        Aula em Andamento
    </h2>

    @php
        $activeClass = $classrooms->where('status', 'active')->first();
    @endphp

    @if($activeClass)
        <div class="current-class-card">
            <div class="current-class-header">
                <div class="current-class-info">
                    <h2>{{ $activeClass->title }}</h2>
                    <div class="current-class-meta">
                        <div class="meta-item">
                            <i class="fas fa-book"></i>
                            {{ $activeClass->subject }}
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-users"></i>
                            {{ $activeClass->students()->count() }} alunos
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-clock"></i>
                            {{ $activeClass->start_time?->format('H:i') ?? 'Sem horário' }}
                        </div>
                    </div>
                </div>
                <span class="live-badge">
                    <i class="fas fa-circle"></i>
                    AO VIVO
                </span>
            </div>

            <div class="current-class-stats">
                <div class="stat-box">
                    <h4>{{ $activeClass->students()->count() }}</h4>
                    <p>Participantes</p>
                </div>
                <div class="stat-box">
                    <h4>{{ $activeClass->materials()->count() }}</h4>
                    <p>Materiais</p>
                </div>
                <div class="stat-box">
                    <h4>45 min</h4>
                    <p>Duração</p>
                </div>
            </div>

            <div class="current-class-actions">
                <a href="{{ route('aluno.show', $activeClass->id) }}" class="action-btn primary">
                    <i class="fas fa-video"></i>
                    Acessar Aula
                </a>
                <button class="action-btn secondary">
                    <i class="fas fa-cog"></i>
                    Configurações
                </button>
            </div>
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <h3>Nenhuma aula em andamento</h3>
        </div>
    @endif
</div>

<div class="past-classes-section">
    <div class="filter-bar">
        <h2 class="section-title">
            <i class="fas fa-list"></i>
            Todas as Salas
        </h2>
        <div style="display: flex; gap: 10px;">
            <div class="search-box">
                <input type="text" id="searchClassrooms" placeholder="Buscar sala...">
                <i class="fas fa-search"></i>
            </div>
            <select class="filter-select" id="filterStatus">
                <option value="">Todos os Status</option>
                <option value="active">Ativas</option>
                <option value="completed">Concluídas</option>
                <option value="archived">Arquivadas</option>
            </select>
        </div>
    </div>

    <div class="classes-grid">
        @forelse($classrooms as $classroom)
            <div class="class-card">
                <div class="class-card-header">
                    <div class="class-card-title">
                        <h3>{{ $classroom->title }}</h3>
                        <p class="class-card-date">{{ $classroom->created_at->format('d/m/Y') }}</p>
                    </div>
                    <span class="class-status {{ $classroom->status }}">
                        @if($classroom->status === 'active')
                            <i class="fas fa-play"></i> Ativa
                        @elseif($classroom->status === 'completed')
                            <i class="fas fa-check"></i> Concluída
                        @else
                            <i class="fas fa-archive"></i> Arquivada
                        @endif
                    </span>
                </div>

                <div class="class-card-info">
                    <div class="info-row">
                        <i class="fas fa-book"></i>
                        <span>{{ $classroom->subject ?? 'Sem matéria' }}</span>
                    </div>
                    <div class="info-row">
                        <i class="fas fa-layer-group"></i>
                        <span>{{ $classroom->level ?? 'Sem nível' }}</span>
                    </div>
                    <div class="info-row">
                        <i class="fas fa-align-left"></i>
                        <span>{{ Str::limit($classroom->description, 50) }}</span>
                    </div>
                </div>

                <div class="class-card-footer">
                    <div class="students-count">
                        <i class="fas fa-users"></i>
                        <span>{{ $classroom->students()->count() }} / {{ $classroom->max_students }} alunos</span>
                    </div>
                    <div class="card-actions">
                        <a href="{{ route('aluno.show', $classroom->id) }}" class="icon-btn" title="Acessar">
                            <i class="fas fa-eye"></i>
                        </a>
                        <button class="icon-btn" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="icon-btn" title="Deletar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h3>Nenhuma sala criada</h3>
                <p>Crie sua primeira sala para começar</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div style="margin-top: 30px;">
        {{ $classrooms->links() }}
    </div>
</div>
@endsection

@section('styles')
<link rel="stylesheet" href="{{ asset('css/sala-professor.css') }}">
@endsection

@section('scripts')
<script src="{{ asset('js/sala-professor.js') }}"></script>
@endsection
