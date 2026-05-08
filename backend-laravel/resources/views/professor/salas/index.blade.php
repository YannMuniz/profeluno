{{-- resources/views/professor/salas/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Minhas Salas de Aula')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/sala-professor.css') }}">
@endsection

@section('content')

{{-- ── FLASH MESSAGES ── --}}
@if(session('success'))
    <div class="alert-flash success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert-flash danger"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
@endif

{{-- ── TABS DE NAVEGAÇÃO ── --}}
<div class="tabs-nav">
    <button class="tab-btn active" data-tab="todas">
        <i class="fas fa-th-large"></i>
        Todas
        <span class="tab-count">{{ $salas->total() ?? 0 }}</span>
    </button>
    <button class="tab-btn" data-tab="ao-vivo">
        <i class="fas fa-circle pulse-dot"></i>
        Ao Vivo
        <span class="tab-count live-count">{{ $salasAtivas->count() }}</span>
    </button>
    <button class="tab-btn" data-tab="agendadas">
        <i class="fas fa-calendar-alt"></i>
        Agendadas
        <span class="tab-count">{{ $salasAgendadas->count() }}</span>
    </button>
    <button class="tab-btn" data-tab="concluidas">
        <i class="fas fa-check-circle"></i>
        Concluídas
        <span class="tab-count">{{ $salasConcluidas->count() }}</span>
    </button>
    <div class="page-header-right">
        <a href="{{ route('professor.salas.create') }}" class="btn-new-class">
            <i class="fas fa-plus"></i>
            Nova Sala
        </a>
    </div>
</div>

{{-- ── AULA AO VIVO ── --}}
@if($salaAtiva)
<div class="live-banner" id="tab-ao-vivo">
    <div class="live-banner-glow"></div>
    <div class="live-banner-content">
        <div class="live-left">
            <span class="live-pill">
                <span class="live-dot"></span>
                AO VIVO
            </span>
            <div class="live-info">
                <h2>{{ $salaAtiva->titulo }}</h2>
                <div class="live-meta">
                    <span><i class="fas fa-book"></i> {{ $salaAtiva->materia }}</span>
                    <span><i class="fas fa-clock"></i>
                        {{ $salaAtiva->data_hora_inicio?->format('H:i') ?? '--:--' }}
                    </span>
                </div>
            </div>
        </div>
        <div class="live-right">
            <div class="live-stats">
                <div class="live-stat">
                    <strong>
                        <span id="live-alunos-count">—</span>
                        <span class="stat-sep">/</span>
                        {{ $salaAtiva->maxAlunos }}
                    </strong>
                    <span>Participantes</span>
                </div>
                <div class="live-stat">
                    <strong id="live-timer">--:--</strong>
                    <span>Duração</span>
                </div>
            </div>
            <div class="live-actions">
                <a href="{{ route('professor.salas.video-aula', $salaAtiva->id) }}" class="btn-enter-live">
                    <i class="fas fa-video"></i>
                    Entrar na Aula
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Dado para o JS calcular o timer com base no início real --}}
<div id="live-sala-meta"
     data-sala-id="{{ $salaAtiva->id }}"
     data-hora-inicio="{{ $salaAtiva->data_hora_inicio?->toIso8601String() }}"
     style="display:none"></div>
@endif

{{-- ── SALAS AGENDADAS ── --}}
@if($salasAgendadas->count())
<div class="section-block" id="tab-agendadas">
    <div class="section-block-header">
        <h2 class="section-title">
            <i class="fas fa-calendar-check"></i>
            Agendadas &amp; Prontas para Iniciar
        </h2>
    </div>
    <div class="scheduled-list">
        @foreach($salasAgendadas as $sala)
        <div class="scheduled-card">
            <div class="scheduled-date-block">
                <span class="sched-day">
                    {{ $sala->data_hora_inicio?->format('d') ?? '--' }}
                </span>
                <span class="sched-month">
                    {{ $sala->data_hora_inicio?->translatedFormat('M') ?? '---' }}
                </span>
                <span class="sched-time">
                    {{ $sala->data_hora_inicio?->format('H:i') ?? '--:--' }}
                </span>
            </div>
            <div class="scheduled-info">
                <h4>{{ $sala->titulo }}</h4>
                <p>
                    <i class="fas fa-book"></i> {{ $sala->materia }}
                    &nbsp;&nbsp;
                    <i class="fas fa-users"></i> {{ $sala->maxAlunos }} alunos
                </p>
            </div>
            <div class="scheduled-actions">
                @php
                    $agora  = now();
                    $inicio = $sala->data_hora_inicio;
                    $pronta = $inicio && $agora->gte($inicio->copy()->subMinutes(15));
                @endphp
                @if($pronta)
                    <button class="btn-start-now btn-confirmar-inicio"
                            data-id="{{ $sala->id }}"
                            data-titulo="{{ $sala->titulo }}">
                        <i class="fas fa-play"></i>
                        Iniciar Agora
                    </button>
                @else
                    <span class="countdown-badge"
                          data-start="{{ $sala->data_hora_inicio?->toIso8601String() }}">
                        <i class="fas fa-hourglass-half"></i>
                        <span class="countdown-label">Em breve</span>
                    </span>
                @endif
                <a href="{{ route('professor.salas.edit', $sala->id) }}" class="icon-btn" title="Editar">
                    <i class="fas fa-edit"></i>
                </a>
                <button class="icon-btn danger btn-delete-sala"
                        data-id="{{ $sala->id }}"
                        data-titulo="{{ $sala->titulo }}"
                        title="Deletar">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- ── GRID DE TODAS AS SALAS ── --}}
<div class="section-block" id="tab-todas">
    <div class="filter-bar">
        <h2 class="section-title">
            <i class="fas fa-list"></i>
            Todas as Salas
        </h2>
        <div class="filter-controls">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchSalas" placeholder="Buscar sala, matéria...">
            </div>
            <select class="filter-select" id="filterStatus">
                <option value="">Todos os status</option>
                <option value="active">Ativas</option>
                <option value="pending">Agendadas</option>
                <option value="completed">Concluídas</option>
            </select>
            <select class="filter-select" id="filterMateria">
                <option value="">Todas as matérias</option>
                @foreach($salas->pluck('materia')->unique()->filter() as $mat)
                    <option value="{{ $mat }}">{{ $mat }}</option>
                @endforeach
            </select>
            <div class="view-toggle">
                <button class="view-btn-toggle active" data-view="grid" title="Grade">
                    <i class="fas fa-th-large"></i>
                </button>
                <button class="view-btn-toggle" data-view="list" title="Lista">
                    <i class="fas fa-list-ul"></i>
                </button>
            </div>
        </div>
    </div>

    <div class="classes-grid" id="classesGrid">
        @forelse($salas as $sala)
        <div class="class-card"
             data-status="{{ $sala->status }}"
             data-materia="{{ Str::lower($sala->materia) }}"
             data-titulo="{{ Str::lower($sala->titulo) }}">

            {{-- Ribbon --}}
            <div class="card-ribbon {{ $sala->status }}">
                @if($sala->status === 'active')
                    <i class="fas fa-circle"></i> Ao Vivo
                @elseif($sala->status === 'pending')
                    <i class="fas fa-clock"></i> Agendada
                @else
                    <i class="fas fa-check"></i> Concluída
                @endif
            </div>

            <div class="class-card-body">
                <div class="class-card-icon">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <h3 class="class-card-title">{{ $sala->titulo }}</h3>
                <span class="class-card-subject">{{ $sala->materia }}</span>

                <div class="class-card-meta">
                    <div class="meta-chip">
                        <i class="fas fa-users"></i>
                        {{ $sala->qtd_alunos }} alunos
                    </div>
                    <div class="meta-chip">
                        <i class="fas fa-calendar"></i>
                        {{ $sala->data_hora_inicio?->format('d/m/Y') ?? 'Sem data' }}
                    </div>
                    <div class="meta-chip">
                        <i class="fas fa-star"></i>
                        {{ $sala->avaliacao !== null ? number_format($sala->avaliacao, 1) : '-' }}
                    </div>
                </div>

                @if($sala->descricao)
                <p class="class-card-desc">{{ Str::limit($sala->descricao, 80) }}</p>
                @endif
            </div>

            <div class="class-card-footer">
 
                {{-- Visualizar: sempre disponível --}}
                <a href="{{ route('professor.salas.show', $sala->id) }}"
                class="icon-btn" title="Visualizar">
                    <i class="fas fa-eye"></i>
                </a>
            
                {{-- Editar: apenas pending e active --}}
                @if($sala->status !== 'completed')
                <a href="{{ route('professor.salas.edit', $sala->id) }}"
                class="icon-btn" title="Editar">
                    <i class="fas fa-edit"></i>
                </a>
                @endif
            
                {{-- Entrar na aula: active --}}
                @if($sala->status === 'active')
                <a href="{{ route('professor.salas.video-aula', $sala->id) }}"
                class="icon-btn success" title="Entrar na aula">
                    <i class="fas fa-video"></i>
                </a>
                @endif
            
                {{-- Iniciar: apenas pending --}}
                @if($sala->status === 'pending')
                <button class="icon-btn success btn-confirmar-inicio"
                        data-id="{{ $sala->id }}"
                        data-titulo="{{ $sala->titulo }}"
                        title="Iniciar">
                    <i class="fas fa-play"></i>
                </button>
                @endif
            
                {{-- Deletar: apenas pending e active (não permite deletar concluída) --}}
                @if($sala->status !== 'completed')
                <button class="icon-btn danger btn-delete-sala"
                        data-id="{{ $sala->id }}"
                        data-titulo="{{ $sala->titulo }}"
                        title="Deletar">
                    <i class="fas fa-trash"></i>
                </button>
                @endif
            
            </div>
        </div>
        @empty
        <div class="empty-state full-width">
            <div class="empty-icon">
                <i class="fas fa-chalkboard"></i>
            </div>
            <h3>Nenhuma sala criada ainda</h3>
            <p>Crie sua primeira sala e comece a ensinar</p>
            <a href="{{ route('professor.salas.create') }}" class="btn-new-class">
                <i class="fas fa-plus"></i> Criar Sala
            </a>
        </div>
        @endforelse
    </div>

    <div class="pagination-wrapper">
        {{ $salas->links() }}
    </div>
</div>

{{-- ── MODAL DELETAR ── --}}
<div class="modal-overlay" id="deleteModal">
    <div class="modal-box">
        <div class="modal-icon danger">
            <i class="fas fa-trash-alt"></i>
        </div>
        <h3>Deletar Sala</h3>
        <p>Tem certeza que deseja deletar <strong id="delete-sala-titulo"></strong>?
           Esta ação não pode ser desfeita.</p>
        <div class="modal-actions">
            <button class="modal-btn cancel" id="cancelDelete">Cancelar</button>
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="modal-btn confirm danger">Deletar</button>
            </form>
        </div>
    </div>
</div>

{{-- ── MODAL INICIAR ── --}}
<div class="modal-overlay" id="iniciarModal">
    <div class="modal-box">
        <div class="modal-icon success">
            <i class="fas fa-play-circle"></i>
        </div>
        <h3>Iniciar Aula</h3>
        <p>Deseja iniciar a sala <strong id="iniciar-sala-titulo"></strong> agora?</p>
        <div class="modal-actions">
            <button class="modal-btn cancel" id="cancelIniciar">Cancelar</button>
            <form id="iniciarForm" method="POST">
                @csrf
                @method('PATCH')
                <button type="submit" class="modal-btn confirm success">Iniciar</button>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
/* ═══════════════════════════════════════
   TIMER AO VIVO — baseado no dataHoraInicio real da API
═══════════════════════════════════════ */
(function () {
    const meta = document.getElementById('live-sala-meta');
    const el   = document.getElementById('live-timer');
    if (!meta || !el) return;

    const salaId     = meta.dataset.salaId;
    const horaInicio = meta.dataset.horaInicio;

    // ── TIMER ──
    if (horaInicio) {
        const start = new Date(horaInicio);

        if (!isNaN(start.getTime())) {
            function pad(n) { return String(n).padStart(2, '0'); }

            function tick() {
                const diff = Math.max(0, Math.floor((Date.now() - start.getTime()) / 1000));
                const h = Math.floor(diff / 3600);
                const m = Math.floor((diff % 3600) / 60);
                const s = diff % 60;
                el.textContent = h > 0
                    ? `${pad(h)}:${pad(m)}:${pad(s)}`
                    : `${pad(m)}:${pad(s)}`;
            }

            tick();
            setInterval(tick, 1000);
        } else {
            console.warn('[ProfeLuno] data_hora_inicio inválida:', horaInicio);
            el.textContent = '--:--';
        }
    }

    // ── CONTAGEM DE ALUNOS VIA AJAX ──
    const countEl = document.getElementById('live-alunos-count');
    @if($salaAtiva)
    if (countEl && salaId) {
        const url = @json(route('professor.salas.contagemAlunos', $salaAtiva->id));

        async function fetchCount() {
            try {
                const r    = await fetch(url, { headers: { 'Accept': 'application/json' } });
                const data = await r.json();
                countEl.textContent = data.count ?? 0;
            } catch (e) {
                // silencia erros de rede
            }
        }

        fetchCount();
        setInterval(fetchCount, 10000); // atualiza a cada 10s
    }
    @endif
})();

/* ═══════════════════════════════════════
   COUNTDOWNS (salas agendadas)
═══════════════════════════════════════ */
document.querySelectorAll('.countdown-badge').forEach(function (badge) {
    const start = new Date(badge.dataset.start);
    const label = badge.querySelector('.countdown-label');
    if (!label || isNaN(start.getTime())) return;

    function updateCountdown() {
        const diff = Math.floor((start.getTime() - Date.now()) / 1000);
        if (diff <= 0) {
            label.textContent = 'Pronta para iniciar';
            return;
        }
        const h = Math.floor(diff / 3600);
        const m = Math.floor((diff % 3600) / 60);
        const s = diff % 60;
        label.textContent = h > 0
            ? `${h}h ${String(m).padStart(2,'0')}m`
            : `${String(m).padStart(2,'0')}m ${String(s).padStart(2,'0')}s`;
    }

    updateCountdown();
    setInterval(updateCountdown, 1000);
});

/* ═══════════════════════════════════════
   MODAL DELETE
═══════════════════════════════════════ */
const deleteModal   = document.getElementById('deleteModal');
const deleteForm    = document.getElementById('deleteForm');
const cancelDelete  = document.getElementById('cancelDelete');
const deleteTitulo  = document.getElementById('delete-sala-titulo');

document.querySelectorAll('.btn-delete-sala').forEach(function (btn) {
    btn.addEventListener('click', function () {
        const id     = this.dataset.id;
        const titulo = this.dataset.titulo || 'esta sala';
        deleteTitulo.textContent = '"' + titulo + '"';
        deleteForm.action = '/professor/salas/' + id;
        deleteModal.classList.add('open');
    });
});

cancelDelete.addEventListener('click', function () {
    deleteModal.classList.remove('open');
});

deleteModal.addEventListener('click', function (e) {
    if (e.target === deleteModal) deleteModal.classList.remove('open');
});

/* ═══════════════════════════════════════
   MODAL INICIAR
═══════════════════════════════════════ */
const iniciarModal   = document.getElementById('iniciarModal');
const iniciarForm    = document.getElementById('iniciarForm');
const cancelIniciar  = document.getElementById('cancelIniciar');
const iniciarTitulo  = document.getElementById('iniciar-sala-titulo');

document.querySelectorAll('.btn-confirmar-inicio').forEach(function (btn) {
    btn.addEventListener('click', function () {
        const id     = this.dataset.id;
        const titulo = this.dataset.titulo || 'esta sala';
        iniciarTitulo.textContent = '"' + titulo + '"';
        iniciarForm.action = '/professor/salas/' + id + '/iniciar';
        iniciarModal.classList.add('open');
    });
});

cancelIniciar.addEventListener('click', function () {
    iniciarModal.classList.remove('open');
});

iniciarModal.addEventListener('click', function (e) {
    if (e.target === iniciarModal) iniciarModal.classList.remove('open');
});

/* ═══════════════════════════════════════
   SEARCH + FILTROS
═══════════════════════════════════════ */
const searchInput   = document.getElementById('searchSalas');
const filterStatus  = document.getElementById('filterStatus');
const filterMateria = document.getElementById('filterMateria');
const cards         = document.querySelectorAll('.class-card');

function filterCards() {
    const q       = (searchInput?.value || '').toLowerCase().trim();
    const status  = filterStatus?.value  || '';
    const materia = (filterMateria?.value || '').toLowerCase();

    cards.forEach(function (card) {
        const titulo  = (card.dataset.titulo  || '').toLowerCase();
        const mat     = (card.dataset.materia || '').toLowerCase();
        const st      = card.dataset.status   || '';

        const matchQ  = !q       || titulo.includes(q) || mat.includes(q);
        const matchSt = !status  || st === status;
        const matchMt = !materia || mat === materia;

        card.style.display = (matchQ && matchSt && matchMt) ? '' : 'none';
    });
}

searchInput?.addEventListener('input',  filterCards);
filterStatus?.addEventListener('change', filterCards);
filterMateria?.addEventListener('change', filterCards);

/* ═══════════════════════════════════════
   VIEW TOGGLE (grid / list)
═══════════════════════════════════════ */
document.querySelectorAll('.view-btn-toggle').forEach(function (btn) {
    btn.addEventListener('click', function () {
        document.querySelectorAll('.view-btn-toggle').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        const grid = document.getElementById('classesGrid');
        if (grid) {
            grid.classList.toggle('list-view', this.dataset.view === 'list');
        }
    });
});

/* ═══════════════════════════════════════
   TABS
═══════════════════════════════════════ */
document.querySelectorAll('.tab-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');

        const tab = this.dataset.tab;
        const sections = {
            'todas'    : 'tab-todas',
            'ao-vivo'  : 'tab-ao-vivo',
            'agendadas': 'tab-agendadas',
            'concluidas': null,
        };

        // Mostrar/ocultar seções
        ['tab-todas', 'tab-ao-vivo', 'tab-agendadas'].forEach(function (id) {
            const el = document.getElementById(id);
            if (!el) return;
            el.style.display = (tab === 'todas' || sections[tab] === id) ? '' : 'none';
        });

        // Concluídas: filtro no grid de todas
        if (tab === 'concluidas') {
            filterStatus.value = 'completed';
            filterCards();
        } else if (tab === 'todas') {
            filterStatus.value = '';
            filterCards();
        }
    });
});

/* Auto-dismiss flash messages */
setTimeout(function () {
    document.querySelectorAll('.alert-flash').forEach(function (el) {
        el.style.opacity = '0';
        setTimeout(() => el.remove(), 400);
    });
}, 4000);
</script>

{{-- JS externo da sala se existir --}}
@if(file_exists(public_path('js/sala-professor.js')))
<script src="{{ asset('js/sala-professor.js') }}"></script>
@endif
@endsection