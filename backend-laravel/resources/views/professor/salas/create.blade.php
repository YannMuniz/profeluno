{{-- resources/views/professor/salas/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Nova Sala de Aula')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/sala-professor.css') }}">
@endsection

@section('content')

<div class="page-header">
    <div class="page-header-left">
        <a href="{{ route('professor.salas.index') }}" class="back-link">
            <i class="fas fa-arrow-left"></i>
            Voltar
        </a>
        <h1 class="page-title">Nova Sala de Aula</h1>
        <p class="page-subtitle">Preencha os dados para criar uma nova sala</p>
    </div>
    <div class="page-header-right">
        <div class="steps-indicator">
            <div class="step active" data-step="1">
                <span class="step-num">1</span>
                <span class="step-label">Informações</span>
            </div>
            <div class="step-line"></div>
            <div class="step" data-step="2">
                <span class="step-num">2</span>
                <span class="step-label">Material</span>
            </div>
            <div class="step-line"></div>
            <div class="step" data-step="3">
                <span class="step-num">3</span>
                <span class="step-label">Simulado</span>
            </div>
        </div>
    </div>
</div>

@if(session('error'))
<div class="alert alert-danger">
    <i class="fas fa-exclamation-circle"></i>
    {{ session('error') }}
</div>
@endif

@if($errors->any())
<div class="alert alert-danger">
    <ul style="margin:0; padding-left: 18px;">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form action="{{ route('professor.salas.store') }}" method="POST" enctype="multipart/form-data" id="formCriarSala">
    @csrf

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
                                maxlength="150"
                                required
                            >
                            <span class="char-count"><span id="tituloCount">0</span>/150</span>
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
                                <label for="materia" class="form-label">
                                    Matéria <span class="required">*</span>
                                </label>
                                <select
                                    id="materia"
                                    name="materia_id"
                                    class="form-control @error('materia_id') is-invalid @enderror"
                                    required
                                >
                                    <option value="">— Selecione uma matéria —</option>
                                    @foreach($materias as $materia)
                                        <option
                                            value="{{ $materia['idMateria'] ?? $materia['id'] ?? '' }}"
                                            {{ old('materia_id') == ($materia['idMateria'] ?? $materia['id'] ?? '') ? 'selected' : '' }}
                                        >
                                            {{ $materia['nomeMateria'] ?? $materia['nome'] ?? 'Sem nome' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('materia_id')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="qtd_alunos" class="form-label">
                                    Qtd. Máx. de Alunos <span class="required">*</span>
                                </label>
                                <input
                                    type="number"
                                    id="qtd_alunos"
                                    name="max_alunos"
                                    class="form-control @error('max_alunos') is-invalid @enderror"
                                    placeholder="Ex: 30"
                                    value="{{ old('max_alunos') }}"
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

                        <div class="form-row-two">
                            <div class="form-group">
                                <label for="url" class="form-label">
                                    URL / Link da Aula <span class="required">*</span>
                                </label>
                                <div class="input-with-icon">
                                    <i class="fas fa-link"></i>
                                    <input
                                        type="url"
                                        id="url"
                                        name="url"
                                        class="form-control @error('url') is-invalid @enderror"
                                        placeholder="https://meet.google.com/..."
                                        value="{{ old('url') }}"
                                        required
                                    >
                                </div>
                                @error('url')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="status" class="form-label">Status Inicial</label>
                                <select id="status" name="status" class="form-control filter-select @error('status') is-invalid @enderror">
                                    <option value="pending" {{ old('status') === 'pending' ? 'selected' : '' }}>
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

                        <div class="form-row-two">
                            <div class="form-group">
                                <label for="conteudo_id" class="form-label">Conteúdo associado</label>
                                <select id="conteudo_id" name="conteudo_id" class="form-control filter-select @error('conteudo_id') is-invalid @enderror">
                                    <option value="">Nenhum conteúdo</option>
                                    @foreach($conteudos as $conteudo)
                                        <option
                                            value="{{ $conteudo['idConteudo'] ?? $conteudo['id'] ?? '' }}"
                                            {{ old('conteudo_id') == ($conteudo['idConteudo'] ?? $conteudo['id'] ?? '') ? 'selected' : '' }}
                                        >
                                            {{ $conteudo['titulo'] ?? 'Conteúdo sem título' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('conteudo_id')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="simulado_id" class="form-label">Simulado associado</label>
                                <select id="simulado_id" name="simulado_id" class="form-control filter-select @error('simulado_id') is-invalid @enderror">
                                    <option value="">Nenhum simulado</option>
                                    @foreach($simulados as $simulado)
                                        <option
                                            value="{{ $simulado['idSimulado'] ?? $simulado['id'] ?? '' }}"
                                            {{ old('simulado_id') == ($simulado['idSimulado'] ?? $simulado['id'] ?? '') ? 'selected' : '' }}
                                        >
                                            {{ $simulado['titulo'] ?? 'Simulado sem título' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('simulado_id')
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
                                <span><i class="fas fa-users"></i> <span id="previewAlunos">0</span> alunos</span>
                                <span><i class="fas fa-calendar"></i> <span id="previewData">Sem data</span></span>
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
                        <li>Use um título claro e descritivo para facilitar a busca</li>
                        <li>Defina a data/hora de início para que alunos se programem</li>
                        <li>Adicione um link válido de videoconferência</li>
                        <li>Você poderá adicionar materiais e simulados depois</li>
                    </ul>
                </div>
            </div>

        </div>

        <div class="form-actions">
            <a href="{{ route('professor.salas.index') }}" class="btn-form-cancel">
                <i class="fas fa-times"></i> Cancelar
            </a>
            <button type="button" class="btn-form-next" id="nextToStep2">
                Próximo: Material
                <i class="fas fa-arrow-right"></i>
            </button>
        </div>
    </div>

    <div class="form-step" id="step-2">
        <div class="form-card">
            <div class="form-card-header">
                <i class="fas fa-folder-open"></i>
                <h3>Material de Apoio <span class="optional-tag">Opcional</span></h3>
            </div>
            <div class="form-card-body">

                <div class="material-toggle-row">
                    <label class="toggle-switch">
                        <input type="checkbox" id="addMaterial" name="add_material" value="1" {{ old('add_material') ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                    <span>Adicionar material a esta sala</span>
                </div>

                <div id="materialFields" class="hidden-fields {{ old('add_material') ? 'visible' : '' }}">
                    <div class="form-group">
                        <label class="form-label">Título do Material <span class="required">*</span></label>
                        <input type="text" name="material_titulo" class="form-control" placeholder="Ex: Apostila — Capítulo 3" value="{{ old('material_titulo') }}">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Descrição</label>
                        <textarea name="material_descricao" class="form-control" rows="3" placeholder="Descreva o material...">{{ old('material_descricao') }}</textarea>
                    </div>

                    <div class="form-row-two">
                        <div class="form-group">
                            <label class="form-label">Tipo <span class="required">*</span></label>
                            <select name="material_type" id="mat_type" class="form-control filter-select">
                                <option value="">Selecione o tipo</option>
                                <option value="pdf" {{ old('material_type') === 'pdf' ? 'selected' : '' }}>PDF</option>
                                <option value="slide" {{ old('material_type') === 'slide' ? 'selected' : '' }}>Slide</option>
                                <option value="video" {{ old('material_type') === 'video' ? 'selected' : '' }}>Vídeo</option>
                                <option value="document" {{ old('material_type') === 'document' ? 'selected' : '' }}>Documento</option>
                                <option value="other" {{ old('material_type') === 'other' ? 'selected' : '' }}>Outro</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">URL do Material</label>
                            <div class="input-with-icon">
                                <i class="fas fa-link"></i>
                                <input type="url" id="mat_file_url" name="material_file_url" class="form-control" placeholder="https://..." value="{{ old('material_file_url') }}">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Arquivo</label>
                        <div class="file-drop-zone" id="fileDropZone">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>Arraste o arquivo aqui ou <label for="materialFile" class="file-link">clique para selecionar</label></p>
                            <span class="file-hint">PDF, PPTX, DOCX, MP4 — Máx. 50MB</span>
                            <input type="file" id="materialFile" name="material_file" class="file-input" accept=".pdf,.pptx,.ppt,.docx,.doc,.mp4,.avi">
                        </div>
                        <div id="filePreview" class="file-preview hidden"></div>
                    </div>
                </div>
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

    <div class="form-step" id="step-3">
        <div class="form-card">
            <div class="form-card-header">
                <i class="fas fa-question-circle"></i>
                <h3>Simulado <span class="optional-tag">Opcional</span></h3>
            </div>
            <div class="form-card-body">

                <div class="material-toggle-row">
                    <label class="toggle-switch">
                        <input type="checkbox" id="addSimulado" name="add_simulado" value="1" {{ old('add_simulado') || old('questoes') ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                    <span>Adicionar simulado a esta sala</span>
                </div>

                <div id="simuladoFields" class="hidden-fields {{ old('add_simulado') || old('questoes') ? 'visible' : '' }}">
                    <div id="questoesContainer">
                        {{-- Questão 1 é adicionada pelo JS --}}
                    </div>
                    <button type="button" class="btn-add-questao" id="addQuestao">
                        <i class="fas fa-plus"></i>
                        Adicionar Questão
                    </button>
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

<template id="questaoTemplate">
    <div class="questao-block" data-index="__INDEX__">
        <div class="questao-header">
            <span class="questao-num">Questão <strong>__NUM__</strong></span>
            <button type="button" class="btn-remove-questao" title="Remover">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="form-group">
            <label class="form-label">Enunciado da Questão <span class="required">*</span></label>
            <textarea name="questoes[__INDEX__][enunciado]" class="form-control" rows="2" placeholder="Digite o enunciado..."></textarea>
        </div>
        <div class="alternativas-grid">
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
        <div class="form-group">
            <label class="form-label">Alternativa Correta <span class="required">*</span></label>
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
@endsection
