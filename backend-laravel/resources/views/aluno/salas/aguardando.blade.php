{{-- resources/views/aluno/salas/aguardando.blade.php --}}
<!DOCTYPE html>
<html lang="pt-BR" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Aguardando Entrada | Profeluno</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/variables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/aguardando.css') }}">
    <script src="{{ asset('js/theme-toggle.js') }}"></script>
</head>
<body>

<div class="logo-top">
    <i class="fas fa-graduation-cap"></i> Profeluno
</div>

<!-- Theme Toggle Button -->
<button id="themeToggleBtn" class="theme-toggle-btn" style="position: fixed; top: 20px; right: 20px; background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 8px; padding: 8px 12px; cursor: pointer; color: var(--text-primary); font-size: 18px; z-index: 999; transition: all 0.3s ease;">
    <i class="fas fa-sun"></i>
</button>

<div class="waiting-card">

    {{-- Sala info --}}
    <div class="sala-info">
        <div class="sala-badge">
            @if($sala->status === 'active')
                <i class="fas fa-circle" style="color:var(--danger);font-size:8px"></i>
                Ao Vivo
            @else
                <i class="fas fa-calendar" style="color:var(--warning);font-size:8px"></i>
                Agendada
            @endif
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
            @if($sala->status === 'active')
                Aguardando<span class="waiting-dots"><span></span><span></span><span></span></span>
            @else
                Aula Agendada<span class="waiting-dots"><span></span><span></span><span></span></span>
            @endif
        </div>
        <div class="waiting-subtitle">
            @if($sala->status === 'active')
                O professor liberará sua entrada em breve.<br>
                Por favor, mantenha esta página aberta.
            @else
                A aula está agendada para {{ $sala->data_hora_inicio ? $sala->data_hora_inicio->format('d/m/Y H:i') : 'data indefinida' }}.<br>
                Você será notificado quando iniciar.
            @endif
        </div>
    </div>

    {{-- Countdown for scheduled --}}
    @if($sala->status === 'pending' && $sala->data_hora_inicio)
    <div class="countdown-box" id="countdownBox">
        <div class="countdown-title">Tempo restante</div>
        <div class="countdown-timer" id="countdownTimer">--:--:--</div>
    </div>
    @endif

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
    const SALA_STATUS = @json($sala->status);
    const SALA_INICIO = @json($sala->data_hora_inicio ? $sala->data_hora_inicio->toISOString() : null);
    let   attempts   = 0;
    let   intervalId = null;
    let   countdownInterval = null;

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
    const ENTRAR_URL = @json(route('aluno.salas.entrar', $sala->id));
    const VIDEO_URL = @json(route('aluno.salas.video', $sala->id));

    async function onLiberado() {
        clearInterval(intervalId);
        clearInterval(countdownInterval);
        document.getElementById('waitingVisual').style.display = 'none';
        document.getElementById('countdownBox')?.style.display = 'none';
        document.getElementById('liberadoBox').classList.add('show');
        document.getElementById('btnEntrarWrap').style.display = 'block';
        addLog('Sala liberada! Entrando automaticamente...', 'ok');

        // Fazer AJAX POST em vez de form submit bloqueante
        setTimeout(async () => {
            const sucesso = await tentarEntrar();
            if (!sucesso) {
                // Se falhar, continuar tentando periodicamente
                checkLiberada();
                intervalId = setInterval(checkLiberada, 5000);
            }
        }, 2000);
    }

    function startCountdown(targetTime) {
        const timerEl = document.getElementById('countdownTimer');
        if (!timerEl) return;

        function updateCountdown() {
            const now = new Date().getTime();
            const distance = targetTime - now;

            if (distance <= 0) {
                clearInterval(countdownInterval);
                timerEl.textContent = '00:00:00';
                addLog('A aula está começando! Aguardando liberação...', 'ok');
                // Agora aguardar liberação
                checkLiberada();
                intervalId = setInterval(checkLiberada, 5000);
                return;
            }

            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            timerEl.textContent = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        }

        updateCountdown();
        countdownInterval = setInterval(updateCountdown, 1000);
    }

    async function checkLiberada() {
        attempts++;
        try {
            const response = await fetch(CHECK_URL, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN }
            });

            if (!response.ok) throw new Error('HTTP ' + response.status);

            const data = await response.json();

            // Sala encerrada — para de verificar
            if (data.encerrada) {
                clearInterval(intervalId);
                clearInterval(countdownInterval);
                addLog('Esta sala foi encerrada.', 'error');
                setTimeout(() => { window.location.href = @json(route('aluno.salas.index')); }, 3000);
                return;
            }

            if (data.liberada) {
                onLiberado();
            } else if (attempts % 6 === 0) {
                addLog(`Ainda aguardando... (${Math.floor(attempts * 5 / 60)}min ${(attempts * 5) % 60}s)`, 'wait');
            }
        } catch (err) {
            if (attempts % 12 === 0) {
                addLog('Erro de conexão. Tentando novamente...', 'error');
            }
        }
    }

    // Inicialização
    if (SALA_STATUS === 'pending' && SALA_INICIO) {
        const inicioTime = new Date(SALA_INICIO).getTime();
        if (inicioTime > Date.now()) {
            addLog('Aguardando o horário da aula agendada.', 'wait');
            startCountdown(inicioTime);
        } else {
            // Já passou, aguardar liberação
            addLog('A aula já deveria ter iniciado. Aguardando liberação...', 'wait');
            checkLiberada();
            intervalId = setInterval(checkLiberada, 5000);
        }
    } else if (SALA_STATUS === 'active') {
        // Sala ativa, aguardar liberação
        addLog('Sala ao vivo. Aguardando liberação do professor...', 'wait');
        checkLiberada();
        intervalId = setInterval(checkLiberada, 5000);
    }

    // Função reutilizável para entrar na sala
    async function tentarEntrar() {
        try {
            const response = await fetch(ENTRAR_URL, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN
                }
            });

            const data = await response.json();
            
            if (data.success) {
                addLog('Entrada confirmada! Redirecionando para a sala...', 'ok');
                setTimeout(() => {
                    window.location.href = VIDEO_URL;
                }, 1000);
                return true;
            } else {
                addLog('Erro ao processar entrada: ' + (data.message || 'Desconhecido'), 'error');
                return false;
            }
        } catch (err) {
            addLog('Erro de rede ao processar entrada: ' + err.message, 'error');
            return false;
        }
    }

    document.getElementById('btnEntrarManual')?.addEventListener('click', async function () {
        sessionStorage.setItem(STORAGE_KEY, '1');
        await tentarEntrar();
    });

    // Aviso se sair da página
    window.addEventListener('beforeunload', () => {
        clearInterval(intervalId);
        clearInterval(countdownInterval);
    });
</script>
</body>
</html>