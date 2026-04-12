{{-- resources/views/professor/salas/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Editar Sala — ' . ($sala->titulo ?? ''))

@section('styles')
<link rel="stylesheet" href="{{ asset('css/sala-professor.css') }}">
<link rel="stylesheet" href="{{ asset('css/steps-conteudo-simulado.css') }}">
<style>
    /* ── BOTÃO COPIAR URL ── */
    .input-with-icon {
        position: relative;
        display: flex;
        align-items: center;
        gap: 0;
    }

    .input-with-icon > i {
        position: absolute;
        left: 12px;
        color: var(--primary-color, #7367f0);
        font-size: 13px;
        pointer-events: none;
        z-index: 2;
    }

    .input-with-icon .form-control {
        padding-left: 34px;
        padding-right: 44px;
        background: var(--light-bg, #f8f8f8);
        color: var(--text-muted, #6e6b7b);
        cursor: default;
    }

    .btn-copy-url {
        position: absolute;
        right: 8px;
        width: 30px;
        height: 30px;
        background: rgba(115, 103, 240, .1);
        border: 1px solid rgba(115, 103, 240, .35);
        border-radius: 6px;
        color: var(--primary-color, #7367f0);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        transition: .2s;
        z-index: 3;
    }

    .btn-copy-url:hover {
        background: var(--primary-color, #7367f0);
        color: #fff;
        border-color: var(--primary-color, #7367f0);
    }

    /* ── BOTÃO DELETAR (ações rápidas) ── */
    .btn-danger-outline {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 16px;
        background: transparent;
        border: 1px solid rgba(234, 84, 85, .45);
        border-radius: 8px;
        color: #ea5455;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        width: 100%;
        transition: .2s;
        text-align: left;
    }

    .btn-danger-outline:hover {
        background: rgba(234, 84, 85, .08);
        border-color: #ea5455;
    }

    /* ── BOTÃO ENTRAR NA AULA (sidebar) ── */
    .btn-enter-live-side {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 16px;
        background: #28c76f;
        border: none;
        border-radius: 8px;
        color: #fff;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        width: 100%;
        text-decoration: none;
        transition: .2s;
    }

    .btn-enter-live-side:hover {
        background: #24b263;
        color: #fff;
    }

    /* ── BOTÃO INICIAR AGORA (sidebar) ── */
    .btn-iniciar-side {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 16px;
        background: var(--primary-color, #7367f0);
        border: none;
        border-radius: 8px;
        color: #fff;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        width: 100%;
        text-decoration: none;
        transition: .2s;
    }

    .btn-iniciar-side:hover {
        background: #6258d3;
        color: #fff;
    }

    /* ── HINT DE STATUS (campos data) ── */
    .field-hint {
        font-size: 12px;
        color: var(--text-muted, #6e6b7b);
        margin-top: 4px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .field-hint i { color: var(--primary-color, #7367f0); }

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
        width: 60px; height: 60px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 26px; margin: 0 auto 18px;
    }

    .modal-icon.danger  { background: rgba(234,84,85,.12);  color: #ea5455; }

    .modal-box h3 { font-size: 18px; font-weight: 700; margin-bottom: 8px; }
    .modal-box p  { font-size: 14px; color: var(--text-muted, #6e6b7b); margin-bottom: 24px; line-height: 1.6; }

    .modal-actions { display: flex; gap: 12px; justify-content: center; }

    .modal-btn {
        padding: 10px 28px; border-radius: 8px;
        font-weight: 600; font-size: 14px;
        cursor: pointer; border: none; transition: .25s;
    }

    .modal-btn.cancel { background: var(--light-bg, #f8f8f8); border: 1px solid var(--border-color, #e5e7eb); color: var(--text-muted, #6e6b7b); }
    .modal-btn.danger { background: #ea5455; color: #fff; }
    .modal-btn.cancel:hover { background: var(--border-color, #e5e7eb); }
    .modal-btn.danger:hover { background: #d84545; }
</style>
@endsection

@section('content')

<div class="page-header">
    <div class="page-header-left">
        <a href="{{ route('professor.salas.index') }}" class="back-link">
            <i class="fas fa-arrow-left"></i>
            Voltar
        </a>
        <h1 class="page-title">Editar Sala de Aula</h1>
        <p class="page-subtitle">{{ $sala->titulo ?? '' }}</p>
    </div>
    <div class="page-header-right">
        <span class="status-badge {{ $sala->status ?? 'pending' }}">
            @if(($sala->status ?? '') === 'active')
                <i class="fas fa-circle pulse-dot"></i> Ao Vivo
            @elseif(($sala->status ?? '') === 'completed')
                <i class="fas fa-check-circle"></i> Concluída
            @else
                <i class="fas fa-clock"></i> Agendada
            @endif
        </span>
    </div>
</div>

{{-- Alertas --}}
@if($errors->has('api'))
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i>
        {{ $errors->first('api') }}
    </div>
@endif

@if($errors->any() && !$errors->has('api'))
    <div class="alert alert-danger">
        <ul style="margin:0; padding-left: 18px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if(session('warning'))
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle"></i>
        {{ session('warning') }}
    </div>
@endif

@php
    $salaMateriaId  = old('materia_id',  $sala->idMateria  ?? '');
    $salaMaxAlunos  = old('max_alunos',  $sala->maxAlunos  ?? $sala->qtd_alunos ?? '');
    $salaDataInicio = old('data_hora_inicio', optional($sala->data_hora_inicio)->format('Y-m-d\TH:i'));
    $salaDataFim    = old('data_hora_fim',    optional($sala->data_hora_fim)->format('Y-m-d\TH:i'));
    $salaStatus     = old('status',      $sala->status     ?? 'pending');
    $salaConteudoId = old('conteudo_id', $sala->idConteudo ?? '');
    $salaSimuladoId = old('simulado_id', $sala->idSimulado ?? '');
    $salaUrl        = old('url',         $sala->url        ?? '');
@endphp

<form
    action="{{ route('professor.salas.update', $sala->id) }}"
    method="POST"
    id="formEditarSala"
>
    @csrf
    @method('PUT')

    <div class="form-grid-two">

        {{-- ── COLUNA PRINCIPAL ── --}}
        <div class="form-col-main">

            {{-- CARD: Dados Principais --}}
            <div class="form-card">
                <div class="form-card-header">
                    <i class="fas fa-info-circle"></i>
                    <h3>Dados Principais</h3>
                </div>
                <div class="form-card-body">

                    {{-- Título --}}
                    <div class="form-group">
                        <label for="titulo" class="form-label">
                            Título da Sala <span class="required">*</span>
                        </label>
                        <input
                            type="text"
                            id="titulo"
                            name="titulo"
                            class="form-control @error('titulo') is-invalid @enderror"
                            placeholder="Ex: Álgebra Linear — Turma A"
                            value="{{ old('titulo', $sala->titulo ?? '') }}"
                            maxlength="255"
                            required
                        >
                        <span class="char-count">
                            <span id="tituloCount">{{ strlen(old('titulo', $sala->titulo ?? '')) }}</span>/255
                        </span>
                        @error('titulo')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Descrição --}}
                    <div class="form-group">
                        <label for="descricao" class="form-label">Descrição</label>
                        <textarea
                            id="descricao"
                            name="descricao"
                            class="form-control @error('descricao') is-invalid @enderror"
                            rows="4"
                            placeholder="Descreva o conteúdo que será abordado..."
                        >{{ old('descricao', $sala->descricao ?? '') }}</textarea>
                        @error('descricao')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Matéria + Max Alunos --}}
                    <div class="form-row-two">
                        <div class="form-group">
                            <label for="materia_id" class="form-label">
                                Matéria <span class="required">*</span>
                            </label>
                            <select
                                id="materia_id"
                                name="materia_id"
                                class="form-control filter-select @error('materia_id') is-invalid @enderror"
                                required
                            >
                                <option value="">Selecione a matéria</option>
                                @forelse($materias as $materia)
                                    <option
                                        value="{{ $materia['idMateria'] }}"
                                        {{ $salaMateriaId == $materia['idMateria'] ? 'selected' : '' }}
                                    >
                                        {{ $materia['nomeMateria'] }}
                                    </option>
                                @empty
                                    <option value="" disabled>Nenhuma matéria disponível</option>
                                @endforelse
                            </select>
                            @error('materia_id')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="max_alunos" class="form-label">
                                Máx. de Alunos <span class="required">*</span>
                            </label>
                            <input
                                type="number"
                                id="max_alunos"
                                name="max_alunos"
                                class="form-control @error('max_alunos') is-invalid @enderror"
                                placeholder="Ex: 30"
                                value="{{ $salaMaxAlunos }}"
                                min="1"
                                max="500"
                                required
                            >
                            @error('max_alunos')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="form-group">
                        <label for="status" class="form-label">Status</label>
                        <select
                            id="status"
                            name="status"
                            class="form-control filter-select @error('status') is-invalid @enderror"
                        >
                            <option value="pending"   {{ $salaStatus === 'pending'   ? 'selected' : '' }}>
                                Agendada (Pendente)
                            </option>
                            <option value="active"    {{ $salaStatus === 'active'    ? 'selected' : '' }}>
                                Ativa (Ao Vivo)
                            </option>
                            <option value="completed" {{ $salaStatus === 'completed' ? 'selected' : '' }}>
                                Concluída
                            </option>
                        </select>
                        @error('status')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Data/hora — dinâmico conforme status --}}
                    {{-- Bloco ao vivo: só mostra campo de fim --}}
                    <div id="block-active" style="{{ $salaStatus === 'active' ? '' : 'display:none' }}">
                        <div class="form-group">
                            <label for="data_hora_fim_active" class="form-label">
                                Previsão de Término
                            </label>
                            <input
                                type="datetime-local"
                                id="data_hora_fim_active"
                                class="form-control @error('data_hora_fim') is-invalid @enderror"
                                value="{{ $salaDataFim }}"
                            >
                            <p class="field-hint">
                                <i class="fas fa-info-circle"></i>
                                O início foi registrado quando a aula foi iniciada.
                            </p>
                            @error('data_hora_fim')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Bloco agendada: mostra início e fim --}}
                    <div id="block-pending" style="{{ $salaStatus === 'pending' ? '' : 'display:none' }}">
                        <div class="form-row-two">
                            <div class="form-group">
                                <label for="data_hora_inicio" class="form-label">
                                    Previsão de Início
                                </label>
                                <input
                                    type="datetime-local"
                                    id="data_hora_inicio"
                                    name="data_hora_inicio"
                                    class="form-control @error('data_hora_inicio') is-invalid @enderror"
                                    value="{{ $salaDataInicio }}"
                                >
                                @error('data_hora_inicio')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="data_hora_fim_pending" class="form-label">
                                    Previsão de Término
                                </label>
                                <input
                                    type="datetime-local"
                                    id="data_hora_fim_pending"
                                    class="form-control @error('data_hora_fim') is-invalid @enderror"
                                    value="{{ $salaDataFim }}"
                                >
                                @error('data_hora_fim')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Bloco concluída: campos readonly --}}
                    <div id="block-completed" style="{{ $salaStatus === 'completed' ? '' : 'display:none' }}">
                        <div class="form-row-two">
                            <div class="form-group">
                                <label class="form-label">Início Real</label>
                                <input
                                    type="datetime-local"
                                    class="form-control"
                                    value="{{ $salaDataInicio }}"
                                    readonly
                                >
                            </div>
                            <div class="form-group">
                                <label class="form-label">Término Real</label>
                                <input
                                    type="datetime-local"
                                    class="form-control"
                                    value="{{ $salaDataFim }}"
                                    readonly
                                >
                            </div>
                        </div>
                    </div>

                    {{-- Inputs hidden que enviam os valores corretos para o controller --}}
                    <input type="hidden" id="data_hora_fim" name="data_hora_fim" value="{{ $salaDataFim }}">

                    {{-- URL da sala (somente leitura) --}}
                    @if($salaUrl)
                    <div class="form-group" style="margin-top: 8px;">
                        <label class="form-label">
                            Link da Sala
                            <span class="optional-tag">Gerado automaticamente</span>
                        </label>
                        <div class="input-with-icon">
                            <i class="fas fa-video"></i>
                            <input
                                type="text"
                                class="form-control"
                                value="{{ $salaUrl }}"
                                readonly
                            >
                            <button
                                type="button"
                                class="btn-copy-url"
                                data-copy="{{ $salaUrl }}"
                                title="Copiar link"
                            >
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    @endif

                </div>
            </div>

            {{-- CARD: Conteúdo de Apoio --}}
            <div class="form-card">
                <div class="form-card-header">
                    <i class="fas fa-folder-open"></i>
                    <h3>
                        Conteúdo de Apoio
                        <span class="optional-tag">Opcional</span>
                    </h3>
                </div>
                <div class="form-card-body">

                    @if(count($conteudos))
                        <p class="section-hint">
                            <i class="fas fa-info-circle"></i>
                            Selecione o conteúdo vinculado a esta sala ou remova o vínculo.
                        </p>

                        <div class="conteudo-grid" id="conteudoGrid">
                            @foreach($conteudos as $conteudo)
                            <label
                                class="conteudo-card"
                                for="conteudo_{{ $conteudo['idConteudo'] }}"
                                data-url="{{ $conteudo['url'] ?? '' }}"
                                data-tipo="{{ $conteudo['tipo'] ?? 'other' }}"
                            >
                                <input
                                    type="radio"
                                    id="conteudo_{{ $conteudo['idConteudo'] }}"
                                    name="conteudo_id"
                                    value="{{ $conteudo['idConteudo'] }}"
                                    {{ $salaConteudoId == $conteudo['idConteudo'] ? 'checked' : '' }}
                                    class="conteudo-radio"
                                >
                                <div class="conteudo-card-inner">
                                    <div class="conteudo-tipo-badge {{ $conteudo['tipo'] ?? 'other' }}">
                                        @switch($conteudo['tipo'] ?? '')
                                            @case('pdf')    <i class="fas fa-file-pdf"></i> PDF @break
                                            @case('slide')  <i class="fas fa-file-powerpoint"></i> Slide @break
                                            @case('document') <i class="fas fa-file-word"></i> Doc @break
                                            @case('link')   <i class="fas fa-link"></i> Link @break
                                            @default        <i class="fas fa-file"></i> Arquivo
                                        @endswitch
                                    </div>
                                    <div class="conteudo-info">
                                        <strong>{{ $conteudo['titulo'] }}</strong>
                                        @if(!empty($conteudo['descricao']))
                                            <span>{{ Str::limit($conteudo['descricao'], 60) }}</span>
                                        @endif
                                    </div>
                                    <div class="conteudo-check">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                </div>
                            </label>
                            @endforeach

                            <label class="conteudo-card conteudo-none" for="conteudo_none">
                                <input
                                    type="radio"
                                    id="conteudo_none"
                                    name="conteudo_id"
                                    value=""
                                    {{ empty($salaConteudoId) ? 'checked' : '' }}
                                    class="conteudo-radio"
                                >
                                <div class="conteudo-card-inner">
                                    <div class="conteudo-tipo-badge none">
                                        <i class="fas fa-ban"></i>
                                    </div>
                                    <div class="conteudo-info">
                                        <strong>Sem conteúdo</strong>
                                        <span>Remover vínculo de conteúdo</span>
                                    </div>
                                    <div class="conteudo-check">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div class="conteudo-preview-wrapper" id="conteudoPreviewWrapper" style="display:none;">
                            <div class="conteudo-preview-header">
                                <span>
                                    <i class="fas fa-eye"></i>
                                    <span id="conteudoPreviewTitle">Prévia</span>
                                </span>
                                <a href="#" target="_blank" id="btnAbrirNovaAba" class="btn-abrir-nova-aba">
                                    <i class="fas fa-external-link-alt"></i> Abrir em nova aba
                                </a>
                            </div>
                            <iframe
                                id="conteudoIframe"
                                class="conteudo-iframe"
                                src=""
                                allowfullscreen
                                style="display:none;"
                            ></iframe>
                            <div id="conteudoFallback" class="conteudo-preview-fallback" style="display:none;"></div>
                        </div>

                    @else
                        <div class="empty-state-inline">
                            <i class="fas fa-folder-open"></i>
                            <p>Você ainda não tem conteúdos cadastrados.</p>
                            <a href="{{ route('professor.conteudo.create') }}" class="btn-form-next" target="_blank">
                                <i class="fas fa-plus"></i> Cadastrar Conteúdo
                            </a>
                        </div>
                        <input type="hidden" name="conteudo_id" value="">
                    @endif

                </div>
            </div>

            {{-- CARD: Simulado --}}
            <div class="form-card">
                <div class="form-card-header">
                    <i class="fas fa-clipboard-list"></i>
                    <h3>
                        Simulado Vinculado
                        <span class="optional-tag">Opcional</span>
                    </h3>
                </div>
                <div class="form-card-body">

                    @if(count($simulados))
                        <div class="conteudo-grid">
                            @foreach($simulados as $simulado)
                            <label class="conteudo-card" for="simulado_{{ $simulado['idSimulado'] }}">
                                <input
                                    type="radio"
                                    id="simulado_{{ $simulado['idSimulado'] }}"
                                    name="simulado_id"
                                    value="{{ $simulado['idSimulado'] }}"
                                    {{ $salaSimuladoId == $simulado['idSimulado'] ? 'checked' : '' }}
                                    class="simulado-radio"
                                >
                                <div class="conteudo-card-inner">
                                    <div class="conteudo-tipo-badge simulado">
                                        <i class="fas fa-clipboard-list"></i>
                                    </div>
                                    <div class="conteudo-info">
                                        <strong>{{ $simulado['titulo'] }}</strong>
                                        @if(!empty($simulado['descricao']))
                                            <span>{{ Str::limit($simulado['descricao'], 60) }}</span>
                                        @endif
                                        @if(!empty($simulado['questoes_count']))
                                            <span class="badge-questoes">
                                                {{ $simulado['questoes_count'] }} questões
                                            </span>
                                        @endif
                                    </div>
                                    <div class="conteudo-check">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                </div>
                            </label>
                            @endforeach

                            <label class="conteudo-card conteudo-none" for="simulado_none">
                                <input
                                    type="radio"
                                    id="simulado_none"
                                    name="simulado_id"
                                    value=""
                                    {{ empty($salaSimuladoId) ? 'checked' : '' }}
                                    class="simulado-radio"
                                >
                                <div class="conteudo-card-inner">
                                    <div class="conteudo-tipo-badge none">
                                        <i class="fas fa-ban"></i>
                                    </div>
                                    <div class="conteudo-info">
                                        <strong>Sem simulado</strong>
                                        <span>Remover vínculo de simulado</span>
                                    </div>
                                    <div class="conteudo-check">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                </div>
                            </label>
                        </div>

                    @else
                        <div class="empty-state-inline">
                            <i class="fas fa-clipboard-list"></i>
                            <p>Nenhum simulado cadastrado.</p>
                        </div>
                        <input type="hidden" name="simulado_id" value="">
                    @endif

                </div>
            </div>

        </div>

        {{-- ── COLUNA LATERAL ── --}}
        <div class="form-col-side">

            {{-- Prévia do card --}}
            <div class="preview-card">
                <div class="preview-card-header">
                    <i class="fas fa-eye"></i>
                    Prévia do Card
                </div>
                <div class="preview-card-body">
                    <div class="mini-class-card">
                        <div class="mini-ribbon {{ $salaStatus }}" id="previewRibbon">
                            @if($salaStatus === 'active')
                                <i class="fas fa-circle"></i> Ao Vivo
                            @elseif($salaStatus === 'completed')
                                <i class="fas fa-check"></i> Concluída
                            @else
                                <i class="fas fa-clock"></i> Agendada
                            @endif
                        </div>
                        <div class="mini-card-icon">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <h4 id="previewTitulo">{{ $sala->titulo ?? 'Título da Sala' }}</h4>
                        <span id="previewMateria" class="mini-subject">
                            {{ optional(collect($materias)->firstWhere('idMateria', $salaMateriaId))['nomeMateria'] ?? '—' }}
                        </span>
                        <div class="mini-meta">
                            <span>
                                <i class="fas fa-users"></i>
                                <span id="previewAlunos">{{ $salaMaxAlunos ?: 0 }}</span> alunos
                            </span>
                            <span>
                                <i class="fas fa-calendar"></i>
                                <span id="previewData">
                                    {{ $salaDataInicio ? \Carbon\Carbon::parse($salaDataInicio)->format('d/m/Y') : 'Sem data' }}
                                </span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Informações da sala --}}
            <div class="tips-card">
                <div class="tips-card-header">
                    <i class="fas fa-clock-rotate-left"></i>
                    Informações da Sala
                </div>
                <ul class="tips-list">
                    @if(!empty($sala->createdAt ?? $sala->created_at))
                    <li>
                        <strong>Criada em:</strong>
                        {{ \Carbon\Carbon::parse($sala->createdAt ?? $sala->created_at)->format('d/m/Y H:i') }}
                    </li>
                    @endif
                    @if($sala->avaliacao !== null)
                    <li>
                        <strong>Avaliação:</strong>
                        ⭐ {{ number_format($sala->avaliacao, 1) }}
                    </li>
                    @endif
                    @if($salaUrl)
                    <li>
                        <strong>Link da sala:</strong>
                        <a href="{{ $salaUrl }}" target="_blank" class="truncate-link">
                            {{ Str::limit($salaUrl, 30) }}
                        </a>
                    </li>
                    @endif
                </ul>
            </div>

            {{-- Ações rápidas --}}
            <div class="tips-card">
                <div class="tips-card-header">
                    <i class="fas fa-bolt"></i>
                    Ações Rápidas
                </div>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    @if(($sala->status ?? '') === 'pending')
                        <button
                            type="button"
                            class="btn-iniciar-side btn-confirmar-inicio"
                            data-id="{{ $sala->id }}"
                            data-titulo="{{ $sala->titulo }}"
                        >
                            <i class="fas fa-play"></i> Iniciar Agora
                        </button>
                    @elseif(($sala->status ?? '') === 'active')
                        <a href="{{ route('professor.salas.video-aula', $sala->id) }}"
                           class="btn-enter-live-side">
                            <i class="fas fa-video"></i> Entrar na Aula
                        </a>
                    @endif

                    @if(($sala->status ?? '') !== 'completed')
                    <button
                        type="button"
                        class="btn-danger-outline"
                        id="btnDeleteSala"
                    >
                        <i class="fas fa-trash"></i> Deletar Sala
                    </button>
                    @endif
                </div>
            </div>

        </div>
    </div>

    {{-- Ações do formulário --}}
    <div class="form-actions">
        <a href="{{ route('professor.salas.index') }}" class="btn-form-cancel">
            <i class="fas fa-times"></i> Cancelar
        </a>
        <button type="submit" class="btn-form-submit">
            <i class="fas fa-save"></i>
            Salvar Alterações
        </button>
    </div>

</form>

{{-- Modal de confirmação de exclusão --}}
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

{{-- Modal de confirmação de início --}}
<div class="modal-overlay" id="iniciarModal">
    <div class="modal-box">
        <div class="modal-icon" style="background:rgba(40,199,111,.12);color:#28c76f">
            <i class="fas fa-play-circle"></i>
        </div>
        <h3>Iniciar Aula</h3>
        <p>Deseja iniciar <strong id="iniciar-sala-titulo"></strong> agora?<br>
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

@endsection

@section('scripts')
<script src="{{ asset('js/sala-professor.js') }}"></script>
@if(count($conteudos))
<script src="{{ asset('js/steps-conteudo-simulado.js') }}"></script>
@endif

<script>
/* ── MAPA DE MATÉRIAS (prévia) ── */
const materiaNames = {
    @foreach($materias as $m)
        "{{ $m['idMateria'] }}": "{{ $m['nomeMateria'] }}",
    @endforeach
};

/* ── LÓGICA DE CAMPOS DE DATA POR STATUS ── */
const statusSelect       = document.getElementById('status');
const blockActive        = document.getElementById('block-active');
const blockPending       = document.getElementById('block-pending');
const blockCompleted     = document.getElementById('block-completed');

// Inputs reais que alimentam os hidden
const inputFimActive     = document.getElementById('data_hora_fim_active');
const inputInicioPending = document.getElementById('data_hora_inicio');
const inputFimPending    = document.getElementById('data_hora_fim_pending');

// Inputs hidden enviados ao controller
const hiddenFim          = document.getElementById('data_hora_fim');

function syncHiddens() {
    const st = statusSelect.value;
    if (st === 'active') {
        hiddenFim.value = inputFimActive?.value || '';
    } else if (st === 'pending') {
        hiddenFim.value = inputFimPending?.value || '';
    }
    // completed: valores já estão readonly, não alteramos
}

function toggleDateBlocks() {
    const st = statusSelect.value;
    blockActive.style.display    = st === 'active'    ? '' : 'none';
    blockPending.style.display   = st === 'pending'   ? '' : 'none';
    blockCompleted.style.display = st === 'completed' ? '' : 'none';
    syncHiddens();
    updatePreview();
}

statusSelect?.addEventListener('change', toggleDateBlocks);

// Sincroniza os hiddens ao alterar qualquer campo de data
[inputFimActive, inputInicioPending, inputFimPending].forEach(function (el) {
    el?.addEventListener('change', syncHiddens);
});

/* ── PRÉVIA ── */
function updatePreview() {
    const titulo  = document.getElementById('titulo')?.value       || '';
    const matId   = document.getElementById('materia_id')?.value   || '';
    const alunos  = document.getElementById('max_alunos')?.value   || '0';
    const status  = statusSelect?.value                            || 'pending';

    // Data para a prévia: pega do bloco visível
    let inicio = '';
    if (status === 'active')    inicio = inputFimActive?.value     || '';
    if (status === 'pending')   inicio = inputInicioPending?.value || '';

    document.getElementById('previewTitulo').textContent  = titulo || 'Título da Sala';
    document.getElementById('previewMateria').textContent = materiaNames[matId] || '—';
    document.getElementById('previewAlunos').textContent  = alunos;

    if (inicio) {
        try {
            document.getElementById('previewData').textContent =
                new Date(inicio).toLocaleDateString('pt-BR');
        } catch(e) {}
    }

    const ribbon = document.getElementById('previewRibbon');
    const labels = {
        active:    '<i class="fas fa-circle"></i> Ao Vivo',
        pending:   '<i class="fas fa-clock"></i> Agendada',
        completed: '<i class="fas fa-check"></i> Concluída',
    };
    ribbon.className = `mini-ribbon ${status}`;
    ribbon.innerHTML = labels[status] || labels.pending;
}

['titulo', 'materia_id', 'max_alunos', 'status',
 'data_hora_inicio', 'data_hora_fim_active', 'data_hora_fim_pending'].forEach(function (id) {
    document.getElementById(id)?.addEventListener('input',  updatePreview);
    document.getElementById(id)?.addEventListener('change', updatePreview);
});

/* ── CONTADOR TÍTULO ── */
document.getElementById('titulo')?.addEventListener('input', function () {
    document.getElementById('tituloCount').textContent = this.value.length;
});

/* ── COPIAR URL ── */
document.querySelector('.btn-copy-url')?.addEventListener('click', function () {
    const url = this.dataset.copy;
    navigator.clipboard.writeText(url).then(() => {
        const icon = this.querySelector('i');
        icon.className = 'fas fa-check';
        setTimeout(() => { icon.className = 'fas fa-copy'; }, 2000);
    });
});

/* ── MODAL DELETE ── */
const deleteModal  = document.getElementById('deleteModal');
const cancelDelete = document.getElementById('cancelDelete');

document.getElementById('btnDeleteSala')?.addEventListener('click', function () {
    deleteModal.classList.add('active');
});

cancelDelete?.addEventListener('click', function () {
    deleteModal.classList.remove('active');
});

deleteModal?.addEventListener('click', function (e) {
    if (e.target === this) this.classList.remove('active');
});

/* ── MODAL INICIAR ── */
const iniciarModal  = document.getElementById('iniciarModal');
const iniciarForm   = document.getElementById('iniciarForm');
const cancelIniciar = document.getElementById('cancelIniciar');

document.querySelectorAll('.btn-confirmar-inicio').forEach(function (btn) {
    btn.addEventListener('click', function () {
        document.getElementById('iniciar-sala-titulo').textContent = '"' + this.dataset.titulo + '"';
        iniciarForm.action = '/professor/salas/' + this.dataset.id + '/iniciar';
        iniciarModal.classList.add('active');
    });
});

cancelIniciar?.addEventListener('click', function () {
    iniciarModal.classList.remove('active');
});

iniciarModal?.addEventListener('click', function (e) {
    if (e.target === this) this.classList.remove('active');
});

// Inicializa os blocos corretamente ao carregar
toggleDateBlocks();
</script>
@endsection