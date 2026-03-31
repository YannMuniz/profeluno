document.addEventListener('DOMContentLoaded', function () {

    // ── Mapa de tipos ────────────────────────────────────────────
    const TIPO_MAP = {
        pdf:      { icon: 'fa-file-pdf',  cor: '#ea5455', label: 'PDF',       anexo: 'upload' },
        slide:    { icon: 'fa-desktop',   cor: '#ff9f43', label: 'Slide',     anexo: 'upload' },
        link:     { icon: 'fa-film',      cor: '#00cfe8', label: 'Link',      anexo: 'link'   },
        document: { icon: 'fa-file-word', cor: '#7367f0', label: 'Documento', anexo: 'upload' },
        other:    { icon: 'fa-file',      cor: '#82868b', label: 'Outro',     anexo: 'ambos'  },
    };

    // ── Hints de accept por tipo ──────────────────────────────────
    const ACCEPT_MAP = {
        pdf:      { accept: '.pdf',                        hint: 'PDF — Máx. 50 MB'                  },
        slide:    { accept: '.pptx,.ppt',                  hint: 'PPTX, PPT — Máx. 50 MB'            },
        document: { accept: '.docx,.doc',                  hint: 'DOCX, DOC — Máx. 50 MB'            },
        other:    { accept: '.pdf,.pptx,.ppt,.docx,.doc,.mp4,.avi,.mov', hint: 'PDF, PPTX, DOCX, MP4 — Máx. 50 MB' },
    };

    // ── Elementos do card de anexo ────────────────────────────────
    const elCardTitulo  = document.getElementById('cardAnexoTitulo');
    const elCardAviso   = document.getElementById('cardAnexoAviso');
    const elSemTipo     = document.getElementById('anexoSemTipo');
    const elSecaoLink   = document.getElementById('secaoLink');
    const elSecaoDivider = document.getElementById('secaoDivider');
    const elSecaoUpload = document.getElementById('secaoUpload');
    const elFileInput   = document.getElementById('mat_file');
    const elFileHint    = document.getElementById('matFileHint');

    // ── Controle de campos URL / Upload por tipo ─────────────────
    const matType      = document.getElementById('mat_type');
    const urlField     = document.getElementById('mat_file_url');
    const urlWrapper   = urlField?.closest('.form-group');
    const dropZone     = document.getElementById('matFileDropZone');
    const fileInput    = document.getElementById('mat_file');
    const filePreview  = document.getElementById('matFilePreview');
    const dropWrapper  = dropZone?.closest('.form-group');

    const tipoConfig = {
        '':         { url: false, upload: false },
        'link':     { url: true,  upload: false },  // Link
        'pdf':      { url: false, upload: true  },
        'slide':    { url: false, upload: true  },
        'document': { url: false, upload: true  },
        'other':    { url: true,  upload: true  },  // Ambos
    };

    function handleTipoChange() {
        const config = tipoConfig[matType.value] ?? { url: false, upload: false };

        // ── URL ──────────────────────────────────────────────────
        if (urlWrapper) {
            urlWrapper.style.display = config.url ? '' : 'none';
            if (!config.url) urlField.value = '';
        }

        // ── Upload ───────────────────────────────────────────────
        if (dropWrapper) {
            dropWrapper.style.display = config.upload ? '' : 'none';

            if (!config.upload) {
                // Limpa o arquivo selecionado
                fileInput.value = '';
                filePreview.innerHTML = '';
                filePreview.classList.add('hidden');

                // Desabilita o input para não enviar no submit
                fileInput.disabled = true;
                dropZone.classList.add('disabled');
            } else {
                fileInput.disabled = false;
                dropZone.classList.remove('disabled');
            }
        }
    }

    // ── Prévia lateral ───────────────────────────────────────────
    const elIcon    = document.getElementById('matPreviewIcon');
    const elTitulo  = document.getElementById('matPreviewTitulo');
    const elType    = document.getElementById('matPreviewType');
    const elMateria = document.getElementById('matPreviewMateria');

    function updatePreview() {
        const titulo  = document.getElementById('mat_titulo').value.trim() || 'Título do material';
        const type    = document.getElementById('mat_type').value;
        const matSel  = document.getElementById('materia_id');
        const matText = matSel.options[matSel.selectedIndex]?.text ?? '';

        elTitulo.textContent  = titulo;
        elMateria.textContent = matText || 'Matéria não selecionada';

        const t = TIPO_MAP[type];
        if (t) {
            elIcon.style.color      = t.cor;
            elIcon.style.background = t.cor + '1a';
            elIcon.innerHTML        = `<i class="fas ${t.icon}" style="font-size:28px"></i>`;
            elType.textContent      = t.label;
        } else {
            elIcon.style.color      = '#82868b';
            elIcon.style.background = 'rgba(130,134,139,.12)';
            elIcon.innerHTML        = '<i class="fas fa-file" style="font-size:28px"></i>';
            elType.textContent      = 'Tipo não selecionado';
        }
    }

    // ── Listener: mudança de tipo ─────────────────────────────────
    const tipoSelect = document.getElementById('mat_type');

    if (tipoSelect) {
        tipoSelect.addEventListener('change', () => {
            updatePreview();
            handleTipoChange();
        });
    }

    document.getElementById('mat_titulo').addEventListener('input',  updatePreview);
    document.getElementById('materia_id').addEventListener('change', updatePreview);

    // Inicializa com o tipo já selecionado (edit) ou vazio (create)
    updatePreview();
    handleTipoChange();

    // ── Helpers de URL ───────────────────────────────────────────
    function getYouTubeId(url) {
        const m = url.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([^&?/]+)/);
        return m ? m[1] : null;
    }
    function getGDriveId(url) {
        const m = url.match(/drive\.google\.com\/file\/d\/([^/]+)/);
        return m ? m[1] : null;
    }

    // ── Preview de Link ──────────────────────────────────────────
    const urlInput         = document.getElementById('mat_file_url');
    const linkWrapper      = document.getElementById('linkPreviewWrapper');
    const linkContent      = document.getElementById('linkPreviewContent');
    const btnCloseLinkPrev = document.getElementById('btnCloseLinkPreview');

    function renderLinkPreview(url) {
        if (!url) { linkWrapper.classList.add('hidden'); return; }

        const ytId = getYouTubeId(url);
        if (ytId) {
            linkContent.innerHTML = `<iframe src="https://www.youtube.com/embed/${ytId}" allowfullscreen></iframe>`;
            linkWrapper.classList.remove('hidden');
            return;
        }

        const gdId = getGDriveId(url);
        if (gdId) {
            linkContent.innerHTML = `<iframe src="https://drive.google.com/file/d/${gdId}/preview"></iframe>`;
            linkWrapper.classList.remove('hidden');
            return;
        }

        linkContent.innerHTML = `
            <div class="generic-link-preview">
                <i class="fas fa-external-link-alt"></i>
                <div>
                    <p style="margin:0 0 4px;font-size:13px;font-weight:600;">Link externo</p>
                    <a href="${url}" target="_blank" rel="noopener">${url}</a>
                </div>
            </div>`;
        linkWrapper.classList.remove('hidden');
    }

    // Renderiza preview ao carregar página (edit com URL já preenchida)
    if (urlInput && urlInput.value.trim()) renderLinkPreview(urlInput.value.trim());

    let linkDebounce;
    if (urlInput) {
        urlInput.addEventListener('input', () => {
            clearTimeout(linkDebounce);
            linkDebounce = setTimeout(() => renderLinkPreview(urlInput.value.trim()), 600);
        });
    }
    if (btnCloseLinkPrev) {
        btnCloseLinkPrev.addEventListener('click', () => {
            linkWrapper.classList.add('hidden');
            linkContent.innerHTML = '';
        });
    }

    // ── Preview de Arquivo ───────────────────────────────────────
    const fileWrapper      = document.getElementById('filePreviewWrapper');
    const fileContent      = document.getElementById('filePreviewContent');
    const btnCloseFilePrev = document.getElementById('btnCloseFilePreview');

    function renderFilePreview(file) {
        if (!file) { fileWrapper.classList.add('hidden'); return; }

        fileWrapper.classList.remove('hidden');

        if (file.type === 'application/pdf') {
            const url = URL.createObjectURL(file);
            fileContent.innerHTML = `<iframe src="${url}" style="width:100%;height:380px;border:none;"></iframe>`;
            return;
        }

        if (file.type.startsWith('video/')) {
            const url = URL.createObjectURL(file);
            fileContent.innerHTML = `
                <video controls style="width:100%;max-height:280px;display:block;background:#000;">
                    <source src="${url}" type="${file.type}">
                </video>`;
            return;
        }

        const ext  = file.name.split('.').pop().toUpperCase();
        const size = (file.size / 1024 / 1024).toFixed(2);
        fileContent.innerHTML = `
            <div class="generic-link-preview">
                <i class="fas fa-file-alt"></i>
                <div>
                    <p style="margin:0 0 4px;font-size:13px;font-weight:600;">${file.name}</p>
                    <small>${ext} · ${size} MB</small>
                </div>
            </div>`;
    }

    if (fileInput) {
        fileInput.addEventListener('change', () => renderFilePreview(fileInput.files[0] ?? null));
    }

    if (btnCloseFilePrev) {
        btnCloseFilePrev.addEventListener('click', () => {
            fileWrapper.classList.add('hidden');
            fileContent.innerHTML = '';
            fileInput.value = '';
        });
    }

    // ── Drag & drop ──────────────────────────────────────────────
    if (dropZone) {
        ['dragenter', 'dragover'].forEach(evt =>
            dropZone.addEventListener(evt, e => { e.preventDefault(); dropZone.classList.add('drag-over'); })
        );
        ['dragleave', 'drop'].forEach(evt =>
            dropZone.addEventListener(evt, e => { e.preventDefault(); dropZone.classList.remove('drag-over'); })
        );
        dropZone.addEventListener('drop', e => {
            const file = e.dataTransfer.files[0];
            if (!file) return;
            const dt = new DataTransfer();
            dt.items.add(file);
            fileInput.files = dt.files;
            renderFilePreview(file);
        });
    }

});