{{-- resources/views/aluno/salas/aguardando.blade.php --}}
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Aguardando Entrada | Profeluno</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/aguardando.css') }}">

    <style>
        /* CSS Styles removed - see public/css/aguardando.css */
        :root {
            --primary:    #7367f0;
            --success:    #28c76f;
            --danger:     #ea5455;
            --warning:    #ff9f43;
            --dark-bg:    #1e1e2d;
            --card-bg:    #2b2b40;
            --sidebar-bg: #262637;
            --text:       #e0e0e0;
            --text-muted: #b4b4c6;
            --border:     #3b3b52;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Montserrat', sans-serif;
            background: var(--dark-bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        /* ── WAITING CARD ── */
        .waiting-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 48px 40px;
            max-width: 480px;
            width: 100%;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .waiting-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), #9f8cfe, var(--success));
            animation: shimmer 2s ease-in-out infinite;
            background-size: 200% auto;
        }

        @keyframes shimmer {
            0%   { background-position: 0% center; }
            100% { background-position: 200% center; }
        }

        /* ── SALA INFO ── */
        .sala-info {
            margin-bottom: 32px;
        }

        .sala-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 14px;
            background: rgba(115,103,240,.15);
            border: 1px solid rgba(115,103,240,.3);
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 16px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .sala-titulo {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--text);
        }

        .sala-meta {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            font-size: 13px;
            color: var(--text-muted);
            flex-wrap: wrap;
        }

        .sala-meta span {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .sala-meta i { color: var(--primary); }

        /* ── PROFESSOR AVATAR ── */
        .prof-block {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            margin-bottom: 32px;
        }

        .prof-avatar {
            width: 64px; height: 64px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), #9f8cfe);
            display: flex; align-items: center; justify-content: center;
            font-size: 26px;
            color: #fff;
            border: 3px solid rgba(115,103,240,.3);
            position: relative;
        }

        .prof-avatar-status {
            position: absolute;
            bottom: 2px; right: 2px;
            width: 14px; height: 14px;
            background: var(--success);
            border-radius: 50%;
            border: 2px solid var(--card-bg);
        }

        .prof-name {
            font-size: 15px;
            font-weight: 600;
        }

        .prof-label {
            font-size: 12px;
            color: var(--text-muted);
        }

        /* ── WAITING ANIMATION ── */
        .waiting-visual {
            margin-bottom: 28px;
        }

        .waiting-ring {
            width: 80px; height: 80px;
            border-radius: 50%;
            border: 3px solid var(--border);
            border-top-color: var(--primary);
            border-right-color: #9f8cfe;
            animation: spin 1.2s linear infinite;
            margin: 0 auto 20px;
            position: relative;
        }

        .waiting-ring::after {
            content: '';
            position: absolute;
            inset: 6px;
            border-radius: 50%;
            border: 2px solid transparent;
            border-top-color: rgba(115,103,240,.3);
            animation: spin 2s linear infinite reverse;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        .waiting-title {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .waiting-subtitle {
            font-size: 13px;
            color: var(--text-muted);
            line-height: 1.6;
        }

        /* ── DOTS ── */
        .waiting-dots {
            display: inline-flex;
            gap: 5px;
            margin-left: 4px;
        }

        .waiting-dots span {
            width: 5px; height: 5px;
            background: var(--primary);
            border-radius: 50%;
            display: inline-block;
            animation: bounce 1.2s ease-in-out infinite;
        }

        .waiting-dots span:nth-child(2) { animation-delay: .2s; }
        .waiting-dots span:nth-child(3) { animation-delay: .4s; }

        @keyframes bounce {
            0%,80%,100% { transform: scale(0.8); opacity: .5; }
            40%          { transform: scale(1.2); opacity: 1; }
        }

        /* ── STATUS LOG ── */
        .status-log {
            background: var(--sidebar-bg);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 14px 16px;
            margin-bottom: 24px;
            text-align: left;
            font-size: 12px;
            color: var(--text-muted);
            max-height: 100px;
            overflow-y: auto;
        }

        .status-log p {
            margin-bottom: 4px;
        }

        .status-log p:last-child { margin-bottom: 0; }

        .status-log .log-ok    { color: var(--success); }
        .status-log .log-wait  { color: var(--warning); }
        .status-log .log-error { color: var(--danger); }

        /* ── ACTIONS ── */
        .btn-voltar {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 22px;
            background: transparent;
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text-muted);
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: .3s;
            font-family: inherit;
        }

        .btn-voltar:hover {
            background: var(--border);
            color: var(--text);
        }

        /* ── LIBERADO STATE ── */
        .liberado-box {
            display: none;
            flex-direction: column;
            align-items: center;
            gap: 16px;
            padding: 20px;
            background: rgba(40,199,111,.1);
            border: 1px solid rgba(40,199,111,.3);
            border-radius: 12px;
            margin-bottom: 24px;
        }

        .liberado-box.show { display: flex; }

        .liberado-icon {
            width: 56px; height: 56px;
            border-radius: 50%;
            background: rgba(40,199,111,.2);
            display: flex; align-items: center; justify-content: center;
            font-size: 24px;
            color: var(--success);
            animation: pop .4s ease;
        }

        @keyframes pop {
            0%  { transform: scale(.5); opacity: 0; }
            70% { transform: scale(1.15); }
            100%{ transform: scale(1);   opacity: 1; }
        }

        .liberado-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--success);
        }

        .liberado-sub {
            font-size: 13px;
            color: var(--text-muted);
        }

        .btn-entrar {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 14px 32px;
            background: var(--success);
            border: none;
            border-radius: 10px;
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            font-family: inherit;
            transition: .3s;
            animation: pulse-btn 1s ease-in-out infinite;
        }

        @keyframes pulse-btn {
            0%,100% { box-shadow: 0 0 0 0 rgba(40,199,111,.4); }
            50%      { box-shadow: 0 0 0 12px rgba(40,199,111,0); }
        }

        .btn-entrar:hover { background: #24b263; }

        /* Logo */
        .logo-top {
            position: fixed;
            top: 20px; left: 50%;
            transform: translateX(-50%);
            font-size: 18px;
            font-weight: 700;
            color: var(--primary);
            letter-spacing: 1px;
        }
        /* Rest of CSS moved to public/css/aguardando.css */
    </style>
</head>
<body>

<div class="logo-top">
    <i class="fas fa-graduation-cap"></i> Profeluno
</div>

<div class="waiting-card">

    {{-- Sala info --}}
    <div class="sala-info">
        <div class="sala-badge">
            <i class="fas fa-circle" style="color:var(--danger);font-size:8px"></i>
            Ao Vivo
        </div>
        <div class="sala-titulo">{{ $sala->titulo }}</div>
        <div class="sala-meta">
            <span><i class="fas fa-book"></i> {{ $sala->materia ?? 'Matéria' }}</span>
            <span><i class="fas fa-users"></i> {{ $sala->qtd_alunos }} vagas</span>
        </div>
    </div>

    {{-- Professor --}}
    <div class="prof-block">
        <div class="prof-avatar">
            <i class="fas fa-user-tie"></i>
            <div class="prof-avatar-status"></div>
        </div>
        <div class="prof-name">Prof. {{ $nomeProfessor ?? 'Professor' }}</div>
        <div class="prof-label">está conduzindo a aula</div>
    </div>

    {{-- Liberado state --}}
    <div class="liberado-box" id="liberadoBox">
        <div class="liberado-icon">
            <i class="fas fa-door-open"></i>
        </div>
        <div>
            <div class="liberado-title">Sala liberada!</div>
            <div class="liberado-sub">O professor permitiu sua entrada.</div>
        </div>
    </div>

    {{-- Waiting animation --}}
    <div class="waiting-visual" id="waitingVisual">
        <div class="waiting-ring"></div>
        <div class="waiting-title">
            Aguardando<span class="waiting-dots"><span></span><span></span><span></span></span>
        </div>
        <div class="waiting-subtitle">
            O professor liberará sua entrada em breve.<br>
            Por favor, mantenha esta página aberta.
        </div>
    </div>

    {{-- Status log --}}
    <div class="status-log" id="statusLog">
        <p class="log-ok"><i class="fas fa-check-circle"></i> Conectado à sala</p>
        <p class="log-wait"><i class="fas fa-hourglass-half"></i> Aguardando liberação do professor...</p>
    </div>

    {{-- Form hidden para entrar (POST com CSRF) --}}
    <form id="formEntrar" method="POST" action="{{ route('aluno.salas.join', $sala->id) }}" style="display:none">
        @csrf
    </form>

    {{-- Botão entrar (aparece quando liberado) --}}
    <div id="btnEntrarWrap" style="display:none;margin-bottom:20px">
        <button class="btn-entrar" id="btnEntrarManual" type="button">
            <i class="fas fa-sign-in-alt"></i>
            Entrar na Aula Agora
        </button>
    </div>

    <a href="{{ route('aluno.salas.index') }}" class="btn-voltar">
        <i class="fas fa-arrow-left"></i>
        Voltar para as salas
    </a>
</div>

<script>
    const CHECK_URL  = @json(route('aluno.salas.checkLiberada', $sala->id));
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;
    let   attempts   = 0;
    let   intervalId = null;

    function addLog(message, type = 'wait') {
        const log  = document.getElementById('statusLog');
        const p    = document.createElement('p');
        const now  = new Date().toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        p.className = 'log-' + type;
        p.innerHTML = `<i class="fas fa-${type === 'ok' ? 'check-circle' : type === 'error' ? 'times-circle' : 'hourglass-half'}"></i> [${now}] ${message}`;
        log.appendChild(p);
        log.scrollTop = log.scrollHeight;
    }

    const STORAGE_KEY = 'aguardando_join_attempted_{{ $sala->id }}';

    function onLiberado() {
        clearInterval(intervalId);

        document.getElementById('waitingVisual').style.display = 'none';
        document.getElementById('liberadoBox').classList.add('show');
        document.getElementById('btnEntrarWrap').style.display = 'block';

        addLog('Sala liberada pelo professor!', 'ok');

        if (!sessionStorage.getItem(STORAGE_KEY)) {
            sessionStorage.setItem(STORAGE_KEY, '1');
            setTimeout(() => {
                document.getElementById('formEntrar').submit();
            }, 3000);
        } else {
            addLog('Aguardando confirmação manual para entrar.', 'wait');
        }
    }

    async function checkLiberada() {
        attempts++;
        try {
            const response = await fetch(CHECK_URL, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN }
            });

            if (!response.ok) throw new Error('HTTP ' + response.status);

            const data = await response.json();

            if (data.liberada) {
                onLiberado();
            } else if (attempts % 6 === 0) {
                // Log a cada 30s (6 tentativas × 5s)
                addLog(`Ainda aguardando... (${Math.floor(attempts * 5 / 60)}min ${(attempts * 5) % 60}s)`, 'wait');
            }
        } catch (err) {
            if (attempts % 12 === 0) {
                addLog('Erro de conexão. Tentando novamente...', 'error');
            }
        }
    }

    // Verifica imediatamente (pode já estar liberada)
    checkLiberada();

    // Poll a cada 5 segundos
    intervalId = setInterval(checkLiberada, 5000);

    document.getElementById('btnEntrarManual')?.addEventListener('click', function () {
        sessionStorage.setItem(STORAGE_KEY, '1');
        document.getElementById('formEntrar').submit();
    });

    // Aviso se sair da página
    window.addEventListener('beforeunload', () => {
        clearInterval(intervalId);
    });
</script>
</body>
</html>