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

@if($aulas->count() > 0)

<div class="classes-list">
    @foreach($aulas as $sala)
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
                    @if($sala->idConteudo)
                    &nbsp;·&nbsp;
                    <i class="fas fa-file-alt me-1" title="Conteúdo disponível"></i>
                    @endif
                    @if($sala->idSimulado)
                    &nbsp;·&nbsp;
                    <i class="fas fa-list-ol me-1" title="Simulado disponível"></i>
                    @endif
                </p>
            </div>
        </div>

        <div class="class-meta d-flex align-items-center gap-2">
            {{-- Badges de recursos --}}
            @if($sala->idConteudo)
                <span class="badge-resource" title="Possui conteúdo">
                    <i class="fas fa-file-alt"></i>
                </span>
            @endif
            @if($sala->idSimulado)
                <span class="badge-resource" title="Possui simulado">
                    <i class="fas fa-list-ol"></i>
                </span>
            @endif

            {{-- Status --}}
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
    {{ $aulas->links() }}
</div>

@else

<div class="empty-state">
    <i class="fas fa-history"></i>
    <h3>Nenhuma aula no histórico</h3>
    <p>Você ainda não concluiu nenhuma aula.</p>
    <a href="{{ route('aluno.salas.index') }}" class="btn-primary mt-3">
        <i class="fas fa-search"></i> Buscar Salas
    </a>
</div>

@endif

@endsection

@push('styles')
<style>
    .badge-resource {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        border-radius: 6px;
        background: rgba(115, 103, 240, 0.1);
        color: var(--primary-color);
        font-size: 12px;
        border: 1px solid rgba(115, 103, 240, 0.2);
    }
</style>
@endpush