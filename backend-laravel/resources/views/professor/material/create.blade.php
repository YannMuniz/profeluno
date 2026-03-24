{{-- resources/views/professor/material-criar.blade.php --}}
@extends('layouts.app')

@section('title', 'Novo Material')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/sala-professor.css') }}">
@endsection

@section('content')

<div class="page-header">
    <div class="page-header-left">
        <a href="{{ route('professor.sala.show', $sala->id) }}" class="back-link">
            <i class="fas fa-arrow-left"></i>
            Voltar para a Sala
        </a>
        <h1 class="page-title">Novo Material</h1>
        <p class="page-subtitle">Adicione um material de apoio para a sala <strong>{{ $sala->titulo }}</strong></p>
    </div>
</div>

<div class="form-grid-two">

    {{-- Formulário principal --}}
    <div class="form-col-main">
        <form action="{{ route('professor.material.store') }}" method="POST" enctype="multipart/form-data" id="formMaterial">
            @csrf
            <input type="hidden" name="sala_aula_id" value="{{ $sala->id }}">

            <div class="form-card">
                <div class="form-card-header">
                    <i class="fas fa-folder-open"></i>
                    <h3>Informações do Material</h3>
                </div>
                <div class="form-card-body">

                    <div class="form-group">
                        <label for="mat_titulo" class="form-label">
                            Título <span class="required">*</span>
                        </label>
                        <input
                            type="text"
                            id="mat_titulo"
                            name="titulo"
                            class="form-control @error('titulo') is-invalid @enderror"
                            placeholder="Ex: Apostila — Capítulo 3"
                            value="{{ old('titulo') }}"
                            required
                        >
                        @error('titulo')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="mat_descricao" class="form-label">Descrição</label>
                        <textarea
                            id="mat_descricao"
                            name="descricao"
                            class="form-control @error('descricao') is-invalid @enderror"
                            rows="3"
                            placeholder="Descreva o conteúdo deste material..."
                        >{{ old('descricao') }}</textarea>
                        @error('descricao')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-row-two">
                        <div class="form-group">
                            <label for="mat_type" class="form-label">
                                Tipo <span class="required">*</span>
                            </label>
                            <select
                                id="mat_type"
                                name="type"
                                class="form-control filter-select @error('type') is-invalid @enderror"
                                required
                            >
                                <option value="">Selecione o tipo...</option>
                                <option value="pdf"      {{ old('type') === 'pdf'      ? 'selected' : '' }}>📄 PDF</option>
                                <option value="slide"    {{ old('type') === 'slide'    ? 'selected' : '' }}>🖥️ Slide</option>
                                <option value="video"    {{ old('type') === 'video'    ? 'selected' : '' }}>🎬 Vídeo</option>
                                <option value="document" {{ old('type') === 'document' ? 'selected' : '' }}>📝 Documento</option>
                                <option value="other"    {{ old('type') === 'other'    ? 'selected' : '' }}>📦 Outro</option>
                            </select>
                            @error('type')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="mat_file_url" class="form-label">URL do Material</label>
                            <div class="input-with-icon">
                                <i class="fas fa-link"></i>
                                <input
                                    type="url"
                                    id="mat_file_url"
                                    name="file_url"
                                    class="form-control @error('file_url') is-invalid @enderror"
                                    placeholder="https://..."
                                    value="{{ old('file_url') }}"
                                >
                            </div>
                            @error('file_url')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Upload de arquivo --}}
                    <div class="form-group">
                        <label class="form-label">Arquivo</label>
                        <div class="divider-text"><span>ou faça upload</span></div>
                        <div class="file-drop-zone" id="matFileDropZone">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>Arraste o arquivo aqui ou <label for="mat_file" class="file-link">clique para selecionar</label></p>
                            <span class="file-hint">PDF, PPTX, DOCX, MP4 — Máx. 50MB</span>
                            <input
                                type="file"
                                id="mat_file"
                                name="file_path"
                                class="file-input"
                                accept=".pdf,.pptx,.ppt,.docx,.doc,.mp4,.avi,.mov"
                            >
                        </div>
                        <div id="matFilePreview" class="file-preview hidden"></div>
                        @error('file_path')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('professor.sala.show', $sala->id) }}" class="btn-form-cancel">
                    <i class="fas fa-times"></i> Cancelar
                </a>
                <button type="submit" class="btn-form-submit">
                    <i class="fas fa-save"></i>
                    Salvar Material
                </button>
            </div>

        </form>
    </div>

    {{-- Lateral — ícones por tipo + materiais existentes --}}
    <div class="form-col-side">
        <div class="preview-card">
            <div class="preview-card-header">
                <i class="fas fa-eye"></i>
                Prévia do Material
            </div>
            <div class="preview-card-body">
                <div class="material-preview-icon" id="matPreviewIcon">
                    <i class="fas fa-file"></i>
                </div>
                <h4 id="matPreviewTitulo" class="preview-label">Título do material</h4>
                <span id="matPreviewType" class="mini-subject">Tipo não selecionado</span>
            </div>
        </div>

        @if($sala->materiais && $sala->materiais->count())
        <div class="tips-card">
            <div class="tips-card-header">
                <i class="fas fa-folder"></i>
                Materiais desta Sala
            </div>
            <ul class="material-list-side">
                @foreach($sala->materiais as $mat)
                <li class="material-list-item">
                    <span class="mat-type-icon {{ $mat->type }}">
                        @switch($mat->type)
                            @case('pdf')     <i class="fas fa-file-pdf"></i>   @break
                            @case('slide')   <i class="fas fa-desktop"></i>    @break
                            @case('video')   <i class="fas fa-film"></i>       @break
                            @case('document')<i class="fas fa-file-word"></i>  @break
                            @default         <i class="fas fa-file"></i>
                        @endswitch
                    </span>
                    <span class="mat-name">{{ Str::limit($mat->titulo, 35) }}</span>
                </li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="tips-card">
            <div class="tips-card-header">
                <i class="fas fa-lightbulb"></i>
                Dicas
            </div>
            <ul class="tips-list">
                <li>Use URL para links do YouTube, Google Drive ou Moodle</li>
                <li>Para arquivos maiores, prefira hospedar externamente e inserir a URL</li>
                <li>Slides e PDFs ficam disponíveis para download pelos alunos</li>
            </ul>
        </div>
    </div>

</div>

@endsection

@section('scripts')
<script src="{{ asset('js/sala-professor.js') }}"></script>
@endsection