{{-- resources/views/aluno/salas/video-aula.blade.php --}}
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $sala->titulo ?? 'Aula ao Vivo' }} | Profeluno</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary:   #7367f0;
            --success:   #28c76f;
            --danger:    #ea5455;
            --warning:   #ff9f43;
            --info:      #00cfe8;
            --dark-bg:   #1e1e2d;
            --card-bg:   #2b2b40;
            --sidebar-bg:#262637;
            --text:      #e0e0e0;
            --text-muted:#b4b4c6;
            --border:    #3b3b52;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Montserrat', sans-serif;
            background: var(--dark-bg);
            color: var(--text);
            overflow: hidden;
        }

        /* ── TOP BAR ── */
        .top-bar {
            height: 60px;
            background: var(--sidebar-bg);
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 1000;
        }

        .class-info { display: flex; align-items: center; gap: 15px; }
        .class-title h2 { font-size: 16px; font-weight: 600; margin: 0; }
        .class-title p  { font-size: 12px; color: var(--text-muted); margin: 0; }

        .live-badge {
            background: var(--danger);
            color: white;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 5px;
            animation: blink 2s infinite;
        }
        @keyframes blink { 0%,100%{opacity:1} 50%{opacity:.7} }

        .top-actions { display: flex; align-items: center; gap: 10px; }

        .btn-leave {
            background: var(--danger);
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            font-size: 13px;
            transition: .3s;
        }
        .btn-leave:hover { background: #d84545; }

        /* ── LAYOUT ── */
        .main-container {
            display: grid;
            grid-template-columns: 1fr 340px;
            height: calc(100vh - 60px);
            margin-top: 60px;
        }

        /* ── VIDEO SECTION ── */
        .video-section { display: flex; flex-direction: column; background: var(--dark-bg); }

        .main-video-container {
            flex: 1;
            position: relative;
            background: #000;
        }

        #jitsi-container,
        .video-placeholder {
            width: 100%;
            height: 100%;
        }

        .video-placeholder {
            background: linear-gradient(135deg,#1a1a2e,#16213e);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            gap: 12px;
        }
        .video-placeholder i { font-size: 64px; opacity: .3; }
        .video-placeholder p { font-size: 14px; }

        .video-overlay {
            position: absolute;
            top: 14px; left: 14px;
            background: rgba(0,0,0,.7);
            padding: 7px 14px;
            border-radius: 8px;
            display: flex; align-items: center; gap: 8px;
            font-size: 13px;
        }

        .viewer-count {
            position: absolute;
            top: 14px; right: 14px;
            background: rgba(0,0,0,.7);
            padding: 7px 14px;
            border-radius: 8px;
            display: flex; align-items: center; gap: 8px;
            font-size: 13px;
        }
        .viewer-count i { color: var(--success); }

        /* ── PARTICIPANTS STRIP ── */
        .participants-strip {
            height: 120px;
            background: var(--card-bg);
            border-top: 1px solid var(--border);
            padding: 10px;
            display: flex; gap: 10px;
            overflow-x: auto; overflow-y: hidden;
        }
        .participants-strip::-webkit-scrollbar { height: 5px; }
        .participants-strip::-webkit-scrollbar-track { background: var(--dark-bg); }
        .participants-strip::-webkit-scrollbar-thumb { background: var(--primary); border-radius: 3px; }

        .participant-thumb {
            min-width: 140px; width: 140px; height: 100px;
            background: #1a1a2e; border-radius: 8px;
            position: relative; overflow: hidden;
            border: 2px solid transparent; transition: .3s; cursor: pointer; flex-shrink: 0;
        }
        .participant-thumb:hover    { border-color: var(--primary); transform: scale(1.05); }
        .participant-thumb.speaking { border-color: var(--success); box-shadow: 0 0 14px rgba(40,199,111,.45); }

        .thumb-avatar {
            width: 100%; height: 100%;
            display: flex; align-items: center; justify-content: center;
            background: linear-gradient(135deg, var(--primary), #9f8cfe);
            font-size: 24px; color: white;
        }
        .thumb-name {
            position: absolute; bottom: 5px; left: 5px; right: 5px;
            background: rgba(0,0,0,.8);
            padding: 3px 6px; border-radius: 4px;
            font-size: 11px; text-align: center;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .thumb-badge {
            position: absolute; top: 5px; right: 5px;
            width: 20px; height: 20px;
            background: rgba(0,0,0,.8); border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 10px;
        }
        .thumb-badge.muted   { color: var(--danger); }
        .thumb-badge.hand    { color: var(--warning); }
        .thumb-badge.teacher { color: var(--primary); }

        /* ── CONTROL BAR ── */
        .control-bar {
            height: 80px;
            background: var(--sidebar-bg);
            border-top: 1px solid var(--border);
            display: flex; justify-content: center; align-items: center;
            gap: 12px; padding: 0 20px;
        }

        .ctrl-btn {
            width: 50px; height: 50px; border-radius: 50%;
            background: var(--card-bg); border: 1px solid var(--border);
            color: var(--text); cursor: pointer; transition: .3s;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px;
        }
        .ctrl-btn:hover  { background: var(--primary); border-color: var(--primary); color: #fff; transform: scale(1.1); }
        .ctrl-btn.active { background: var(--primary); border-color: var(--primary); color: #fff; }
        .ctrl-btn.muted  { background: var(--danger);  border-color: var(--danger);  color: #fff; }
        .ctrl-btn.warn   { background: var(--warning); border-color: var(--warning); color: #fff; }
        .ctrl-sep { width: 1px; height: 36px; background: var(--border); margin: 0 6px; }

        /* ── SIDEBAR ── */
        .sidebar {
            background: var(--sidebar-bg);
            border-left: 1px solid var(--border);
            display: flex; flex-direction: column;
            max-height: calc(100vh - 60px);
        }

        .sidebar-tabs {
            display: flex;
            background: var(--card-bg);
            border-bottom: 1px solid var(--border);
        }

        .s-tab {
            flex: 1; padding: 14px 8px;
            background: transparent; border: none;
            border-bottom: 2px solid transparent;
            color: var(--text-muted); cursor: pointer;
            font-weight: 600; font-size: 12px; transition: .3s;
        }
        .s-tab:hover  { color: var(--text); }
        .s-tab.active { color: var(--primary); border-bottom-color: var(--primary); }

        .s-content {
            flex: 1; overflow-y: auto; padding: 15px;
            display: none;
        }
        .s-content.active { display: flex; flex-direction: column; }

        .s-content::-webkit-scrollbar { width: 5px; }
        .s-content::-webkit-scrollbar-track { background: var(--dark-bg); }
        .s-content::-webkit-scrollbar-thumb { background: var(--primary); border-radius: 3px; }

        /* ── CHAT ── */
        .chat-messages { flex: 1; overflow-y: auto; margin-bottom: 12px; }
        .chat-msg { margin-bottom: 14px; animation: fadeUp .3s ease; }
        @keyframes fadeUp { from{opacity:0;transform:translateY(8px)} to{opacity:1;transform:translateY(0)} }

        .msg-head { display: flex; align-items: center; gap: 8px; margin-bottom: 4px; }
        .msg-avatar {
            width: 28px; height: 28px; border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), #9f8cfe);
            display: flex; align-items: center; justify-content: center;
            font-size: 11px; font-weight: 700; color: #fff; flex-shrink: 0;
        }
        .msg-name  { font-weight: 600; font-size: 13px; }
        .msg-time  { font-size: 11px; color: var(--text-muted); margin-left: auto; }
        .msg-body {
            margin-left: 36px; padding: 10px 12px;
            background: var(--card-bg);
            border-radius: 0 8px 8px 8px;
            font-size: 13px; line-height: 1.5;
        }
        .msg-body.professor { background: rgba(115,103,240,.12); border-left: 3px solid var(--primary); }
        .msg-body.mine      { background: rgba(40,199,111,.08);  border-left: 3px solid var(--success); }

        .chat-form { display: flex; gap: 8px; }
        .chat-input {
            flex: 1; padding: 10px 12px;
            background: var(--card-bg); border: 1px solid var(--border);
            border-radius: 8px; color: var(--text); font-size: 13px; font-family: inherit;
        }
        .chat-input:focus { outline: none; border-color: var(--primary); }

        .btn-send {
            width: 40px; height: 40px;
            background: var(--primary); border: none;
            border-radius: 8px; color: #fff; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            transition: .3s; flex-shrink: 0;
        }
        .btn-send:hover { background: #6258d3; }

        /* ── PARTICIPANTS LIST ── */
        .participant-list { display: flex; flex-direction: column; gap: 8px; }
        .p-item {
            display: flex; align-items: center; gap: 10px;
            padding: 10px; background: var(--card-bg); border-radius: 8px; transition: .3s;
        }
        .p-item:hover { background: rgba(115,103,240,.1); }
        .p-avatar {
            width: 38px; height: 38px; border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), #9f8cfe);
            display: flex; align-items: center; justify-content: center;
            font-size: 15px; color: #fff; flex-shrink: 0;
        }
        .p-info { flex: 1; }
        .p-name { font-size: 14px; font-weight: 600; }
        .p-role { font-size: 11px; color: var(--text-muted); }
        .p-role.teacher { color: var(--primary); }

        /* ── MATERIALS ── */
        .mat-list { display: flex; flex-direction: column; gap: 12px; }
        .mat-item {
            padding: 14px; background: var(--card-bg);
            border: 1px solid var(--border); border-radius: 10px; transition: .3s; cursor: pointer;
        }
        .mat-item:hover { border-color: var(--primary); transform: translateY(-2px); }
        .mat-head { display: flex; align-items: center; gap: 12px; margin-bottom: 10px; }
        .mat-icon {
            width: 44px; height: 44px; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px; flex-shrink: 0;
        }
        .mat-icon.pdf   { background: rgba(234,84,85,.15);   color: var(--danger); }
        .mat-icon.slide { background: rgba(255,159,67,.15);  color: var(--warning); }
        .mat-icon.video { background: rgba(115,103,240,.15); color: var(--primary); }
        .mat-title { font-size: 14px; font-weight: 600; margin-bottom: 2px; }
        .mat-desc  { font-size: 12px; color: var(--text-muted); margin: 0; }
        .mat-actions { display: flex; gap: 8px; }
        .btn-mat {
            flex: 1; padding: 7px;
            background: rgba(115,103,240,.1); border: 1px solid var(--primary);
            border-radius: 6px; color: var(--primary); font-size: 12px; font-weight: 600;
            cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 5px; transition: .3s;
        }
        .btn-mat:hover { background: var(--primary); color: #fff; }

        /* ── RESPONSIVE ── */
        @media (max-width: 1024px) {
            .main-container { grid-template-columns: 1fr; }
            .sidebar { display: none; }
        }
        @media (max-width: 768px) {
            .ctrl-btn { width: 44px; height: 44px; font-size: 16px; }
            .class-title p { display: none; }
        }
    </style>
</head>
<body>

{{-- ── TOP BAR ── --}}
<div class="top-bar">
    <div class="class-info">
        <div class="class-title">
            <h2>{{ $sala->titulo }}</h2>
            <p>{{ $sala->materia ?? 'Matéria' }}</p>
        </div>
        <div class="live-badge">
            <i class="fas fa-circle"></i> AO VIVO
        </div>
    </div>
    <div class="top-actions">
        <button class="btn-leave" id="btnSair">
            <i class="fas fa-sign-out-alt"></i> Sair da Aula
        </button>
    </div>
</div>

{{-- ── MAIN ── --}}
<div class="main-container">

    <div class="video-section">

        <div class="main-video-container">
            @if(!empty($sala->url))
                <div id="jitsi-container"></div>
            @else
                <div class="video-placeholder">
                    <i class="fas fa-video"></i>
                    <p>Aguardando o professor iniciar o vídeo…</p>
                </div>
            @endif

            <div class="video-overlay">
                <i class="fas fa-user-tie"></i>
                <span>Prof. {{ $nomeProfessor ?? 'Professor' }}</span>
            </div>

            <div class="viewer-count">
                <i class="fas fa-eye"></i>
                <span id="viewer-count">{{ count((array)($sala->alunoSalas ?? [])) }}</span>
                assistindo
            </div>
        </div>

        {{-- PARTICIPANTS STRIP --}}
        <div class="participants-strip" id="participantsStrip">
            {{-- Aluno (você) --}}
            <div class="participant-thumb">
                <div class="thumb-avatar" style="background:linear-gradient(135deg,var(--success),#3ce094)">
                    <i class="fas fa-user"></i>
                </div>
                <div class="thumb-name">Você</div>
                <div class="thumb-badge muted"><i class="fas fa-microphone-slash"></i></div>
            </div>
        </div>

        {{-- CONTROL BAR --}}
        <div class="control-bar">
            <button class="ctrl-btn muted" id="btnMic" title="Microfone (mudo)">
                <i class="fas fa-microphone-slash"></i>
            </button>
            <button class="ctrl-btn muted" id="btnCam" title="Câmera (desligada)">
                <i class="fas fa-video-slash"></i>
            </button>
            <div class="ctrl-sep"></div>
            <button class="ctrl-btn" id="btnHand" title="Levantar a mão">
                <i class="fas fa-hand-paper"></i>
            </button>
            <button class="ctrl-btn" id="btnReact" title="Reações">
                <i class="fas fa-smile"></i>
            </button>
            <div class="ctrl-sep"></div>
            <button class="ctrl-btn" id="btnChat" title="Abrir chat" onclick="showTab('chat')">
                <i class="fas fa-comment"></i>
            </button>
        </div>
    </div>

    {{-- SIDEBAR --}}
    <div class="sidebar">
        <div class="sidebar-tabs">
            <button class="s-tab active" onclick="showTab('chat')">
                <i class="fas fa-comment"></i> Chat
            </button>
            <button class="s-tab" onclick="showTab('participants')">
                <i class="fas fa-users"></i> Participantes
            </button>
            <button class="s-tab" onclick="showTab('materials')">
                <i class="fas fa-folder"></i> Materiais
            </button>
        </div>

        {{-- CHAT --}}
        <div class="s-content active" id="tab-chat">
            <div class="chat-messages" id="chatMessages"></div>
            <div class="chat-form">
                <input type="text" class="chat-input" id="chatInput" placeholder="Sua mensagem…">
                <button class="btn-send" id="btnSend">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>

        {{-- PARTICIPANTS --}}
        <div class="s-content" id="tab-participants">
            <div class="participant-list" id="participantList">
                {{-- Professor --}}
                <div class="p-item">
                    <div class="p-avatar" style="background:linear-gradient(135deg,var(--primary),#9f8cfe)">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div class="p-info">
                        <div class="p-name">Prof. {{ $nomeProfessor ?? 'Professor' }}</div>
                        <div class="p-role teacher">Professor</div>
                    </div>
                </div>
                {{-- Você --}}
                <div class="p-item">
                    <div class="p-avatar" style="background:linear-gradient(135deg,var(--success),#3ce094)">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="p-info">
                        <div class="p-name">Você</div>
                        <div class="p-role">Aluno</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- MATERIALS --}}
        <div class="s-content" id="tab-materials">
            <div class="mat-list">
                @if(!empty($sala->conteudo))
                <div class="mat-item">
                    <div class="mat-head">
                        <div class="mat-icon pdf"><i class="fas fa-file-pdf"></i></div>
                        <div>
                            <div class="mat-title">{{ $sala->conteudo['titulo'] ?? 'Conteúdo da Aula' }}</div>
                            <p class="mat-desc">Disponibilizado pelo professor</p>
                        </div>
                    </div>
                    <div class="mat-actions">
                        <button class="btn-mat"><i class="fas fa-download"></i> Baixar</button>
                        <button class="btn-mat"><i class="fas fa-eye"></i> Visualizar</button>
                    </div>
                </div>
                @else
                <p style="font-size:13px;color:var(--text-muted);text-align:center;padding:20px 0">
                    Nenhum material disponível ainda.
                </p>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Dados para JS --}}
<script>
    const SALA_NOME       = @json($sala->nomeSala ?? '');
    const SALA_URL        = @json($sala->url ?? '');
    const USER_NAME       = @json(Auth::user()->name ?? 'Aluno');
    const IS_PROFESSOR    = false;
    const ROUTE_VOLTAR    = @json(route('aluno.salas.index') ?? '/');
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
/* ── TABS ── */
function showTab(name) {
    document.querySelectorAll('.s-content').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.s-tab').forEach(el => el.classList.remove('active'));
    document.getElementById('tab-' + name)?.classList.add('active');
    const map  = { chat: 0, participants: 1, materials: 2 };
    const tabs = document.querySelectorAll('.s-tab');
    if (map[name] !== undefined) tabs[map[name]]?.classList.add('active');
}

/* ── SAIR ── */
document.getElementById('btnSair').addEventListener('click', () => {
    if (confirm('Deseja sair da aula?')) {
        window.location.href = ROUTE_VOLTAR;
    }
});

/* ── MIC TOGGLE ── */
document.getElementById('btnMic').addEventListener('click', function () {
    this.classList.toggle('muted');
    this.classList.toggle('active');
    const icon = this.querySelector('i');
    icon.classList.toggle('fa-microphone');
    icon.classList.toggle('fa-microphone-slash');
});

/* ── CAM TOGGLE ── */
document.getElementById('btnCam').addEventListener('click', function () {
    this.classList.toggle('muted');
    this.classList.toggle('active');
    const icon = this.querySelector('i');
    icon.classList.toggle('fa-video');
    icon.classList.toggle('fa-video-slash');
});

/* ── LEVANTAR A MÃO ── */
document.getElementById('btnHand').addEventListener('click', function () {
    const raised = this.classList.toggle('warn');
    this.title = raised ? 'Baixar a mão' : 'Levantar a mão';
    appendSystemMessage(raised ? '✋ Você levantou a mão.' : 'Você baixou a mão.');
});

/* ── CHAT ── */
const chatMessages = document.getElementById('chatMessages');
const chatInput    = document.getElementById('chatInput');
const btnSend      = document.getElementById('btnSend');

appendSystemMessage('Você entrou na aula. Bem-vindo(a)!');

function appendSystemMessage(text) {
    const div = document.createElement('div');
    div.className = 'chat-msg';
    div.innerHTML = `<div class="msg-body" style="margin-left:0;background:rgba(0,207,232,.08);border-left:3px solid var(--info);color:var(--text-muted);font-size:12px">${text}</div>`;
    chatMessages.appendChild(div);
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

function appendMessage(name, initials, text, isMe) {
    const now = new Date().toLocaleTimeString('pt-BR', {hour:'2-digit', minute:'2-digit'});
    const colorStyle = isMe
        ? 'background:linear-gradient(135deg,var(--success),#3ce094)'
        : 'background:linear-gradient(135deg,var(--primary),#9f8cfe)';
    const div = document.createElement('div');
    div.className = 'chat-msg';
    div.innerHTML = `
        <div class="msg-head">
            <div class="msg-avatar" style="${colorStyle}">${initials}</div>
            <span class="msg-name">${name}</span>
            <span class="msg-time">${now}</span>
        </div>
        <div class="msg-body ${isMe ? 'mine' : ''}">${text}</div>
    `;
    chatMessages.appendChild(div);
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

function sendMsg() {
    const text = chatInput.value.trim();
    if (!text) return;
    const initials = USER_NAME.split(' ').map(w => w[0]).join('').substring(0,2).toUpperCase();
    appendMessage('Você', initials, text, true);
    chatInput.value = '';
}

btnSend.addEventListener('click', sendMsg);
chatInput.addEventListener('keydown', e => { if (e.key === 'Enter') sendMsg(); });

/* ── JITSI ── */
if (SALA_URL && SALA_NOME) {
    const script = document.createElement('script');
    script.src   = 'https://meet.jit.si/external_api.js';
    script.onload = () => {
        new JitsiMeetExternalAPI('meet.jit.si', {
            roomName:   SALA_NOME,
            parentNode: document.getElementById('jitsi-container'),
            userInfo:   { displayName: USER_NAME },
            configOverwrite: {
                startWithAudioMuted: true,
                startWithVideoMuted: true,
                toolbarButtons: [],
            },
            interfaceConfigOverwrite: {
                TOOLBAR_BUTTONS: [],
                SHOW_JITSI_WATERMARK: false,
            }
        });
    };
    document.head.appendChild(script);
}
</script>
</body>
</html>