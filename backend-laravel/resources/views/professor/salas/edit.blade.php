{{-- resources/views/professor/salas/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Editar Sala — ' . ($sala->titulo ?? ''))

@section('styles')
<link rel="stylesheet" href="{{ asset('css/sala-professor.css') }}">
<link rel="stylesheet" href="{{ asset('css/steps-conteudo-simulado.css') }}">
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
    // Normaliza os valores salvos para preencher os campos
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

        {{-- Coluna principal --}}
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
                                value="{{ $salaDataInicio }}"
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
                                value="{{ $salaDataFim }}"
                            >
                            @error('data_hora_fim')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="form-row-two">
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

                        {{-- URL da sala (somente leitura — gerada automaticamente) --}}
                        @if($salaUrl)
                        <div class="form-group">
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
                                            @default         <i class="fas fa-file"></i> Arquivo
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

                            {{-- Opção sem conteúdo --}}
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

                        {{-- Preview do conteúdo selecionado --}}
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

                            {{-- Opção sem simulado --}}
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

        {{-- Coluna lateral  --}}
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
                <div class="quick-actions" style="display: flex; flex-direction: column; gap: 10px;">
                    @if(($sala->status ?? '') === 'pending')
                        <a href="{{ route('professor.salas.iniciar', $sala->id) }}" class="btn-start-now">
                            <i class="fas fa-play"></i> Iniciar Agora
                        </a>
                    @elseif(($sala->status ?? '') === 'active')
                        <a href="{{ $salaUrl }}" target="_blank" class="btn-enter-live">
                            <i class="fas fa-video"></i> Entrar na Aula
                        </a>
                    @endif
                    <button
                        type="button"
                        class="btn-danger-outline btn-delete-sala"
                        data-id="{{ $sala->id }}"
                    >
                        <i class="fas fa-trash"></i> Deletar Sala
                    </button>
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
        <p>Tem certeza que deseja deletar esta sala? Esta ação não pode ser desfeita.</p>
        <div class="modal-actions">
            <button class="modal-btn cancel" id="cancelDelete">Cancelar</button>
            <form method="POST" action="{{ route('professor.salas.destroy', $sala->id) }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="modal-btn confirm danger">Deletar</button>
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
// Mapa de nomes de matéria para a prévia 
const materiaNames = {
    @foreach($materias as $m)
        "{{ $m['idMateria'] }}": "{{ $m['nomeMateria'] }}",
    @endforeach
};

// Atualiza prévia em tempo real 
function updatePreview() {
    const titulo  = document.getElementById('titulo')?.value         || '';
    const matId   = document.getElementById('materia_id')?.value     || '';
    const alunos  = document.getElementById('max_alunos')?.value     || '0';
    const inicio  = document.getElementById('data_hora_inicio')?.value;
    const status  = document.getElementById('status')?.value         || 'pending';

    document.getElementById('previewTitulo').textContent  = titulo || 'Título da Sala';
    document.getElementById('previewMateria').textContent = materiaNames[matId] || '—';
    document.getElementById('previewAlunos').textContent  = alunos;

    if (inicio) {
        document.getElementById('previewData').textContent =
            new Date(inicio).toLocaleDateString('pt-BR');
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

['titulo', 'materia_id', 'max_alunos', 'data_hora_inicio', 'status'].forEach(id => {
    document.getElementById(id)?.addEventListener('input',  updatePreview);
    document.getElementById(id)?.addEventListener('change', updatePreview);
});

// Contador de caracteres do título
document.getElementById('titulo')?.addEventListener('input', function () {
    document.getElementById('tituloCount').textContent = this.value.length;
});

// Copiar URL da sala
document.querySelector('.btn-copy-url')?.addEventListener('click', function () {
    const url = this.dataset.copy;
    navigator.clipboard.writeText(url).then(() => {
        const original = this.innerHTML;
        this.innerHTML = '<i class="fas fa-check"></i>';
        setTimeout(() => { this.innerHTML = original; }, 2000);
    });
});

// Modal de exclusão
document.querySelector('.btn-delete-sala')?.addEventListener('click', () => {
    document.getElementById('deleteModal').classList.add('active');
});
document.getElementById('cancelDelete')?.addEventListener('click', () => {
    document.getElementById('deleteModal').classList.remove('active');
});
document.querySelector('.modal-overlay')?.addEventListener('click', function (e) {
    if (e.target === this) this.classList.remove('active');
});
</script>
@endsection