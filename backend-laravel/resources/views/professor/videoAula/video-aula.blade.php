{{-- resources/views/professor/video-aula.blade.php --}}
@extends('layouts.app')

@section('title', 'Aula ao Vivo - Professor | ' . ($aula->titulo ?? 'Sistema de Aulas Virtuais'))

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/video-aula-professor.css') }}">
@endpush

@section('content')

{{-- ===================== TOP BAR ===================== --}}
<div class="top-bar">
    <div class="class-info">
        <div class="class-title">
            <h2>{{ $aula->titulo ?? 'Matemática Avançada - Trigonometria' }}</h2>
            <p>{{ Auth::user()->name }}</p>
        </div>

        <div class="live-indicator">
            <i class="fas fa-circle"></i> AO VIVO
        </div>

        <div class="timer">
            <i class="fas fa-clock"></i>
            <span id="class-timer">00:00:00</span>
        </div>
    </div>

    <div class="top-actions">
        <button class="btn-end-class">
            <i class="fas fa-stop-circle"></i> Encerrar Aula
        </button>
    </div>
</div>

{{-- ===================== MAIN CONTAINER ===================== --}}
<div class="main-container">

    {{-- =========== VIDEO SECTION =========== --}}
    <div class="video-section">

        {{-- Vídeo principal (área do professor / compartilhamento de tela) --}}
        <div class="main-video-container">

            {{-- Placeholder — substitua por elemento <video> ou iframe da API de vídeo --}}
            <div class="video-placeholder">
                <i class="fas fa-video"></i>
                {{-- <p>Câmera do professor (integração de API aqui)</p> --}}
            </div>

            {{-- Badge: professor transmitindo --}}
            <div class="video-overlay">
                <i class="fas fa-user-tie"></i>
                <span>{{ Auth::user()->name }}</span>
            </div>

            {{-- Contagem de espectadores --}}
            <div class="viewer-count">
                <i class="fas fa-eye"></i>
                <span id="viewer-count">{{ $aula->participantes_count ?? 0 }} assistindo</span>
            </div>

            {{-- Badge compartilhamento de tela --}}
            <div class="screen-sharing-badge">
                <i class="fas fa-desktop"></i> Compartilhando tela
            </div>

            {{-- Alertas de mão levantada --}}
            <div id="hand-alert-container" style="position:absolute;bottom:20px;left:50%;transform:translateX(-50%);display:flex;flex-direction:column;gap:10px;align-items:center;width:max-content;max-width:90%;"></div>
        </div>

        {{-- ── Strip de participantes ── --}}
        <div class="participants-strip" id="participants-strip">

            {{-- Câmera do próprio professor --}}
            <div class="participant-video me">
                <div class="participant-avatar">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="participant-name">Você (professor)</div>
            </div>

            {{-- Alunos conectados – loop dinâmico --}}
            @isset($participantes)
                @foreach($participantes as $aluno)
                <div class="participant-video {{ $aluno->falando ? 'speaking' : '' }} {{ $aluno->mao_levantada ? 'hand-raised' : '' }}"
                     data-user-id="{{ $aluno->id }}">
                    <div class="participant-avatar"
                         style="background: linear-gradient(135deg, {{ $aluno->cor_primaria ?? 'var(--primary-color)' }}, {{ $aluno->cor_secundaria ?? '#9f8cfe' }});">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="participant-name">{{ $aluno->name }}</div>
                    @if($aluno->mutado)
                    <div class="participant-status muted">
                        <i class="fas fa-microphone-slash"></i>
                    </div>
                    @elseif($aluno->mao_levantada)
                    <div class="participant-status hand">
                        <i class="fas fa-hand-paper"></i>
                    </div>
                    @endif
                </div>
                @endforeach
            @else
                {{-- Dados estáticos de exemplo (remova em produção) --}}
                <div class="participant-video">
                    <div class="participant-avatar" style="background:linear-gradient(135deg,var(--success-color),#3ce094);">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="participant-name">Maria Silva</div>
                </div>

                <div class="participant-video speaking">
                    <div class="participant-avatar" style="background:linear-gradient(135deg,var(--warning-color),#ffb976);">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="participant-name">Pedro Santos</div>
                </div>

                <div class="participant-video hand-raised">
                    <div class="participant-avatar" style="background:linear-gradient(135deg,var(--info-color),#3ee5fe);">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="participant-name">Ana Costa</div>
                    <div class="participant-status hand">
                        <i class="fas fa-hand-paper"></i>
                    </div>
                </div>

                <div class="participant-video">
                    <div class="participant-avatar" style="background:linear-gradient(135deg,var(--danger-color),#f37575);">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="participant-name">Carlos Oliveira</div>
                    <div class="participant-status muted">
                        <i class="fas fa-microphone-slash"></i>
                    </div>
                </div>

                <div class="participant-video">
                    <div class="participant-avatar" style="background:linear-gradient(135deg,#ff6b9d,#ff8fab);">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="participant-name">Julia Mendes</div>
                </div>

                <div class="participant-video">
                    <div class="participant-avatar" style="background:linear-gradient(135deg,#4facfe,#00f2fe);">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="participant-name">Lucas Ferreira</div>
                </div>
            @endisset
        </div>

        {{-- ── Barra de controles do professor ── --}}
        <div class="control-bar">

            {{-- Microfone --}}
            <button class="control-btn danger" data-toggle="mic" title="Microfone">
                <i class="fas fa-microphone"></i>
            </button>

            {{-- Câmera --}}
            <button class="control-btn active" data-toggle="camera" title="Câmera">
                <i class="fas fa-video"></i>
            </button>

            {{-- Compartilhar tela --}}
            <button class="control-btn" data-toggle="screen" title="Compartilhar tela">
                <i class="fas fa-desktop"></i>
            </button>

            {{-- Gravar aula --}}
            <button class="control-btn" data-toggle="record" title="Gravar aula">
                <i class="fas fa-circle" style="color:var(--danger-color);font-size:14px;"></i>
            </button>

            {{-- Enquete --}}
            <button class="control-btn" title="Enquete"
                    onclick="switchTab('enquete')">
                <i class="fas fa-poll"></i>
            </button>

            {{-- Configurações --}}
            <button class="control-btn" title="Configurações">
                <i class="fas fa-cog"></i>
            </button>
        </div>
    </div>{{-- /video-section --}}


    {{-- =========== SIDEBAR =========== --}}
    <div class="sidebar">

        {{-- Tabs --}}
        <div class="sidebar-tabs">
            <button class="sidebar-tab" data-tab="chat"         onclick="switchTab('chat')">
                <i class="fas fa-comment"></i> Chat
            </button>
            <button class="sidebar-tab" data-tab="participants" onclick="switchTab('participants')">
                <i class="fas fa-users"></i> Alunos
            </button>
            <button class="sidebar-tab" data-tab="materials"    onclick="switchTab('materials')">
                <i class="fas fa-folder"></i> Conteúdos
            </button>
            <button class="sidebar-tab" data-tab="enquete"      onclick="switchTab('enquete')">
                <i class="fas fa-poll"></i> Enquete
            </button>
        </div>

        {{-- ── TAB: CHAT ── --}}
        <div class="sidebar-content" id="chat-tab" style="display:flex;flex-direction:column;">
            <div class="chat-container">
                <div class="chat-messages" id="chat-messages">

                    {{-- Mensagens históricas --}}
                    @isset($mensagens)
                        @foreach($mensagens as $msg)
                        <div class="chat-message">
                            <div class="message-header">
                                <div class="message-avatar"
                                     style="background:linear-gradient(135deg,{{ $msg->cor ?? 'var(--primary-color)' }},#9f8cfe);">
                                    {{ strtoupper(substr($msg->autor, 0, 2)) }}
                                </div>
                                <span class="message-name">{{ $msg->autor }}</span>
                                <span class="message-time">{{ $msg->hora }}</span>
                            </div>
                            <div class="message-text {{ $msg->is_professor ? 'teacher' : '' }}">
                                {{ $msg->texto }}
                            </div>
                        </div>
                        @endforeach
                    @else
                        {{-- Mensagem inicial estática --}}
                        <div class="chat-message">
                            <div class="message-header">
                                <div class="message-avatar" style="background:linear-gradient(135deg,var(--primary-color),#9f8cfe);">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                                </div>
                                <span class="message-name">{{ Auth::user()->name }} (você)</span>
                                <span class="message-time">{{ now()->format('H:i') }}</span>
                            </div>
                            <div class="message-text teacher">
                                Boa tarde a todos! Vamos começar nossa aula sobre {{ $aula->titulo ?? 'trigonometria' }}. Alguém tem dúvidas da aula anterior?
                            </div>
                        </div>
                    @endisset

                </div>

                <div class="chat-input-container">
                    <input type="text" class="chat-input" id="chat-input"
                           placeholder="Digite sua mensagem...">
                    <button class="btn-send" id="btn-send">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- ── TAB: PARTICIPANTES ── --}}
        <div class="sidebar-content" id="participants-tab" style="display:none;flex-direction:column;">

            {{-- Ações rápidas do professor --}}
            <div class="teacher-actions">
                <button class="btn-teacher-action" onclick="muteAll()">
                    <i class="fas fa-microphone-slash"></i> Mutar todos
                </button>
                <button class="btn-teacher-action danger">
                    <i class="fas fa-hand-paper"></i> Limpar mãos
                </button>
            </div>

            <div class="participants-list" id="participants-list">

                {{-- O próprio professor --}}
                <div class="participant-item">
                    <div class="participant-item-avatar">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div class="participant-item-info">
                        <div class="participant-item-name">{{ Auth::user()->name }}</div>
                        <div class="participant-item-role teacher">Professor (você)</div>
                    </div>
                    <div class="participant-item-status">
                        <div class="status-icon"><i class="fas fa-microphone"></i></div>
                    </div>
                </div>

                {{-- Loop de alunos --}}
                @isset($participantes)
                    @foreach($participantes as $aluno)
                    <div class="participant-item" data-user-id="{{ $aluno->id }}">
                        <div class="participant-item-avatar"
                             style="background:linear-gradient(135deg,{{ $aluno->cor_primaria ?? 'var(--success-color)' }},#3ce094);">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="participant-item-info">
                            <div class="participant-item-name">{{ $aluno->name }}</div>
                            <div class="participant-item-role">Aluno</div>
                        </div>
                        <div class="participant-item-status">
                            <div class="status-icon {{ $aluno->mutado ? 'muted' : '' }}" title="Microfone">
                                <i class="fas {{ $aluno->mutado ? 'fa-microphone-slash' : 'fa-microphone' }}"></i>
                            </div>
                            @if($aluno->mao_levantada)
                            <div class="status-icon hand-raised" title="Mão levantada">
                                <i class="fas fa-hand-paper"></i>
                            </div>
                            @endif
                            <div class="status-icon kick" title="Remover da sala">
                                <i class="fas fa-user-minus"></i>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    {{-- Exemplos estáticos --}}
                    @php
                        $exemploAlunos = [
                            ['nome' => 'Maria Silva',      'cor' => 'var(--success-color),#3ce094', 'mutado' => false, 'mao' => false],
                            ['nome' => 'Pedro Santos',     'cor' => 'var(--warning-color),#ffb976', 'mutado' => false, 'mao' => false],
                            ['nome' => 'Ana Costa',        'cor' => 'var(--info-color),#3ee5fe',    'mutado' => false, 'mao' => true],
                            ['nome' => 'Carlos Oliveira',  'cor' => 'var(--danger-color),#f37575',  'mutado' => true,  'mao' => false],
                            ['nome' => 'Julia Mendes',     'cor' => '#ff6b9d,#ff8fab',              'mutado' => false, 'mao' => false],
                            ['nome' => 'Lucas Ferreira',   'cor' => '#4facfe,#00f2fe',              'mutado' => true,  'mao' => false],
                        ];
                    @endphp

                    @foreach($exemploAlunos as $aluno)
                    <div class="participant-item">
                        <div class="participant-item-avatar"
                             style="background:linear-gradient(135deg,{{ $aluno['cor'] }});">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="participant-item-info">
                            <div class="participant-item-name">{{ $aluno['nome'] }}</div>
                            <div class="participant-item-role">Aluno</div>
                        </div>
                        <div class="participant-item-status">
                            <div class="status-icon {{ $aluno['mutado'] ? 'muted' : '' }}" title="Microfone">
                                <i class="fas {{ $aluno['mutado'] ? 'fa-microphone-slash' : 'fa-microphone' }}"></i>
                            </div>
                            @if($aluno['mao'])
                            <div class="status-icon hand-raised" title="Mão levantada">
                                <i class="fas fa-hand-paper"></i>
                            </div>
                            @endif
                            <div class="status-icon kick" title="Remover da sala">
                                <i class="fas fa-user-minus"></i>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endisset

            </div>
        </div>

        {{-- ── TAB: CONTEÚDOS / MATERIAIS ── --}}
        <div class="sidebar-content" id="materials-tab" style="display:none;flex-direction:column;">

            <button class="btn-add-material" id="btn-add-material">
                <i class="fas fa-plus-circle"></i> Adicionar conteúdo
            </button>

            <p class="section-label">Materiais desta aula</p>

            <div class="materials-list" id="materials-list">

                @isset($materiais)
                    @foreach($materiais as $material)
                    <div class="material-item">
                        <div class="material-header">
                            <div class="material-icon {{ $material->tipo }}">
                                <i class="fas {{ $material->icone }}"></i>
                            </div>
                            <div class="material-info">
                                <h4>{{ $material->titulo }}</h4>
                                <p>{{ $material->tamanho ?? '—' }}</p>
                            </div>
                        </div>
                        <div class="material-actions">
                            <button class="btn-material">
                                <i class="fas fa-share-alt"></i> Compartilhar
                            </button>
                            <button class="btn-material">
                                <i class="fas fa-download"></i> Baixar
                            </button>
                            <button class="btn-material danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    @endforeach
                @else
                    {{-- Exemplos estáticos --}}
                    <div class="material-item">
                        <div class="material-header">
                            <div class="material-icon slide"><i class="fas fa-file-powerpoint"></i></div>
                            <div class="material-info">
                                <h4>Trigonometria – Slides</h4>
                                <p>2,4 MB · PowerPoint</p>
                            </div>
                        </div>
                        <div class="material-actions">
                            <button class="btn-material"><i class="fas fa-share-alt"></i> Compartilhar</button>
                            <button class="btn-material danger"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>

                    <div class="material-item">
                        <div class="material-header">
                            <div class="material-icon pdf"><i class="fas fa-file-pdf"></i></div>
                            <div class="material-info">
                                <h4>Lista de Exercícios</h4>
                                <p>340 KB · PDF</p>
                            </div>
                        </div>
                        <div class="material-actions">
                            <button class="btn-material"><i class="fas fa-share-alt"></i> Compartilhar</button>
                            <button class="btn-material danger"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>

                    <div class="material-item">
                        <div class="material-header">
                            <div class="material-icon video"><i class="fas fa-film"></i></div>
                            <div class="material-info">
                                <h4>Vídeo Explicativo</h4>
                                <p>45 min · MP4</p>
                            </div>
                        </div>
                        <div class="material-actions">
                            <button class="btn-material"><i class="fas fa-share-alt"></i> Compartilhar</button>
                            <button class="btn-material danger"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                @endisset

            </div>
        </div>

        {{-- ── TAB: ENQUETE ── --}}
        <div class="sidebar-content" id="enquete-tab" style="display:none;flex-direction:column;">

            <div class="poll-section">

                <p class="section-label">Enquete ativa</p>

                <div class="poll-card">
                    <div class="poll-question">Você entendeu o conceito de seno e cosseno?</div>

                    <div class="poll-option">
                        <div class="poll-option-label">
                            <span>Sim, entendi bem!</span>
                            <span>58%</span>
                        </div>
                        <div class="poll-bar"><div class="poll-bar-fill" style="width:58%;background:var(--success-color);"></div></div>
                    </div>

                    <div class="poll-option">
                        <div class="poll-option-label">
                            <span>Mais ou menos</span>
                            <span>29%</span>
                        </div>
                        <div class="poll-bar"><div class="poll-bar-fill" style="width:29%;background:var(--warning-color);"></div></div>
                    </div>

                    <div class="poll-option">
                        <div class="poll-option-label">
                            <span>Não entendi</span>
                            <span>13%</span>
                        </div>
                        <div class="poll-bar"><div class="poll-bar-fill" style="width:13%;background:var(--danger-color);"></div></div>
                    </div>

                    <div style="margin-top:12px;">
                        <button class="btn-material danger" style="width:100%;">
                            <i class="fas fa-stop"></i> Encerrar enquete
                        </button>
                    </div>
                </div>

                <button class="btn-new-poll" style="margin-top:12px;">
                    <i class="fas fa-plus"></i> Nova enquete
                </button>

            </div>
        </div>

    </div>{{-- /sidebar --}}

</div>{{-- /main-container --}}


{{-- ===================== MODAL: Upload de material ===================== --}}
<div class="modal-overlay" id="modal-upload">
    <div class="modal-box">
        <div class="modal-title">
            <i class="fas fa-cloud-upload-alt" style="color:var(--primary-color);margin-right:8px;"></i>
            Adicionar conteúdo
        </div>

        <div class="form-group">
            <label class="form-label" for="material-title">Título do material</label>
            <input type="text" class="form-control" id="material-title" placeholder="Ex.: Slides – Aula 3">
        </div>

        <div class="form-group">
            <label class="form-label">Arquivo</label>
            <div class="drop-zone" id="drop-zone">
                <i class="fas fa-cloud-upload-alt"></i>
                <span>Arraste um arquivo ou clique para selecionar</span>
            </div>
            <input type="file" id="file-input" style="display:none;"
                   accept=".pdf,.ppt,.pptx,.mp4,.mov,.doc,.docx,.jpg,.png">
        </div>

        <div class="modal-actions">
            <button class="btn-modal secondary" id="btn-modal-cancel">Cancelar</button>
            <button class="btn-modal primary"   id="btn-modal-confirm">
                <i class="fas fa-check"></i> Adicionar
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/video-aula-professor.js') }}"></script>
@endpush