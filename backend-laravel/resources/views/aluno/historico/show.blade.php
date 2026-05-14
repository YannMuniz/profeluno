{{-- resources/views/aluno/historico/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detalhes da Aula')

@push('styles')
<style>
    .detail-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 16px;
        margin-top: 16px;
    }

    .detail-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 14px;
        background: var(--table-header-bg, rgba(115,103,240,0.04));
        border: 1px solid var(--border-color);
        border-radius: 10px;
    }

    .detail-item i {
        color: var(--primary-color);
        font-size: 16px;
        margin-top: 2px;
        flex-shrink: 0;
    }

    .detail-label {
        display: block;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--text-secondary);
        margin-bottom: 3px;
    }

    .detail-value {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: var(--text-primary);
    }

    /* ── Seções de recurso ─────────────────────────────────────── */
    .resource-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 20px;
    }

    .resource-header {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 14px 20px;
        background: var(--table-header-bg, rgba(115,103,240,0.05));
        border-bottom: 1px solid var(--border-color);
        font-size: 14px;
        font-weight: 600;
        color: var(--text-primary);
    }

    .resource-header i {
        color: var(--primary-color);
        font-size: 16px;
    }

    .resource-body {
        padding: 20px;
    }

    /* ── Conteúdo: link / arquivo ──────────────────────────────── */
    .conteudo-preview {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 16px;
        border: 1px solid var(--border-color);
        border-radius: 10px;
        background: var(--table-header-bg, rgba(115,103,240,0.03));
        text-decoration: none;
        color: inherit;
        transition: border-color 0.2s, background 0.2s;
    }

    .conteudo-preview:hover {
        border-color: var(--primary-color);
        background: rgba(115, 103, 240, 0.06);
    }

    .conteudo-icon-wrap {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        background: rgba(115, 103, 240, 0.12);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .conteudo-icon-wrap i {
        font-size: 20px;
        color: var(--primary-color);
    }

    .conteudo-meta strong {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 3px;
    }

    .conteudo-meta span {
        font-size: 12px;
        color: var(--text-secondary);
    }

    .conteudo-action {
        margin-left: auto;
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        font-weight: 600;
        color: var(--primary-color);
        flex-shrink: 0;
    }

    /* ── Simulado card ─────────────────────────────────────────── */
    .simulado-cta {
        display: flex;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
    }

    .simulado-stats {
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
    }

    .simulado-stat {
        text-align: center;
        padding: 10px 16px;
        background: var(--table-header-bg, rgba(115,103,240,0.04));
        border: 1px solid var(--border-color);
        border-radius: 8px;
        min-width: 72px;
    }

    .simulado-stat strong {
        display: block;
        font-size: 20px;
        font-weight: 700;
        color: var(--primary-color);
        line-height: 1;
        margin-bottom: 3px;
    }

    .simulado-stat span {
        font-size: 11px;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }

    /* ── Professor card ────────────────────────────────────────── */
    .professor-card {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .professor-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: rgba(115, 103, 240, 0.12);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 20px;
        color: var(--primary-color);
    }

    .professor-info strong {
        display: block;
        font-size: 15px;
        font-weight: 600;
        color: var(--text-primary);
    }

    .professor-info span {
        font-size: 12px;
        color: var(--text-secondary);
    }

    /* ── Estado vazio ──────────────────────────────────────────── */
    .resource-empty {
        text-align: center;
        padding: 28px;
        color: var(--text-secondary);
        font-size: 13px;
    }

    .resource-empty i {
        display: block;
        font-size: 28px;
        margin-bottom: 8px;
        color: var(--border-color);
    }
</style>
@endpush

@section('content')

<div class="page-header mb-4">
    <a href="{{ route('aluno.historico') }}" class="btn-back">
        <i class="fas fa-arrow-left"></i> Voltar ao Histórico
    </a>
    <h1 class="mt-2">{{ $sala->titulo }}</h1>
    <p class="text-muted">{{ $sala->materia }}</p>
</div>

<div class="row g-4">

    {{-- ── Coluna principal ──────────────────────────────────── --}}
    <div class="col-lg-8">

        {{-- Detalhes da aula --}}
        <div class="resource-card">
            <div class="resource-header">
                <i class="fas fa-info-circle"></i> Detalhes da Aula
            </div>
            <div class="resource-body">

                @if($sala->descricao)
                <p style="font-size: 14px; color: var(--text-secondary); margin-bottom: 16px;">
                    {{ $sala->descricao }}
                </p>
                @endif

                <div class="detail-grid">
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

        {{-- Professor --}}
        <div class="resource-card">
            <div class="resource-header">
                <i class="fas fa-user-tie"></i> Professor
            </div>
            <div class="resource-body">
                @if($professor)
                <div class="professor-card">
                    <div class="professor-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="professor-info">
                        <strong>{{ $professor['nome'] ?? $professor['name'] ?? 'Professor' }}</strong>
                        @if(!empty($professor['email']))
                        <span><i class="fas fa-envelope me-1"></i>{{ $professor['email'] }}</span>
                        @endif
                        @if(!empty($professor['descricao']))
                        <p style="font-size: 12px; color: var(--text-secondary); margin: 6px 0 0;">
                            {{ $professor['descricao'] }}
                        </p>
                        @endif
                    </div>
                </div>
                @else
                <div class="resource-empty">
                    <i class="fas fa-user-slash"></i>
                    <p>Informações do professor não disponíveis.</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Conteúdo --}}
        <div class="resource-card">
            <div class="resource-header">
                <i class="fas fa-file-alt"></i> Conteúdo da Aula
            </div>
            <div class="resource-body">
                @if($conteudo)
                    @php
                        // Detecta tipo: link externo ou arquivo
                        $link    = $conteudo['link']    ?? $conteudo['url']     ?? null;
                        $arquivo = $conteudo['arquivo'] ?? $conteudo['arquivo'] ?? null;
                        $tipo    = $conteudo['tipo']    ?? null;
                        $tituloConteudo  = $conteudo['titulo']   ?? 'Conteúdo';
                        $descConteudo    = $conteudo['descricao'] ?? null;

                        // Ícone por extensão/tipo
                        $iconMap = [
                            'pdf'  => 'fa-file-pdf',
                            'doc'  => 'fa-file-word',
                            'docx' => 'fa-file-word',
                            'ppt'  => 'fa-file-powerpoint',
                            'pptx' => 'fa-file-powerpoint',
                            'xls'  => 'fa-file-excel',
                            'xlsx' => 'fa-file-excel',
                            'zip'  => 'fa-file-archive',
                            'rar'  => 'fa-file-archive',
                        ];

                        $ext  = $arquivo ? strtolower(pathinfo($arquivo, PATHINFO_EXTENSION)) : null;
                        $icon = $link ? 'fa-link' : ($iconMap[$ext] ?? 'fa-file');
                        $isLink = !empty($link);
                    @endphp

                    @if(!empty($descConteudo))
                    <p style="font-size: 13px; color: var(--text-secondary); margin-bottom: 14px;">
                        {{ $descConteudo }}
                    </p>
                    @endif

                    @if($isLink)
                    {{-- Link externo → abre nova aba --}}
                    <a href="{{ $link }}" target="_blank" rel="noopener" class="conteudo-preview">
                        <div class="conteudo-icon-wrap">
                            <i class="fas {{ $icon }}"></i>
                        </div>
                        <div class="conteudo-meta">
                            <strong>{{ $tituloConteudo }}</strong>
                            <span>{{ $link }}</span>
                        </div>
                        <div class="conteudo-action">
                            <i class="fas fa-external-link-alt"></i> Abrir
                        </div>
                    </a>
                    @elseif($arquivo)
                    {{-- Arquivo → download --}}
                    <a href="{{ $arquivo }}" download class="conteudo-preview">
                        <div class="conteudo-icon-wrap">
                            <i class="fas {{ $icon }}"></i>
                        </div>
                        <div class="conteudo-meta">
                            <strong>{{ $tituloConteudo }}</strong>
                            <span>{{ strtoupper($ext ?? 'arquivo') }} · Clique para baixar</span>
                        </div>
                        <div class="conteudo-action">
                            <i class="fas fa-download"></i> Baixar
                        </div>
                    </a>
                    @else
                    <div class="resource-empty">
                        <i class="fas fa-file-slash"></i>
                        <p>Arquivo ou link não disponível.</p>
                    </div>
                    @endif

                @else
                <div class="resource-empty">
                    <i class="fas fa-folder-open"></i>
                    <p>Nenhum conteúdo foi disponibilizado nesta aula.</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Simulado --}}
        <div class="resource-card">
            <div class="resource-header">
                <i class="fas fa-list-ol"></i> Simulado
            </div>
            <div class="resource-body">
                @if($simulado)
                    @php
                        $questoes = $simulado['simuladoQuestao'] ?? [];
                        $total    = count($questoes);
                    @endphp

                    <div class="simulado-cta">
                        <div class="simulado-stats">
                            <div class="simulado-stat">
                                <strong>{{ $total }}</strong>
                                <span>Questões</span>
                            </div>
                            <div class="simulado-stat">
                                <strong>{{ $simulado['situacao'] ? '✓' : '✗' }}</strong>
                                <span>{{ $simulado['situacao'] ? 'Ativo' : 'Inativo' }}</span>
                            </div>
                        </div>

                        <div style="flex: 1; min-width: 200px;">
                            <p style="font-size: 14px; font-weight: 600; margin-bottom: 4px; color: var(--text-primary);">
                                {{ $simulado['titulo'] ?? 'Simulado' }}
                            </p>
                            @if(!empty($simulado['descricao']))
                            <p style="font-size: 12px; color: var(--text-secondary); margin: 0;">
                                {{ $simulado['descricao'] }}
                            </p>
                            @endif
                        </div>

                        @if($simulado['situacao'] && $total > 0)
                        <a href="{{ route('aluno.historico.simulado', $sala->id) }}" class="btn-primary" style="white-space: nowrap;">
                            <i class="fas fa-play"></i> Fazer Simulado
                        </a>
                        @else
                        <span style="font-size: 12px; color: var(--text-secondary);">
                            <i class="fas fa-lock me-1"></i>Simulado indisponível
                        </span>
                        @endif
                    </div>
                @else
                <div class="resource-empty">
                    <i class="fas fa-clipboard-list"></i>
                    <p>Nenhum simulado foi disponibilizado nesta aula.</p>
                </div>
                @endif
            </div>
        </div>

    </div>

    {{-- ── Coluna lateral ─────────────────────────────────────── --}}
    <div class="col-lg-4">
        <div class="card-section">
            <h2 class="section-title">
                <i class="fas fa-chart-bar"></i> Status da Aula
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

        {{-- Recursos disponíveis --}}
        <div class="card-section mt-4">
            <h2 class="section-title">
                <i class="fas fa-boxes"></i> Recursos
            </h2>
            <ul style="list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; gap: 10px;">
                <li style="display: flex; align-items: center; gap: 10px; font-size: 13px; color: {{ $conteudo ? 'var(--text-primary)' : 'var(--text-secondary)' }};">
                    <i class="fas fa-file-alt" style="color: {{ $conteudo ? 'var(--primary-color)' : 'var(--border-color)' }}; width: 16px;"></i>
                    Conteúdo
                    @if($conteudo)
                        <span style="margin-left: auto; font-size: 11px; background: rgba(40,199,111,0.12); color: #28c76f; padding: 2px 8px; border-radius: 10px; font-weight: 600;">
                            Disponível
                        </span>
                    @else
                        <span style="margin-left: auto; font-size: 11px; color: var(--text-secondary);">—</span>
                    @endif
                </li>
                <li style="display: flex; align-items: center; gap: 10px; font-size: 13px; color: {{ $simulado ? 'var(--text-primary)' : 'var(--text-secondary)' }};">
                    <i class="fas fa-list-ol" style="color: {{ $simulado ? 'var(--primary-color)' : 'var(--border-color)' }}; width: 16px;"></i>
                    Simulado
                    @if($simulado)
                        <span style="margin-left: auto; font-size: 11px; background: rgba(40,199,111,0.12); color: #28c76f; padding: 2px 8px; border-radius: 10px; font-weight: 600;">
                            {{ count($simulado['simuladoQuestao'] ?? []) }} questões
                        </span>
                    @else
                        <span style="margin-left: auto; font-size: 11px; color: var(--text-secondary);">—</span>
                    @endif
                </li>
            </ul>
        </div>

        <div class="card-section mt-4">
            <a href="{{ route('aluno.historico') }}" class="btn-secondary w-100 text-center">
                <i class="fas fa-arrow-left"></i> Voltar ao Histórico
            </a>
        </div>
    </div>

</div>

@endsection