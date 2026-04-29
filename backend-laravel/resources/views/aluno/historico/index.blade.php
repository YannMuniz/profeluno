{{-- resources/views/aluno/historico/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Histórico de Aulas')

@section('content')

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="page-header d-flex align-items-center justify-content-between mb-4">
    <div>
        <h1>Histórico de Aulas</h1>
        <p class="text-muted">Todas as aulas que você participou</p>
    </div>
    <a href="{{ route('aluno.salas.index') }}" class="btn-primary">
        <i class="fas fa-search"></i> Buscar Novas Salas
    </a>
</div>

@if($historico->count() > 0)

<div class="classes-list">
    @foreach($historico as $sala)
    <div class="class-item">
        <div class="class-info">
            <div class="class-icon">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <div class="class-details">
                <h4>{{ $sala->titulo }}</h4>
                <p>
                    <i class="fas fa-book me-1"></i>{{ $sala->materia }}
                    @if($sala->data_hora_inicio)
                    &nbsp;·&nbsp;
                    <i class="fas fa-calendar me-1"></i>
                    {{ $sala->data_hora_inicio->format('d/m/Y \à\s H:i') }}
                    @endif
                </p>
            </div>
        </div>

        <div class="class-meta">
            @if($sala->status === 'completed')
                <span class="class-status status-completed">
                    <i class="fas fa-check"></i> Concluída
                </span>
            @elseif($sala->status === 'active')
                <span class="class-status status-pending">
                    <i class="fas fa-circle"></i> Ao Vivo
                </span>
            @else
                <span class="class-status">
                    <i class="fas fa-clock"></i> Agendada
                </span>
            @endif
        </div>

        <a href="{{ route('aluno.historico.show', $sala->id) }}" class="view-btn">
            <i class="fas fa-eye"></i>
        </a>
    </div>
    @endforeach
</div>

{{-- Paginação --}}
<div class="pagination-wrapper mt-4">
    {{ $historico->links() }}
</div>

@else

<div class="empty-state">
    <i class="fas fa-history"></i>
    <h3>Nenhuma aula no histórico</h3>
    <p>Você ainda não participou de nenhuma aula.</p>
    <a href="{{ route('aluno.salas.index') }}" class="btn-primary mt-3">
        <i class="fas fa-search"></i> Buscar Salas
    </a>
</div>

@endif

@endsection