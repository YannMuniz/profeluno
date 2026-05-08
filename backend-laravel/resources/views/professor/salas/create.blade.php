{{-- resources/views/professor/salas/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Nova Sala de Aula')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/sala-professor.css') }}">
<link rel="stylesheet" href="{{ asset('css/steps-conteudo-simulado.css') }}">
<style>
    .conteudo-tab-btn {
        flex: 1;
        padding: 12px 16px;
        background: transparent;
        border: none;
        border-bottom: 2px solid transparent;
        color: var(--text-secondary, #6e6b7b);
        cursor: pointer;
        font-weight: 600;
        font-size: 13px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: color .2s, border-color .2s;
    }
    .conteudo-tab-btn:hover       { color: var(--primary-color, #7367f0); }
    .conteudo-tab-btn.active      { color: var(--primary-color, #7367f0); border-bottom-color: var(--primary-color, #7367f0); }
    .conteudo-tab-panel           { display: none; }
    .conteudo-tab-panel.active    { display: block; }

    /* Simulado tabs — idênticos ao padrão de conteúdo */
    .simulado-tab-btn {
        flex: 1;
        padding: 12px 16px;
        background: transparent;
        border: none;
        border-bottom: 2px solid transparent;
        color: var(--text-secondary, #6e6b7b);
        cursor: pointer;
        font-weight: 600;
        font-size: 13px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: color .2s, border-color .2s;
    }
    .simulado-tab-btn:hover  { color: var(--primary-color, #7367f0); }
    .simulado-tab-btn.active { color: var(--primary-color, #7367f0); border-bottom-color: var(--primary-color, #7367f0); }
    .simulado-tab-panel      { display: none; }
    .simulado-tab-panel.active { display: block; }

    .btn-refresh-lista {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        background: rgba(115,103,240,.08);
        border: 1px solid var(--primary-color, #7367f0);
        border-radius: 6px;
        color: var(--primary-color, #7367f0);
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: .2s;
        margin-top: 10px;
    }
    .btn-refresh-lista:hover { background: var(--primary-color, #7367f0); color: #fff; }
    .btn-refresh-lista i.spin { animation: girando .8s linear infinite; }
    @keyframes girando { to { transform: rotate(360deg); } }
</style>
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

@if($errors->has('api'))
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i>
        {{ $errors->first('api') }}
    </div>
@endif
@if($errors->has('simulado'))
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle"></i>
        {{ $errors->first('simulado') }}
    </div>
@endif
@if(session('warning'))
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle"></i>
        {{ session('warning') }}
    </div>
@endif

<form action="{{ route('professor.salas.store') }}" method="POST" id="formCriarSala">
    @csrf

    {{-- ══════════════════════════════════════
         STEP 1 — Informações da Sala
    ══════════════════════════════════════ --}}
    <div class="form-step active" id="step-1">
        <div class="form-grid-two">

            <div class="form-col-main">
                <div class="form-card">
                    <div class="form-card-header">
                        <i class="fas fa-info-circle"></i>
                        <h3>Dados Principais</h3>
                    </div>
                    <div class="form-card-body">

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

                        <div class="form-row-two">
                            <div class="form-group" id="grupo-data-inicio">
                                <label for="data_hora_inicio" class="form-label">Data e Hora de Início</label>
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
                                <label for="data_hora_fim" class="form-label">Data e Hora de Fim</label>
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

    {{-- ══════════════════════════════════════
         STEP 2 — Conteúdo (opcional)
    ══════════════════════════════════════ --}}
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

                <div class="simulado-tabs">
                    <button type="button" class="conteudo-tab-btn active" data-conteudo-tab="existente">
                        <i class="fas fa-link"></i>
                        Vincular Conteúdo
                    </button>
                    <button type="button" class="conteudo-tab-btn" data-conteudo-tab="nenhum">
                        <i class="fas fa-ban"></i>
                        Sem Conteúdo
                    </button>
                </div>

                <div class="conteudo-tab-panel active" id="conteudoTab-existente">
                    <div id="conteudoGridWrapper">
                        @if(count($conteudos))
                            <p class="section-hint">
                                <i class="fas fa-info-circle"></i>
                                Selecione um conteúdo já cadastrado. Para cadastrar um novo, acesse
                                <a href="{{ route('professor.conteudo.create') }}" target="_blank"><strong>Conteúdos</strong></a>.
                            </p>
                            <div class="conteudo-grid" id="conteudoGrid">
                                @foreach($conteudos as $conteudo)
                                <label
                                    class="conteudo-card"
                                    for="conteudo_opt_{{ $conteudo['idConteudo'] }}"
                                    data-url="{{ $conteudo['url'] ?? '' }}"
                                    data-tipo="{{ $conteudo['tipo'] ?? 'other' }}"
                                >
                                    <input
                                        type="radio"
                                        id="conteudo_opt_{{ $conteudo['idConteudo'] }}"
                                        name="_conteudo_radio"
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
                        @else
                            <div class="empty-state-inline">
                                <i class="fas fa-folder-open"></i>
                                <p>Você ainda não tem conteúdos cadastrados.</p>
                                <a href="{{ route('professor.conteudo.create') }}" class="btn-form-next" target="_blank">
                                    <i class="fas fa-plus"></i> Cadastrar Conteúdo
                                </a>
                            </div>
                        @endif
                    </div>
                    <button type="button" class="btn-refresh-lista" id="btnRefreshConteudo">
                        <i class="fas fa-sync-alt" id="iconRefreshConteudo"></i> Atualizar lista
                    </button>
                </div>

                <div class="conteudo-tab-panel" id="conteudoTab-nenhum">
                    <div class="empty-state-inline muted">
                        <i class="fas fa-ban"></i>
                        <p>Esta sala será criada sem conteúdo vinculado.<br>Você poderá adicionar um depois.</p>
                    </div>
                </div>

                {{-- ÚNICO campo hidden de conteudo_id enviado ao server --}}
                <input type="hidden" id="conteudo_id_final" name="conteudo_id" value="{{ old('conteudo_id', '') }}">

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

    {{-- ══════════════════════════════════════
         STEP 3 — Simulado (opcional)
    ══════════════════════════════════════ --}}
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

                {{-- TABS do simulado — agora com classe própria .simulado-tab-btn --}}
                <div class="simulado-tabs">
                    <button type="button" class="simulado-tab-btn active" data-sim-tab="existente">
                        <i class="fas fa-link"></i>
                        Vincular Existente
                    </button>
                    <button type="button" class="simulado-tab-btn" data-sim-tab="novo">
                        <i class="fas fa-plus"></i>
                        Criar Novo
                    </button>
                    <button type="button" class="simulado-tab-btn" data-sim-tab="nenhum">
                        <i class="fas fa-ban"></i>
                        Sem Simulado
                    </button>
                </div>

                {{-- TAB: Vincular existente --}}
                <div class="simulado-tab-panel active" id="simTab-existente">
                    <div id="simuladoGridWrapper">
                        @if(count($simulados))
                            <div class="conteudo-grid">
                                @foreach($simulados as $simulado)
                                <label class="conteudo-card" for="simulado_opt_{{ $simulado['idSimulado'] }}">
                                    <input
                                        type="radio"
                                        id="simulado_opt_{{ $simulado['idSimulado'] }}"
                                        name="_simulado_radio"
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
                                                <span class="badge-questoes">{{ $simulado['questoes_count'] }} questões</span>
                                            @endif
                                        </div>
                                        <div class="conteudo-check">
                                            <i class="fas fa-check-circle"></i>
                                        </div>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        @else
                            <div class="empty-state-inline" id="simuladoEmptyState">
                                <i class="fas fa-clipboard-list"></i>
                                <p>Nenhum simulado cadastrado ainda.</p>
                            </div>
                        @endif
                    </div>
                    <button type="button" class="btn-refresh-lista" id="btnRefreshSimulado">
                        <i class="fas fa-sync-alt" id="iconRefreshSimulado"></i> Atualizar lista
                    </button>
                </div>

                {{-- TAB: Criar novo --}}
                <div class="simulado-tab-panel" id="simTab-novo">
                    <div class="empty-state-inline">
                        <i class="fas fa-clipboard-list"></i>
                        <p>Crie um simulado e depois volte para vinculá-lo aqui.</p>
                        <a href="{{ route('professor.simulados.create') }}" target="_blank" class="btn-form-next">
                            <i class="fas fa-external-link-alt"></i>
                            Criar Simulado em Nova Aba
                        </a>
                    </div>
                    <button type="button" class="btn-refresh-lista" id="btnRefreshSimuladoNovo">
                        <i class="fas fa-sync-alt" id="iconRefreshSimuladoNovo"></i> Atualizar lista e vincular
                    </button>
                </div>

                {{-- TAB: Sem simulado --}}
                <div class="simulado-tab-panel" id="simTab-nenhum">
                    <div class="empty-state-inline muted">
                        <i class="fas fa-ban"></i>
                        <p>Esta sala será criada sem simulado vinculado.<br>Você poderá adicionar um depois.</p>
                    </div>
                </div>

            </div>
        </div>

        {{-- Resumo --}}
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

    {{-- ÚNICO campo hidden para simulado_id (sem duplicatas) --}}
    <input type="hidden" id="simulado_id_final" name="simulado_id" value="{{ old('simulado_id', '') }}">

</form>

@endsection

@section('scripts')
<script src="{{ asset('js/sala-professor.js') }}"></script>

<script>
/* ══════════════════════════════════════════
   MAPA DE MATÉRIAS
══════════════════════════════════════════ */
const materiaNames = {
    @foreach($materias as $m)
        "{{ $m['idMateria'] }}": "{{ $m['nomeMateria'] }}",
    @endforeach
};

const ROUTE_REFRESH_CONTEUDOS = @json(route('professor.salas.refreshConteudos'));
const ROUTE_REFRESH_SIMULADOS = @json(route('professor.salas.refreshSimulados'));
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

/* ══════════════════════════════════════════
   REFERÊNCIAS
══════════════════════════════════════════ */
const inicioInput  = document.getElementById('data_hora_inicio');
const statusSelect = document.getElementById('status');
const grupoInicio  = document.getElementById('grupo-data-inicio');
const conteudoIdFinal = document.getElementById('conteudo_id_final');
const simuladoIdFinal = document.getElementById('simulado_id_final');

/* ══════════════════════════════════════════
   DATA MÍNIMA
══════════════════════════════════════════ */
function nowIso() {
    const d = new Date();
    d.setSeconds(0, 0);
    return d.toISOString().slice(0, 16);
}

if (inicioInput) {
    inicioInput.min = nowIso();
    setInterval(() => { inicioInput.min = nowIso(); }, 60000);

    inicioInput.addEventListener('change', function () {
        if (!this.value) return;
        const selected = new Date(this.value);
        if (selected <= new Date()) {
            statusSelect.value = 'active';
            this.value = '';
        } else {
            statusSelect.value = 'pending';
        }
        toggleDateFields();
        updatePreview();
    });
}

function toggleDateFields() {
    if (!statusSelect || !grupoInicio) return;
    const isActive = statusSelect.value === 'active';
    grupoInicio.style.display = isActive ? 'none' : '';
    if (isActive && inicioInput) inicioInput.value = '';
}

statusSelect?.addEventListener('change', () => { toggleDateFields(); updatePreview(); });
toggleDateFields();

/* ══════════════════════════════════════════
   PRÉVIA DO CARD
══════════════════════════════════════════ */
function updatePreview() {
    const titulo = document.getElementById('titulo')?.value || '';
    const matId  = document.getElementById('materia_id')?.value || '';
    const alunos = document.getElementById('max_alunos')?.value || '30';
    const inicio = inicioInput?.value || '';
    const status = statusSelect?.value || 'pending';

    document.getElementById('previewTitulo').textContent  = titulo || 'Título da Sala';
    document.getElementById('previewMateria').textContent = materiaNames[matId] || 'Matéria';
    document.getElementById('previewAlunos').textContent  = alunos;
    document.getElementById('previewData').textContent    =
        status === 'active' ? 'Agora' : (inicio ? new Date(inicio).toLocaleDateString('pt-BR') : 'Sem data');

    const ribbon = document.getElementById('previewRibbon');
    if (ribbon) {
        ribbon.className = `mini-ribbon ${status}`;
        ribbon.innerHTML = status === 'active'
            ? '<i class="fas fa-circle"></i> Ao Vivo'
            : '<i class="fas fa-clock"></i> Agendada';
    }
}

['titulo','materia_id','max_alunos','data_hora_inicio','status'].forEach(id => {
    document.getElementById(id)?.addEventListener('input',  updatePreview);
    document.getElementById(id)?.addEventListener('change', updatePreview);
});

document.getElementById('titulo')?.addEventListener('input', function () {
    document.getElementById('tituloCount').textContent = this.value.length;
});

/* ══════════════════════════════════════════
   NAVEGAÇÃO ENTRE STEPS
══════════════════════════════════════════ */
function goToStep(n) {
    document.querySelectorAll('.form-step').forEach(s => s.classList.remove('active'));
    document.getElementById(`step-${n}`)?.classList.add('active');
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
    if (!titulo?.value.trim() || !materiaId?.value || !maxAlunos?.value) {
        titulo?.reportValidity();
        materiaId?.reportValidity();
        maxAlunos?.reportValidity();
        return;
    }
    goToStep(2);
});

document.getElementById('backToStep1')?.addEventListener('click', () => goToStep(1));
document.getElementById('nextToStep3')?.addEventListener('click', () => { updateResumo(); goToStep(3); });
document.getElementById('backToStep2')?.addEventListener('click', () => goToStep(2));

/* ══════════════════════════════════════════
   TABS DO CONTEÚDO
══════════════════════════════════════════ */
document.querySelectorAll('.conteudo-tab-btn').forEach(function (tab) {
    tab.addEventListener('click', function () {
        document.querySelectorAll('.conteudo-tab-btn').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.conteudo-tab-panel').forEach(p => p.classList.remove('active'));
        this.classList.add('active');
        document.getElementById('conteudoTab-' + this.dataset.conteudoTab)?.classList.add('active');
        if (this.dataset.conteudoTab === 'nenhum') {
            document.querySelectorAll('.conteudo-radio').forEach(r => { r.checked = false; });
            if (conteudoIdFinal) conteudoIdFinal.value = '';
        }
    });
});

document.querySelectorAll('.conteudo-radio').forEach(function (radio) {
    radio.addEventListener('change', function () {
        if (conteudoIdFinal) conteudoIdFinal.value = this.value;
    });
});

/* ══════════════════════════════════════════
   TABS DO SIMULADO
══════════════════════════════════════════ */
document.querySelectorAll('.simulado-tab-btn').forEach(function (tab) {
    tab.addEventListener('click', function () {
        document.querySelectorAll('.simulado-tab-btn').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.simulado-tab-panel').forEach(p => p.classList.remove('active'));
        this.classList.add('active');
        const panel = document.getElementById('simTab-' + this.dataset.simTab);
        if (panel) panel.classList.add('active');
        // Limpa seleção quando sai da aba "existente"
        if (this.dataset.simTab !== 'existente') {
            document.querySelectorAll('.simulado-radio').forEach(r => { r.checked = false; });
            if (simuladoIdFinal) simuladoIdFinal.value = '';
        }
    });
});

document.querySelectorAll('.simulado-radio').forEach(function (radio) {
    radio.addEventListener('change', function () {
        if (simuladoIdFinal) simuladoIdFinal.value = this.value;
    });
});

/* ══════════════════════════════════════════
   REFRESH CONTEÚDO
══════════════════════════════════════════ */
async function refreshConteudos() {
    const icon = document.getElementById('iconRefreshConteudo');
    if (icon) icon.classList.add('spin');
    try {
        const r = await fetch(ROUTE_REFRESH_CONTEUDOS, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN }
        });
        if (!r.ok) return;
        const list = await r.json();
        const wrapper = document.getElementById('conteudoGridWrapper');
        if (!wrapper) return;
        if (!list.length) {
            wrapper.innerHTML = `<div class="empty-state-inline"><i class="fas fa-folder-open"></i><p>Nenhum conteúdo cadastrado.</p></div>`;
            return;
        }
        const grid = list.map(c => {
            const tipo = c.tipo ?? 'other';
            const icones = { pdf:'fa-file-pdf', pptx:'fa-file-powerpoint', docx:'fa-file-word', mp4:'fa-file-video', link:'fa-link' };
            const ic = icones[tipo] ?? 'fa-file';
            return `
            <label class="conteudo-card" for="conteudo_opt_${c.idConteudo}">
                <input type="radio" id="conteudo_opt_${c.idConteudo}" name="_conteudo_radio"
                    value="${c.idConteudo}" class="conteudo-radio">
                <div class="conteudo-card-inner">
                    <div class="conteudo-tipo-badge ${tipo}"><i class="fas ${ic}"></i></div>
                    <div class="conteudo-info">
                        <strong>${c.titulo ?? ''}</strong>
                        ${c.descricao ? `<span>${c.descricao.substring(0,60)}</span>` : ''}
                    </div>
                    <div class="conteudo-check"><i class="fas fa-check-circle"></i></div>
                </div>
            </label>`;
        }).join('');
        wrapper.innerHTML = `<p class="section-hint"><i class="fas fa-info-circle"></i> Selecione um conteúdo já cadastrado.</p><div class="conteudo-grid">${grid}</div>`;
        // Re-bind radio listeners
        wrapper.querySelectorAll('.conteudo-radio').forEach(r => {
            r.addEventListener('change', function () {
                if (conteudoIdFinal) conteudoIdFinal.value = this.value;
            });
        });
    } catch(e) { console.error('Erro ao atualizar conteúdos:', e); }
    finally { if (icon) icon.classList.remove('spin'); }
}

document.getElementById('btnRefreshConteudo')?.addEventListener('click', refreshConteudos);

/* ══════════════════════════════════════════
   REFRESH SIMULADO
══════════════════════════════════════════ */
async function refreshSimulados(switchToExistente = false) {
    const icon1 = document.getElementById('iconRefreshSimulado');
    const icon2 = document.getElementById('iconRefreshSimuladoNovo');
    [icon1, icon2].forEach(i => i?.classList.add('spin'));
    try {
        const r = await fetch(ROUTE_REFRESH_SIMULADOS, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN }
        });
        if (!r.ok) return;
        const list = await r.json();
        const wrapper = document.getElementById('simuladoGridWrapper');
        if (!wrapper) return;
        if (!list.length) {
            wrapper.innerHTML = `<div class="empty-state-inline"><i class="fas fa-clipboard-list"></i><p>Nenhum simulado cadastrado ainda.</p></div>`;
        } else {
            const cards = list.map(s => `
            <label class="conteudo-card" for="simulado_opt_${s.idSimulado}">
                <input type="radio" id="simulado_opt_${s.idSimulado}" name="_simulado_radio"
                    value="${s.idSimulado}" class="simulado-radio">
                <div class="conteudo-card-inner">
                    <div class="conteudo-tipo-badge simulado"><i class="fas fa-clipboard-list"></i></div>
                    <div class="conteudo-info">
                        <strong>${s.titulo ?? ''}</strong>
                        ${s.descricao ? `<span>${s.descricao.substring(0,60)}</span>` : ''}
                        ${s.questoes_count ? `<span class="badge-questoes">${s.questoes_count} questões</span>` : ''}
                    </div>
                    <div class="conteudo-check"><i class="fas fa-check-circle"></i></div>
                </div>
            </label>`).join('');
            wrapper.innerHTML = `<div class="conteudo-grid">${cards}</div>`;
            wrapper.querySelectorAll('.simulado-radio').forEach(r => {
                r.addEventListener('change', function () {
                    if (simuladoIdFinal) simuladoIdFinal.value = this.value;
                });
            });
        }
        // Se veio do tab "novo", muda para "existente" automaticamente
        if (switchToExistente) {
            document.querySelectorAll('.simulado-tab-btn').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.simulado-tab-panel').forEach(p => p.classList.remove('active'));
            document.querySelector('[data-sim-tab="existente"]')?.classList.add('active');
            document.getElementById('simTab-existente')?.classList.add('active');
        }
    } catch(e) { console.error('Erro ao atualizar simulados:', e); }
    finally { [icon1, icon2].forEach(i => i?.classList.remove('spin')); }
}

document.getElementById('btnRefreshSimulado')?.addEventListener('click', () => refreshSimulados(false));
document.getElementById('btnRefreshSimuladoNovo')?.addEventListener('click', () => refreshSimulados(true));

/* ══════════════════════════════════════════
   RESUMO FINAL
══════════════════════════════════════════ */
function updateResumo() {
    const matId  = document.getElementById('materia_id')?.value || '';
    const inicio = inicioInput?.value || '';
    const status = statusSelect?.value || 'pending';

    const activeConteudoTab = document.querySelector('.conteudo-tab-btn.active')?.dataset.conteudoTab;
    let conteudoLabel = 'Sem conteúdo';
    if (activeConteudoTab === 'existente' && conteudoIdFinal?.value) {
        const checked = document.querySelector('.conteudo-radio:checked');
        conteudoLabel = checked?.closest('.conteudo-card')?.querySelector('strong')?.textContent?.trim() || 'Conteúdo selecionado';
    }

    const activeSimTab = document.querySelector('.simulado-tab-btn.active')?.dataset.simTab;
    let simuladoLabel = 'Sem simulado';
    if (activeSimTab === 'existente' && simuladoIdFinal?.value) {
        const checked = document.querySelector('.simulado-radio:checked');
        simuladoLabel = checked?.closest('.conteudo-card')?.querySelector('strong')?.textContent?.trim() || 'Simulado selecionado';
    } else if (activeSimTab === 'novo') {
        simuladoLabel = 'Criar novo (aba externa)';
    }

    document.getElementById('resumoTitulo').textContent   = document.getElementById('titulo')?.value || '—';
    document.getElementById('resumoMateria').textContent  = materiaNames[matId] || '—';
    document.getElementById('resumoAlunos').textContent   = document.getElementById('max_alunos')?.value || '—';
    document.getElementById('resumoInicio').textContent   = status === 'active'
        ? 'Agora (ao vivo)'
        : (inicio ? new Date(inicio).toLocaleString('pt-BR') : 'Sem data definida');
    document.getElementById('resumoConteudo').textContent = conteudoLabel;
    document.getElementById('resumoSimulado').textContent = simuladoLabel;
}
</script>
@endsection