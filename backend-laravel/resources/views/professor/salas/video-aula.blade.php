{{-- resources/views/professor/salas/video-aula.blade.php --}}
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
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Montserrat',sans-serif; background:var(--dark-bg); color:var(--text); overflow:hidden; }

        /* TOP BAR */
        .top-bar {
            height:60px; background:var(--sidebar-bg); border-bottom:1px solid var(--border);
            display:flex; justify-content:space-between; align-items:center; padding:0 20px;
            position:fixed; top:0; left:0; right:0; z-index:1000;
        }
        .class-info { display:flex; align-items:center; gap:15px; }
        .class-title h2 { font-size:16px; font-weight:600; margin:0; }
        .class-title p  { font-size:12px; color:var(--text-muted); margin:0; }
        .live-badge {
            background:var(--danger); color:#fff; padding:5px 12px; border-radius:15px;
            font-size:11px; font-weight:700; display:flex; align-items:center; gap:5px;
            animation:blink 2s infinite;
        }
        @keyframes blink { 0%,100%{opacity:1} 50%{opacity:.7} }
        .top-stats { display:flex; align-items:center; gap:20px; font-size:13px; color:var(--text-muted); }
        .top-stats .stat { display:flex; align-items:center; gap:6px; }
        .top-stats .stat i { color:var(--primary); }
        .top-stats .stat.timer-stat i { color:var(--success); }
        .top-actions { display:flex; align-items:center; gap:10px; }

        .btn-liberar {
            background:transparent; color:var(--success); border:1.5px solid var(--success);
            padding:8px 16px; border-radius:8px; font-weight:600; cursor:pointer; font-size:12px;
            transition:.3s; display:flex; align-items:center; gap:6px;
        }
        .btn-liberar:hover { background:var(--success); color:#fff; }
        .btn-liberar.liberado {
            background:rgba(40,199,111,.15); color:var(--success); border-color:var(--success);
            cursor:default; pointer-events:none;
        }
        .btn-encerrar {
            background:var(--danger); color:#fff; border:none; padding:8px 20px;
            border-radius:8px; font-weight:600; cursor:pointer; font-size:13px; transition:.3s;
        }
        .btn-encerrar:hover { background:#d84545; }

        /* LAYOUT */
        .main-container {
            display:grid; grid-template-columns:1fr 340px;
            height:calc(100vh - 60px); margin-top:60px;
        }

        /* VIDEO */
        .video-section { display:flex; flex-direction:column; background:var(--dark-bg); }
        .main-video-container { flex:1; position:relative; background:#000; }
        #jitsi-container, .video-placeholder { width:100%; height:100%; }
        .video-placeholder {
            background:linear-gradient(135deg,#1a1a2e,#16213e);
            display:flex; flex-direction:column; align-items:center; justify-content:center;
            color:var(--text-muted); gap:12px;
        }
        .video-placeholder i { font-size:64px; opacity:.3; }
        .video-placeholder p { font-size:14px; }

        /* PARTICIPANTS STRIP */
        .participants-strip {
            height:120px; background:var(--card-bg); border-top:1px solid var(--border);
            padding:10px; display:flex; gap:10px; overflow-x:auto; overflow-y:hidden;
        }
        .participants-strip::-webkit-scrollbar { height:5px; }
        .participants-strip::-webkit-scrollbar-thumb { background:var(--primary); border-radius:3px; }
        .participant-thumb {
            min-width:140px; width:140px; height:100px; background:#1a1a2e; border-radius:8px;
            position:relative; overflow:hidden; border:2px solid transparent; transition:.3s;
            cursor:pointer; flex-shrink:0;
        }
        .participant-thumb.speaking { border-color:var(--success); box-shadow:0 0 14px rgba(40,199,111,.45); }
        .thumb-avatar {
            width:100%; height:100%; display:flex; align-items:center; justify-content:center;
            background:linear-gradient(135deg,var(--primary),#9f8cfe); font-size:24px; color:#fff;
        }
        .thumb-name {
            position:absolute; bottom:5px; left:5px; right:5px; background:rgba(0,0,0,.8);
            padding:3px 6px; border-radius:4px; font-size:11px; text-align:center;
            white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
        }
        .thumb-badge {
            position:absolute; top:5px; right:5px; width:20px; height:20px;
            background:rgba(0,0,0,.8); border-radius:50%;
            display:flex; align-items:center; justify-content:center; font-size:10px;
        }
        .thumb-badge.teacher { color:var(--primary); }

        /* CONTROL BAR */
        .control-bar {
            height:80px; background:var(--sidebar-bg); border-top:1px solid var(--border);
            display:flex; justify-content:center; align-items:center; gap:12px; padding:0 20px;
        }
        .ctrl-btn {
            width:50px; height:50px; border-radius:50%; background:var(--card-bg);
            border:1px solid var(--border); color:var(--text); cursor:pointer; transition:.3s;
            display:flex; align-items:center; justify-content:center; font-size:18px;
        }
        .ctrl-btn:hover  { background:var(--primary); border-color:var(--primary); color:#fff; transform:scale(1.1); }
        .ctrl-btn.active { background:var(--primary); border-color:var(--primary); color:#fff; }
        .ctrl-btn.on-air { background:var(--danger);  border-color:var(--danger);  color:#fff; }
        .ctrl-sep { width:1px; height:36px; background:var(--border); margin:0 6px; }

        /* SIDEBAR */
        .sidebar {
            background:var(--sidebar-bg); border-left:1px solid var(--border);
            display:flex; flex-direction:column; max-height:calc(100vh - 60px);
        }
        .sidebar-tabs { display:flex; background:var(--card-bg); border-bottom:1px solid var(--border); }
        .s-tab {
            flex:1; padding:12px 6px; background:transparent; border:none;
            border-bottom:2px solid transparent; color:var(--text-muted); cursor:pointer;
            font-weight:600; font-size:11px; transition:.3s;
        }
        .s-tab:hover  { color:var(--text); }
        .s-tab.active { color:var(--primary); border-bottom-color:var(--primary); }
        .s-content { flex:1; overflow-y:auto; padding:15px; display:none; }
        .s-content.active { display:flex; flex-direction:column; }
        .s-content::-webkit-scrollbar { width:5px; }
        .s-content::-webkit-scrollbar-thumb { background:var(--primary); border-radius:3px; }

        /* CHAT */
        .chat-messages { flex:1; overflow-y:auto; margin-bottom:12px; }
        .chat-msg { margin-bottom:14px; animation:fadeUp .3s ease; }
        @keyframes fadeUp { from{opacity:0;transform:translateY(8px)} to{opacity:1;transform:translateY(0)} }
        .msg-head { display:flex; align-items:center; gap:8px; margin-bottom:4px; }
        .msg-avatar {
            width:28px; height:28px; border-radius:50%;
            background:linear-gradient(135deg,var(--primary),#9f8cfe);
            display:flex; align-items:center; justify-content:center;
            font-size:11px; font-weight:700; color:#fff; flex-shrink:0;
        }
        .msg-name  { font-weight:600; font-size:13px; }
        .msg-time  { font-size:11px; color:var(--text-muted); margin-left:auto; }
        .msg-body {
            margin-left:36px; padding:10px 12px; background:var(--card-bg);
            border-radius:0 8px 8px 8px; font-size:13px; line-height:1.5;
        }
        .msg-body.professor { background:rgba(115,103,240,.12); border-left:3px solid var(--primary); }
        .chat-form { display:flex; gap:8px; }
        .chat-input {
            flex:1; padding:10px 12px; background:var(--card-bg); border:1px solid var(--border);
            border-radius:8px; color:var(--text); font-size:13px; font-family:inherit;
        }
        .chat-input:focus { outline:none; border-color:var(--primary); }
        .btn-send {
            width:40px; height:40px; background:var(--primary); border:none; border-radius:8px;
            color:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center;
            transition:.3s; flex-shrink:0;
        }
        .btn-send:hover { background:#6258d3; }

        /* PARTICIPANTS */
        .participant-list { display:flex; flex-direction:column; gap:8px; }
        .p-item {
            display:flex; align-items:center; gap:10px; padding:10px;
            background:var(--card-bg); border-radius:8px; transition:.3s;
        }
        .p-item:hover { background:rgba(115,103,240,.1); }
        .p-avatar {
            width:38px; height:38px; border-radius:50%;
            background:linear-gradient(135deg,var(--primary),#9f8cfe);
            display:flex; align-items:center; justify-content:center;
            font-size:15px; color:#fff; flex-shrink:0;
        }
        .p-info { flex:1; }
        .p-name { font-size:14px; font-weight:600; }
        .p-role { font-size:11px; color:var(--text-muted); }
        .p-role.teacher { color:var(--primary); }
        .p-empty { font-size:13px; color:var(--text-muted); text-align:center; padding:20px 0; }

        /* MATERIALS */
        .mat-list { display:flex; flex-direction:column; gap:12px; }
        .mat-item {
            padding:14px; background:var(--card-bg); border:1px solid var(--border);
            border-radius:10px; transition:.3s;
        }
        .mat-item:hover { border-color:var(--primary); }
        .mat-head { display:flex; align-items:center; gap:12px; margin-bottom:10px; }
        .mat-icon {
            width:44px; height:44px; border-radius:8px;
            display:flex; align-items:center; justify-content:center; font-size:20px; flex-shrink:0;
        }
        .mat-icon.pdf   { background:rgba(234,84,85,.15);  color:var(--danger); }
        .mat-icon.slide { background:rgba(255,159,67,.15); color:var(--warning); }
        .mat-icon.video { background:rgba(115,103,240,.15);color:var(--primary); }
        .mat-icon.link  { background:rgba(0,207,232,.15);  color:var(--info); }
        .mat-icon.other { background:rgba(115,103,240,.15);color:var(--primary); }
        .mat-title { font-size:14px; font-weight:600; margin-bottom:2px; }
        .mat-desc  { font-size:12px; color:var(--text-muted); margin:0; }
        .mat-actions { display:flex; gap:8px; margin-top:10px; }
        .btn-mat {
            flex:1; padding:7px; background:rgba(115,103,240,.1); border:1px solid var(--primary);
            border-radius:6px; color:var(--primary); font-size:12px; font-weight:600; cursor:pointer;
            display:flex; align-items:center; justify-content:center; gap:5px; transition:.3s;
            text-decoration:none;
        }
        .btn-mat:hover { background:var(--primary); color:#fff; }
        .mat-empty { text-align:center; padding:30px 0; color:var(--text-muted); font-size:13px; }
        .mat-empty i { font-size:32px; opacity:.3; display:block; margin-bottom:10px; }

        /* MODAL */
        .modal-overlay {
            position:fixed; inset:0; background:rgba(0,0,0,.7);
            display:none; align-items:center; justify-content:center; z-index:2000;
        }
        .modal-overlay.open { display:flex; }
        .modal-box {
            background:var(--card-bg); border:1px solid var(--border);
            border-radius:16px; padding:30px; max-width:420px; width:90%; text-align:center;
        }
        .modal-icon {
            width:60px; height:60px; border-radius:50%;
            display:flex; align-items:center; justify-content:center; font-size:26px;
            margin:0 auto 16px;
        }
        .modal-icon.danger  { background:rgba(234,84,85,.15);  color:var(--danger); }
        .modal-icon.success { background:rgba(40,199,111,.15); color:var(--success); }
        .modal-box h3 { font-size:18px; margin-bottom:8px; }
        .modal-box p  { font-size:14px; color:var(--text-muted); margin-bottom:20px; }
        .modal-actions { display:flex; gap:12px; justify-content:center; }
        .modal-btn {
            padding:10px 28px; border-radius:8px; font-weight:600;
            font-size:14px; cursor:pointer; border:none; transition:.3s;
        }
        .modal-btn.cancel  { background:var(--dark-bg); color:var(--text-muted); border:1px solid var(--border); }
        .modal-btn.danger  { background:var(--danger); color:#fff; }
        .modal-btn.success { background:var(--success); color:#fff; }
        .modal-btn.cancel:hover { background:var(--border); }
        .modal-btn.danger:hover { background:#d84545; }
        .modal-btn.success:hover{ background:#24b263; }

        @media (max-width:1024px) { .main-container { grid-template-columns:1fr; } .sidebar { display:none; } }
        @media (max-width:768px)  { .top-stats .stat span { display:none; } .btn-liberar span { display:none; } }
    </style>
</head>
<body>

{{-- TOP BAR --}}
<div class="top-bar">
    <div class="class-info">
        <div class="class-title">
            <h2>{{ $sala->titulo }}</h2>
            <p>{{ $sala->materia ?? 'Matéria' }}</p>
        </div>
        <div class="live-badge"><i class="fas fa-circle"></i> AO VIVO</div>
    </div>

    <div class="top-stats">
        <div class="stat">
            <i class="fas fa-users"></i>
            <strong id="aluno-count">0</strong>
            <span>/ {{ $sala->maxAlunos }} alunos</span>
        </div>
        <div class="stat timer-stat">
            <i class="fas fa-clock"></i>
            <span id="live-timer">00:00</span>
        </div>
    </div>

    <div class="top-actions">
        <button class="btn-liberar {{ $liberada ? 'liberado' : '' }}" id="btnLiberar">
            <i class="fas fa-{{ $liberada ? 'check-circle' : 'door-open' }}" id="iconLiberar"></i>
            <span id="textoLiberar">{{ $liberada ? 'Alunos liberados' : 'Liberar Alunos' }}</span>
        </button>
        <button class="btn-encerrar" id="btnEncerrar">
            <i class="fas fa-stop-circle"></i> Encerrar Aula
        </button>
    </div>
</div>

{{-- MAIN --}}
<div class="main-container">

    <div class="video-section">

        <div class="main-video-container">
            @if(!empty($sala->url))
                <div id="jitsi-container"></div>
            @else
                <div class="video-placeholder">
                    <i class="fas fa-video"></i>
                    <p>Aguardando conexão de vídeo…</p>
                </div>
            @endif
        </div>

        {{-- PARTICIPANTS STRIP --}}
        <div class="participants-strip" id="participantsStrip">
            <div class="participant-thumb speaking">
                <div class="thumb-avatar"><i class="fas fa-user-tie"></i></div>
                <div class="thumb-name">Você (Prof.)</div>
                <div class="thumb-badge teacher"><i class="fas fa-chalkboard-teacher"></i></div>
            </div>
        </div>

        {{-- CONTROL BAR — sem botão gravar --}}
        <div class="control-bar">
            <button class="ctrl-btn on-air" id="btnMic" title="Microfone">
                <i class="fas fa-microphone"></i>
            </button>
            <button class="ctrl-btn on-air" id="btnCam" title="Câmera">
                <i class="fas fa-video"></i>
            </button>
            <div class="ctrl-sep"></div>
            <button class="ctrl-btn" id="btnScreen" title="Compartilhar tela">
                <i class="fas fa-desktop"></i>
            </button>
            <div class="ctrl-sep"></div>
            <button class="ctrl-btn" title="Chat" onclick="showTab('chat')">
                <i class="fas fa-comment"></i>
            </button>
            <button class="ctrl-btn" title="Participantes" onclick="showTab('participants')">
                <i class="fas fa-users"></i>
            </button>
        </div>
    </div>

    {{-- SIDEBAR — 3 abas: Chat, Alunos, Materiais --}}
    <div class="sidebar">
        <div class="sidebar-tabs">
            <button class="s-tab active" onclick="showTab('chat')">
                <i class="fas fa-comment"></i> Chat
            </button>
            <button class="s-tab" onclick="showTab('participants')">
                <i class="fas fa-users"></i> Alunos
                <span id="badgeAlunos" style="background:var(--primary);color:#fff;border-radius:10px;padding:1px 6px;font-size:10px;margin-left:4px">0</span>
            </button>
            <button class="s-tab" onclick="showTab('materials')">
                <i class="fas fa-folder"></i> Materiais
            </button>
        </div>

        {{-- CHAT --}}
        <div class="s-content active" id="tab-chat">
            <div class="chat-messages" id="chatMessages"></div>
            <div class="chat-form">
                <input type="text" class="chat-input" id="chatInput" placeholder="Mensagem para os alunos…">
                <button class="btn-send" id="btnSend"><i class="fas fa-paper-plane"></i></button>
            </div>
        </div>

        {{-- PARTICIPANTS --}}
        <div class="s-content" id="tab-participants">
            <div class="participant-list" id="participantList">
                {{-- Professor sempre fixo --}}
                <div class="p-item" id="itemProfessor">
                    <div class="p-avatar"><i class="fas fa-user-tie"></i></div>
                    <div class="p-info">
                        <div class="p-name">{{ session('user_nome') ?? Auth::user()->name ?? 'Você' }}</div>
                        <div class="p-role teacher">Professor (você)</div>
                    </div>
                </div>
            </div>
            <p class="p-empty" id="semAlunos" style="display:none">Nenhum aluno na sala ainda.</p>
        </div>

        {{-- MATERIALS --}}
        <div class="s-content" id="tab-materials">
            <div class="mat-list">
                @if($conteudo)
                    @php
                        $tipo = $conteudo['tipo'] ?? 'other';
                        $iconMap = [
                            'pdf'   => ['icon'=>'fa-file-pdf',         'class'=>'pdf'],
                            'slide' => ['icon'=>'fa-file-powerpoint',  'class'=>'slide'],
                            'pptx'  => ['icon'=>'fa-file-powerpoint',  'class'=>'slide'],
                            'video' => ['icon'=>'fa-file-video',       'class'=>'video'],
                            'mp4'   => ['icon'=>'fa-file-video',       'class'=>'video'],
                            'link'  => ['icon'=>'fa-link',             'class'=>'link'],
                        ];
                        $iconData = $iconMap[$tipo] ?? ['icon'=>'fa-file-alt','class'=>'other'];
                    @endphp
                    <div class="mat-item">
                        <div class="mat-head">
                            <div class="mat-icon {{ $iconData['class'] }}">
                                <i class="fas {{ $iconData['icon'] }}"></i>
                            </div>
                            <div>
                                <div class="mat-title">{{ $conteudo['titulo'] ?? 'Material da Aula' }}</div>
                                <p class="mat-desc">{{ $conteudo['descricao'] ?? 'Material de apoio' }}</p>
                            </div>
                        </div>
                        {{-- Link: botão Abrir Link apenas. Arquivo: nenhum botão. --}}
                        @if($tipo === 'link' && !empty($conteudo['url']))
                        <div class="mat-actions">
                            <a href="{{ $conteudo['url'] }}" target="_blank" class="btn-mat">
                                <i class="fas fa-external-link-alt"></i> Abrir Link
                            </a>
                        </div>
                        @endif
                    </div>
                @else
                    <div class="mat-empty">
                        <i class="fas fa-folder-open"></i>
                        Nenhum material vinculado a esta aula.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- MODAL ENCERRAR --}}
<div class="modal-overlay" id="encerrarModal">
    <div class="modal-box">
        <div class="modal-icon danger"><i class="fas fa-stop-circle"></i></div>
        <h3>Encerrar Aula</h3>
        <p>Deseja encerrar a aula agora? Todos os alunos serão desconectados.</p>
        <div class="modal-actions">
            <button class="modal-btn cancel" id="cancelEncerrar">Cancelar</button>
            <form method="POST" action="{{ route('professor.salas.encerrar', $sala->id) }}" style="display:inline">
                @csrf
                @method('PATCH')
                <button type="submit" class="modal-btn danger">Encerrar</button>
            </form>
        </div>
    </div>
</div>

{{-- MODAL LIBERAR --}}
<div class="modal-overlay" id="liberarModal">
    <div class="modal-box">
        <div class="modal-icon success"><i class="fas fa-door-open"></i></div>
        <h3>Liberar Alunos</h3>
        <p>Os alunos na sala de espera poderão entrar. Deseja liberar o acesso agora?</p>
        <div class="modal-actions">
            <button class="modal-btn cancel" id="cancelLiberar">Cancelar</button>
            <button class="modal-btn success" id="confirmLiberar">Liberar</button>
        </div>
    </div>
</div>

<script>
    const SALA_ID          = {{ $sala->id ?? 'null' }};
    const SALA_NOME        = @json($sala->nomeSala ?? '');
    const SALA_URL         = @json($sala->url ?? '');
    const HORA_INICIO_ISO  = @json(optional($sala->data_hora_inicio)->toIso8601String() ?? now()->toIso8601String());
    const USER_NAME        = @json(session('user_nome') ?? (Auth::user()->name ?? 'Professor'));
    const SALA_LIBERADA    = {{ $liberada ? 'true' : 'false' }};
    const ROUTE_LIBERAR    = @json(route('professor.salas.liberar', $sala->id));
    const ROUTE_MEMBROS    = @json(route('professor.salas.membros', $sala->id));
    const CSRF_TOKEN       = document.querySelector('meta[name="csrf-token"]').content;
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
/* ── TIMER ── */
(function () {
    const el    = document.getElementById('live-timer');
    const start = new Date(HORA_INICIO_ISO);
    if (isNaN(start.getTime())) return;
    function pad(n) { return String(n).padStart(2,'0'); }
    function tick() {
        const diff = Math.max(0, Math.floor((Date.now()-start.getTime())/1000));
        const h=Math.floor(diff/3600), m=Math.floor((diff%3600)/60), s=diff%60;
        el.textContent = h>0 ? `${pad(h)}:${pad(m)}:${pad(s)}` : `${pad(m)}:${pad(s)}`;
    }
    tick(); setInterval(tick,1000);
})();

/* ── TABS ── */
function showTab(name) {
    document.querySelectorAll('.s-content').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.s-tab').forEach(el => el.classList.remove('active'));
    document.getElementById('tab-'+name)?.classList.add('active');
    const map={chat:0,participants:1,materials:2};
    const tabs=document.querySelectorAll('.s-tab');
    if (map[name]!==undefined) tabs[map[name]]?.classList.add('active');
}

/* ── MODAL ENCERRAR ── */
document.getElementById('btnEncerrar').addEventListener('click', () => {
    document.getElementById('encerrarModal').classList.add('open');
});
document.getElementById('cancelEncerrar').addEventListener('click', () => {
    document.getElementById('encerrarModal').classList.remove('open');
});
document.getElementById('encerrarModal').addEventListener('click', function(e){
    if(e.target===this) this.classList.remove('open');
});
document.querySelector('#encerrarModal form')?.addEventListener('submit', function(){
    if(window.jitsiApi){ try{window.jitsiApi.executeCommand('endConference');}catch(e){} }
});

/* ── MODAL LIBERAR (AJAX — sem reload) ── */
const liberarModal = document.getElementById('liberarModal');
const btnLiberar   = document.getElementById('btnLiberar');

if (!SALA_LIBERADA) {
    btnLiberar.addEventListener('click', () => liberarModal.classList.add('open'));
}

document.getElementById('cancelLiberar').addEventListener('click', () => liberarModal.classList.remove('open'));
liberarModal.addEventListener('click', e => { if(e.target===liberarModal) liberarModal.classList.remove('open'); });

document.getElementById('confirmLiberar').addEventListener('click', async function() {
    liberarModal.classList.remove('open');
    this.disabled = true;
    try {
        const r = await fetch(ROUTE_LIBERAR, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
            }
        });
        const data = await r.json();
        if (data.success) {
            btnLiberar.classList.add('liberado');
            btnLiberar.disabled = true;
            document.getElementById('iconLiberar').className = 'fas fa-check-circle';
            document.getElementById('textoLiberar').textContent = 'Alunos liberados';
            appendMessage('Sistema', null, 'Alunos liberados para entrar na sala.', 'system');
        }
    } catch(e) { console.error('Erro ao liberar:', e); }
});

/* ── POLLING DE MEMBROS ── */
async function atualizarMembros() {
    try {
        const r = await fetch(ROUTE_MEMBROS, {
            headers: { 'Accept':'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN }
        });
        if (!r.ok) return;
        const data = await r.json();

        // Atualiza contadores
        const total = data.total || 0;
        const countEl = document.getElementById('aluno-count');
        if (countEl) countEl.textContent = total;
        const badge = document.getElementById('badgeAlunos');
        if (badge) badge.textContent = total;

        // Atualiza lista de alunos (mantém o professor fixo)
        const list = document.getElementById('participantList');
        const semAlunos = document.getElementById('semAlunos');
        if (!list) return;

        // Remove entradas de alunos antigas
        list.querySelectorAll('.p-item.aluno-entry').forEach(el => el.remove());

        const alunos = data.alunos || [];
        if (alunos.length === 0) {
            if (semAlunos) semAlunos.style.display = 'block';
        } else {
            if (semAlunos) semAlunos.style.display = 'none';
            alunos.forEach(a => {
                const initials = (a.nome||'?').split(' ').map(w=>w[0]).join('').substring(0,2).toUpperCase();
                const div = document.createElement('div');
                div.className = 'p-item aluno-entry';
                div.innerHTML = `
                    <div class="p-avatar" style="background:linear-gradient(135deg,var(--success),#3ce094)">
                        ${initials}
                    </div>
                    <div class="p-info">
                        <div class="p-name">${a.nome}</div>
                        <div class="p-role">Aluno</div>
                    </div>`;
                list.appendChild(div);
            });
        }

        // Atualiza strip de participantes
        const strip = document.getElementById('participantsStrip');
        if (strip) {
            strip.querySelectorAll('.participant-thumb.aluno-strip').forEach(el => el.remove());
            alunos.forEach(a => {
                const initials = (a.nome||'?').split(' ').map(w=>w[0]).join('').substring(0,2).toUpperCase();
                const thumb = document.createElement('div');
                thumb.className = 'participant-thumb aluno-strip';
                thumb.innerHTML = `
                    <div class="thumb-avatar" style="background:linear-gradient(135deg,var(--success),#3ce094);font-size:16px;font-weight:700">
                        ${initials}
                    </div>
                    <div class="thumb-name">${a.nome}</div>`;
                strip.appendChild(thumb);
            });
        }
    } catch(e) {}
}

atualizarMembros();
setInterval(atualizarMembros, 15000);

/* ── CHAT ── */
const chatMessages = document.getElementById('chatMessages');
const chatInput    = document.getElementById('chatInput');
const btnSend      = document.getElementById('btnSend');

appendMessage('Sistema', null, 'Aula iniciada. Boas-vindas, professor!', 'system');

function appendMessage(name, initials, text, type) {
    const now   = new Date().toLocaleTimeString('pt-BR',{hour:'2-digit',minute:'2-digit'});
    const isProf= type==='professor';
    const isSystem= type==='system';
    const div   = document.createElement('div');
    div.className='chat-msg';
    if (isSystem) {
        div.innerHTML=`<div class="msg-body" style="margin-left:0;background:rgba(0,207,232,.08);border-left:3px solid var(--info);color:var(--text-muted);font-size:12px">${text}</div>`;
    } else {
        const avatarBg=isProf?'background:linear-gradient(135deg,var(--primary),#9f8cfe)':'background:linear-gradient(135deg,var(--success),#3ce094)';
        div.innerHTML=`
            <div class="msg-head">
                <div class="msg-avatar" style="${avatarBg}">${initials||'<i class="fas fa-info-circle"></i>'}</div>
                <span class="msg-name">${name}</span>
                <span class="msg-time">${now}</span>
            </div>
            <div class="msg-body ${isProf?'professor':''}">${text}</div>`;
    }
    chatMessages.appendChild(div);
    chatMessages.scrollTop=chatMessages.scrollHeight;
}

function sendMsg() {
    const text=chatInput.value.trim(); if(!text) return;
    if(window.jitsiApi){
        window.jitsiApi.executeCommand('sendChatMessage', text);
    }
    const initials=USER_NAME.split(' ').map(w=>w[0]).join('').substring(0,2).toUpperCase();
    appendMessage(USER_NAME,initials,text,'professor');
    chatInput.value='';
}
btnSend.addEventListener('click',sendMsg);
chatInput.addEventListener('keydown',e=>{ if(e.key==='Enter') sendMsg(); });

/* ── CONTROLES VISUAIS --
document.getElementById('btnScreen')?.addEventListener('click',function(){this.classList.toggle('active');});

/* ── JITSI ── */
function initJitsi() {
    if (!SALA_URL || !SALA_NOME) return;
    const script=document.createElement('script');
    script.src='https://meet.jit.si/external_api.js';
    script.onload=()=>{
        const api=new JitsiMeetExternalAPI('meet.jit.si',{
            roomName:SALA_NOME,
            parentNode:document.getElementById('jitsi-container'),
            userInfo:{displayName:USER_NAME},
            configOverwrite:{
                startWithAudioMuted:false,
                startWithVideoMuted:true,
                toolbarButtons:[],
                disableDeepLinking:true,
                enableWelcomePage:false,
            },
            interfaceConfigOverwrite:{TOOLBAR_BUTTONS:[],SHOW_JITSI_WATERMARK:false},
        });
        window.jitsiApi=api;

        api.on('incomingMessage', (event) => {
            const { nick, message } = event;
            const initials = nick.split(' ').map(w => w[0]).join('').substring(0,2).toUpperCase();
            appendMessage(nick, initials, message, 'aluno');
        });

        let micOn=true;
        const btnMic=document.getElementById('btnMic');
        btnMic?.addEventListener('click',function(){
            api.executeCommand('toggleAudio'); micOn=!micOn;
            this.classList.toggle('on-air',micOn); this.classList.toggle('active',!micOn);
            this.querySelector('i').className=micOn?'fas fa-microphone':'fas fa-microphone-slash';
        });

        let camOn=false;
        const btnCam=document.getElementById('btnCam');
        btnCam?.addEventListener('click',function(){
            api.executeCommand('toggleVideo'); camOn=!camOn;
            this.classList.toggle('on-air',camOn); this.classList.toggle('active',!camOn);
            this.querySelector('i').className=camOn?'fas fa-video':'fas fa-video-slash';
        });

        document.getElementById('btnScreen')?.addEventListener('click',function(){
            api.executeCommand('toggleShareScreen'); this.classList.toggle('active');
        });

        api.on('audioMuteStatusChanged',({muted})=>{
            micOn=!muted;
            btnMic?.classList.toggle('on-air',micOn); btnMic?.classList.toggle('active',!micOn);
            const ic=btnMic?.querySelector('i'); if(ic) ic.className=micOn?'fas fa-microphone':'fas fa-microphone-slash';
        });
        api.on('videoMuteStatusChanged',({muted})=>{
            camOn=!muted;
            btnCam?.classList.toggle('on-air',camOn); btnCam?.classList.toggle('active',!camOn);
            const ic=btnCam?.querySelector('i'); if(ic) ic.className=camOn?'fas fa-video':'fas fa-video-slash';
        });
    };
    document.head.appendChild(script);
}

initJitsi();
</script>
</body>
</html>