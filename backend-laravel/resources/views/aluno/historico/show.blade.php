{{-- resources/views/aluno/historico/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detalhes da Aula')

@section('content')

<div class="page-header mb-4">
    <a href="{{ route('aluno.historico') }}" class="btn-back">
        <i class="fas fa-arrow-left"></i> Voltar ao Histórico
    </a>
    <h1 class="mt-2">{{ $sala->titulo }}</h1>
    <p class="text-muted">{{ $sala->materia }}</p>
</div>

<div class="row g-4">

    {{-- Coluna principal --}}
    <div class="col-lg-8">
        <div class="card-section">
            <h2 class="section-title">
                <i class="fas fa-info-circle"></i> Detalhes da Aula
            </h2>

            <p>{{ $sala->descricao ?? 'Sem descrição disponível.' }}</p>

            <div class="detail-grid mt-4">
                <div class="detail-item">
                    <i class="fas fa-book"></i>
                    <div>
                        <span class="detail-label">Matéria</span>
                        <span class="detail-value">{{ $sala->materia }}</span>
                    </div>
                </div>
                <div class="detail-item">
                    <i class="fas fa-calendar-check"></i>
                    <div>
                        <span class="detail-label">Início</span>
                        <span class="detail-value">
                            {{ $sala->data_hora_inicio ? $sala->data_hora_inicio->format('d/m/Y \à\s H:i') : '—' }}
                        </span>
                    </div>
                </div>
                <div class="detail-item">
                    <i class="fas fa-flag-checkered"></i>
                    <div>
                        <span class="detail-label">Encerramento</span>
                        <span class="detail-value">
                            {{ $sala->data_hora_fim ? $sala->data_hora_fim->format('d/m/Y \à\s H:i') : '—' }}
                        </span>
                    </div>
                </div>
                <div class="detail-item">
                    <i class="fas fa-hourglass-end"></i>
                    <div>
                        <span class="detail-label">Duração</span>
                        <span class="detail-value">
                            @if($sala->data_hora_inicio && $sala->data_hora_fim)
                                {{ $sala->data_hora_inicio->diff($sala->data_hora_fim)->format('%H:%I') }}h
                            @else
                                —
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Coluna lateral --}}
    <div class="col-lg-4">
        <div class="card-section">
            <h2 class="section-title">
                <i class="fas fa-chart-bar"></i> Status
            </h2>

            @if($sala->status === 'completed')
                <div class="status-badge status-completed p-3 rounded text-center">
                    <i class="fas fa-check-circle fa-2x mb-2"></i>
                    <p class="fw-bold mb-0">Aula Concluída</p>
                </div>
            @elseif($sala->status === 'active')
                <div class="status-badge status-active p-3 rounded text-center">
                    <i class="fas fa-circle fa-2x mb-2 text-success"></i>
                    <p class="fw-bold mb-0">Em Andamento</p>
                    <a href="{{ route('aluno.salas.video', $sala->id) }}" class="btn-primary mt-3 w-100 d-block text-center">
                        <i class="fas fa-sign-in-alt"></i> Voltar para Aula
                    </a>
                </div>
            @else
                <div class="status-badge p-3 rounded text-center">
                    <i class="fas fa-clock fa-2x mb-2"></i>
                    <p class="fw-bold mb-0">Agendada</p>
                </div>
            @endif
        </div>

        <div class="card-section mt-4">
            <a href="{{ route('aluno.historico') }}" class="btn-secondary w-100 text-center">
                <i class="fas fa-arrow-left"></i> Voltar ao Histórico
            </a>
        </div>
    </div>

</div>

@endsection