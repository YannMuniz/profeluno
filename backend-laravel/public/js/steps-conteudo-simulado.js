/**
 * steps-conteudo-simulado.js
 *
 * Cole este bloco DENTRO do callback do seu DOMContentLoaded existente
 * em sala-professor.js — ou carregue este arquivo separado após sala-professor.js.
 *
 * Cobre:
 *  - Step 2: seleção de conteúdo + preview iframe/fallback
 *  - Step 3: tabs de simulado + preview de questões do simulado existente
 */

/* ================================================================
   STEP 2 — CONTEÚDO: Preview iframe/fallback ao selecionar card
   ================================================================ */

(function initConteudoPreview() {

    // Tipos que conseguimos exibir num iframe
    const IFRAME_TYPES = ['link', 'mp4'];

    // Estrutura de dados dos conteúdos: montada a partir dos atributos
    // data-url e data-tipo em cada <label.conteudo-card>
    // (adicione data-url="{{ $conteudo['url'] }}" data-tipo="{{ $conteudo['tipo'] }}"
    //  nas labels do Blade — veja instrução abaixo)

    const previewWrapper = document.getElementById('conteudoPreviewWrapper');
    const previewIframe  = document.getElementById('conteudoIframe');
    const previewFallback = document.getElementById('conteudoFallback');
    const previewTitle   = document.getElementById('conteudoPreviewTitle');
    const btnNovaAba     = document.getElementById('btnAbrirNovaAba');

    if (!previewWrapper) return; // bloco não está na página

    function showPreview(url, tipo, titulo) {
        previewWrapper.classList.add('visible');

        if (previewTitle) {
            previewTitle.textContent = titulo || 'Conteúdo selecionado';
        }

        if (btnNovaAba && url) {
            btnNovaAba.href = url;
            btnNovaAba.style.display = '';
        } else if (btnNovaAba) {
            btnNovaAba.style.display = 'none';
        }

        const canIframe = url && IFRAME_TYPES.includes(tipo);

        if (canIframe) {
            previewIframe.src = url;
            previewIframe.style.display = '';
            if (previewFallback) previewFallback.style.display = 'none';
        } else {
            previewIframe.src = '';
            previewIframe.style.display = 'none';
            if (previewFallback) {
                previewFallback.style.display = '';
                // Mensagem adequada por tipo
                const msgs = {
                    pdf:  { icon: 'fas fa-file-pdf',        text: 'PDF — clique em "Abrir em nova aba" para visualizar.' },
                    pptx: { icon: 'fas fa-file-powerpoint',  text: 'Apresentação PPTX — visualize em nova aba.' },
                    docx: { icon: 'fas fa-file-word',        text: 'Documento DOCX — visualize em nova aba.' },
                    mp4:  { icon: 'fas fa-film',             text: 'Vídeo — carregando...' },
                    link: { icon: 'fas fa-link',             text: 'Link externo — clique em "Abrir em nova aba".' },
                };
                const m = msgs[tipo] || { icon: 'fas fa-file', text: 'Prévia não disponível para este tipo de arquivo.' };
                previewFallback.innerHTML = `
                    <i class="${m.icon}"></i>
                    <p>${m.text}</p>
                `;
            }
        }
    }

    function hidePreview() {
        previewWrapper.classList.remove('visible');
        if (previewIframe) previewIframe.src = '';
    }

    // Escuta mudança nos radios de conteúdo
    document.querySelectorAll('.conteudo-radio').forEach(radio => {
        radio.addEventListener('change', function () {
            // Opção "sem conteúdo"
            if (!this.value) {
                hidePreview();
                return;
            }

            const card  = this.closest('.conteudo-card');
            const url   = card?.dataset.url  || '';
            const tipo  = card?.dataset.tipo  || 'other';
            const titulo = card?.querySelector('strong')?.textContent || '';

            showPreview(url, tipo, titulo);
        });
    });

    // Se já houver uma seleção (old()), aciona imediatamente
    const checkedRadio = document.querySelector('.conteudo-radio:checked');
    if (checkedRadio && checkedRadio.value) {
        checkedRadio.dispatchEvent(new Event('change'));
    }
})();


/* ================================================================
   STEP 3 — SIMULADO: Preview de questões ao selecionar simulado
   ================================================================ */

(function initSimuladoPreview() {

    const previewContainer = document.getElementById('simuladoQuestoesPreview');
    const previewList      = document.getElementById('simuladoQuestoesList');
    const previewCount     = document.getElementById('simuladoQuestoesCount');
    const PREVIEW_MAX      = 5; // questões exibidas por padrão

    if (!previewContainer) return;

    /**
     * Monta o HTML de uma questão para o preview.
     * Os dados vem do objeto window.simuladosData (injetado pelo Blade).
     */
    function renderQuestao(q, num) {
        const letras = ['A', 'B', 'C', 'D', 'E'];
        const alternativas = [
            { key: 'questao_a', letra: 'A' },
            { key: 'questao_b', letra: 'B' },
            { key: 'questao_c', letra: 'C' },
            { key: 'questao_d', letra: 'D' },
            { key: 'questao_e', letra: 'E' },
        ].filter(a => q[a.key]);

        const altsHTML = alternativas.map(a => {
            // questao_correta = 1(A), 2(B), 3(C), 4(D), 5(E)
            const letraIndex = letras.indexOf(a.letra) + 1;
            const isCorreta  = parseInt(q.questao_correta) === letraIndex;
            return `
                <div class="sq-alt ${isCorreta ? 'correta' : ''}">
                    <span class="sq-alt-letter">${a.letra}</span>
                    <span class="sq-alt-text">${escapeHtml(q[a.key])}</span>
                    ${isCorreta ? '<i class="fas fa-check" style="margin-left:auto;font-size:11px;"></i>' : ''}
                </div>
            `;
        }).join('');

        return `
            <div class="sq-item">
                <span class="sq-num">${num}</span>
                <div class="sq-content">
                    <p class="sq-enunciado">${escapeHtml(q.enunciado || '—')}</p>
                    <div class="sq-alternativas">${altsHTML}</div>
                </div>
            </div>
        `;
    }

    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function showSimuladoPreview(idSimulado) {
        // window.simuladosData deve ser injetado no Blade (ver instrução abaixo)
        const data = window.simuladosData || {};
        const sim  = data[idSimulado];

        if (!sim || !sim.questoes || !sim.questoes.length) {
            previewContainer.classList.remove('visible');
            return;
        }

        const questoes = sim.questoes;
        if (previewCount) {
            previewCount.textContent = `${questoes.length} questão(ões)`;
        }

        // Renderiza até PREVIEW_MAX questões
        const visíveis = questoes.slice(0, PREVIEW_MAX);
        const restantes = questoes.length - visíveis.length;

        let html = visíveis.map((q, i) => renderQuestao(q, i + 1)).join('');

        if (restantes > 0) {
            html += `
                <div class="simulado-preview-more" id="btnVerMaisQuestoes">
                    <i class="fas fa-chevron-down"></i>
                    Ver mais ${restantes} questão(ões)
                </div>
            `;
        }

        if (previewList) previewList.innerHTML = html;
        previewContainer.classList.add('visible');

        // Botão "ver mais"
        const btnMais = document.getElementById('btnVerMaisQuestoes');
        btnMais?.addEventListener('click', () => {
            const todasHTML = questoes.map((q, i) => renderQuestao(q, i + 1)).join('');
            previewList.innerHTML = todasHTML;
        });
    }

    function hideSimuladoPreview() {
        previewContainer.classList.remove('visible');
        if (previewList) previewList.innerHTML = '';
    }

    // Escuta mudança nos radios de simulado existente
    document.querySelectorAll('.simulado-radio').forEach(radio => {
        radio.addEventListener('change', function () {
            if (this.value) {
                showSimuladoPreview(this.value);
            } else {
                hideSimuladoPreview();
            }
        });
    });

    // Se já houver seleção (old()), aciona imediatamente
    const checkedSim = document.querySelector('.simulado-radio:checked');
    if (checkedSim && checkedSim.value) {
        checkedSim.dispatchEvent(new Event('change'));
    }

    // Esconde preview ao trocar de tab
    document.querySelectorAll('.simulado-tab').forEach(tab => {
        tab.addEventListener('click', function () {
            if (this.dataset.simuladoTab !== 'existente') {
                hideSimuladoPreview();
            } else {
                // Re-aciona se já havia seleção
                const checked = document.querySelector('.simulado-radio:checked');
                if (checked && checked.value) showSimuladoPreview(checked.value);
            }
        });
    });
})();