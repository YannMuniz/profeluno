{{-- resources/views/professor/salas/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detalhes da Sala — ' . ($sala->titulo ?? ''))

@section('styles')
<link rel="stylesheet" href="{{ asset('css/sala-professor.css') }}">
<link rel="stylesheet" href="{{ asset('css/sala-professor-show.css') }}">
@endsection

@section('content')

{{-- ── PAGE HEADER ── --}}
<div class="page-header">
    <div class="page-header-left">
        <a href="{{ route('professor.salas.index') }}" class="back-link">
            <i class="fas fa-arrow-left"></i>
            Voltar
        </a>
        <h1 class="page-title">Detalhes da Sala</h1>
        <p class="page-subtitle">{{ $sala->titulo }}</p>
    </div>
    @if($sala->status !== 'completed')
    <div class="page-header-right">
        <a href="{{ route('professor.salas.edit', $sala->id) }}" class="btn-new-class">
            <i class="fas fa-edit"></i>
            Editar Sala
        </a>
    </div>
    @endif
</div>

<div class="show-grid">

    {{-- ════════════════════════════════
         COLUNA PRINCIPAL
    ════════════════════════════════ --}}
    <div>

        {{-- ── HERO CARD ── --}}
        <div class="hero-card">
            <div class="hero-banner status-{{ $sala->status }}">
                <div class="hero-banner-decoration">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <div class="hero-status-pill">
                    @if($sala->status === 'active')
                        <span class="dot"></span> AO VIVO
                    @elseif($sala->status === 'pending')
                        <i class="fas fa-clock"></i> AGENDADA
                    @else
                        <i class="fas fa-check-circle"></i> CONCLUÍDA
                    @endif
                </div>
            </div>
            <div class="hero-body">
                <h2 class="hero-title">{{ $sala->titulo }}</h2>
                <p class="hero-subtitle">
                    {{ $sala->descricao ?: 'Sem descrição cadastrada.' }}
                </p>
                <div class="hero-chips">
                    <span class="hero-chip">
                        <i class="fas fa-book"></i>
                        {{ $sala->materia }}
                    </span>
                    <span class="hero-chip">
                        <i class="fas fa-users"></i>
                        {{ $sala->maxAlunos }} alunos máx.
                    </span>
                    <span class="hero-chip">
                        <i class="fas fa-calendar-alt"></i>
                        {{ $sala->data_hora_inicio?->format('d/m/Y') ?? 'Sem data' }}
                    </span>
                    @if($sala->data_hora_inicio)
                    <span class="hero-chip">
                        <i class="fas fa-clock"></i>
                        {{ $sala->data_hora_inicio->format('H:i') }}
                        @if($sala->data_hora_fim)
                         – {{ $sala->data_hora_fim->format('H:i') }}
                        @endif
                    </span>
                    @endif
                    @if($sala->avaliacao !== null && $sala->avaliacao > 0)
                    <span class="hero-chip">
                        <i class="fas fa-star"></i>
                        {{ number_format($sala->avaliacao, 1) }}
                    </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── INFORMAÇÕES DETALHADAS ── --}}
        <div class="section-card">
            <div class="section-card-header">
                <i class="fas fa-info-circle"></i>
                Informações Detalhadas
            </div>
            <div class="section-card-body">
                <div class="info-list">
                    <div class="info-row">
                        <span class="info-label">Status</span>
                        <span class="info-value">
                            <span class="status-tag {{ $sala->status }}">
                                @if($sala->status === 'active')
                                    <i class="fas fa-circle"></i> Ao Vivo
                                @elseif($sala->status === 'pending')
                                    <i class="fas fa-clock"></i> Agendada
                                @else
                                    <i class="fas fa-check-circle"></i> Concluída
                                @endif
                            </span>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Matéria</span>
                        <span class="info-value">{{ $sala->materia }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Máx. Alunos</span>
                        <span class="info-value">{{ $sala->maxAlunos }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Início Previsto</span>
                        <span class="info-value">
                            @if($sala->data_hora_inicio)
                                {{ $sala->data_hora_inicio->format('d/m/Y \à\s H:i') }}
                            @else
                                <span class="muted">Não definido</span>
                            @endif
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Fim Previsto</span>
                        <span class="info-value">
                            @if($sala->data_hora_fim)
                                {{ $sala->data_hora_fim->format('d/m/Y \à\s H:i') }}
                                @if($sala->data_hora_inicio && $sala->data_hora_fim)
                                @php
                                    $dur = $sala->data_hora_inicio->diffInMinutes($sala->data_hora_fim);
                                    $h   = intdiv($dur, 60);
                                    $m   = $dur % 60;
                                @endphp
                                <span style="font-size:12px;color:var(--text-muted,#6e6b7b);margin-left:6px">
                                    ({{ $h > 0 ? $h.'h ' : '' }}{{ $m }}min)
                                </span>
                                @endif
                            @else
                                <span class="muted">Não definido</span>
                            @endif
                        </span>
                    </div>
                    @if(!empty($sala->url))
                    <div class="info-row">
                        <span class="info-label">Link da Sala</span>
                        <span class="info-value">
                            <div class="url-box">
                                <i class="fas fa-video"></i>
                                <a href="{{ $sala->url }}" target="_blank">{{ $sala->url }}</a>
                                <button class="btn-copy" data-copy="{{ $sala->url }}" title="Copiar link">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </span>
                    </div>
                    @endif
                    @if(!empty($sala->createdAt ?? $sala->created_at))
                    <div class="info-row">
                        <span class="info-label">Criada em</span>
                        <span class="info-value">
                            {{ \Carbon\Carbon::parse($sala->createdAt ?? $sala->created_at)->format('d/m/Y H:i') }}
                        </span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── TIMELINE ── --}}
        <div class="section-card">
            <div class="section-card-header">
                <i class="fas fa-timeline"></i>
                Linha do Tempo
            </div>
            <div class="section-card-body">
                <div class="timeline">
                    <div class="tl-item">
                        <div class="tl-dot"></div>
                        <div class="tl-label">Sala Criada</div>
                        <div class="tl-value">
                            {{ \Carbon\Carbon::parse($sala->createdAt ?? $sala->created_at ?? now())->format('d/m/Y H:i') }}
                        </div>
                    </div>
                    <div class="tl-item">
                        <div class="tl-dot {{ $sala->data_hora_inicio ? '' : 'muted' }}"></div>
                        <div class="tl-label">Início da Aula</div>
                        <div class="tl-value {{ $sala->data_hora_inicio ? '' : 'muted' }}">
                            {{ $sala->data_hora_inicio?->format('d/m/Y H:i') ?? 'Não iniciada' }}
                        </div>
                    </div>
                    <div class="tl-item">
                        <div class="tl-dot {{ $sala->status === 'completed' ? '' : 'muted' }}"></div>
                        <div class="tl-label">Encerramento</div>
                        <div class="tl-value {{ $sala->status === 'completed' ? '' : 'muted' }}">
                            @if($sala->status === 'completed' && $sala->data_hora_fim)
                                {{ $sala->data_hora_fim->format('d/m/Y H:i') }}
                            @else
                                Não encerrada
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── CONTEÚDO E SIMULADO ── --}}
        <div class="section-card">
            <div class="section-card-header">
                <i class="fas fa-folder-open"></i>
                Conteúdo &amp; Simulado
            </div>
            <div class="section-card-body" style="display:flex;flex-direction:column;gap:12px">

                {{-- Conteúdo --}}
                @if(!empty($sala->conteudo))
                <div class="content-item">
                    <div class="content-item-icon {{ $sala->conteudo['tipo'] ?? 'other' }}">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="content-item-info">
                        <strong>{{ $sala->conteudo['titulo'] ?? 'Conteúdo vinculado' }}</strong>
                        <span>{{ $sala->conteudo['descricao'] ?? 'Material de apoio da aula' }}</span>
                    </div>
                </div>
                @else
                <div class="empty-info">
                    <i class="fas fa-folder-open"></i>
                    Nenhum conteúdo vinculado a esta sala.
                </div>
                @endif

                {{-- Simulado --}}
                @if(!empty($sala->simulados))
                <div class="content-item">
                    <div class="content-item-icon simulado">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <div class="content-item-info">
                        <strong>{{ $sala->simulados['titulo'] ?? 'Simulado vinculado' }}</strong>
                        <span>
                            @if(!empty($sala->simulados['questoes_count']))
                                {{ $sala->simulados['questoes_count'] }} questões
                            @else
                                Simulado de avaliação
                            @endif
                        </span>
                    </div>
                </div>
                @else
                <div class="empty-info" style="padding:10px 0 0">
                    <i class="fas fa-clipboard-list"></i>
                    Nenhum simulado vinculado.
                </div>
                @endif

            </div>
        </div>

        {{-- ── LISTA DE ALUNOS ── --}}
        <div class="section-card">
            <div class="section-card-header">
                <i class="fas fa-users"></i>
                Alunos Inscritos
                <span style="margin-left:auto;font-size:12px;font-weight:400;color:var(--text-muted,#6e6b7b)">
                    {{ count((array)($sala->alunoSalas ?? [])) }} / {{ $sala->maxAlunos }}
                </span>
            </div>
            <div class="section-card-body">
                @php $alunos = (array)($sala->alunoSalas ?? []); @endphp
                @if(count($alunos) > 0)
                    <table class="alunos-table">
                        <thead>
                            <tr>
                                <th>Aluno</th>
                                <th>Status</th>
                                <th>Entrada</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($alunos as $aluno)
                            <tr>
                                <td>
                                    @php
                                        $nome = $aluno['nomeAluno'] ?? $aluno['nome'] ?? 'Aluno';
                                        $ini  = strtoupper(substr($nome,0,1));
                                    @endphp
                                    <span class="aluno-avatar-sm">{{ $ini }}</span>
                                    {{ $nome }}
                                </td>
                                <td>
                                    <span class="status-tag {{ $aluno['status'] ?? 'pending' }}" style="font-size:11px">
                                        {{ ucfirst($aluno['status'] ?? 'inscrito') }}
                                    </span>
                                </td>
                                <td style="color:var(--text-muted,#6e6b7b);font-size:12px">
                                    @if(!empty($aluno['dataEntrada']))
                                        {{ \Carbon\Carbon::parse($aluno['dataEntrada'])->format('H:i') }}
                                    @else
                                        —
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="empty-info">
                        <i class="fas fa-user-slash"></i>
                        Nenhum aluno inscrito ainda.
                    </div>
                @endif
            </div>
        </div>

    </div>

    {{-- ════════════════════════════════
         COLUNA LATERAL
    ════════════════════════════════ --}}
    <div>

        {{-- Estatísticas --}}
        <div class="side-card">
            <div class="side-card-header">
                <i class="fas fa-chart-bar"></i>
                Estatísticas
            </div>
            <div class="side-card-body">
                <div class="stat-boxes">
                    <div class="stat-box">
                        <strong>{{ count((array)($sala->alunoSalas ?? [])) }}</strong>
                        <span>Alunos</span>
                    </div>
                    <div class="stat-box">
                        <strong>{{ $sala->maxAlunos }}</strong>
                        <span>Vagas</span>
                    </div>
                    <div class="stat-box">
                        <strong>
                            @if($sala->data_hora_inicio && $sala->data_hora_fim)
                                @php
                                    $minutos = $sala->data_hora_inicio->diffInMinutes($sala->data_hora_fim);
                                    echo $minutos >= 60 ? intdiv($minutos,60).'h '.($minutos%60).'m' : $minutos.'min';
                                @endphp
                            @else
                                —
                            @endif
                        </strong>
                        <span>Duração</span>
                    </div>
                    <div class="stat-box">
                        <strong style="font-size:18px">
                            {{ $sala->avaliacao !== null ? number_format($sala->avaliacao,1) : '—' }}
                        </strong>
                        <span>Avaliação</span>
                    </div>
                </div>

                {{-- Barra de ocupação --}}
                @php
                    $inscritos = count((array)($sala->alunoSalas ?? []));
                    $pct       = $sala->maxAlunos > 0 ? min(100, round($inscritos / $sala->maxAlunos * 100)) : 0;
                @endphp
                <div class="occupancy-bar">
                    <div class="occupancy-track">
                        <div class="occupancy-fill" style="width: {{ $pct }}%"></div>
                    </div>
                    <div class="occupancy-label">
                        <span>Ocupação</span>
                        <span>{{ $pct }}%</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Avaliação --}}
        @if($sala->avaliacao !== null)
        <div class="side-card">
            <div class="side-card-header">
                <i class="fas fa-star"></i>
                Avaliação dos Alunos
            </div>
            <div class="side-card-body">
                <div class="rating-display">
                    <span class="rating-number">{{ number_format($sala->avaliacao, 1) }}</span>
                    <div>
                        <div class="stars">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $sala->avaliacao >= $i ? '' : 'empty' }}"></i>
                            @endfor
                        </div>
                        <span class="rating-label">Média dos alunos</span>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Ações --}}
        <div class="side-card">
            <div class="side-card-header">
                <i class="fas fa-bolt"></i>
                Ações
            </div>
            <div class="side-card-body">
                <div class="action-list">
                    {{-- Ao vivo: entrar na aula --}}
                    @if($sala->status === 'active')
                        <a href="{{ route('professor.salas.video-aula', $sala->id) }}"
                           class="btn-action success">
                            <i class="fas fa-video"></i>
                            Entrar na Aula
                        </a>

                    {{-- Agendada: iniciar ou editar --}}
                    @elseif($sala->status === 'pending')
                        <button class="btn-action success btn-confirmar-inicio"
                                data-id="{{ $sala->id }}"
                                data-titulo="{{ $sala->titulo }}">
                            <i class="fas fa-play"></i>
                            Iniciar Aula
                        </button>
                        <a href="{{ route('professor.salas.edit', $sala->id) }}"
                           class="btn-action outline">
                            <i class="fas fa-edit"></i>
                            Editar Sala
                        </a>
                        <button class="btn-action danger-outline" id="btnDeleteSala">
                            <i class="fas fa-trash"></i>
                            Deletar Sala
                        </button>

                    {{-- Concluída: somente visualização --}}
                    @else
                        <a href="{{ route('professor.salas.index') }}"
                           class="btn-action outline">
                            <i class="fas fa-arrow-left"></i>
                            Voltar para Salas
                        </a>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>

{{-- ── MODAL INICIAR ── --}}
<div class="modal-overlay" id="iniciarModal">
    <div class="modal-box">
        <div class="modal-icon" style="background:rgba(40,199,111,.12);color:#28c76f">
            <i class="fas fa-play-circle"></i>
        </div>
        <h3>Iniciar Aula</h3>
        <p>Deseja iniciar <strong id="iniciar-titulo"></strong> agora?<br>
           Você será redirecionado para a sala ao vivo.</p>
        <div class="modal-actions">
            <button class="modal-btn cancel" id="cancelIniciar">Cancelar</button>
            <form id="iniciarForm" method="POST">
                @csrf
                @method('PATCH')
                <button type="submit" class="modal-btn" style="background:#28c76f;color:#fff">Iniciar</button>
            </form>
        </div>
    </div>
</div>

{{-- ── MODAL DELETAR ── --}}
<div class="modal-overlay" id="deleteModal">
    <div class="modal-box">
        <div class="modal-icon danger">
            <i class="fas fa-trash-alt"></i>
        </div>
        <h3>Deletar Sala</h3>
        <p>Tem certeza que deseja deletar <strong>"{{ $sala->titulo }}"</strong>?<br>
           Esta ação não pode ser desfeita.</p>
        <div class="modal-actions">
            <button class="modal-btn cancel" id="cancelDelete">Cancelar</button>
            <form method="POST" action="{{ route('professor.salas.destroy', $sala->id) }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="modal-btn danger">Deletar</button>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
/* ── COPIAR LINK ── */
document.querySelectorAll('.btn-copy').forEach(function (btn) {
    btn.addEventListener('click', function () {
        navigator.clipboard.writeText(this.dataset.copy).then(() => {
            const icon = this.querySelector('i');
            icon.className = 'fas fa-check';
            setTimeout(() => { icon.className = 'fas fa-copy'; }, 2000);
        });
    });
});

/* ── MODAL INICIAR ── */
const iniciarModal = document.getElementById('iniciarModal');
const iniciarForm  = document.getElementById('iniciarForm');

document.querySelectorAll('.btn-confirmar-inicio').forEach(function (btn) {
    btn.addEventListener('click', function () {
        document.getElementById('iniciar-titulo').textContent = '"' + this.dataset.titulo + '"';
        iniciarForm.action = '/professor/salas/' + this.dataset.id + '/iniciar';
        iniciarModal.classList.add('active');
    });
});

document.getElementById('cancelIniciar')?.addEventListener('click', function () {
    iniciarModal.classList.remove('active');
});

iniciarModal?.addEventListener('click', function (e) {
    if (e.target === this) this.classList.remove('active');
});

/* ── MODAL DELETAR ── */
const deleteModal = document.getElementById('deleteModal');

document.getElementById('btnDeleteSala')?.addEventListener('click', function () {
    deleteModal.classList.add('active');
});

document.getElementById('cancelDelete')?.addEventListener('click', function () {
    deleteModal.classList.remove('active');
});

deleteModal?.addEventListener('click', function (e) {
    if (e.target === this) this.classList.remove('active');
});
</script>
@endsection