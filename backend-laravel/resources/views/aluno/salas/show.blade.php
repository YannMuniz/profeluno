{{-- resources/views/aluno/salas/show.blade.php --}}
@extends('layouts.app')

@section('title', $sala->titulo)

@section('styles')
<link rel="stylesheet" href="{{ asset('css/sala-aluno-show.css') }}">
@endsection

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
                    <i class="fas fa-user-check"></i>
                    <div>
                        <span class="detail-label">Inscritos</span>
                        <span class="detail-value">{{ $sala->qtd_alunos_atual ?? 0 }} alunos</span>
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

        {{-- Conteúdo da Aula --}}
        <div class="card-section">
            <h2 class="section-title">
                <i class="fas fa-file-alt"></i> Conteúdo da Aula
            </h2>
            @if(!empty($conteudo))
                @php
                    $conteudoLink = $conteudo['url'] ?? $conteudo['link'] ?? null;
                    $conteudoTitle = $conteudo['titulo'] ?? 'Conteúdo da Aula';
                    $conteudoDesc  = $conteudo['descricao'] ?? 'Material de apoio da aula';
                    $conteudoTipo  = $conteudo['tipo'] ?? null;
                    $conteudoIcon  = [
                        'pdf'   => 'fa-file-pdf',
                        'slide' => 'fa-file-powerpoint',
                        'video' => 'fa-file-video',
                        'link'  => 'fa-link',
                    ][$conteudoTipo] ?? 'fa-file-alt';
                @endphp
                <a href="{{ $conteudoLink ?? '#' }}" target="{{ $conteudoLink ? '_blank' : '_self' }}" class="conteudo-preview" style="display:flex;align-items:center;gap:16px;padding:16px;border:1px solid #ddd;border-radius:12px;text-decoration:none;color:inherit;">
                    <div style="width:48px;height:48px;border-radius:12px;background:rgba(115,103,240,0.12);display:flex;align-items:center;justify-content:center;">
                        <i class="fas {{ $conteudoIcon }}" style="font-size:20px;color:#7367f0"></i>
                    </div>
                    <div>
                        <strong style="display:block;font-size:15px;color:#111;">{{ $conteudoTitle }}</strong>
                        <span style="font-size:13px;color:#6c6b7d;">{{ $conteudoDesc }}</span>
                    </div>
                </a>
            @else
                <div style="padding:20px;border:1px solid #ddd;border-radius:12px;background:#f8f9ff;text-align:center;color:#6c6b7d;">
                    <i class="fas fa-folder-open" style="font-size:24px;margin-bottom:8px;display:block;"></i>
                    Nenhum conteúdo vinculado a esta sala.
                </div>
            @endif
        </div>

        {{-- Simulado --}}
        <div class="card-section">
            <h2 class="section-title">
                <i class="fas fa-clipboard-list"></i> Simulado
            </h2>
            @if(!empty($simulado))
                @php
                    $questoes = $simulado['simuladoQuestao'] ?? [];
                    $totalQuestoes = count($questoes);
                @endphp
                <div style="padding:20px;border:1px solid #ddd;border-radius:12px;background:#fff;">
                    <p style="font-size:15px;font-weight:600;margin-bottom:8px;color:#111;">{{ $simulado['titulo'] ?? 'Simulado da Aula' }}</p>
                    <p style="font-size:13px;color:#6c6b7d;margin-bottom:12px;">{{ $simulado['descricao'] ?? 'Avaliação disponibilizada pelo professor.' }}</p>
                    <p style="font-size:13px;color:#6c6b7d;margin-bottom:16px;"><strong>{{ $totalQuestoes }}</strong> questões</p>
                    @if(!empty($simulado['situacao']) && $totalQuestoes > 0 && $sala->status === 'completed')
                        <a href="{{ route('aluno.historico.simulado', $sala->id) }}" class="btn-primary" style="display:inline-flex;align-items:center;gap:8px;">
                            <i class="fas fa-play"></i> Fazer Simulado
                        </a>
                    @else
                        <span style="font-size:13px;color:#6c6b7d;">
                            @if($sala->status !== 'completed')
                                Simulado disponível após a conclusão da aula.
                            @else
                                Simulado indisponível.
                            @endif
                        </span>
                    @endif
                </div>
            @else
                <div style="padding:20px;border:1px solid #ddd;border-radius:12px;background:#f8f9ff;text-align:center;color:#6c6b7d;">
                    <i class="fas fa-clipboard-list" style="font-size:24px;margin-bottom:8px;display:block;"></i>
                    Nenhum simulado vinculado a esta sala.
                </div>
            @endif
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
                <a href="{{ route('aluno.salas.aguardando', $sala->id) }}" class="btn-primary w-100 text-center">
                    <i class="fas fa-sign-in-alt"></i> Entrar na Aula Agora
                </a>
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
                       style="font-size:24px;cursor:pointer;color:var(--border-color,#3b3b52);transition:.2s"></i>
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
            s.style.color = i < value ? '#ff9f43' : 'var(--border-color,#3b3b52)';
        });
    });

    star.addEventListener('mouseenter', function () {
        const value = this.dataset.value;
        document.querySelectorAll('.star-btn').forEach((s, i) => {
            s.style.color = i < value ? '#ff9f43' : 'var(--border-color,#3b3b52)';
        });
    });
});

document.getElementById('starRating')?.addEventListener('mouseleave', function () {
    const selected = document.getElementById('notaInput').value;
    document.querySelectorAll('.star-btn').forEach((s, i) => {
        s.style.color = selected && i < selected ? '#ff9f43' : 'var(--border-color,#3b3b52)';
    });
});
</script>
@endpush