/**
 * video-aula-professor.js
 * Lógica de interface da sala de aula ao vivo – visão do Professor
 */

document.addEventListener('DOMContentLoaded', () => {

    // ── Tabs da sidebar ──────────────────────────────────────────────
    const tabs        = document.querySelectorAll('.sidebar-tab');
    const tabContents = document.querySelectorAll('.sidebar-content');

    window.switchTab = function(tabName) {
        tabs.forEach(t => t.classList.remove('active'));
        tabContents.forEach(c => c.style.display = 'none');

        document.querySelector(`[data-tab="${tabName}"]`).classList.add('active');
        document.getElementById(`${tabName}-tab`).style.display = 'flex';
    };

    // Exibe a primeira tab ao carregar
    switchTab('chat');


    // ── Timer da aula ────────────────────────────────────────────────
    let seconds = 0;
    const timerEl = document.getElementById('class-timer');

    setInterval(() => {
        seconds++;
        const h = String(Math.floor(seconds / 3600)).padStart(2, '0');
        const m = String(Math.floor((seconds % 3600) / 60)).padStart(2, '0');
        const s = String(seconds % 60).padStart(2, '0');
        if (timerEl) timerEl.textContent = `${h}:${m}:${s}`;
    }, 1000);


    // ── Botões de controle (microfone, câmera, etc.) ─────────────────
    document.querySelectorAll('.control-btn[data-toggle]').forEach(btn => {
        btn.addEventListener('click', () => {
            const type = btn.dataset.toggle;

            if (type === 'mic') {
                btn.classList.toggle('danger');
                const icon = btn.querySelector('i');
                icon.classList.toggle('fa-microphone');
                icon.classList.toggle('fa-microphone-slash');
            }

            if (type === 'camera') {
                btn.classList.toggle('active');
                const icon = btn.querySelector('i');
                icon.classList.toggle('fa-video');
                icon.classList.toggle('fa-video-slash');
            }

            if (type === 'screen') {
                btn.classList.toggle('active');
                const badge = document.querySelector('.screen-sharing-badge');
                if (badge) badge.classList.toggle('active');
            }

            if (type === 'record') {
                btn.classList.toggle('success');
                const icon = btn.querySelector('i');
                icon.classList.toggle('fa-circle');
                icon.classList.toggle('fa-stop-circle');
                const label = btn.dataset.label || '';
                btn.title = btn.classList.contains('success') ? 'Parar gravação' : 'Gravar aula';
            }
        });
    });


    // ── Chat ─────────────────────────────────────────────────────────
    const chatInput    = document.getElementById('chat-input');
    const chatMessages = document.getElementById('chat-messages');
    const btnSend      = document.getElementById('btn-send');

    function sendMessage() {
        const text = chatInput.value.trim();
        if (!text) return;

        const now  = new Date();
        const time = `${String(now.getHours()).padStart(2,'0')}:${String(now.getMinutes()).padStart(2,'0')}`;

        const msg = document.createElement('div');
        msg.classList.add('chat-message');
        msg.innerHTML = `
            <div class="message-header">
                <div class="message-avatar" style="background: linear-gradient(135deg,#7367f0,#9f8cfe);">JS</div>
                <span class="message-name">Prof. João Silva <span style="font-size:10px;color:var(--primary-color);margin-left:4px;">(você)</span></span>
                <span class="message-time">${time}</span>
            </div>
            <div class="message-text teacher">${escapeHtml(text)}</div>
        `;

        chatMessages.appendChild(msg);
        chatMessages.scrollTop = chatMessages.scrollHeight;
        chatInput.value = '';
    }

    if (btnSend) btnSend.addEventListener('click', sendMessage);

    if (chatInput) {
        chatInput.addEventListener('keydown', e => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });
    }

    function escapeHtml(str) {
        const d = document.createElement('div');
        d.appendChild(document.createTextNode(str));
        return d.innerHTML;
    }


    // ── "Mão levantada" – alertas e badge no strip ───────────────────
    const handAlertContainer = document.getElementById('hand-alert-container');

    // Simula aluno levantando a mão (substitua por evento WebSocket/API real)
    window.simulateHandRaise = function(studentName) {
        if (!handAlertContainer) return;

        const alert = document.createElement('div');
        alert.classList.add('hand-alert');
        alert.innerHTML = `
            <i class="fas fa-hand-paper"></i>
            <span>${escapeHtml(studentName)} levantou a mão</span>
            <button class="btn-allow" onclick="allowStudent('${escapeHtml(studentName)}', this.closest('.hand-alert'))">
                Permitir falar
            </button>
            <button style="background:transparent;border:none;color:white;cursor:pointer;margin-left:4px;" 
                    onclick="this.closest('.hand-alert').remove()">
                <i class="fas fa-times"></i>
            </button>
        `;
        handAlertContainer.appendChild(alert);
        setTimeout(() => alert.remove(), 15000);
    };

    window.allowStudent = function(name, alertEl) {
        alertEl.remove();
        addSystemMessage(`✅ ${name} pode falar agora.`);
    };

    function addSystemMessage(text) {
        if (!chatMessages) return;
        const el = document.createElement('div');
        el.style.cssText = 'text-align:center;font-size:11px;color:var(--text-secondary);padding:6px 0;';
        el.textContent = text;
        chatMessages.appendChild(el);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // Dispara um exemplo após 5 s (remova em produção)
    setTimeout(() => simulateHandRaise('Ana Costa'), 5000);


    // ── Mutar todos ──────────────────────────────────────────────────
    window.muteAll = function() {
        document.querySelectorAll('.participant-video:not(.me) .participant-status').forEach(s => {
            s.classList.add('muted');
            s.innerHTML = '<i class="fas fa-microphone-slash"></i>';
        });
        addSystemMessage('🔇 Todos os alunos foram mutados.');
    };


    // ── Modal de upload de material ──────────────────────────────────
    const modalOverlay    = document.getElementById('modal-upload');
    const btnOpenModal    = document.getElementById('btn-add-material');
    const btnCloseModal   = document.getElementById('btn-modal-cancel');
    const btnConfirmUpload= document.getElementById('btn-modal-confirm');
    const dropZone        = document.getElementById('drop-zone');
    const fileInput       = document.getElementById('file-input');
    const materialsList   = document.getElementById('materials-list');

    if (btnOpenModal)  btnOpenModal.addEventListener('click',  () => modalOverlay.classList.add('open'));
    if (btnCloseModal) btnCloseModal.addEventListener('click', () => modalOverlay.classList.remove('open'));

    if (modalOverlay) {
        modalOverlay.addEventListener('click', e => {
            if (e.target === modalOverlay) modalOverlay.classList.remove('open');
        });
    }

    if (dropZone) {
        dropZone.addEventListener('click', () => fileInput && fileInput.click());

        dropZone.addEventListener('dragover', e => {
            e.preventDefault();
            dropZone.classList.add('drag-over');
        });

        dropZone.addEventListener('dragleave', () => dropZone.classList.remove('drag-over'));

        dropZone.addEventListener('drop', e => {
            e.preventDefault();
            dropZone.classList.remove('drag-over');
            if (e.dataTransfer.files.length) handleFile(e.dataTransfer.files[0]);
        });
    }

    if (fileInput) {
        fileInput.addEventListener('change', () => {
            if (fileInput.files.length) handleFile(fileInput.files[0]);
        });
    }

    let pendingFile = null;

    function handleFile(file) {
        pendingFile = file;
        if (dropZone) {
            dropZone.innerHTML = `<i class="fas fa-check-circle" style="color:var(--success-color)"></i>
                                  <span>${escapeHtml(file.name)}</span>`;
        }
    }

    if (btnConfirmUpload) {
        btnConfirmUpload.addEventListener('click', () => {
            const titleInput = document.getElementById('material-title');
            const title = titleInput ? titleInput.value.trim() : (pendingFile ? pendingFile.name : 'Novo material');

            if (!title && !pendingFile) return;

            addMaterialCard(title || pendingFile.name, pendingFile);
            modalOverlay.classList.remove('open');

            // Reset
            pendingFile = null;
            if (fileInput) fileInput.value = '';
            if (dropZone) dropZone.innerHTML = `<i class="fas fa-cloud-upload-alt"></i><span>Arraste um arquivo ou clique para selecionar</span>`;
            if (titleInput) titleInput.value = '';
        });
    }

    function addMaterialCard(title, file) {
        if (!materialsList) return;

        let iconClass = 'slide', iconSymbol = 'fa-file-alt';
        if (file) {
            const ext = file.name.split('.').pop().toLowerCase();
            if (ext === 'pdf')                       { iconClass = 'pdf';   iconSymbol = 'fa-file-pdf'; }
            else if (['mp4','mov','avi'].includes(ext)){ iconClass = 'video'; iconSymbol = 'fa-film'; }
            else if (['ppt','pptx'].includes(ext))   { iconClass = 'slide'; iconSymbol = 'fa-file-powerpoint'; }
        }

        const size = file ? `${(file.size / 1024).toFixed(0)} KB` : '—';

        const card = document.createElement('div');
        card.classList.add('material-item');
        card.innerHTML = `
            <div class="material-header">
                <div class="material-icon ${iconClass}"><i class="fas ${iconSymbol}"></i></div>
                <div class="material-info">
                    <h4>${escapeHtml(title)}</h4>
                    <p>${size}</p>
                </div>
            </div>
            <div class="material-actions">
                <button class="btn-material"><i class="fas fa-share-alt"></i> Compartilhar</button>
                <button class="btn-material danger" onclick="this.closest('.material-item').remove()">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;

        materialsList.appendChild(card);
    }


    // ── Encerrar aula ────────────────────────────────────────────────
    const btnEnd = document.querySelector('.btn-end-class');
    if (btnEnd) {
        btnEnd.addEventListener('click', () => {
            if (confirm('Tem certeza que deseja encerrar a aula para todos?')) {
                alert('Aula encerrada. Redirecionando...');
                // window.location.href = '/professor/dashboard';
            }
        });
    }

});