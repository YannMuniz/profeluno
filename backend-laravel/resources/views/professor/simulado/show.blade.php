{{-- resources/views/professor/simulado/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Ver Simulado')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/forms.css') }}">
<style>
    /* ── Layout de duas colunas (igual ao _form) ─────────────────── */
    .show-grid {
        display: grid;
        grid-template-columns: 1fr 320px;
        gap: 24px;
        align-items: start;
    }

    @media (max-width: 900px) {
        .show-grid { grid-template-columns: 1fr; }
    }

    /* ── Cabeçalho da questão ─────────────────────────────────────── */
    .questao-block {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        margin-bottom: 16px;
        overflow: hidden;
    }

    .questao-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 20px;
        background: var(--table-header-bg, rgba(115,103,240,0.05));
        border-bottom: 1px solid var(--border-color);
    }

    .questao-num-wrap {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .questao-num-badge {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: var(--primary-color);
        color: #fff;
        font-size: 13px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .questao-num-label {
        font-size: 14px;
        font-weight: 600;
        color: var(--text-primary);
    }

    .questao-body {
        padding: 20px;
    }

    /* ── Enunciado ────────────────────────────────────────────────── */
    .enunciado-text {
        font-size: 14px;
        line-height: 1.6;
        color: var(--text-primary);
        margin-bottom: 20px;
        padding: 14px 16px;
        background: var(--input-bg, rgba(0,0,0,0.02));
        border: 1px solid var(--border-color);
        border-radius: 8px;
    }

    /* ── Alternativas ─────────────────────────────────────────────── */
    .alternativas-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .alternativa-row {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 14px;
        border-radius: 8px;
        border: 1px solid var(--border-color);
        background: var(--input-bg, rgba(0,0,0,0.02));
        transition: background 0.2s;
    }

    .alternativa-row.correta {
        background: rgba(40, 199, 111, 0.08);
        border-color: rgba(40, 199, 111, 0.45);
    }

    .alt-badge {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 700;
        flex-shrink: 0;
        background: var(--border-color);
        color: var(--text-secondary);
    }

    .alternativa-row.correta .alt-badge {
        background: #28c76f;
        color: #fff;
    }

    .alt-texto {
        font-size: 13.5px;
        color: var(--text-primary);
        flex: 1;
    }

    .correta-icon {
        margin-left: auto;
        color: #28c76f;
        font-size: 15px;
        flex-shrink: 0;
    }

    /* ── Sidebar cards ────────────────────────────────────────────── */
    .preview-card,
    .tips-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 16px;
    }

    .preview-card-header,
    .tips-card-header {
        padding: 12px 18px;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 8px;
        border-bottom: 1px solid var(--border-color);
        background: var(--table-header-bg, rgba(115,103,240,0.05));
    }

    .preview-card-header i,
    .tips-card-header i {
        color: var(--primary-color);
    }

    .preview-card-body {
        padding: 18px;
    }

    /* ── Resumo numérico ──────────────────────────────────────────── */
    .simulado-summary {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
        margin-bottom: 14px;
    }

    .summary-item {
        text-align: center;
        padding: 12px 8px;
        background: var(--table-header-bg, rgba(115,103,240,0.04));
        border: 1px solid var(--border-color);
        border-radius: 8px;
    }

    .summary-item strong {
        display: block;
        font-size: 22px;
        font-weight: 700;
        color: var(--primary-color);
        line-height: 1;
        margin-bottom: 4px;
    }

    .summary-item span {
        font-size: 11px;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* ── Info geral (matéria, situação, data) ─────────────────────── */
    .info-list {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .info-list li {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 10px 0;
        border-bottom: 1px solid var(--border-color);
        font-size: 13px;
        color: var(--text-secondary);
    }

    .info-list li:last-child {
        border-bottom: none;
    }

    .info-list li i {
        color: var(--primary-color);
        width: 16px;
        margin-top: 1px;
        flex-shrink: 0;
    }

    .info-list li strong {
        color: var(--text-primary);
        font-weight: 600;
    }

    /* ── Índice de navegação ──────────────────────────────────────── */
    .questao-nav {
        padding: 12px 16px;
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .nav-btn {
        width: 30px;
        height: 30px;
        border-radius: 6px;
        background: var(--table-header-bg, rgba(115,103,240,0.07));
        border: 1px solid var(--border-color);
        color: var(--text-primary);
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.15s, color 0.15s;
    }

    .nav-btn:hover {
        background: var(--primary-color);
        color: #fff;
        border-color: var(--primary-color);
    }

    /* ── Barra de progresso ───────────────────────────────────────── */
    .progress-bar-wrap {
        height: 6px;
        background: var(--border-color);
        border-radius: 6px;
        overflow: hidden;
        margin-bottom: 6px;
    }

    .progress-bar-fill {
        height: 100%;
        background: var(--primary-color);
        border-radius: 6px;
        transition: width 0.4s;
    }

    .progress-label {
        font-size: 11px;
        color: var(--text-secondary);
        text-align: right;
        margin: 0;
    }

    /* ── Botões de ação ───────────────────────────────────────────── */
    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 24px;
    }

    /* ── Estado vazio ─────────────────────────────────────────────── */
    .no-questoes {
        text-align: center;
        padding: 48px 24px;
        color: var(--text-secondary);
    }

    .no-questoes i {
        font-size: 40px;
        display: block;
        margin-bottom: 12px;
        color: var(--border-color);
    }

    /* ── Badge situação ───────────────────────────────────────────── */
    .badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .badge-ativo   { background: rgba(40,199,111,0.12); color: #28c76f; border: 1px solid rgba(40,199,111,0.3); }
    .badge-inativo { background: rgba(234,84,85,0.10);  color: #ea5455; border: 1px solid rgba(234,84,85,0.25); }
</style>
@endpush

@section('content')

@php
    $titulo     = $simulado['titulo']       ?? '—';
    $descricao  = $simulado['descricao']    ?? null;
    $materiaId  = $simulado['materia_id']   ?? null;
    $materia    = $simulado['materia']      ?? '—';
    $sala       = $simulado['sala']         ?? '—';
    $situacao   = $simulado['situacao']     ?? 1;
    $criadoEm   = $simulado['criado_em']    ?? null;
    $questoes   = $simulado['questoes']     ?? [];
    $total      = count($questoes);

    $letraMap = [1 => 'A', 2 => 'B', 3 => 'C', 4 => 'D', 5 => 'E'];
@endphp

<div class="show-grid">

    {{-- ── Coluna principal ──────────────────────────────────────── --}}
    <div class="form-col-main">

        {{-- Card: dados gerais --}}
        <div class="questao-block" style="margin-bottom: 24px;">
            <div class="questao-header">
                <div class="questao-num-wrap">
                    <span class="questao-num-label" style="font-size: 15px; font-weight: 600;">
                        <i class="fas fa-info-circle" style="color: var(--primary-color); margin-right: 6px;"></i>
                        Informações do Simulado
                    </span>
                </div>

                <span class="badge {{ $situacao ? 'badge-ativo' : 'badge-inativo' }}">
                    <i class="fas fa-{{ $situacao ? 'check-circle' : 'times-circle' }}"></i>
                    {{ $situacao ? 'Ativo' : 'Inativo' }}
                </span>
            </div>

            <div class="questao-body">

                {{-- Título --}}
                <div class="form-group" style="margin-bottom: 14px;">
                    <label class="form-label" style="margin-bottom: 4px;">Título</label>
                    <div class="enunciado-text" style="margin-bottom: 0; font-weight: 600; font-size: 15px;">
                        {{ $titulo }}
                    </div>
                </div>

                {{-- Descrição --}}
                @if($descricao)
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" style="margin-bottom: 4px;">Descrição</label>
                    <div class="enunciado-text" style="margin-bottom: 0;">
                        {{ $descricao }}
                    </div>
                </div>
                @endif

            </div>
        </div>

        {{-- Questões --}}
        @if($total > 0)
            @foreach($questoes as $i => $q)
            @php
                $num     = $i + 1;
                $correta = (int) ($q['questao_correta'] ?? 0);

                $alternativas = [
                    1 => $q['questao_a'] ?? null,
                    2 => $q['questao_b'] ?? null,
                    3 => $q['questao_c'] ?? null,
                    4 => $q['questao_d'] ?? null,
                    5 => $q['questao_e'] ?? null,
                ];
            @endphp

            <div class="questao-block" id="questao-block-{{ $num }}">
                <div class="questao-header">
                    <div class="questao-num-wrap">
                        <span class="questao-num-badge">{{ $num }}</span>
                        <span class="questao-num-label">Questão {{ $num }}</span>
                    </div>
                </div>

                <div class="questao-body">

                    {{-- Enunciado --}}
                    <div class="enunciado-text">
                        {{ $q['enunciado'] ?? '—' }}
                    </div>

                    {{-- Alternativas --}}
                    <div class="alternativas-list">
                        @foreach($alternativas as $valor => $texto)
                            @if(!is_null($texto) && $texto !== '')
                            <div class="alternativa-row {{ $correta === $valor ? 'correta' : '' }}">
                                <span class="alt-badge">{{ $letraMap[$valor] }}</span>
                                <span class="alt-texto">{{ $texto }}</span>
                                @if($correta === $valor)
                                    <i class="fas fa-check-circle correta-icon" title="Resposta correta"></i>
                                @endif
                            </div>
                            @endif
                        @endforeach
                    </div>

                </div>
            </div>
            @endforeach
        @else
            <div class="questao-block">
                <div class="no-questoes">
                    <i class="fas fa-list-ol"></i>
                    <p>Este simulado ainda não possui questões cadastradas.</p>
                </div>
            </div>
        @endif

        {{-- Ações --}}
        <div class="form-actions">
            {!! $ultimapagina !!}
            <a href="{{ route('professor.simulados.edit', $simulado['id']) }}" class="btn-form-submit">
                <i class="fas fa-pen"></i>
                Editar Simulado
            </a>
        </div>

    </div>

    {{-- ── Lateral ──────────────────────────────────────────────── --}}
    <div class="form-col-side">

        {{-- Resumo numérico --}}
        <div class="preview-card">
            <div class="preview-card-header">
                <i class="fas fa-chart-bar"></i>
                Resumo do Simulado
            </div>
            <div class="preview-card-body">
                <div class="simulado-summary">
                    <div class="summary-item">
                        <strong>{{ $total }}</strong>
                        <span>Questões</span>
                    </div>
                    <div class="summary-item">
                        @php
                            $completas = collect($questoes)->filter(function($q) {
                                return !empty($q['enunciado'])
                                    && !empty($q['questao_a'])
                                    && !empty($q['questao_b'])
                                    && !empty($q['questao_c'])
                                    && !empty($q['questao_d'])
                                    && !empty($q['questao_correta']);
                            })->count();
                        @endphp
                        <strong>{{ $completas }}</strong>
                        <span>Completas</span>
                    </div>
                </div>

                @if($total > 0)
                @php $pct = round(($completas / $total) * 100); @endphp
                <div class="progress-bar-wrap">
                    <div class="progress-bar-fill" style="width: {{ $pct }}%;"></div>
                </div>
                <p class="progress-label">{{ $pct }}% das questões completas</p>
                @else
                <p class="progress-label" style="text-align: center;">Sem questões cadastradas</p>
                @endif
            </div>
        </div>

        {{-- Informações gerais --}}
        <div class="tips-card">
            <div class="tips-card-header">
                <i class="fas fa-info-circle"></i>
                Detalhes
            </div>
            <div style="padding: 4px 16px 8px;">
                <ul class="info-list">
                    <li>
                        <i class="fas fa-book"></i>
                        <div>
                            <span style="display: block; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 2px;">Matéria</span>
                            <strong>{{ $materia }}</strong>
                        </div>
                    </li>
                    @if($sala !== '—')
                    <li>
                        <i class="fas fa-chalkboard-teacher"></i>
                        <div>
                            <span style="display: block; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 2px;">Sala de Aula</span>
                            <strong>{{ $sala }}</strong>
                        </div>
                    </li>
                    @endif
                    <li>
                        <i class="fas fa-toggle-on"></i>
                        <div>
                            <span style="display: block; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 2px;">Situação</span>
                            <span class="badge {{ $situacao ? 'badge-ativo' : 'badge-inativo' }}">
                                <i class="fas fa-{{ $situacao ? 'check-circle' : 'times-circle' }}"></i>
                                {{ $situacao ? 'Ativo' : 'Inativo' }}
                            </span>
                        </div>
                    </li>
                    @if($criadoEm)
                    <li>
                        <i class="fas fa-calendar-alt"></i>
                        <div>
                            <span style="display: block; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 2px;">Criado em</span>
                            <strong>{{ \Carbon\Carbon::parse($criadoEm)->format('d/m/Y') }}</strong>
                        </div>
                    </li>
                    @endif
                </ul>
            </div>
        </div>

        {{-- Índice de navegação --}}
        @if($total > 0)
        <div class="tips-card">
            <div class="tips-card-header">
                <i class="fas fa-map-signs"></i>
                Navegação
            </div>
            <div class="questao-nav">
                @foreach($questoes as $i => $q)
                <a href="#questao-block-{{ $i + 1 }}" class="nav-btn" title="Ir para questão {{ $i + 1 }}">
                    {{ $i + 1 }}
                </a>
                @endforeach
            </div>
        </div>
        @endif

    </div>

</div>

@endsection