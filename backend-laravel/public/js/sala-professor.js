/**
 * sala-professor.js
 * public/js/sala-professor.js
 *
 * Cobre: sala-buscar, sala-criar, material-criar, simulado-criar
 */

document.addEventListener('DOMContentLoaded', () => {

    /* ========================================================
       1. TABS (sala-buscar)
       ======================================================== */
    const tabBtns = document.querySelectorAll('.tab-btn');
    tabBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            tabBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            const tab = btn.dataset.tab;
            // Scroll suave para a seção correspondente
            const target = document.getElementById(`tab-${tab}`);
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    /* ========================================================
       2. BUSCA + FILTROS (sala-buscar)
       ======================================================== */
    const searchInput  = document.getElementById('searchSalas');
    const filterStatus = document.getElementById('filterStatus');
    const filterMateria= document.getElementById('filterMateria');
    const classCards   = document.querySelectorAll('.class-card[data-status]');

    function filterCards() {
        const q      = (searchInput?.value  || '').toLowerCase();
        const status = (filterStatus?.value || '').toLowerCase();
        const mat    = (filterMateria?.value|| '').toLowerCase();

        classCards.forEach(card => {
            const titulo  = card.dataset.titulo  || '';
            const cardSt  = card.dataset.status  || '';
            const cardMat = card.dataset.materia || '';

            const matchQ   = !q      || titulo.includes(q) || cardMat.includes(q);
            const matchSt  = !status || cardSt === status;
            const matchMat = !mat    || cardMat === mat;

            card.style.display = (matchQ && matchSt && matchMat) ? '' : 'none';
        });
    }

    searchInput?.addEventListener('input',  filterCards);
    filterStatus?.addEventListener('change', filterCards);
    filterMateria?.addEventListener('change', filterCards);

    /* ========================================================
       3. VIEW TOGGLE (grid / list)
       ======================================================== */
    const viewBtns = document.querySelectorAll('.view-btn-toggle');
    const grid     = document.getElementById('classesGrid');

    viewBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            viewBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            if (grid) {
                grid.classList.toggle('list-view', btn.dataset.view === 'list');
            }
        });
    });

    /* ========================================================
       4. MODAL DE EXCLUSÃO (sala-buscar)
       ======================================================== */
    const deleteModal  = document.getElementById('deleteModal');
    const deleteForm   = document.getElementById('deleteForm');
    const cancelDelete = document.getElementById('cancelDelete');

    document.querySelectorAll('.btn-delete-sala').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.id;
            if (deleteForm) {
                deleteForm.action = `/professor/sala/${id}`;
            }
            deleteModal?.classList.add('active');
        });
    });

    cancelDelete?.addEventListener('click', () => {
        deleteModal?.classList.remove('active');
    });

    deleteModal?.addEventListener('click', (e) => {
        if (e.target === deleteModal) deleteModal.classList.remove('active');
    });

    /* ========================================================
       5. LIVE TIMER (sala-buscar — aula ativa)
       ======================================================== */
    const timerEl = document.getElementById('live-timer');
    if (timerEl) {
        let seconds = 0;
        setInterval(() => {
            seconds++;
            const h = Math.floor(seconds / 3600);
            const m = Math.floor((seconds % 3600) / 60);
            const s = seconds % 60;
            timerEl.textContent = h > 0
                ? `${pad(h)}:${pad(m)}:${pad(s)}`
                : `${pad(m)}:${pad(s)}`;
        }, 1000);
    }

    /* ========================================================
       6. COUNTDOWN (salas agendadas)
       ======================================================== */
    document.querySelectorAll('.countdown-badge[data-start]').forEach(badge => {
        const start = new Date(badge.dataset.start);
        const label = badge.querySelector('.countdown-label');

        function updateCountdown() {
            const diff = start - Date.now();
            if (diff <= 0) {
                label.textContent = 'Iniciar agora!';
                badge.style.borderColor = 'var(--success-color)';
                badge.style.color = 'var(--success-color)';
                return;
            }
            const hh = Math.floor(diff / 3600000);
            const mm = Math.floor((diff % 3600000) / 60000);
            label.textContent = hh > 0 ? `${hh}h ${pad(mm)}min` : `${mm}min`;
        }

        updateCountdown();
        setInterval(updateCountdown, 30000);
    });

    /* ========================================================
       7. FORM CRIAR SALA — STEPS
       ======================================================== */
    let currentStep = 1;

    function goToStep(n) {
        document.querySelectorAll('.form-step').forEach(s => s.classList.remove('active'));
        document.querySelector(`#step-${n}`)?.classList.add('active');

        document.querySelectorAll('.step').forEach((s, i) => {
            s.classList.remove('active', 'done');
            if (i + 1 < n)  s.classList.add('done');
            if (i + 1 === n) s.classList.add('active');
        });

        currentStep = n;
    }

    document.getElementById('nextToStep2')?.addEventListener('click', () => {
        if (validateStep1()) goToStep(2);
    });

    document.getElementById('nextToStep3')?.addEventListener('click', () => goToStep(3));

    document.getElementById('backToStep1')?.addEventListener('click', () => goToStep(1));
    document.getElementById('backToStep2')?.addEventListener('click', () => goToStep(2));

    function validateStep1() {
        const titulo = document.getElementById('titulo');
        const materia= document.getElementById('materia');
        const qtd    = document.getElementById('qtd_alunos');
        const url    = document.getElementById('url');
        let ok = true;
        [titulo, materia, qtd, url].forEach(el => {
            if (el && !el.value.trim()) {
                el.classList.add('is-invalid');
                ok = false;
            } else {
                el?.classList.remove('is-invalid');
            }
        });
        return ok;
    }

    /* ========================================================
       8. FORM CRIAR SALA — PRÉVIA (sidebar)
       ======================================================== */
    const tituloInput  = document.getElementById('titulo');
    const materiaInput = document.getElementById('materia');
    const qtdInput     = document.getElementById('qtd_alunos');
    const dateInput    = document.getElementById('data_hora_inicio');
    const statusSelect = document.getElementById('status');
    const tituloCount  = document.getElementById('tituloCount');

    function updatePreview() {
        const t = tituloInput?.value  || 'Título da Sala';
        const m = materiaInput?.value || 'Matéria';
        const q = qtdInput?.value     || '0';
        const d = dateInput?.value
            ? new Date(dateInput.value).toLocaleDateString('pt-BR')
            : 'Sem data';
        const s = statusSelect?.value || 'pending';

        document.getElementById('previewTitulo')?.replaceChildren(document.createTextNode(t));
        document.getElementById('previewMateria')?.replaceChildren(document.createTextNode(m));
        document.getElementById('previewAlunos')?.replaceChildren(document.createTextNode(q));
        document.getElementById('previewData')?.replaceChildren(document.createTextNode(d));

        const ribbon = document.getElementById('previewRibbon');
        if (ribbon) {
            ribbon.className = `mini-ribbon ${s}`;
            ribbon.innerHTML = s === 'active'
                ? '<i class="fas fa-circle"></i> Ao Vivo'
                : '<i class="fas fa-clock"></i> Agendada';
        }

        if (tituloCount) tituloCount.textContent = tituloInput?.value.length || 0;
    }

    [tituloInput, materiaInput, qtdInput, dateInput, statusSelect].forEach(el => {
        el?.addEventListener('input', updatePreview);
        el?.addEventListener('change', updatePreview);
    });

    /* ========================================================
       9. TOGGLE MATERIAL / SIMULADO FIELDS
       ======================================================== */
    function setupToggle(checkboxId, fieldsId) {
        const cb     = document.getElementById(checkboxId);
        const fields = document.getElementById(fieldsId);
        if (!cb || !fields) return;
        cb.addEventListener('change', () => {
            fields.classList.toggle('visible', cb.checked);
        });
    }

    setupToggle('addMaterial', 'materialFields');
    setupToggle('addSimulado', 'simuladoFields');

    /* ========================================================
       10. QUESTÕES — CRUD DINÂMICO (sala-criar + simulado-criar)
       ======================================================== */
    let questaoCount = 0;

    function getTemplate() {
        return document.getElementById('questaoTemplate');
    }

    function buildQuestaoHTML(index, num) {
        const tpl = getTemplate();
        if (!tpl) return null;
        const clone = tpl.content.cloneNode(true);
        // Substituir placeholders
        const wrap = document.createElement('div');
        wrap.appendChild(clone);
        let html = wrap.innerHTML
            .replaceAll('__INDEX__', index)
            .replaceAll('__NUM__', num);
        wrap.innerHTML = html;
        return wrap.firstElementChild;
    }

    function addQuestao(container) {
        if (!container) return;
        questaoCount++;
        const el = buildQuestaoHTML(questaoCount, questaoCount);
        if (!el) return;
        container.appendChild(el);

        // Evento de remoção
        el.querySelector('.btn-remove-questao')?.addEventListener('click', () => {
            el.remove();
            renumberQuestoes(container);
        });

        // Mover cima/baixo
        el.querySelector('.btn-move-up')?.addEventListener('click', () => {
            const prev = el.previousElementSibling;
            if (prev) container.insertBefore(el, prev);
            renumberQuestoes(container);
        });

        el.querySelector('.btn-move-down')?.addEventListener('click', () => {
            const next = el.nextElementSibling;
            if (next) container.insertBefore(next, el);
            renumberQuestoes(container);
        });

        updateSummary(container);
        updateNavigation(container);
        el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function renumberQuestoes(container) {
        container.querySelectorAll('.questao-block').forEach((block, i) => {
            const num = i + 1;
            const badge = block.querySelector('.questao-num-badge');
            const label = block.querySelector('.questao-num-label');
            const legacyNum = block.querySelector('.questao-num strong');
            if (badge) badge.textContent = num;
            if (label) label.textContent = `Questão ${num}`;
            if (legacyNum) legacyNum.textContent = num;
        });
        updateSummary(container);
        updateNavigation(container);
    }

    function updateSummary(container) {
        const blocks = container.querySelectorAll('.questao-block');
        const total  = blocks.length;
        let completas = 0;

        blocks.forEach(b => {
            const enunciado = b.querySelector('textarea')?.value?.trim();
            const checked   = b.querySelector('input[type="radio"]:checked');
            if (enunciado && checked) completas++;
        });

        const faltando = total - completas;
        const pct = total ? Math.round((completas / total) * 100) : 0;

        const totalEl     = document.getElementById('summaryTotal');
        const completasEl = document.getElementById('summaryCompletas');
        const faltandoEl  = document.getElementById('summaryFaltando');
        const fillEl      = document.getElementById('progressFill');
        const labelEl     = document.getElementById('progressLabel');
        const counterEl   = document.getElementById('totalQuestoesLabel');

        if (totalEl)     totalEl.textContent     = total;
        if (completasEl) completasEl.textContent = completas;
        if (faltandoEl)  faltandoEl.textContent  = faltando;
        if (fillEl)      fillEl.style.width       = `${pct}%`;
        if (labelEl)     labelEl.textContent      = total ? `${pct}% preenchido` : 'Preencha as questões';
        if (counterEl)   counterEl.textContent    = `${total} questão(ões)`;
    }

    function updateNavigation(container) {
        const nav = document.getElementById('questaoNav');
        if (!nav) return;
        const blocks = container.querySelectorAll('.questao-block');
        if (!blocks.length) {
            nav.innerHTML = '<p class="nav-empty">Nenhuma questão ainda.</p>';
            return;
        }
        nav.innerHTML = '';
        blocks.forEach((_, i) => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'nav-questao-btn';
            btn.textContent = i + 1;
            btn.addEventListener('click', () => {
                blocks[i].scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
            nav.appendChild(btn);
        });
    }

    // sala-criar: container dentro do step-3
    const addQuestaoStep = document.getElementById('addQuestao');
    const containerStep  = document.getElementById('questoesContainer');

    addQuestaoStep?.addEventListener('click', () => {
        addQuestao(containerStep);
    });

    // simulado-criar: mesmo ID mas página diferente — já coberto acima.
    // Se o toggle de simulado estiver na sala-criar:
    document.getElementById('addSimulado')?.addEventListener('change', function () {
        if (this.checked && containerStep && !containerStep.children.length) {
            addQuestao(containerStep);
        }
    });

    /* ========================================================
       11. FILE DROP ZONE
       ======================================================== */
    function setupDropZone(zoneId, inputId, previewId) {
        const zone    = document.getElementById(zoneId);
        const input   = document.getElementById(inputId);
        const preview = document.getElementById(previewId);
        if (!zone || !input) return;

        zone.addEventListener('dragover', (e) => {
            e.preventDefault();
            zone.classList.add('drag-over');
        });

        zone.addEventListener('dragleave', () => zone.classList.remove('drag-over'));

        zone.addEventListener('drop', (e) => {
            e.preventDefault();
            zone.classList.remove('drag-over');
            if (e.dataTransfer.files[0]) {
                showFilePreview(e.dataTransfer.files[0], preview);
            }
        });

        input.addEventListener('change', () => {
            if (input.files[0]) showFilePreview(input.files[0], preview);
        });
    }

    function showFilePreview(file, previewEl) {
        if (!previewEl) return;
        const size = (file.size / 1024 / 1024).toFixed(2);
        previewEl.classList.remove('hidden');
        previewEl.innerHTML = `
            <i class="fas fa-check-circle"></i>
            <span><strong>${file.name}</strong> — ${size} MB</span>
            <button type="button" class="icon-btn danger" id="clearFile" style="margin-left:auto;">
                <i class="fas fa-times"></i>
            </button>
        `;
        previewEl.querySelector('#clearFile')?.addEventListener('click', () => {
            previewEl.classList.add('hidden');
            previewEl.innerHTML = '';
        });
    }

    setupDropZone('fileDropZone',    'materialFile', 'filePreview');
    setupDropZone('matFileDropZone', 'mat_file',     'matFilePreview');

    /* ========================================================
       12. MATERIAL PREVIEW (material-criar sidebar)
       ======================================================== */
    const matTituloInput = document.getElementById('mat_titulo');
    const matTypeSelect  = document.getElementById('mat_type');

    const typeIcons = {
        pdf:      'fas fa-file-pdf',
        slide:    'fas fa-desktop',
        video:    'fas fa-film',
        document: 'fas fa-file-word',
        other:    'fas fa-file',
        '':       'fas fa-file',
    };

    function updateMatPreview() {
        const titulo  = matTituloInput?.value || 'Título do material';
        const type    = matTypeSelect?.value  || '';
        const iconCls = typeIcons[type] || 'fas fa-file';

        const iconEl  = document.getElementById('matPreviewIcon');
        const titleEl = document.getElementById('matPreviewTitulo');
        const typeEl  = document.getElementById('matPreviewType');

        if (iconEl)  iconEl.innerHTML = `<i class="${iconCls}"></i>`;
        if (titleEl) titleEl.textContent = titulo;
        if (typeEl)  typeEl.textContent  = type ? type.toUpperCase() : 'Tipo não selecionado';
    }

    matTituloInput?.addEventListener('input',  updateMatPreview);
    matTypeSelect?.addEventListener('change', updateMatPreview);

    /* ========================================================
       13. CHAR COUNT (título)
       ======================================================== */
    tituloInput?.addEventListener('input', () => {
        if (tituloCount) tituloCount.textContent = tituloInput.value.length;
    });

    /* ========================================================
       14. HELPERS
       ======================================================== */
    function pad(n) {
        return String(n).padStart(2, '0');
    }
    /* ========================================================
    15. POPULAR QUESTÕES NO EDIT (window.questoesIniciais)
    ======================================================== */
    if (window.questoesIniciais && window.questoesIniciais.length > 0) {
        const container = document.getElementById('questoesContainer');
        if (container) {
            window.questoesIniciais.forEach((q) => {
                // Reutiliza a função já existente para criar o bloco
                addQuestao(container);

                // Pega o último bloco inserido
                const blocks = container.querySelectorAll('.questao-block');
                const block  = blocks[blocks.length - 1];

                // Preenche o enunciado
                const enunciadoEl = block.querySelector('textarea');
                if (enunciadoEl) enunciadoEl.value = q.enunciado ?? '';

                // Preenche as alternativas
                const campos = {
                    questao_a: 'questao_a',
                    questao_b: 'questao_b',
                    questao_c: 'questao_c',
                    questao_d: 'questao_d',
                    questao_e: 'questao_e',
                };

                Object.entries(campos).forEach(([key, _]) => {
                    // O name do input é questoes[N][questao_a], buscamos pelo sufixo
                    const input = block.querySelector(`input[name$="[${key}]"]`);
                    if (input) input.value = q[key] ?? '';
                });

                // Marca o radio da questão correta
                if (q.questao_correta) {
                    const radio = block.querySelector(
                        `input[type="radio"][value="${q.questao_correta}"]`
                    );
                    if (radio) radio.checked = true;
                }
            });

            // Atualiza o resumo e navegação após popular tudo
            updateSummary(container);
            updateNavigation(container);
        }
    }
});