{{-- resources/views/professor/salas/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Nova Sala de Aula')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/sala-professor.css') }}">
<link rel="stylesheet" href="{{ asset('css/steps-conteudo-simulado.css') }}">
@endsection

@section('content')

<div class="page-header">
    <div class="page-header-right">
        <div class="steps-indicator">
            <div class="step active" data-step="1">
                <span class="step-num">1</span>
                <span class="step-label">Informações</span>
            </div>
            <div class="step-line"></div>
            <div class="step" data-step="2">
                <span class="step-num">2</span>
                <span class="step-label">Conteúdo</span>
            </div>
            <div class="step-line"></div>
            <div class="step" data-step="3">
                <span class="step-num">3</span>
                <span class="step-label">Simulado</span>
            </div>
        </div>
    </div>
</div>

{{-- Erros globais da API --}}
@if($errors->has('api'))
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i>
        {{ $errors->first('api') }}
    </div>
@endif

@if(session('warning'))
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle"></i>
        {{ session('warning') }}
    </div>
@endif

<form
    action="{{ route('professor.salas.store') }}"
    method="POST"
    id="formCriarSala"
>
    @csrf

    {{-- ============================================================
         STEP 1 — Informações da Sala
         ============================================================ --}}
    <div class="form-step active" id="step-1">
        <div class="form-grid-two">

            {{-- Coluna principal --}}
            <div class="form-col-main">
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
                                value="{{ old('titulo') }}"
                                maxlength="255"
                                required
                            >
                            <span class="char-count">
                                <span id="tituloCount">{{ strlen(old('titulo', '')) }}</span>/255
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
                                placeholder="Descreva o conteúdo que será abordado nesta sala..."
                            >{{ old('descricao') }}</textarea>
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
                                            {{ old('materia_id') == $materia['idMateria'] ? 'selected' : '' }}
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
                                    value="{{ old('max_alunos', 30) }}"
                                    min="1"
                                    max="500"
                                    required
                                >
                                @error('max_alunos')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Data/hora início + fim --}}
                        <div class="form-row-two">
                            <div class="form-group">
                                <label for="data_hora_inicio" class="form-label">
                                    Data e Hora de Início
                                </label>
                                <input
                                    type="datetime-local"
                                    id="data_hora_inicio"
                                    name="data_hora_inicio"
                                    class="form-control @error('data_hora_inicio') is-invalid @enderror"
                                    value="{{ old('data_hora_inicio') }}"
                                >
                                @error('data_hora_inicio')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="data_hora_fim" class="form-label">
                                    Data e Hora de Fim
                                </label>
                                <input
                                    type="datetime-local"
                                    id="data_hora_fim"
                                    name="data_hora_fim"
                                    class="form-control @error('data_hora_fim') is-invalid @enderror"
                                    value="{{ old('data_hora_fim') }}"
                                >
                                @error('data_hora_fim')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Status --}}
                        <div class="form-row-two">
                            <div class="form-group">
                                <label for="status" class="form-label">Status Inicial</label>
                                <select
                                    id="status"
                                    name="status"
                                    class="form-control filter-select @error('status') is-invalid @enderror"
                                >
                                    <option value="pending" {{ old('status', 'pending') === 'pending' ? 'selected' : '' }}>
                                        Agendada (Pendente)
                                    </option>
                                    <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>
                                        Iniciar Agora (Ativa)
                                    </option>
                                </select>
                                @error('status')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- Coluna lateral — Prévia --}}
            <div class="form-col-side">
                <div class="preview-card">
                    <div class="preview-card-header">
                        <i class="fas fa-eye"></i>
                        Prévia do Card
                    </div>
                    <div class="preview-card-body">
                        <div class="mini-class-card">
                            <div class="mini-ribbon pending" id="previewRibbon">
                                <i class="fas fa-clock"></i> Agendada
                            </div>
                            <div class="mini-card-icon">
                                <i class="fas fa-chalkboard-teacher"></i>
                            </div>
                            <h4 id="previewTitulo">Título da Sala</h4>
                            <span id="previewMateria" class="mini-subject">Matéria</span>
                            <div class="mini-meta">
                                <span>
                                    <i class="fas fa-users"></i>
                                    <span id="previewAlunos">30</span> alunos
                                </span>
                                <span>
                                    <i class="fas fa-calendar"></i>
                                    <span id="previewData">Sem data</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tips-card">
                    <div class="tips-card-header">
                        <i class="fas fa-lightbulb"></i>
                        Dicas
                    </div>
                    <ul class="tips-list">
                        <li>Use um título claro e descritivo para facilitar a busca pelos alunos.</li>
                        <li>Defina data e hora de início para que os alunos se programem.</li>
                        <li>Deixe o link externo vazio para usar a sala Jitsi automática.</li>
                        <li>Você vinculará o conteúdo e simulado nos próximos passos.</li>
                    </ul>
                </div>
            </div>

        </div>

        <div class="form-actions">
            <a href="{{ route('professor.salas.index') }}" class="btn-form-cancel">
                <i class="fas fa-times"></i> Cancelar
            </a>
            <button type="button" class="btn-form-next" id="nextToStep2">
                Próximo: Conteúdo
                <i class="fas fa-arrow-right"></i>
            </button>
        </div>
    </div>

    {{-- ============================================================
         STEP 2 — Conteúdo (opcional)
         ============================================================ --}}
    <div class="form-step" id="step-2">
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
                        Selecione um conteúdo já cadastrado para vincular a esta sala.
                        Para cadastrar um novo, acesse a área de <strong>Conteúdos</strong>.
                    </p>

                    {{-- Grid de cards de conteúdo --}}
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
                                {{ old('conteudo_id') == $conteudo['idConteudo'] ? 'checked' : '' }}
                                class="conteudo-radio"
                            >
                            <div class="conteudo-card-inner">
                                <div class="conteudo-tipo-badge {{ $conteudo['tipo'] ?? 'other' }}">
                                    @switch($conteudo['tipo'] ?? '')
                                        @case('pdf')  <i class="fas fa-file-pdf"></i> PDF @break
                                        @case('pptx') <i class="fas fa-file-powerpoint"></i> PPTX @break
                                        @case('docx') <i class="fas fa-file-word"></i> DOCX @break
                                        @case('mp4')  <i class="fas fa-file-video"></i> MP4 @break
                                        @case('link') <i class="fas fa-link"></i> Link @break
                                        @default       <i class="fas fa-file"></i> Arquivo
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
                    </div>

                    {{-- Preview do conteúdo selecionado --}}
                    <div class="conteudo-preview-wrapper" id="conteudoPreviewWrapper">
                        <div class="conteudo-preview-header">
                            <span>
                                <i class="fas fa-eye"></i>
                                <span id="conteudoPreviewTitle">Prévia</span>
                            </span>
                            <a href="#" target="_blank" id="btnAbrirNovaAba" class="btn-abrir-nova-aba">
                                <i class="fas fa-external-link-alt"></i> Abrir em nova aba
                            </a>
                        </div>
                        {{-- Iframe para links e vídeos --}}
                        <iframe
                            id="conteudoIframe"
                            class="conteudo-iframe"
                            src=""
                            allowfullscreen
                            style="display:none;"
                        ></iframe>
                        {{-- Fallback para PDFs, PPTX, DOCX --}}
                        <div id="conteudoFallback" class="conteudo-preview-fallback" style="display:none;"></div>
                    </div>

                    {{-- Opção de nenhum conteúdo --}}
                    <label class="conteudo-card conteudo-none" for="conteudo_none">
                        <input
                            type="radio"
                            id="conteudo_none"
                            name="conteudo_id"
                            value=""
                            {{ old('conteudo_id', '') === '' ? 'checked' : '' }}
                            class="conteudo-radio"
                        >
                        <div class="conteudo-card-inner">
                            <div class="conteudo-tipo-badge none">
                                <i class="fas fa-ban"></i>
                            </div>
                            <div class="conteudo-info">
                                <strong>Sem conteúdo</strong>
                                <span>Criar sala sem vincular conteúdo agora</span>
                            </div>
                            <div class="conteudo-check">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </label>

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

        <div class="form-actions">
            <button type="button" class="btn-form-cancel" id="backToStep1">
                <i class="fas fa-arrow-left"></i> Voltar
            </button>
            <button type="button" class="btn-form-next" id="nextToStep3">
                Próximo: Simulado
                <i class="fas fa-arrow-right"></i>
            </button>
        </div>
    </div>

    {{-- ============================================================
         STEP 3 — Simulado (opcional)
         ============================================================ --}}
    <div class="form-step" id="step-3">
        <div class="form-card">
            <div class="form-card-header">
                <i class="fas fa-question-circle"></i>
                <h3>
                    Simulado
                    <span class="optional-tag">Opcional</span>
                </h3>
            </div>
            <div class="form-card-body">

                {{-- Tabs: vincular existente ou criar novo --}}
                <div class="simulado-tabs">
                    <button type="button" class="simulado-tab active" data-simulado-tab="existente">
                        <i class="fas fa-link"></i>
                        Vincular Simulado Existente
                    </button>
                    <button type="button" class="simulado-tab" data-simulado-tab="novo">
                        <i class="fas fa-plus"></i>
                        Criar Novo Simulado
                    </button>
                    <button type="button" class="simulado-tab" data-simulado-tab="nenhum">
                        <i class="fas fa-ban"></i>
                        Sem Simulado
                    </button>
                </div>

                {{-- TAB: Vincular existente --}}
                <div class="simulado-tab-content active" id="simuladoTab-existente">
                    @if(count($simulados))
                        <div class="conteudo-grid">
                            @foreach($simulados as $simulado)
                            <label class="conteudo-card" for="simulado_{{ $simulado['idSimulado'] }}">
                                <input
                                    type="radio"
                                    id="simulado_{{ $simulado['idSimulado'] }}"
                                    name="simulado_id"
                                    value="{{ $simulado['idSimulado'] }}"
                                    {{ old('simulado_id') == $simulado['idSimulado'] ? 'checked' : '' }}
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
                        </div>

                        {{-- Preview das questões do simulado selecionado --}}
                        <div class="simulado-questoes-preview" id="simuladoQuestoesPreview">
                            <div class="simulado-preview-header">
                                <span>
                                    <i class="fas fa-list-ol"></i>
                                    Questões do simulado
                                </span>
                                <span class="simulado-preview-count" id="simuladoQuestoesCount">0 questões</span>
                            </div>
                            <div class="simulado-questoes-list" id="simuladoQuestoesList"></div>
                        </div>

                    @else
                        <div class="empty-state-inline">
                            <i class="fas fa-clipboard-list"></i>
                            <p>Nenhum simulado cadastrado ainda.</p>
                        </div>
                    @endif
                </div>

                {{-- TAB: Criar novo --}}
                <div class="simulado-tab-content" id="simuladoTab-novo">
                    <input type="hidden" name="simulado_id" value="" id="simuladoIdHidden">

                    <div class="form-group">
                        <label class="form-label">
                            Título do Simulado <span class="required">*</span>
                        </label>
                        <input
                            type="text"
                            name="simulado_titulo"
                            class="form-control"
                            placeholder="Ex: Simulado — Equações do 2º Grau"
                            value="{{ old('simulado_titulo') }}"
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label">Descrição do Simulado</label>
                        <textarea
                            name="simulado_descricao"
                            class="form-control"
                            rows="2"
                            placeholder="Descreva o objetivo do simulado..."
                        >{{ old('simulado_descricao') }}</textarea>
                    </div>

                    <div id="questoesContainer">
                        {{-- Questões adicionadas pelo JS --}}
                    </div>

                    <button type="button" class="btn-add-questao" id="addQuestao">
                        <i class="fas fa-plus"></i>
                        Adicionar Questão
                    </button>
                </div>

                {{-- TAB: Sem simulado --}}
                <div class="simulado-tab-content" id="simuladoTab-nenhum">
                    <div class="empty-state-inline muted">
                        <i class="fas fa-ban"></i>
                        <p>Esta sala será criada sem simulado vinculado.<br>Você poderá adicionar um depois.</p>
                    </div>
                    <input type="hidden" name="simulado_id" value="">
                </div>

            </div>
        </div>

        {{-- Resumo antes de submeter --}}
        <div class="form-card resumo-card" id="resumoFinal">
            <div class="form-card-header">
                <i class="fas fa-list-check"></i>
                <h3>Resumo da Sala</h3>
            </div>
            <div class="form-card-body resumo-body">
                <div class="resumo-item">
                    <span class="resumo-label"><i class="fas fa-tag"></i> Título</span>
                    <span class="resumo-value" id="resumoTitulo">—</span>
                </div>
                <div class="resumo-item">
                    <span class="resumo-label"><i class="fas fa-book"></i> Matéria</span>
                    <span class="resumo-value" id="resumoMateria">—</span>
                </div>
                <div class="resumo-item">
                    <span class="resumo-label"><i class="fas fa-users"></i> Máx. Alunos</span>
                    <span class="resumo-value" id="resumoAlunos">—</span>
                </div>
                <div class="resumo-item">
                    <span class="resumo-label"><i class="fas fa-calendar"></i> Início</span>
                    <span class="resumo-value" id="resumoInicio">—</span>
                </div>
                <div class="resumo-item">
                    <span class="resumo-label"><i class="fas fa-folder"></i> Conteúdo</span>
                    <span class="resumo-value" id="resumoConteudo">Sem conteúdo</span>
                </div>
                <div class="resumo-item">
                    <span class="resumo-label"><i class="fas fa-clipboard-list"></i> Simulado</span>
                    <span class="resumo-value" id="resumoSimulado">Sem simulado</span>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="button" class="btn-form-cancel" id="backToStep2">
                <i class="fas fa-arrow-left"></i> Voltar
            </button>
            <button type="submit" class="btn-form-submit">
                <i class="fas fa-check"></i>
                Criar Sala
            </button>
        </div>
    </div>

</form>

{{-- Template de questão (clonado pelo JS) --}}
<template id="questaoTemplate">
    <div class="questao-block" data-index="__INDEX__">
        <div class="questao-header">
            <span class="questao-num">Questão <strong>__NUM__</strong></span>
            <button type="button" class="btn-remove-questao" title="Remover questão">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="form-group" style="padding: 0 20px;">
            <label class="form-label">
                Enunciado <span class="required">*</span>
            </label>
            <textarea
                name="questoes[__INDEX__][enunciado]"
                class="form-control"
                rows="2"
                placeholder="Digite o enunciado da questão..."
            ></textarea>
        </div>
        <div class="alternativas-grid" style="padding: 0 20px;">
            <div class="alternativa-item">
                <span class="alt-label">A</span>
                <input type="text" name="questoes[__INDEX__][questao_a]" class="form-control" placeholder="Alternativa A">
            </div>
            <div class="alternativa-item">
                <span class="alt-label">B</span>
                <input type="text" name="questoes[__INDEX__][questao_b]" class="form-control" placeholder="Alternativa B">
            </div>
            <div class="alternativa-item">
                <span class="alt-label">C</span>
                <input type="text" name="questoes[__INDEX__][questao_c]" class="form-control" placeholder="Alternativa C">
            </div>
            <div class="alternativa-item">
                <span class="alt-label">D</span>
                <input type="text" name="questoes[__INDEX__][questao_d]" class="form-control" placeholder="Alternativa D">
            </div>
            <div class="alternativa-item">
                <span class="alt-label">E</span>
                <input type="text" name="questoes[__INDEX__][questao_e]" class="form-control" placeholder="Alternativa E (opcional)">
            </div>
        </div>
        <div class="form-group" style="padding: 0 20px 20px;">
            <label class="form-label">
                Alternativa Correta <span class="required">*</span>
            </label>
            <select name="questoes[__INDEX__][questao_correta]" class="form-control filter-select">
                <option value="1">A</option>
                <option value="2">B</option>
                <option value="3">C</option>
                <option value="4">D</option>
                <option value="5">E</option>
            </select>
        </div>
    </div>
</template>

@endsection

@section('scripts')
<script src="{{ asset('js/sala-professor.js') }}"></script>

{{-- Dados dos simulados expostos para o JS (questões para preview) --}}
<script>
window.simuladosData = {
    @foreach($simulados as $simulado)
    "{{ $simulado['idSimulado'] }}": {
        titulo: @json($simulado['titulo']),
        questoes: [
            @if(!empty($simulado['questoes']))
                @foreach($simulado['questoes'] as $q)
                {
                    enunciado:       @json($q['enunciado'] ?? ''),
                    questao_a:       @json($q['questao_a'] ?? ''),
                    questao_b:       @json($q['questao_b'] ?? ''),
                    questao_c:       @json($q['questao_c'] ?? ''),
                    questao_d:       @json($q['questao_d'] ?? ''),
                    questao_e:       @json($q['questao_e'] ?? ''),
                    questao_correta: @json($q['questao_correta'] ?? ''),
                },
                @endforeach
            @endif
        ]
    },
    @endforeach
};
</script>

<script src="{{ asset('js/steps-conteudo-simulado.js') }}"></script>

<script>
// ── Prévia do card ─────────────────────────────────────────────────────────
const materiaSelect = document.getElementById('materia_id');
const materiaNames  = {
    @foreach($materias as $m)
        "{{ $m['idMateria'] }}": "{{ $m['nomeMateria'] }}",
    @endforeach
};

function updatePreview() {
    const titulo  = document.getElementById('titulo').value || 'Título da Sala';
    const matId   = materiaSelect.value;
    const alunos  = document.getElementById('max_alunos').value || '30';
    const inicio  = document.getElementById('data_hora_inicio').value;
    const status  = document.getElementById('status').value;

    document.getElementById('previewTitulo').textContent  = titulo;
    document.getElementById('previewMateria').textContent = materiaNames[matId] || 'Matéria';
    document.getElementById('previewAlunos').textContent  = alunos;
    document.getElementById('previewData').textContent    = inicio
        ? new Date(inicio).toLocaleDateString('pt-BR')
        : 'Sem data';

    const ribbon = document.getElementById('previewRibbon');
    ribbon.className = `mini-ribbon ${status}`;
    ribbon.innerHTML = status === 'active'
        ? '<i class="fas fa-circle"></i> Ao Vivo'
        : '<i class="fas fa-clock"></i> Agendada';
}

['titulo','materia_id','max_alunos','data_hora_inicio','status'].forEach(id => {
    document.getElementById(id)?.addEventListener('input', updatePreview);
    document.getElementById(id)?.addEventListener('change', updatePreview);
});

document.getElementById('titulo').addEventListener('input', function () {
    document.getElementById('tituloCount').textContent = this.value.length;
});

// ── Navegação entre steps ──────────────────────────────────────────────────
function goToStep(n) {
    document.querySelectorAll('.form-step').forEach(s => s.classList.remove('active'));
    document.getElementById(`step-${n}`).classList.add('active');

    document.querySelectorAll('.step').forEach(s => {
        const num = parseInt(s.dataset.step);
        s.classList.toggle('active',    num === n);
        s.classList.toggle('completed', num < n);
    });

    window.scrollTo({ top: 0, behavior: 'smooth' });
}

document.getElementById('nextToStep2')?.addEventListener('click', () => {
    const titulo    = document.getElementById('titulo');
    const materiaId = document.getElementById('materia_id');
    const maxAlunos = document.getElementById('max_alunos');

    if (!titulo.value.trim() || !materiaId.value || !maxAlunos.value) {
        titulo.reportValidity();
        materiaId.reportValidity();
        maxAlunos.reportValidity();
        return;
    }
    goToStep(2);
});

document.getElementById('backToStep1')?.addEventListener('click', () => goToStep(1));
document.getElementById('nextToStep3')?.addEventListener('click', () => {
    updateResumo();
    goToStep(3);
});
document.getElementById('backToStep2')?.addEventListener('click', () => goToStep(2));

// ── Tabs do simulado ───────────────────────────────────────────────────────
document.querySelectorAll('.simulado-tab').forEach(tab => {
    tab.addEventListener('click', function () {
        document.querySelectorAll('.simulado-tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.simulado-tab-content').forEach(c => c.classList.remove('active'));
        this.classList.add('active');
        document.getElementById(`simuladoTab-${this.dataset.simuladoTab}`).classList.add('active');

        if (this.dataset.simuladoTab !== 'novo') {
            document.getElementById('questoesContainer').innerHTML = '';
            questaoIndex = 0;
        }
    });
});

// ── Questões inline ────────────────────────────────────────────────────────
let questaoIndex = 0;
const template   = document.getElementById('questaoTemplate');

function addQuestao() {
    questaoIndex++;
    const html = template.innerHTML
        .replaceAll('__INDEX__', questaoIndex)
        .replaceAll('__NUM__', questaoIndex);
    const wrapper = document.createElement('div');
    wrapper.innerHTML = html;
    const block = wrapper.firstElementChild;

    block.querySelector('.btn-remove-questao').addEventListener('click', () => {
        block.remove();
        renumberQuestoes();
    });

    document.getElementById('questoesContainer').appendChild(block);
}

function renumberQuestoes() {
    document.querySelectorAll('.questao-block').forEach((b, i) => {
        b.querySelector('.questao-num strong').textContent = i + 1;
    });
}

document.getElementById('addQuestao')?.addEventListener('click', addQuestao);

// ── Resumo final ───────────────────────────────────────────────────────────
function updateResumo() {
    const matId      = materiaSelect.value;
    const inicio     = document.getElementById('data_hora_inicio').value;
    const conteudoEl = document.querySelector('.conteudo-radio:checked');
    const simuladoEl = document.querySelector('.simulado-radio:checked');

    document.getElementById('resumoTitulo').textContent  =
        document.getElementById('titulo').value || '—';
    document.getElementById('resumoMateria').textContent =
        materiaNames[matId] || '—';
    document.getElementById('resumoAlunos').textContent  =
        document.getElementById('max_alunos').value || '—';
    document.getElementById('resumoInicio').textContent  =
        inicio ? new Date(inicio).toLocaleString('pt-BR') : 'Sem data';

    const conteudoLabel = conteudoEl
        ? conteudoEl.closest('.conteudo-card').querySelector('strong')?.textContent
        : null;
    document.getElementById('resumoConteudo').textContent =
        (conteudoLabel && conteudoLabel !== 'Sem conteúdo') ? conteudoLabel : 'Sem conteúdo';

    const activeSimTab = document.querySelector('.simulado-tab.active')?.dataset.simuladoTab;
    if (activeSimTab === 'existente' && simuladoEl) {
        const simLabel = simuladoEl.closest('.conteudo-card').querySelector('strong')?.textContent;
        document.getElementById('resumoSimulado').textContent = simLabel || 'Sem simulado';
    } else if (activeSimTab === 'novo') {
        const novoTitulo = document.querySelector('[name="simulado_titulo"]')?.value;
        document.getElementById('resumoSimulado').textContent =
            novoTitulo ? `Novo: ${novoTitulo}` : 'Novo simulado (sem título)';
    } else {
        document.getElementById('resumoSimulado').textContent = 'Sem simulado';
    }
}
</script>
@endsection