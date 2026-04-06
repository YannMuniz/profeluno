/**
 * conteudo-form.js
 * Controla: visibilidade das seções de link/upload, pré-visualização de arquivo e link
 */
(function () {
    'use strict';

    // ── Elementos ──────────────────────────────────────────────────────────────
    const selectTipo        = document.getElementById('mat_type');
    const inputTitulo       = document.getElementById('mat_titulo');
    const selectMateria     = document.getElementById('materia_id');
    const inputFileUrl      = document.getElementById('mat_file_url');
    const inputFile         = document.getElementById('mat_file');
    const dropZone          = document.getElementById('matFileDropZone');

    // Seções do card de anexo
    const secaoSemTipo      = document.getElementById('anexoSemTipo');
    const secaoLink         = document.getElementById('secaoLink');
    const secaoDivider      = document.getElementById('secaoDivider');
    const secaoUpload       = document.getElementById('secaoUpload');
    const cardAnexoTitulo   = document.getElementById('cardAnexoTitulo');
    const cardAnexoAviso    = document.getElementById('cardAnexoAviso');

    // Preview de link
    const linkPreviewWrapper = document.getElementById('linkPreviewWrapper');
    const linkPreviewContent = document.getElementById('linkPreviewContent');
    const btnCloseLinkPreview= document.getElementById('btnCloseLinkPreview');

    // Preview de arquivo novo
    const filePreviewWrapper = document.getElementById('filePreviewWrapper');
    const filePreviewContent = document.getElementById('filePreviewContent');
    const btnCloseFilePreview= document.getElementById('btnCloseFilePreview');

    // Bloco do arquivo atual (edit)
    const arquivoAtualWrapper= document.getElementById('arquivoAtualWrapper');

    // Prévia lateral
    const matPreviewIcon    = document.getElementById('matPreviewIcon');
    const matPreviewTitulo  = document.getElementById('matPreviewTitulo');
    const matPreviewType    = document.getElementById('matPreviewType');
    const matPreviewMateria = document.getElementById('matPreviewMateria');

    // ── Helpers ────────────────────────────────────────────────────────────────
    function show(el) { if (el) el.classList.remove('hidden'); }
    function hide(el) { if (el) el.classList.add('hidden'); }

    function formatBytes(bytes) {
        if (bytes < 1024)        return bytes + ' B';
        if (bytes < 1048576)     return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / 1048576).toFixed(2) + ' MB';
    }

    function getYouTubeId(url) {
        const m = url.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([^&?/]+)/);
        return m ? m[1] : null;
    }

    function getGDriveId(url) {
        const m = url.match(/drive\.google\.com\/file\/d\/([^/]+)/);
        return m ? m[1] : null;
    }

    // Ícones e cores por tipo
    const tipoMeta = {
        pdf:      { icon: 'fa-file-pdf',  cor: '#ea5455', bg: 'rgba(234,84,85,0.15)',    label: 'PDF' },
        slide:    { icon: 'fa-desktop',   cor: '#ff9f43', bg: 'rgba(255,159,67,0.15)',   label: 'Slide' },
        link:     { icon: 'fa-film',      cor: '#00cfe8', bg: 'rgba(0,207,232,0.15)',    label: 'Link' },
        document: { icon: 'fa-file-word', cor: '#7367f0', bg: 'rgba(115,103,240,0.15)', label: 'Documento' },
        other:    { icon: 'fa-file',      cor: '#82868b', bg: 'rgba(130,134,139,0.15)', label: 'Outro' },
    };

    // ── Visibilidade das seções ────────────────────────────────────────────────
    // LÓGICA DE VISIBILIDADE:
    // - link:                 mostra APENAS field de URL
    // - pdf/slide/doc/other:  mostra APENAS field de upload (arquivo)
    // - default (nenhum):     mostra aviso de seleção obrigatória
    function atualizarSecaoAnexo(tipo) {
        // Oculta todas as seções inicialmente
        hide(secaoSemTipo);
        hide(secaoLink);
        hide(secaoDivider);
        hide(secaoUpload);

        if (cardAnexoAviso) cardAnexoAviso.style.display = 'none';

        switch (tipo) {
            case 'link':
                // Tipo LINK: mostra APENAS URL field
                if (cardAnexoTitulo) cardAnexoTitulo.textContent = 'URL do Conteúdo';
                show(secaoLink);
                break;
            case 'pdf':
            case 'slide':
            case 'document':
            case 'other':
                // Tipos ARQUIVO: mostra APENAS upload, SEM URL
                if (cardAnexoTitulo) cardAnexoTitulo.textContent = 'Arquivo do Conteúdo';
                show(secaoUpload);
                break;
            default:
                // Nenhum tipo selecionado: mostra aviso
                if (cardAnexoTitulo) cardAnexoTitulo.textContent = 'Arquivo ou Link';
                show(secaoSemTipo);
                if (cardAnexoAviso) cardAnexoAviso.style.display = '';
        }
    }

    // ── Preview lateral ────────────────────────────────────────────────────────
    function atualizarPrevia() {
        const tipo    = selectTipo    ? selectTipo.value    : '';
        const titulo  = inputTitulo   ? inputTitulo.value   : '';
        const matOpt  = selectMateria ? selectMateria.options[selectMateria.selectedIndex] : null;
        const matNome = matOpt && matOpt.value ? matOpt.text : 'Matéria não selecionada';

        const meta = tipoMeta[tipo] || { icon: 'fa-file', cor: '#82868b', bg: 'rgba(130,134,139,0.15)', label: 'Tipo não selecionado' };

        if (matPreviewIcon) {
            matPreviewIcon.style.background = meta.bg;
            matPreviewIcon.style.color      = meta.cor;
            matPreviewIcon.innerHTML        = `<i class="fas ${meta.icon}" style="font-size:28px;"></i>`;
        }
        if (matPreviewTitulo) matPreviewTitulo.textContent = titulo || 'Título do material';
        if (matPreviewType)   matPreviewType.textContent   = meta.label;
        if (matPreviewMateria)matPreviewMateria.textContent= matNome;
    }

    // ── Preview de LINK ────────────────────────────────────────────────────────
    function renderLinkPreview(url) {
        if (!linkPreviewContent) return;
        linkPreviewContent.innerHTML = '';

        const ytId = getYouTubeId(url);
        if (ytId) {
            linkPreviewContent.innerHTML = `<iframe
                src="https://www.youtube.com/embed/${ytId}"
                style="width:100%;height:100%;min-height:400px;border:none;"
                allowfullscreen></iframe>`;
            show(linkPreviewWrapper);
            return;
        }

        const gdId = getGDriveId(url);
        if (gdId) {
            linkPreviewContent.innerHTML = `<iframe
                src="https://drive.google.com/file/d/${gdId}/preview"
                style="width:100%;height:100%;min-height:400px;border:none;"></iframe>`;
            show(linkPreviewWrapper);
            return;
        }

        // URL genérica — mostra botão de abertura
        linkPreviewContent.innerHTML = `
            <div style="display:flex;flex-direction:column;align-items:center;
                        justify-content:center;height:100%;padding:32px;gap:12px;
                        color:var(--text-secondary);text-align:center;">
                <i class="fas fa-link" style="font-size:40px;opacity:.4;"></i>
                <p style="margin:0;word-break:break-all;font-size:13px;">${url}</p>
                <a href="${url}" target="_blank" rel="noopener"
                   style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;
                          background:var(--primary-color,#7367f0);color:#fff;border-radius:6px;
                          text-decoration:none;font-size:13px;">
                    <i class="fas fa-external-link-alt"></i> Abrir link
                </a>
            </div>`;
        show(linkPreviewWrapper);
    }

    let linkDebounce = null;
    function onUrlInput() {
        clearTimeout(linkDebounce);
        const url = inputFileUrl ? inputFileUrl.value.trim() : '';
        if (!url) { hide(linkPreviewWrapper); return; }
        linkDebounce = setTimeout(() => renderLinkPreview(url), 600);
    }

    if (btnCloseLinkPreview) {
        btnCloseLinkPreview.addEventListener('click', () => {
            hide(linkPreviewWrapper);
            if (linkPreviewContent) linkPreviewContent.innerHTML = '';
        });
    }

    // ── Preview de ARQUIVO novo selecionado ────────────────────────────────────
    function renderFilePreview(file) {
        if (!filePreviewContent || !filePreviewWrapper) return;
        filePreviewContent.innerHTML = '';

        const ext = file.name.split('.').pop().toLowerCase();

        // Cabeçalho com info do arquivo
        const infoBar = `
            <div style="display:flex;align-items:center;gap:10px;
                        padding:10px 14px;background:rgba(115,103,240,0.07);
                        border-bottom:1px solid rgba(115,103,240,0.15);font-size:13px;">
                <i class="fas fa-file" style="color:var(--primary-color);"></i>
                <strong>${file.name}</strong>
                <span style="color:var(--text-secondary);margin-left:auto;">${formatBytes(file.size)}</span>
            </div>`;

        if (ext === 'pdf') {
            const objUrl = URL.createObjectURL(file);
            filePreviewContent.innerHTML = infoBar + `<iframe
                src="${objUrl}"
                style="width:100%;height:440px;border:none;"
                type="application/pdf"></iframe>`;
        } else if (['mp4', 'avi', 'mov'].includes(ext)) {
            const objUrl = URL.createObjectURL(file);
            filePreviewContent.innerHTML = infoBar + `<video controls
                style="width:100%;max-height:440px;background:#000;">
                <source src="${objUrl}" type="video/${ext}">
            </video>`;
        } else {
            // Arquivos que não têm preview nativo (docx, pptx, etc.)
            filePreviewContent.innerHTML = infoBar + `
                <div style="display:flex;flex-direction:column;align-items:center;
                            justify-content:center;height:200px;gap:10px;
                            color:var(--text-secondary);">
                    <i class="fas fa-file" style="font-size:40px;opacity:.4;"></i>
                    <p style="margin:0;font-size:13px;">Arquivo selecionado: <strong>${file.name}</strong></p>
                    <p style="margin:0;font-size:12px;color:var(--text-secondary);">
                        Tamanho: ${formatBytes(file.size)} · Tipo: .${ext.toUpperCase()}
                    </p>
                    <p style="font-size:11px;opacity:.6;">Pré-visualização não disponível para este formato.</p>
                </div>`;
        }

        show(filePreviewWrapper);

        // Quando seleciona novo arquivo, oculta o bloco do arquivo atual (edit)
        if (arquivoAtualWrapper) arquivoAtualWrapper.style.display = 'none';
    }

    if (btnCloseFilePreview) {
        btnCloseFilePreview.addEventListener('click', () => {
            hide(filePreviewWrapper);
            if (filePreviewContent) filePreviewContent.innerHTML = '';
            if (inputFile) inputFile.value = '';
            // Restaura o bloco do arquivo atual se existir
            if (arquivoAtualWrapper) arquivoAtualWrapper.style.display = '';
        });
    }

    // ── Drag & Drop ────────────────────────────────────────────────────────────
    if (dropZone) {
        dropZone.addEventListener('dragover', e => {
            e.preventDefault();
            dropZone.classList.add('drag-over');
        });
        dropZone.addEventListener('dragleave', () => dropZone.classList.remove('drag-over'));
        dropZone.addEventListener('drop', e => {
            e.preventDefault();
            dropZone.classList.remove('drag-over');
            const file = e.dataTransfer.files[0];
            if (file && inputFile) {
                // Simula seleção via DataTransfer
                const dt = new DataTransfer();
                dt.items.add(file);
                inputFile.files = dt.files;
                renderFilePreview(file);
            }
        });
    }

    // ── Listeners ──────────────────────────────────────────────────────────────
    if (selectTipo) {
        selectTipo.addEventListener('change', function () {
            atualizarSecaoAnexo(this.value);
            atualizarPrevia();
        });
    }

    if (inputTitulo)   inputTitulo.addEventListener('input',  atualizarPrevia);
    if (selectMateria) selectMateria.addEventListener('change', atualizarPrevia);

    if (inputFileUrl) {
        inputFileUrl.addEventListener('input', onUrlInput);
        inputFileUrl.addEventListener('paste', () => setTimeout(onUrlInput, 100));
    }

    if (inputFile) {
        inputFile.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                renderFilePreview(this.files[0]);
            }
        });
    }

    // ── Init ───────────────────────────────────────────────────────────────────
    // Dispara com o valor já selecionado (edit mode ou old())
    if (selectTipo && selectTipo.value) {
        atualizarSecaoAnexo(selectTipo.value);
    }

    // Se há URL já preenchida (edit), renderiza o preview de link
    if (inputFileUrl && inputFileUrl.value.trim()) {
        renderLinkPreview(inputFileUrl.value.trim());
    }

    atualizarPrevia();

})();