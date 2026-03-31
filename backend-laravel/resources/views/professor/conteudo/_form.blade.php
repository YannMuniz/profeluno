{{-- resources/views/professor/conteudo/_form.blade.php --}}
{{--
    Variáveis esperadas:
      $conteudo → array|null  (null no create, preenchido no edit)
      $materias → array       (lista de matérias da API)
      $action   → string      (URL do form)
      $method   → string      ('POST' | 'PUT')
--}}

@php
    $isEdit     = isset($conteudo) && !empty($conteudo);
    $httpMethod = strtoupper($method ?? 'POST');

    $titulo    = $isEdit ? ($conteudo['titulo']    ?? '') : old('titulo',     '');
    $descricao = $isEdit ? ($conteudo['descricao'] ?? '') : old('descricao',  '');
    $materiaId = $isEdit ? ($conteudo['idMateria'] ?? '') : old('materia_id', '');
    $tipo      = $isEdit ? ($conteudo['tipo']      ?? '') : old('type',       '');
    $situacao  = $isEdit ? ($conteudo['situacao']  ?? 1)  : old('situacao',   1);
    $fileUrl   = $isEdit ? ($conteudo['file_url']  ?? '') : old('file_url',   '');
    $filePath  = $isEdit ? ($conteudo['file_path'] ?? '') : '';
@endphp

<div class="form-grid-two">

    <div class="form-col-main">
        <form
            action="{{ $action }}"
            method="POST"
            enctype="multipart/form-data"
            id="formConteudo"
        >
            @csrf
            @if($httpMethod === 'PUT')
                @method('PUT')
            @endif

            <div class="questao-block" style="margin-bottom: 24px;">
                <div class="questao-header">
                    <div class="questao-num-wrap">
                        <span class="questao-num-label" style="font-size: 15px; font-weight: 600;">
                            <i class="fas fa-folder-open" style="color: var(--primary-color); margin-right: 6px;"></i>
                            Informações do Conteúdo
                        </span>
                    </div>

                    @if($isEdit)
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="font-size: 13px; color: var(--text-secondary);">Situação:</span>
                        <label class="toggle-switch" title="{{ $situacao ? 'Desativar' : 'Ativar' }} conteúdo">
                            <input type="hidden"   name="situacao" value="0">
                            <input type="checkbox" name="situacao" value="1"
                                   id="toggleSituacao"
                                   {{ $situacao ? 'checked' : '' }}
                                   onchange="
                                       this.previousElementSibling.value = this.checked ? 1 : 0;
                                       document.getElementById('situacaoLabel').textContent = this.checked ? 'Ativo' : 'Inativo';
                                       document.getElementById('situacaoLabel').style.color = this.checked ? 'var(--success-color)' : 'var(--danger-color)';
                                   ">
                            <span class="toggle-slider"></span>
                        </label>
                        <span id="situacaoLabel"
                              style="font-size: 13px; font-weight: 600;
                                     color: {{ $situacao ? 'var(--success-color)' : 'var(--danger-color)' }};">
                            {{ $situacao ? 'Ativo' : 'Inativo' }}
                        </span>
                    </div>
                    @endif
                </div>

                <div class="questao-body">

                    <div class="form-group">
                        <label class="form-label" for="mat_titulo">
                            Título <span class="required">*</span>
                        </label>
                        <input
                            type="text"
                            id="mat_titulo"
                            name="titulo"
                            class="form-control @error('titulo') is-invalid @enderror"
                            placeholder="Ex: Apostila — Capítulo 3"
                            value="{{ $titulo }}"
                            required
                        >
                        @error('titulo')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Descrição --}}
                    <div class="form-group">
                        <label class="form-label" for="mat_descricao">
                            Descrição
                            <span style="font-size: 11px; font-weight: 400; color: var(--text-secondary);">(opcional)</span>
                        </label>
                        <textarea
                            id="mat_descricao"
                            name="descricao"
                            class="form-control @error('descricao') is-invalid @enderror"
                            rows="2"
                            placeholder="Descreva brevemente o conteúdo deste material..."
                        >{{ $descricao }}</textarea>
                        @error('descricao')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Matéria --}}
                    <div class="form-group">
                        <label class="form-label" for="materia_id">
                            Matéria <span class="required">*</span>
                        </label>
                        <select
                            id="materia_id"
                            name="materia_id"
                            class="form-control @error('materia_id') is-invalid @enderror"
                            required
                        >
                            <option value="">Selecione uma matéria</option>
                            @foreach($materias as $materia)
                                <option
                                    value="{{ $materia['idMateria'] }}"
                                    {{ $materiaId == $materia['idMateria'] ? 'selected' : '' }}
                                >
                                    {{ $materia['nomeMateria'] }}
                                    @if(!empty($materia['descricao']))— {{ $materia['descricao'] }}@endif
                                </option>
                            @endforeach
                        </select>
                        @error('materia_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Tipo --}}
                    <div class="form-group">
                        <label class="form-label" for="mat_type">
                            Tipo <span class="required">*</span>
                        </label>
                        <select
                            id="mat_type"
                            name="type"
                            class="form-control @error('type') is-invalid @enderror"
                            required
                        >
                            <option value="pdf"      {{ $tipo === 'pdf'      ? 'selected' : '' }}>📄 PDF</option>
                            <option value="slide"    {{ $tipo === 'slide'    ? 'selected' : '' }}>🖥️ Slide</option>
                            <option value="link"     {{ $tipo === 'link'     ? 'selected' : '' }}>🎬 Link</option>
                            <option value="document" {{ $tipo === 'document' ? 'selected' : '' }}>📝 Documento</option>
                            <option value="other"    {{ $tipo === 'other'    ? 'selected' : '' }}>📦 Outro</option>
                        </select>
                        @error('type')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                </div>
            </div>

            <div class="questao-block" style="margin-bottom: 24px;">
                <div class="questao-header">
                    <div class="questao-num-wrap">
                        <span class="questao-num-label" style="font-size: 15px; font-weight: 600;">
                            <i class="fas fa-paperclip" style="color: var(--primary-color); margin-right: 6px;"></i>
                            <span id="cardAnexoTitulo">Arquivo ou Link</span>
                        </span>
                    </div>
                    {{-- Aviso de tipo não selecionado --}}
                    <span id="cardAnexoAviso"
                          style="font-size: 12px; color: var(--text-secondary); font-style: italic;">
                        Selecione um tipo acima
                    </span>
                </div>

                <div class="questao-body">

                    <div id="anexoSemTipo" style="
                        display: flex; align-items: center; gap: 10px;
                        padding: 14px 16px;
                        background: rgba(130,134,139,.07);
                        border: 1px dashed var(--border-color, #ccc);
                        border-radius: 8px;
                        color: var(--text-secondary);
                        font-size: 13px;">
                        <i class="fas fa-info-circle" style="font-size: 16px; opacity: .6;"></i>
                        Selecione o tipo do conteúdo para ver as opções de anexo.
                    </div>

                    <div id="secaoLink" class="hidden">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label" for="mat_file_url">
                                URL do Material <span class="required">*</span>
                            </label>
                            <div class="input-with-icon">
                                <i class="fas fa-link"></i>
                                <input
                                    type="url"
                                    id="mat_file_url"
                                    name="file_url"
                                    class="form-control @error('file_url') is-invalid @enderror"
                                    placeholder="https://youtube.com/watch?v=... ou Google Drive..."
                                    value="{{ $fileUrl }}"
                                >
                            </div>
                            @error('file_url')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror

                            <div id="linkPreviewWrapper" class="link-preview-wrapper{{ empty($fileUrl) ? ' hidden' : '' }}">
                                <div class="link-preview-header">
                                    <i class="fas fa-eye"></i>
                                    <span>Pré-visualização do link</span>
                                    <button type="button" class="link-preview-close" id="btnCloseLinkPreview">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <div id="linkPreviewContent" class="link-preview-content"></div>
                            </div>
                        </div>
                    </div>

                    <div id="secaoDivider" class="divider-text hidden">
                        <span>ou faça upload de um arquivo</span>
                    </div>

                    <div id="secaoUpload" class="hidden">
                        <div class="form-group" style="margin-bottom: 0;">
                            <div class="file-drop-zone" id="matFileDropZone">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p>Arraste o arquivo aqui ou
                                    <label for="mat_file" class="file-link">clique para selecionar</label>
                                </p>
                                <span class="file-hint" id="matFileHint">PDF, PPTX, DOCX, MP4 — Máx. 50 MB</span>
                                <input
                                    type="file"
                                    id="mat_file"
                                    name="file_path"
                                    class="file-input"
                                    accept=".pdf,.pptx,.ppt,.docx,.doc,.mp4,.avi,.mov"
                                >
                            </div>

                            <div id="filePreviewWrapper" class="link-preview-wrapper hidden">
                                <div class="link-preview-header">
                                    <i class="fas fa-eye"></i>
                                    <span>Pré-visualização do arquivo</span>
                                    <button type="button" id="btnCloseFilePreview" class="link-preview-close">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <div id="filePreviewContent" class="link-preview-content"></div>
                            </div>

                            @if($isEdit && !empty($filePath))
                            <div class="current-file-info">
                                <i class="fas fa-paperclip"></i>
                                <span>Arquivo atual: <strong>{{ basename($filePath) }}</strong></span>
                                <small>(deixe vazio para manter o arquivo atual)</small>
                            </div>
                            @endif

                            @error('file_path')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                </div>
            </div>

            @if($errors->any())
            <div class="alert alert-danger" style="margin-top: 16px;">
                <ul style="margin:0; padding-left: 18px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="form-actions">
                <a href="{{ route('professor.conteudo.index') }}" class="btn-form-cancel">
                    <i class="fas fa-times"></i> Cancelar
                </a>
                <button type="submit" class="btn-form-submit">
                    <i class="fas fa-save"></i>
                    {{ $isEdit ? 'Atualizar Conteúdo' : 'Salvar Conteúdo' }}
                </button>
            </div>

        </form>
    </div>

    <div class="form-col-side">

        <div class="preview-card">
            <div class="preview-card-header">
                <i class="fas fa-eye"></i>
                Prévia do Material
            </div>
            <div class="preview-card-body">
                <div class="material-preview-icon" id="matPreviewIcon"
                     style="width:64px;height:64px;border-radius:16px;display:flex;align-items:center;
                            justify-content:center;margin:0 auto 12px;transition:all .25s;">
                    <i class="fas fa-file" style="font-size:28px;"></i>
                </div>
                <h4 id="matPreviewTitulo" class="preview-label">Título do material</h4>
                <span id="matPreviewType"    class="mini-subject" style="display:block;margin-top:6px;">Tipo não selecionado</span>
                <span id="matPreviewMateria" class="mini-subject" style="display:block;margin-top:4px;">Matéria não selecionada</span>
            </div>
        </div>

        <div class="tips-card">
            <div class="tips-card-header">
                <i class="fas fa-lightbulb"></i>
                Dicas
            </div>
            <ul class="tips-list">
                <li>Cole a URL do YouTube para ter pré-visualização do vídeo</li>
                <li>Para Google Drive, use o link de compartilhamento público</li>
                <li>PDFs podem ser pré-visualizados diretamente no navegador</li>
                <li>Para arquivos grandes, prefira hospedar externamente e inserir a URL</li>
                <li>Slides e PDFs ficam disponíveis para download pelos alunos</li>
            </ul>
        </div>

    </div>
</div>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/conteudo-form.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/conteudo-form.js') }}"></script>
@endpush