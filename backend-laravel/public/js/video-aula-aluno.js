// Video Aula JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Initialize sidebar tabs
        // Inicializar abas da barra lateral
    const chatTab = document.getElementById('chatTab');
    const participantsTab = document.getElementById('participantsTab');
    const materialsTab = document.getElementById('materialsTab');
    
    const chatSection = document.getElementById('chatSection');
    const participantsSection = document.getElementById('participantsSection');
    const materialsSection = document.getElementById('materialsSection');

    if (chatTab) {
        chatTab.addEventListener('click', () => showSection('chat'));
        participantsTab.addEventListener('click', () => showSection('participants'));
        materialsTab.addEventListener('click', () => showSection('materials'));
    }

    function showSection(section) {
        // Hide all sections
            // Ocultar todas as seções
        if (chatSection) chatSection.style.display = 'none';
        if (participantsSection) participantsSection.style.display = 'none';
        if (materialsSection) materialsSection.style.display = 'none';

        // Remove active class from all tabs
            // Remover classe ativa de todas as abas
        document.querySelectorAll('.sidebar-tab').forEach(tab => {
            tab.classList.remove('active');
        });

        // Show selected section and mark tab as active
            // Mostrar seção selecionada e marcar aba como ativa
        if (section === 'chat') {
            if (chatSection) chatSection.style.display = 'flex';
            chatTab.classList.add('active');
        } else if (section === 'participants') {
            if (participantsSection) participantsSection.style.display = 'flex';
            participantsTab.classList.add('active');
        } else if (section === 'materials') {
            if (materialsSection) materialsSection.style.display = 'flex';
            materialsTab.classList.add('active');
        }
    }

    // Chat functionality
        // Funcionalidade de Chat
    const sendBtn = document.getElementById('sendBtn');
    const chatInput = document.getElementById('chatInput');
    const chatMessages = document.getElementById('chatMessages');

    if (sendBtn) {
        sendBtn.addEventListener('click', sendMessage);
        chatInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });
    }

    function sendMessage() {
        const message = chatInput.value.trim();
        if (message !== '') {
            const messageElement = document.createElement('div');
            messageElement.className = 'chat-message';
            messageElement.innerHTML = `
                <div class="message-header">
                    <div class="message-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <p class="message-name">Você</p>
                        <p class="message-time">${getCurrentTime()}</p>
                    </div>
                </div>
                <p class="message-text">${escapeHtml(message)}</p>
            `;
            chatMessages.appendChild(messageElement);
            chatMessages.scrollTop = chatMessages.scrollHeight;
            chatInput.value = '';
        }
    }

    // Control buttons
        // Botões de Controle
    const micBtn = document.getElementById('micBtn');
    const cameraBtn = document.getElementById('cameraBtn');
    const screenBtn = document.getElementById('screenBtn');
    const chatBtn = document.getElementById('chatBtn');
    const participantsBtn = document.getElementById('participantsBtn');

    if (micBtn) {
        micBtn.addEventListener('click', function() {
            this.classList.toggle('active');
            const icon = this.querySelector('i');
            if (icon.classList.contains('fa-microphone-slash')) {
                icon.classList.remove('fa-microphone-slash');
                icon.classList.add('fa-microphone');
            } else {
                icon.classList.remove('fa-microphone');
                icon.classList.add('fa-microphone-slash');
            }
        });
    }

    if (cameraBtn) {
        cameraBtn.addEventListener('click', function() {
            this.classList.toggle('active');
            const icon = this.querySelector('i');
            if (icon.classList.contains('fa-video-slash')) {
                icon.classList.remove('fa-video-slash');
                icon.classList.add('fa-video');
            } else {
                icon.classList.remove('fa-video');
                icon.classList.add('fa-video-slash');
            }
        });
    }

    if (screenBtn) {
        screenBtn.addEventListener('click', function() {
            this.classList.toggle('active');
        });
    }

    if (chatBtn) {
        chatBtn.addEventListener('click', function() {
            showSection('chat');
        });
    }

    if (participantsBtn) {
        participantsBtn.addEventListener('click', function() {
            showSection('participants');
        });
    }

    // Participant hand raise functionality
    document.querySelectorAll('.participant-item').forEach(item => {
        item.addEventListener('click', function() {
            // Toggle hand raise status
            const statusIcon = this.querySelector('.status-icon');
            if (statusIcon) {
                statusIcon.classList.toggle('hand-raised');
            }
        });
    });

    function getCurrentTime() {
        const now = new Date();
        return now.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Update viewer count
    const viewerCount = document.getElementById('viewerCount');
    if (viewerCount) {
        // Simular atualizações de participantes
        setInterval(() => {
            // Aqui você poderia fazer requisições para o servidor
            // para obter o número real de participantes
        }, 9000);
    }
});
