{{-- resources/views/professor/salas/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detalhes da Sala — ' . ($sala->titulo ?? ''))

@section('styles')
<link rel="stylesheet" href="{{ asset('css/sala-professor.css') }}">
<style>
    /* ── SHOW-SPECIFIC STYLES ── */
    .show-grid {
        display: grid;
        grid-template-columns: 1fr 320px;
        gap: 24px;
        align-items: start;
    }

    @media (max-width: 992px) {
        .show-grid { grid-template-columns: 1fr; }
    }

    /* ── HERO CARD ── */
    .hero-card {
        background: var(--card-bg, #fff);
        border: 1px solid var(--border-color, #e5e7eb);
        border-radius: 16px;
        overflow: hidden;
        margin-bottom: 20px;
    }

    .hero-banner {
        height: 120px;
        background: linear-gradient(135deg, #7367f0 0%, #9f8cfe 60%, #ce9ffc 100%);
        position: relative;
        display: flex;
        align-items: flex-end;
        padding: 0 28px 20px;
    }

    .hero-banner.status-active {
        background: linear-gradient(135deg, #28c76f 0%, #48da89 60%, #74e8a6 100%);
    }

    .hero-banner.status-completed {
        background: linear-gradient(135deg, #6e6b7b 0%, #82868b 60%, #a8aaad 100%);
    }

    .hero-banner-decoration {
        position: absolute;
        right: 28px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 72px;
        opacity: .12;
        color: #fff;
        line-height: 1;
    }

    .hero-status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        background: rgba(255,255,255,.25);
        color: #fff;
        border: 1px solid rgba(255,255,255,.4);
        backdrop-filter: blur(4px);
    }

    .hero-status-pill .dot {
        width: 7px; height: 7px;
        border-radius: 50%;
        background: #fff;
        animation: blink-dot 1.5s infinite;
    }

    @keyframes blink-dot { 0%,100%{opacity:1} 50%{opacity:.3} }

    .hero-body {
        padding: 24px 28px 20px;
    }

    .hero-title {
        font-size: 22px;
        font-weight: 700;
        margin-bottom: 6px;
        color: var(--text-dark, #2d2d2d);
    }

    .hero-subtitle {
        font-size: 14px;
        color: var(--text-muted, #6e6b7b);
        margin-bottom: 20px;
    }

    .hero-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .hero-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        background: var(--light-bg, #f8f8f8);
        border: 1px solid var(--border-color, #e5e7eb);
        border-radius: 20px;
        font-size: 13px;
        font-weight: 500;
        color: var(--text-dark, #2d2d2d);
    }

    .hero-chip i {
        color: var(--primary-color, #7367f0);
        font-size: 12px;
    }

    /* ── SECTION CARD ── */
    .section-card {
        background: var(--card-bg, #fff);
        border: 1px solid var(--border-color, #e5e7eb);
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 20px;
    }

    .section-card-header {
        padding: 16px 20px;
        border-bottom: 1px solid var(--border-color, #e5e7eb);
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 14px;
        font-weight: 600;
        color: var(--text-dark, #2d2d2d);
    }

    .section-card-header i {
        width: 32px; height: 32px;
        background: rgba(115,103,240,.1);
        color: var(--primary-color, #7367f0);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        flex-shrink: 0;
    }

    .section-card-body {
        padding: 20px;
    }

    /* ── INFO LIST ── */
    .info-list {
        display: flex;
        flex-direction: column;
        gap: 0;
    }

    .info-row {
        display: flex;
        align-items: flex-start;
        padding: 12px 0;
        border-bottom: 1px solid var(--border-color, #e5e7eb);
        gap: 16px;
    }

    .info-row:last-child { border-bottom: none; }

    .info-label {
        flex-shrink: 0;
        width: 160px;
        font-size: 12px;
        font-weight: 600;
        color: var(--text-muted, #6e6b7b);
        text-transform: uppercase;
        letter-spacing: .4px;
        padding-top: 2px;
    }

    .info-value {
        flex: 1;
        font-size: 14px;
        color: var(--text-dark, #2d2d2d);
        line-height: 1.5;
    }

    .info-value.muted { color: var(--text-muted, #6e6b7b); font-style: italic; }

    /* ── STATUS BADGE ── */
    .status-tag {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-tag.active    { background: rgba(40,199,111,.12); color: #28c76f; }
    .status-tag.pending   { background: rgba(255,159,67,.12); color: #ff9f43; }
    .status-tag.completed { background: rgba(130,134,139,.12); color: #82868b; }

    /* ── URL BOX ── */
    .url-box {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 14px;
        background: var(--light-bg, #f8f8f8);
        border: 1px solid var(--border-color, #e5e7eb);
        border-radius: 8px;
    }

    .url-box i { color: var(--primary-color, #7367f0); flex-shrink: 0; }

    .url-box a {
        flex: 1;
        font-size: 13px;
        color: var(--primary-color, #7367f0);
        word-break: break-all;
        text-decoration: none;
    }

    .url-box a:hover { text-decoration: underline; }

    .btn-copy {
        flex-shrink: 0;
        width: 32px; height: 32px;
        background: rgba(115,103,240,.1);
        border: 1px solid rgba(115,103,240,.3);
        border-radius: 6px;
        color: var(--primary-color, #7367f0);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        transition: .2s;
    }

    .btn-copy:hover { background: var(--primary-color, #7367f0); color: #fff; border-color: var(--primary-color, #7367f0); }

    /* ── CONTENT ITEM ── */
    .content-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 14px;
        background: var(--light-bg, #f8f8f8);
        border: 1px solid var(--border-color, #e5e7eb);
        border-radius: 10px;
    }

    .content-item-icon {
        width: 44px; height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    .content-item-icon.pdf    { background: rgba(234,84,85,.12);   color: #ea5455; }
    .content-item-icon.slide  { background: rgba(255,159,67,.12);  color: #ff9f43; }
    .content-item-icon.simulado { background: rgba(115,103,240,.12); color: #7367f0; }
    .content-item-icon.other  { background: rgba(0,207,232,.12);   color: #00cfe8; }

    .content-item-info { flex: 1; }
    .content-item-info strong { font-size: 14px; display: block; margin-bottom: 3px; color: var(--text-dark, #2d2d2d); }
    .content-item-info span   { font-size: 12px; color: var(--text-muted, #6e6b7b); }

    .empty-info {
        text-align: center;
        padding: 20px;
        color: var(--text-muted, #6e6b7b);
        font-size: 13px;
    }

    .empty-info i { font-size: 28px; opacity: .4; display: block; margin-bottom: 8px; }

    /* ── ALUNOS TABLE ── */
    .alunos-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    .alunos-table th {
        text-align: left;
        padding: 10px 12px;
        background: var(--light-bg, #f8f8f8);
        border-bottom: 1px solid var(--border-color, #e5e7eb);
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .4px;
        color: var(--text-muted, #6e6b7b);
    }

    .alunos-table td {
        padding: 10px 12px;
        border-bottom: 1px solid var(--border-color, #e5e7eb);
        color: var(--text-dark, #2d2d2d);
    }

    .alunos-table tr:last-child td { border-bottom: none; }

    .alunos-table tr:hover td { background: var(--light-bg, #f8f8f8); }

    .aluno-avatar-sm {
        width: 30px; height: 30px;
        border-radius: 50%;
        background: linear-gradient(135deg, #7367f0, #9f8cfe);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 11px;
        font-weight: 700;
        margin-right: 8px;
        vertical-align: middle;
    }

    /* ── RATING ── */
    .rating-display {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .stars {
        display: flex;
        gap: 2px;
        color: #ff9f43;
        font-size: 16px;
    }

    .stars .empty { color: var(--border-color, #e5e7eb); }

    .rating-number {
        font-size: 22px;
        font-weight: 700;
        color: var(--text-dark, #2d2d2d);
    }

    .rating-label {
        font-size: 12px;
        color: var(--text-muted, #6e6b7b);
    }

    /* ── SIDE PANEL ── */
    .side-card {
        background: var(--card-bg, #fff);
        border: 1px solid var(--border-color, #e5e7eb);
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 16px;
    }

    .side-card-header {
        padding: 14px 18px;
        border-bottom: 1px solid var(--border-color, #e5e7eb);
        font-size: 13px;
        font-weight: 700;
        color: var(--text-dark, #2d2d2d);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .side-card-header i { color: var(--primary-color, #7367f0); }

    .side-card-body { padding: 16px 18px; }

    /* Stat boxes */
    .stat-boxes {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }

    .stat-box {
        padding: 14px 12px;
        background: var(--light-bg, #f8f8f8);
        border: 1px solid var(--border-color, #e5e7eb);
        border-radius: 10px;
        text-align: center;
    }

    .stat-box strong {
        display: block;
        font-size: 22px;
        font-weight: 700;
        color: var(--primary-color, #7367f0);
        margin-bottom: 2px;
    }

    .stat-box span { font-size: 11px; color: var(--text-muted, #6e6b7b); }

    /* Action buttons */
    .action-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .btn-action {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 11px 16px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        transition: .25s;
        border: none;
        width: 100%;
        text-align: left;
    }

    .btn-action i {
        width: 20px;
        text-align: center;
        flex-shrink: 0;
    }

    .btn-action.primary {
        background: var(--primary-color, #7367f0);
        color: #fff;
    }
    .btn-action.primary:hover { background: #6258d3; color: #fff; }

    .btn-action.success {
        background: #28c76f;
        color: #fff;
    }
    .btn-action.success:hover { background: #24b263; color: #fff; }

    .btn-action.outline {
        background: transparent;
        border: 1px solid var(--border-color, #e5e7eb);
        color: var(--text-dark, #2d2d2d);
    }
    .btn-action.outline:hover {
        background: var(--light-bg, #f8f8f8);
    }

    .btn-action.danger-outline {
        background: transparent;
        border: 1px solid rgba(234,84,85,.4);
        color: #ea5455;
    }
    .btn-action.danger-outline:hover {
        background: rgba(234,84,85,.08);
    }

    /* ── MODAL ── */
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,.5);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }

    .modal-overlay.active { display: flex; }

    .modal-box {
        background: var(--card-bg, #fff);
        border: 1px solid var(--border-color, #e5e7eb);
        border-radius: 16px;
        padding: 32px;
        max-width: 420px;
        width: 90%;
        text-align: center;
        box-shadow: 0 20px 60px rgba(0,0,0,.2);
    }

    .modal-icon {
        width: 60px; height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        margin: 0 auto 18px;
    }

    .modal-icon.danger {
        background: rgba(234,84,85,.12);
        color: #ea5455;
    }

    .modal-box h3 { font-size: 18px; font-weight: 700; margin-bottom: 8px; }
    .modal-box p  { font-size: 14px; color: var(--text-muted, #6e6b7b); margin-bottom: 24px; line-height: 1.6; }

    .modal-actions { display: flex; gap: 12px; justify-content: center; }

    .modal-btn {
        padding: 10px 28px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        border: none;
        transition: .25s;
    }

    .modal-btn.cancel  { background: var(--light-bg, #f8f8f8); border: 1px solid var(--border-color, #e5e7eb); color: var(--text-muted, #6e6b7b); }
    .modal-btn.danger  { background: #ea5455; color: #fff; }
    .modal-btn.cancel:hover { background: var(--border-color, #e5e7eb); }
    .modal-btn.danger:hover { background: #d84545; }

    /* ── PROGRESS BAR (ocupação) ── */
    .occupancy-bar {
        margin-top: 10px;
    }

    .occupancy-track {
        height: 8px;
        background: var(--border-color, #e5e7eb);
        border-radius: 4px;
        overflow: hidden;
        margin-bottom: 6px;
    }

    .occupancy-fill {
        height: 100%;
        background: linear-gradient(90deg, #7367f0, #9f8cfe);
        border-radius: 4px;
        transition: width 1s ease;
    }

    .occupancy-label {
        display: flex;
        justify-content: space-between;
        font-size: 12px;
        color: var(--text-muted, #6e6b7b);
    }

    /* ── TIMELINE ── */
    .timeline {
        display: flex;
        flex-direction: column;
        gap: 0;
        padding-left: 20px;
        border-left: 2px solid var(--border-color, #e5e7eb);
        margin-left: 8px;
    }

    .tl-item {
        position: relative;
        padding: 0 0 18px 20px;
    }

    .tl-item:last-child { padding-bottom: 0; }

    .tl-dot {
        position: absolute;
        left: -9px;
        top: 2px;
        width: 14px; height: 14px;
        border-radius: 50%;
        background: var(--primary-color, #7367f0);
        border: 2px solid var(--card-bg, #fff);
        box-shadow: 0 0 0 2px var(--primary-color, #7367f0);
    }

    .tl-dot.muted { background: var(--border-color, #e5e7eb); box-shadow: 0 0 0 2px var(--border-color, #e5e7eb); }

    .tl-label { font-size: 11px; font-weight: 700; color: var(--text-muted, #6e6b7b); text-transform: uppercase; letter-spacing: .4px; margin-bottom: 3px; }
    .tl-value { font-size: 14px; color: var(--text-dark, #2d2d2d); font-weight: 500; }
    .tl-value.muted { color: var(--text-muted, #6e6b7b); font-style: italic; font-weight: 400; }
</style>
@endsection

@section('content')

{{-- ── PAGE HEADER ── --}}
<div class="page-header">
    <div class="page-header-left">
        <a href="{{ route('professor.salas.index') }}" class="back-link">
            <i class="fas fa-arrow-left"></i>
            Voltar
        </a>
        <h1 class="page-title">Detalhes da Sala</h1>
        <p class="page-subtitle">{{ $sala->titulo }}</p>
    </div>
    @if($sala->status !== 'completed')
    <div class="page-header-right">
        <a href="{{ route('professor.salas.edit', $sala->id) }}" class="btn-new-class">
            <i class="fas fa-edit"></i>
            Editar Sala
        </a>
    </div>
    @endif
</div>

<div class="show-grid">

    {{-- ════════════════════════════════
         COLUNA PRINCIPAL
    ════════════════════════════════ --}}
    <div>

        {{-- ── HERO CARD ── --}}
        <div class="hero-card">
            <div class="hero-banner status-{{ $sala->status }}">
                <div class="hero-banner-decoration">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <div class="hero-status-pill">
                    @if($sala->status === 'active')
                        <span class="dot"></span> AO VIVO
                    @elseif($sala->status === 'pending')
                        <i class="fas fa-clock"></i> AGENDADA
                    @else
                        <i class="fas fa-check-circle"></i> CONCLUÍDA
                    @endif
                </div>
            </div>
            <div class="hero-body">
                <h2 class="hero-title">{{ $sala->titulo }}</h2>
                <p class="hero-subtitle">
                    {{ $sala->descricao ?: 'Sem descrição cadastrada.' }}
                </p>
                <div class="hero-chips">
                    <span class="hero-chip">
                        <i class="fas fa-book"></i>
                        {{ $sala->materia }}
                    </span>
                    <span class="hero-chip">
                        <i class="fas fa-users"></i>
                        {{ $sala->maxAlunos }} alunos máx.
                    </span>
                    <span class="hero-chip">
                        <i class="fas fa-calendar-alt"></i>
                        {{ $sala->data_hora_inicio?->format('d/m/Y') ?? 'Sem data' }}
                    </span>
                    @if($sala->data_hora_inicio)
                    <span class="hero-chip">
                        <i class="fas fa-clock"></i>
                        {{ $sala->data_hora_inicio->format('H:i') }}
                        @if($sala->data_hora_fim)
                         – {{ $sala->data_hora_fim->format('H:i') }}
                        @endif
                    </span>
                    @endif
                    @if($sala->avaliacao !== null && $sala->avaliacao > 0)
                    <span class="hero-chip">
                        <i class="fas fa-star"></i>
                        {{ number_format($sala->avaliacao, 1) }}
                    </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── INFORMAÇÕES DETALHADAS ── --}}
        <div class="section-card">
            <div class="section-card-header">
                <i class="fas fa-info-circle"></i>
                Informações Detalhadas
            </div>
            <div class="section-card-body">
                <div class="info-list">
                    <div class="info-row">
                        <span class="info-label">Status</span>
                        <span class="info-value">
                            <span class="status-tag {{ $sala->status }}">
                                @if($sala->status === 'active')
                                    <i class="fas fa-circle"></i> Ao Vivo
                                @elseif($sala->status === 'pending')
                                    <i class="fas fa-clock"></i> Agendada
                                @else
                                    <i class="fas fa-check-circle"></i> Concluída
                                @endif
                            </span>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Matéria</span>
                        <span class="info-value">{{ $sala->materia }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Máx. Alunos</span>
                        <span class="info-value">{{ $sala->maxAlunos }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Início Previsto</span>
                        <span class="info-value">
                            @if($sala->data_hora_inicio)
                                {{ $sala->data_hora_inicio->format('d/m/Y \à\s H:i') }}
                            @else
                                <span class="muted">Não definido</span>
                            @endif
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Fim Previsto</span>
                        <span class="info-value">
                            @if($sala->data_hora_fim)
                                {{ $sala->data_hora_fim->format('d/m/Y \à\s H:i') }}
                                @if($sala->data_hora_inicio && $sala->data_hora_fim)
                                @php
                                    $dur = $sala->data_hora_inicio->diffInMinutes($sala->data_hora_fim);
                                    $h   = intdiv($dur, 60);
                                    $m   = $dur % 60;
                                @endphp
                                <span style="font-size:12px;color:var(--text-muted,#6e6b7b);margin-left:6px">
                                    ({{ $h > 0 ? $h.'h ' : '' }}{{ $m }}min)
                                </span>
                                @endif
                            @else
                                <span class="muted">Não definido</span>
                            @endif
                        </span>
                    </div>
                    @if(!empty($sala->url))
                    <div class="info-row">
                        <span class="info-label">Link da Sala</span>
                        <span class="info-value">
                            <div class="url-box">
                                <i class="fas fa-video"></i>
                                <a href="{{ $sala->url }}" target="_blank">{{ $sala->url }}</a>
                                <button class="btn-copy" data-copy="{{ $sala->url }}" title="Copiar link">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </span>
                    </div>
                    @endif
                    @if(!empty($sala->createdAt ?? $sala->created_at))
                    <div class="info-row">
                        <span class="info-label">Criada em</span>
                        <span class="info-value">
                            {{ \Carbon\Carbon::parse($sala->createdAt ?? $sala->created_at)->format('d/m/Y H:i') }}
                        </span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── TIMELINE ── --}}
        <div class="section-card">
            <div class="section-card-header">
                <i class="fas fa-timeline"></i>
                Linha do Tempo
            </div>
            <div class="section-card-body">
                <div class="timeline">
                    <div class="tl-item">
                        <div class="tl-dot"></div>
                        <div class="tl-label">Sala Criada</div>
                        <div class="tl-value">
                            {{ \Carbon\Carbon::parse($sala->createdAt ?? $sala->created_at ?? now())->format('d/m/Y H:i') }}
                        </div>
                    </div>
                    <div class="tl-item">
                        <div class="tl-dot {{ $sala->data_hora_inicio ? '' : 'muted' }}"></div>
                        <div class="tl-label">Início da Aula</div>
                        <div class="tl-value {{ $sala->data_hora_inicio ? '' : 'muted' }}">
                            {{ $sala->data_hora_inicio?->format('d/m/Y H:i') ?? 'Não iniciada' }}
                        </div>
                    </div>
                    <div class="tl-item">
                        <div class="tl-dot {{ $sala->status === 'completed' ? '' : 'muted' }}"></div>
                        <div class="tl-label">Encerramento</div>
                        <div class="tl-value {{ $sala->status === 'completed' ? '' : 'muted' }}">
                            @if($sala->status === 'completed' && $sala->data_hora_fim)
                                {{ $sala->data_hora_fim->format('d/m/Y H:i') }}
                            @else
                                Não encerrada
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── CONTEÚDO E SIMULADO ── --}}
        <div class="section-card">
            <div class="section-card-header">
                <i class="fas fa-folder-open"></i>
                Conteúdo &amp; Simulado
            </div>
            <div class="section-card-body" style="display:flex;flex-direction:column;gap:12px">

                {{-- Conteúdo --}}
                @if(!empty($sala->conteudo))
                <div class="content-item">
                    <div class="content-item-icon {{ $sala->conteudo['tipo'] ?? 'other' }}">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="content-item-info">
                        <strong>{{ $sala->conteudo['titulo'] ?? 'Conteúdo vinculado' }}</strong>
                        <span>{{ $sala->conteudo['descricao'] ?? 'Material de apoio da aula' }}</span>
                    </div>
                </div>
                @else
                <div class="empty-info">
                    <i class="fas fa-folder-open"></i>
                    Nenhum conteúdo vinculado a esta sala.
                </div>
                @endif

                {{-- Simulado --}}
                @if(!empty($sala->simulados))
                <div class="content-item">
                    <div class="content-item-icon simulado">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <div class="content-item-info">
                        <strong>{{ $sala->simulados['titulo'] ?? 'Simulado vinculado' }}</strong>
                        <span>
                            @if(!empty($sala->simulados['questoes_count']))
                                {{ $sala->simulados['questoes_count'] }} questões
                            @else
                                Simulado de avaliação
                            @endif
                        </span>
                    </div>
                </div>
                @else
                <div class="empty-info" style="padding:10px 0 0">
                    <i class="fas fa-clipboard-list"></i>
                    Nenhum simulado vinculado.
                </div>
                @endif

            </div>
        </div>

        {{-- ── LISTA DE ALUNOS ── --}}
        <div class="section-card">
            <div class="section-card-header">
                <i class="fas fa-users"></i>
                Alunos Inscritos
                <span style="margin-left:auto;font-size:12px;font-weight:400;color:var(--text-muted,#6e6b7b)">
                    {{ count((array)($sala->alunoSalas ?? [])) }} / {{ $sala->maxAlunos }}
                </span>
            </div>
            <div class="section-card-body">
                @php $alunos = (array)($sala->alunoSalas ?? []); @endphp
                @if(count($alunos) > 0)
                    <table class="alunos-table">
                        <thead>
                            <tr>
                                <th>Aluno</th>
                                <th>Status</th>
                                <th>Entrada</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($alunos as $aluno)
                            <tr>
                                <td>
                                    @php
                                        $nome = $aluno['nomeAluno'] ?? $aluno['nome'] ?? 'Aluno';
                                        $ini  = strtoupper(substr($nome,0,1));
                                    @endphp
                                    <span class="aluno-avatar-sm">{{ $ini }}</span>
                                    {{ $nome }}
                                </td>
                                <td>
                                    <span class="status-tag {{ $aluno['status'] ?? 'pending' }}" style="font-size:11px">
                                        {{ ucfirst($aluno['status'] ?? 'inscrito') }}
                                    </span>
                                </td>
                                <td style="color:var(--text-muted,#6e6b7b);font-size:12px">
                                    @if(!empty($aluno['dataEntrada']))
                                        {{ \Carbon\Carbon::parse($aluno['dataEntrada'])->format('H:i') }}
                                    @else
                                        —
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="empty-info">
                        <i class="fas fa-user-slash"></i>
                        Nenhum aluno inscrito ainda.
                    </div>
                @endif
            </div>
        </div>

    </div>

    {{-- ════════════════════════════════
         COLUNA LATERAL
    ════════════════════════════════ --}}
    <div>

        {{-- Estatísticas --}}
        <div class="side-card">
            <div class="side-card-header">
                <i class="fas fa-chart-bar"></i>
                Estatísticas
            </div>
            <div class="side-card-body">
                <div class="stat-boxes">
                    <div class="stat-box">
                        <strong>{{ count((array)($sala->alunoSalas ?? [])) }}</strong>
                        <span>Alunos</span>
                    </div>
                    <div class="stat-box">
                        <strong>{{ $sala->maxAlunos }}</strong>
                        <span>Vagas</span>
                    </div>
                    <div class="stat-box">
                        <strong>
                            @if($sala->data_hora_inicio && $sala->data_hora_fim)
                                @php
                                    $minutos = $sala->data_hora_inicio->diffInMinutes($sala->data_hora_fim);
                                    echo $minutos >= 60 ? intdiv($minutos,60).'h '.($minutos%60).'m' : $minutos.'min';
                                @endphp
                            @else
                                —
                            @endif
                        </strong>
                        <span>Duração</span>
                    </div>
                    <div class="stat-box">
                        <strong style="font-size:18px">
                            {{ $sala->avaliacao !== null ? number_format($sala->avaliacao,1) : '—' }}
                        </strong>
                        <span>Avaliação</span>
                    </div>
                </div>

                {{-- Barra de ocupação --}}
                @php
                    $inscritos = count((array)($sala->alunoSalas ?? []));
                    $pct       = $sala->maxAlunos > 0 ? min(100, round($inscritos / $sala->maxAlunos * 100)) : 0;
                @endphp
                <div class="occupancy-bar">
                    <div class="occupancy-track">
                        <div class="occupancy-fill" style="width: {{ $pct }}%"></div>
                    </div>
                    <div class="occupancy-label">
                        <span>Ocupação</span>
                        <span>{{ $pct }}%</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Avaliação --}}
        @if($sala->avaliacao !== null)
        <div class="side-card">
            <div class="side-card-header">
                <i class="fas fa-star"></i>
                Avaliação dos Alunos
            </div>
            <div class="side-card-body">
                <div class="rating-display">
                    <span class="rating-number">{{ number_format($sala->avaliacao, 1) }}</span>
                    <div>
                        <div class="stars">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $sala->avaliacao >= $i ? '' : 'empty' }}"></i>
                            @endfor
                        </div>
                        <span class="rating-label">Média dos alunos</span>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Ações --}}
        <div class="side-card">
            <div class="side-card-header">
                <i class="fas fa-bolt"></i>
                Ações
            </div>
            <div class="side-card-body">
                <div class="action-list">
                    {{-- Ao vivo: entrar na aula --}}
                    @if($sala->status === 'active')
                        <a href="{{ route('professor.salas.video-aula', $sala->id) }}"
                           class="btn-action success">
                            <i class="fas fa-video"></i>
                            Entrar na Aula
                        </a>

                    {{-- Agendada: iniciar ou editar --}}
                    @elseif($sala->status === 'pending')
                        <button class="btn-action success btn-confirmar-inicio"
                                data-id="{{ $sala->id }}"
                                data-titulo="{{ $sala->titulo }}">
                            <i class="fas fa-play"></i>
                            Iniciar Aula
                        </button>
                        <a href="{{ route('professor.salas.edit', $sala->id) }}"
                           class="btn-action outline">
                            <i class="fas fa-edit"></i>
                            Editar Sala
                        </a>
                        <button class="btn-action danger-outline" id="btnDeleteSala">
                            <i class="fas fa-trash"></i>
                            Deletar Sala
                        </button>

                    {{-- Concluída: somente visualização --}}
                    @else
                        <a href="{{ route('professor.salas.index') }}"
                           class="btn-action outline">
                            <i class="fas fa-arrow-left"></i>
                            Voltar para Salas
                        </a>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>

{{-- ── MODAL INICIAR ── --}}
<div class="modal-overlay" id="iniciarModal">
    <div class="modal-box">
        <div class="modal-icon" style="background:rgba(40,199,111,.12);color:#28c76f">
            <i class="fas fa-play-circle"></i>
        </div>
        <h3>Iniciar Aula</h3>
        <p>Deseja iniciar <strong id="iniciar-titulo"></strong> agora?<br>
           Você será redirecionado para a sala ao vivo.</p>
        <div class="modal-actions">
            <button class="modal-btn cancel" id="cancelIniciar">Cancelar</button>
            <form id="iniciarForm" method="POST">
                @csrf
                @method('PATCH')
                <button type="submit" class="modal-btn" style="background:#28c76f;color:#fff">Iniciar</button>
            </form>
        </div>
    </div>
</div>

{{-- ── MODAL DELETAR ── --}}
<div class="modal-overlay" id="deleteModal">
    <div class="modal-box">
        <div class="modal-icon danger">
            <i class="fas fa-trash-alt"></i>
        </div>
        <h3>Deletar Sala</h3>
        <p>Tem certeza que deseja deletar <strong>"{{ $sala->titulo }}"</strong>?<br>
           Esta ação não pode ser desfeita.</p>
        <div class="modal-actions">
            <button class="modal-btn cancel" id="cancelDelete">Cancelar</button>
            <form method="POST" action="{{ route('professor.salas.destroy', $sala->id) }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="modal-btn danger">Deletar</button>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
/* ── COPIAR LINK ── */
document.querySelectorAll('.btn-copy').forEach(function (btn) {
    btn.addEventListener('click', function () {
        navigator.clipboard.writeText(this.dataset.copy).then(() => {
            const icon = this.querySelector('i');
            icon.className = 'fas fa-check';
            setTimeout(() => { icon.className = 'fas fa-copy'; }, 2000);
        });
    });
});

/* ── MODAL INICIAR ── */
const iniciarModal = document.getElementById('iniciarModal');
const iniciarForm  = document.getElementById('iniciarForm');

document.querySelectorAll('.btn-confirmar-inicio').forEach(function (btn) {
    btn.addEventListener('click', function () {
        document.getElementById('iniciar-titulo').textContent = '"' + this.dataset.titulo + '"';
        iniciarForm.action = '/professor/salas/' + this.dataset.id + '/iniciar';
        iniciarModal.classList.add('active');
    });
});

document.getElementById('cancelIniciar')?.addEventListener('click', function () {
    iniciarModal.classList.remove('active');
});

iniciarModal?.addEventListener('click', function (e) {
    if (e.target === this) this.classList.remove('active');
});

/* ── MODAL DELETAR ── */
const deleteModal = document.getElementById('deleteModal');

document.getElementById('btnDeleteSala')?.addEventListener('click', function () {
    deleteModal.classList.add('active');
});

document.getElementById('cancelDelete')?.addEventListener('click', function () {
    deleteModal.classList.remove('active');
});

deleteModal?.addEventListener('click', function (e) {
    if (e.target === this) this.classList.remove('active');
});
</script>
@endsection