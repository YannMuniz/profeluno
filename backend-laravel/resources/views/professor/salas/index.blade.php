{{-- resources/views/professor/sala-buscar.blade.php --}}
@extends('layouts.app')

@section('title', 'Minhas Salas de Aula')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/sala-professor.css') }}">
@endsection

@section('content')

{{-- TABS DE NAVEGAÇÃO --}}
<div class="tabs-nav">
    <button class="tab-btn active" data-tab="todas">
        <i class="fas fa-th-large"></i>
        Todas
        <span class="tab-count">{{ $salas->total() ?? 0 }}</span>
    </button>
    <button class="tab-btn" data-tab="ao-vivo">
        <i class="fas fa-circle pulse-dot"></i>
        Ao Vivo
        <span class="tab-count live-count">{{ $salas->where('status', 'active')->count() ?? 0 }}</span>
    </button>
    <button class="tab-btn" data-tab="agendadas">
        <i class="fas fa-calendar-alt"></i>
        Agendadas
        <span class="tab-count">{{ $salas->where('status', 'pending')->count() ?? 0 }}</span>
    </button>
    <button class="tab-btn" data-tab="concluidas">
        <i class="fas fa-check-circle"></i>
        Concluídas
        <span class="tab-count">{{ $salas->where('status', 'completed')->count() ?? 0 }}</span>
    </button>
    <div class="page-header-right">
        <a href="{{ route('professor.salas.create') }}" class="btn-new-class">
            <i class="fas fa-plus"></i>
            Nova Sala
        </a>
    </div>
</div>

{{-- AULA AO VIVO (aparece só se houver active) --}}
@php
    $salaAtiva = $salas->where('status', 'active')->first();
@endphp

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
                    <span><i class="fas fa-book"></i> {{ $salaAtiva->idMateria }}</span>
                    <span><i class="fas fa-clock"></i> {{ \Carbon\Carbon::parse($salaAtiva->dataHoraInicio)->format('H:i') }}</span>
                </div>
            </div>
        </div>
        <div class="live-right">
            <div class="live-stats">
                <div class="live-stat">
                    <strong>
                        {{ count((array)($salaAtiva->alunoSalas ?? [])) }}
                        <span class="stat-sep">/</span>
                        {{ $salaAtiva->maxAlunos }}
                    </strong>
                    <span>Participantes</span>
                </div>
                <div class="live-stat">
                    <strong id="live-timer">00:00</strong>
                    <span>Duração</span>
                </div>
            </div>
            <div class="live-actions">
                <a href="{{ route('professor.salas.show', $salaAtiva->idSalaAula) }}" class="btn-enter-live">
                    <i class="fas fa-video"></i>
                    Entrar na Aula
                </a>
                <!-- <button class="btn-live-config" title="Configurações">
                    <i class="fas fa-cog"></i>
                </button> -->
            </div>
        </div>
    </div>
</div>

<div id="live-sala-meta"
     data-sala-id="{{ $salaAtiva->idSalaAula }}"
     data-hora-inicio="{{ \Carbon\Carbon::parse($salaAtiva->dataHoraInicio)->toIso8601String() }}"
     style="display:none"></div>
@endif

{{-- SALAS AGENDADAS / PRONTAS PARA INICIAR --}}
@php
    $salasAgendadas = $salas->where('status', 'pending');
@endphp

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
                <span class="sched-day">{{ \Carbon\Carbon::parse($sala->dataHoraInicio)->format('d') ?? '--' }}</span>
                <span class="sched-month">{{ \Carbon\Carbon::parse($sala->dataHoraInicio)->translatedFormat('M') ?? '---' }}</span>
                <span class="sched-time">{{ \Carbon\Carbon::parse($sala->dataHoraInicio)->format('H:i') ?? '--:--' }}</span>
            </div>
            <div class="scheduled-info">
                <h4>{{ $sala->titulo }}</h4>
                <p>
                    <i class="fas fa-book"></i> {{ $sala->idMateria }}
                    &nbsp;&nbsp;
                    <i class="fas fa-users"></i> {{ $sala->maxAlunos }} alunos
                </p>
            </div>
            <div class="scheduled-actions">
                @php
                    $agora = now();
                    $inicio = \Carbon\Carbon::parse($sala->dataHoraInicio);
                    $pronta = $inicio && $agora->gte($inicio->copy()->subMinutes(15));
                @endphp
                @if($pronta)
                    <button class="btn-start-now btn-confirmar-inicio"
                            data-id="{{ $sala->idSalaAula }}"
                            data-titulo="{{ $sala->titulo }}">
                        <i class="fas fa-play"></i>
                        Iniciar Agora
                    </button>
                @else
                    <span class="countdown-badge" data-start="{{ \Carbon\Carbon::parse($sala->dataHoraInicio)->toIso8601String() }}">
                        <i class="fas fa-hourglass-half"></i>
                        <span class="countdown-label">Em breve</span>
                    </span>
                @endif
                <a href="{{ route('professor.salas.edit', $sala->idSalaAula) }}" class="icon-btn" title="Editar">
                    <i class="fas fa-edit"></i>
                </a>
                <button class="icon-btn danger" data-id="{{ $sala->idSalaAula }}" title="Cancelar">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- FILTRO + GRID DE TODAS AS SALAS --}}
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
                @foreach($salas->pluck('materia')->unique() as $mat)
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

            {{-- Ribbon de status --}}
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
                        {{ number_format($sala->avaliacao, 1) ?? '-' }}
                    </div>
                </div>

                @if($sala->descricao)
                <p class="class-card-desc">{{ Str::limit($sala->descricao, 80) }}</p>
                @endif
            </div>

            <div class="class-card-footer">
                <a href="{{ route('professor.salas.show', $sala->id) }}" class="icon-btn" title="Visualizar">
                    <i class="fas fa-eye"></i>
                </a>
                <a href="{{ route('professor.salas.edit', $sala->id) }}" class="icon-btn" title="Editar">
                    <i class="fas fa-edit"></i>
                </a>
                @if($sala->status === 'pending')
                <a href="{{ route('professor.salas.iniciar', $sala->id) }}" class="icon-btn success" title="Iniciar">
                    <i class="fas fa-play"></i>
                </a>
                @endif
                <button class="icon-btn danger btn-delete-sala" data-id="{{ $sala->id }}" title="Deletar">
                    <i class="fas fa-trash"></i>
                </button>
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

{{-- Modal de confirmação de exclusão --}}
<div class="modal-overlay" id="deleteModal">
    <div class="modal-box">
        <div class="modal-icon danger">
            <i class="fas fa-trash-alt"></i>
        </div>
        <h3>Deletar Sala</h3>
        <p>Tem certeza que deseja deletar esta sala? Esta ação não pode ser desfeita.</p>
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

{{-- Modal de confirmação de início --}}
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
                <button type="submit" class="modal-btn confirm success">Iniciar</button>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="{{ asset('js/sala-professor.js') }}"></script>