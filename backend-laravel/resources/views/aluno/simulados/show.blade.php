{{-- resources/views/aluno/simulados/show.blade.php --}}
@extends('layouts.app')

@section('title', $simulado->titulo ?? 'Simulado')

@section('content')

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="page-header d-flex align-items-center justify-content-between mb-4">
    <div>
        <a href="{{ route('aluno.simulados') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
        <h1 class="mt-2">{{ $simulado->titulo ?? 'Simulado' }}</h1>
        <p class="text-muted">{{ $simulado->materia ?? 'Simulado' }}</p>
    </div>
</div>

<div class="row g-4">

    {{-- Coluna principal --}}
    <div class="col-lg-8">
        <div class="card-section">
            <h2 class="section-title">
                <i class="fas fa-file-alt"></i> Sobre o Simulado
            </h2>
            <p>Realize este simulado para testar seus conhecimentos.</p>

            <div class="detail-grid mt-4">
                @if(isset($simulado->questoes) && count($simulado->questoes) > 0)
                <div class="detail-item">
                    <i class="fas fa-list"></i>
                    <div>
                        <span class="detail-label">Questões</span>
                        <span class="detail-value">{{ count($simulado->questoes) }} questão(ões)</span>
                    </div>
                </div>
                @endif
                @if(isset($simulado->materia))
                <div class="detail-item">
                    <i class="fas fa-book"></i>
                    <div>
                        <span class="detail-label">Matéria</span>
                        <span class="detail-value">{{ $simulado->materia }}</span>
                    </div>
                </div>
                @endif
                @if(isset($simulado->situacao))
                <div class="detail-item">
                    <i class="fas fa-{{ $simulado->situacao ? 'check-circle' : 'clock' }}"></i>
                    <div>
                        <span class="detail-label">Status</span>
                        <span class="detail-value">{{ $simulado->situacao ? 'Disponível' : 'Indisponível' }}</span>
                    </div>
                </div>
                @endif
            </div>

            @if(isset($simulado->questoes) && count($simulado->questoes) > 0)
            <div class="mt-4">
                <h3 class="section-title">
                    <i class="fas fa-question-circle"></i> Questões
                </h3>
                <div class="questoes-list">
                    @foreach($simulado->questoes as $index => $questao)
                    <div class="questao-item">
                        <div class="questao-number">{{ $index + 1 }}</div>
                        <div class="questao-content">
                            <p class="questao-titulo">{{ $questao['enunciado'] ?? 'Questão sem enunciado' }}</p>
                            <div class="alternativas">
                                @php
                                    $alternativas = [
                                        'a' => $questao['questao_a'] ?? '',
                                        'b' => $questao['questao_b'] ?? '',
                                        'c' => $questao['questao_c'] ?? '',
                                        'd' => $questao['questao_d'] ?? '',
                                        'e' => $questao['questao_e'] ?? '',
                                    ];
                                    $correta = $questao['questao_correta'] ?? null;
                                @endphp
                                @foreach($alternativas as $letra => $texto)
                                    @if($texto)
                                    <label class="alternativa">
                                        <input type="radio" name="questao_{{ $index }}" value="{{ $letra }}">
                                        <span class="alternativa-text">
                                            <strong>{{ strtoupper($letra) }})</strong> {{ $texto }}
                                        </span>
                                    </label>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Coluna lateral --}}
    <div class="col-lg-4">
        <div class="card-section">
            <h2 class="section-title">
                <i class="fas fa-play"></i> Ações
            </h2>

            @if($simulado->situacao ?? false)
                <p class="text-success fw-bold mb-3">
                    <i class="fas fa-check-circle"></i> Simulado disponível
                </p>
                <button class="btn-primary w-100" id="btnIniciar">
                    <i class="fas fa-play"></i> Iniciar Simulado
                </button>
            @else
                <p class="text-muted mb-3">
                    <i class="fas fa-lock"></i> Este simulado não está disponível no momento.
                </p>
                <button class="btn-primary w-100" disabled>
                    <i class="fas fa-lock"></i> Indisponível
                </button>
            @endif
        </div>

        <div class="card-section mt-4">
            <a href="{{ route('aluno.simulados') }}" class="btn-secondary w-100 text-center">
                <i class="fas fa-arrow-left"></i> Voltar aos Simulados
            </a>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
document.getElementById('btnIniciar')?.addEventListener('click', function() {
    alert('Simulado iniciado! Esta funcionalidade será implementada em breve.');
});
</script>
@endpush

<style>
.card-section {
    background: var(--card-bg, #fff);
    border: 1px solid var(--border-color, #e0e0e0);
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
}

.section-title {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.detail-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 15px;
}

.detail-item {
    display: flex;
    gap: 12px;
    padding: 15px;
    background: var(--bg-light, #f8f9fa);
    border-radius: 8px;
}

.detail-item i {
    font-size: 20px;
    color: #7367f0;
    flex-shrink: 0;
}

.detail-label {
    display: block;
    font-size: 12px;
    color: var(--text-muted, #666);
    text-transform: uppercase;
    font-weight: 600;
}

.detail-value {
    display: block;
    font-size: 16px;
    font-weight: 600;
    margin-top: 4px;
}

.questoes-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.questao-item {
    display: flex;
    gap: 15px;
    padding: 20px;
    background: var(--bg-light, #f8f9fa);
    border-radius: 10px;
    border-left: 4px solid #7367f0;
}

.questao-number {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #7367f0;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    flex-shrink: 0;
}

.questao-content {
    flex: 1;
}

.questao-titulo {
    font-weight: 600;
    margin-bottom: 12px;
    font-size: 15px;
}

.alternativas {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.alternativa {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    cursor: pointer;
    padding: 10px;
    border-radius: 6px;
    transition: background 0.2s;
}

.alternativa:hover {
    background: rgba(115, 103, 240, 0.1);
}

.alternativa input[type="radio"] {
    margin-top: 4px;
    cursor: pointer;
}

.alternativa-text {
    font-size: 14px;
}

.btn-primary, .btn-secondary {
    padding: 12px 20px;
    border-radius: 8px;
    border: none;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    font-size: 14px;
}

.btn-primary {
    background: #7367f0;
    color: white;
}

.btn-primary:hover:not(:disabled) {
    background: #6258d3;
}

.btn-primary:disabled {
    background: #ccc;
    cursor: not-allowed;
}

.btn-secondary {
    background: #f0f0f0;
    color: #666;
}

.btn-secondary:hover {
    background: #e8e8e8;
}

.btn-back {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: #7367f0;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    margin-bottom: 12px;
}

.btn-back:hover {
    color: #6258d3;
    gap: 10px;
}

.page-header {
    margin-bottom: 30px;
}

.page-header h1 {
    font-size: 28px;
    font-weight: 700;
    margin: 15px 0 5px;
}

.page-header .text-muted {
    color: #999;
}

.text-center {
    text-align: center;
}

.alert {
    padding: 15px;
    border-radius: 8px;
    border: none;
    display: flex;
    align-items: center;
    gap: 12px;
}

.alert-danger {
    background: #fee;
    color: #c33;
}

.alert-success {
    background: #efe;
    color: #3c3;
}

.btn-close {
    background: transparent;
    border: none;
    font-size: 20px;
    cursor: pointer;
    color: inherit;
    margin-left: auto;
}
</style>
