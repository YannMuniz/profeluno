{{-- resources/views/aluno/salas/show.blade.php --}}
@extends('layouts.app')

@section('title', $sala->titulo)

@section('content')

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Cabeçalho da sala --}}
<div class="page-header d-flex align-items-center justify-content-between mb-4">
    <div>
        <a href="{{ route('aluno.salas.index') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
        <h1 class="mt-2">{{ $sala->titulo }}</h1>
        <p class="text-muted">{{ $sala->materia }}</p>
    </div>
    @if($sala->status === 'active')
    <div class="live-badge" style="font-size:14px;padding:8px 18px">
        <i class="fas fa-circle"></i> AO VIVO
    </div>
    @endif
</div>

<div class="row g-4">

    {{-- Coluna principal --}}
    <div class="col-lg-8">
        <div class="card-section">
            <h2 class="section-title">
                <i class="fas fa-info-circle"></i> Sobre a Sala
            </h2>
            <p>{{ $sala->descricao ?? 'Sem descrição disponível.' }}</p>

            <div class="detail-grid mt-4">
                <div class="detail-item">
                    <i class="fas fa-users"></i>
                    <div>
                        <span class="detail-label">Capacidade</span>
                        <span class="detail-value">{{ $sala->qtd_alunos }} alunos</span>
                    </div>
                </div>
                <div class="detail-item">
                    <i class="fas fa-book"></i>
                    <div>
                        <span class="detail-label">Matéria</span>
                        <span class="detail-value">{{ $sala->materia }}</span>
                    </div>
                </div>
                <div class="detail-item">
                    <i class="fas fa-calendar"></i>
                    <div>
                        <span class="detail-label">Início</span>
                        <span class="detail-value">
                            {{ $sala->data_hora_inicio ? $sala->data_hora_inicio->format('d/m/Y \à\s H:i') : 'A definir' }}
                        </span>
                    </div>
                </div>
                <div class="detail-item">
                    <i class="fas fa-flag-checkered"></i>
                    <div>
                        <span class="detail-label">Fim</span>
                        <span class="detail-value">
                            {{ $sala->data_hora_fim ? $sala->data_hora_fim->format('d/m/Y \à\s H:i') : 'A definir' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Coluna lateral --}}
    <div class="col-lg-4">

        {{-- Card professor --}}
        @if($professor)
        <div class="card-section mb-4">
            <h2 class="section-title">
                <i class="fas fa-user-tie"></i> Professor
            </h2>
            <div class="teacher-mini-card">
                <div class="teacher-avatar">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div>
                    <strong>{{ $professor['nome'] ?? $professor['name'] ?? 'Professor' }}</strong>
                    <p class="text-muted small mb-0">
                        {{ $professor['email'] ?? '' }}
                    </p>
                </div>
            </div>
        </div>
        @endif

        {{-- Ação --}}
        <div class="card-section">
            @if($sala->status === 'active')
                <p class="text-success fw-bold mb-3">
                    <i class="fas fa-circle"></i> Esta sala está ao vivo agora!
                </p>
                <form action="{{ route('aluno.salas.join', $sala->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-primary w-100">
                        <i class="fas fa-sign-in-alt"></i> Entrar na Aula Agora
                    </button>
                </form>
            @elseif($sala->status === 'pending')
                <p class="text-muted mb-3">
                    <i class="fas fa-calendar"></i>
                    Agendada para
                    {{ $sala->data_hora_inicio ? $sala->data_hora_inicio->format('d/m/Y \à\s H:i') : 'data a definir' }}.
                </p>
                <button class="btn-primary w-100" disabled>
                    <i class="fas fa-clock"></i> Aguardando Início
                </button>
            @else
                <p class="text-muted mb-3">
                    <i class="fas fa-check-circle"></i> Esta aula já foi encerrada.
                </p>
                <a href="{{ route('aluno.salas.index') }}" class="btn-secondary w-100 text-center">
                    <i class="fas fa-search"></i> Buscar Outras Salas
                </a>
            @endif
        </div>

        {{-- Avaliar professor --}}
        @if($sala->status === 'completed' && $professor)
        <div class="card-section mt-4">
            <h2 class="section-title">
                <i class="fas fa-star"></i> Avaliar Professor
            </h2>
            <form action="{{ route('aluno.salas.rating', $professor['idUsuario'] ?? $professor['id']) }}" method="POST">
                @csrf
                <div class="star-rating mb-3" id="starRating">
                    @for($i = 1; $i <= 5; $i++)
                    <i class="fas fa-star star-btn" data-value="{{ $i }}"
                       style="font-size:24px;cursor:pointer;color:var(--border-color);transition:.2s"></i>
                    @endfor
                </div>
                <input type="hidden" name="nota" id="notaInput" value="">
                <textarea name="comentario" class="form-control mb-3" rows="3"
                          placeholder="Deixe um comentário (opcional)..." maxlength="500"></textarea>
                <button type="submit" class="btn-primary w-100"
                        id="btnAvaliar" disabled>
                    <i class="fas fa-paper-plane"></i> Enviar Avaliação
                </button>
            </form>
        </div>
        @endif

    </div>
</div>

@endsection

@push('scripts')
<script>
document.querySelectorAll('.star-btn').forEach(star => {
    star.addEventListener('click', function () {
        const value = this.dataset.value;
        document.getElementById('notaInput').value = value;
        document.getElementById('btnAvaliar').disabled = false;

        document.querySelectorAll('.star-btn').forEach((s, i) => {
            s.style.color = i < value ? '#ff9f43' : 'var(--border-color)';
        });
    });

    star.addEventListener('mouseenter', function () {
        const value = this.dataset.value;
        document.querySelectorAll('.star-btn').forEach((s, i) => {
            s.style.color = i < value ? '#ff9f43' : 'var(--border-color)';
        });
    });
});

document.getElementById('starRating')?.addEventListener('mouseleave', function () {
    const selected = document.getElementById('notaInput').value;
    document.querySelectorAll('.star-btn').forEach((s, i) => {
        s.style.color = selected && i < selected ? '#ff9f43' : 'var(--border-color)';
    });
});
</script>
@endpush