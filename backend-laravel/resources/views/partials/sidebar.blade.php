{{-- resources/views/partials/sidebar.blade.php --}}
<div class="sidebar">
    <div class="sidebar-header">
        <a href="{{ route('home') }}" class="logo">
            <div class="logo-icon">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div class="logo-text">
                <h2>ProfeLuno</h2>
                @php $cargo = session('user_cargo') @endphp
                @if($cargo === 'admin')
                    <p>Admin Dashboard</p>
                @elseif($cargo === 'professor')
                    <p>Professor Dashboard</p>
                @else
                    <p>Aluno Dashboard</p>
                @endif
            </div>
        </a>
    </div>

    <nav class="sidebar-menu">

        {{-- ── ADMIN ──────────────────────────────────────────── --}}
        @if($cargo === 'admin')
            <div class="menu-item">
                <a href="{{ route('admin.dashboard') }}"
                   class="menu-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </div>
            <div class="menu-item">
                <a href="{{ route('admin.materias.index') }}"
                   class="menu-link {{ request()->routeIs('admin.materias.*') ? 'active' : '' }}">
                    <i class="fas fa-book"></i>
                    <span>Gerenciar Matérias</span>
                </a>
            </div>
            <div class="menu-item">
                <a href="{{ route('admin.usuarios.index') }}"
                   class="menu-link {{ request()->routeIs('admin.usuarios.*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i>
                    <span>Gerenciar Usuários</span>
                </a>
            </div>
            <div class="menu-item">
                <a href="{{ route('admin.cargos.index') }}"
                   class="menu-link {{ request()->routeIs('admin.cargos.*') ? 'active' : '' }}">
                    <i class="fas fa-user-tag"></i>
                    <span>Gerenciar Cargos</span>
                </a>
            </div>
            <div class="menu-item">
                <a href="{{ route('admin.areas.index') }}"
                   class="menu-link {{ request()->routeIs('admin.areas.*') ? 'active' : '' }}">
                    <i class="fas fa-layer-group"></i>
                    <span>Gerenciar Áreas</span>
                </a>
            </div>
            <div class="menu-item">
                <a href="{{ route('admin.escolaridades.index') }}"
                   class="menu-link {{ request()->routeIs('admin.escolaridades.*') ? 'active' : '' }}">
                    <i class="fas fa-school"></i>
                    <span>Gerenciar Escolaridades</span>
                </a>
            </div>

        {{-- ── PROFESSOR ──────────────────────────────────────── --}}
        @elseif($cargo === 'professor')
            <div class="menu-item">
                <a href="{{ route('professor.dashboard') }}"
                   class="menu-link {{ request()->routeIs('professor.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i>
                    <span>Dashboard</span>
                </a>
            </div>
            <div class="menu-item">
                <a href="{{ route('professor.salas.index') }}"
                   class="menu-link {{ request()->routeIs('professor.salas.index') ? 'active' : '' }}">
                    <i class="fas fa-chalkboard-user"></i>
                    <span>Minhas Salas</span>
                </a>
            </div>
            <div class="menu-item">
                <a href="{{ route('professor.conteudo.index') }}"
                   class="menu-link {{ request()->routeIs('professor.conteudo.index') ? 'active' : '' }}">
                    <i class="fas fa-book"></i>
                    <span>Conteúdos</span>
                </a>
            </div>
            <div class="menu-item">
                <a href="{{ route('professor.simulados.index') }}"
                   class="menu-link {{ request()->routeIs('professor.simulados.index') ? 'active' : '' }}">
                    <i class="fas fa-clipboard-check"></i>
                    <span>Simulados</span>
                </a>
            </div>
            {{-- <div class="menu-item">
                <a href="{{ route('professor.relatorios') }}"
                   class="menu-link {{ request()->routeIs('professor.relatorios') ? 'active' : '' }}">
                    <i class="fas fa-file-alt"></i>
                    <span>Relatórios</span>
                </a>
            </div> --}}

        {{-- ── ALUNO ──────────────────────────────────────────── --}}
        @else
            <div class="menu-item">
                <a href="{{ route('aluno.dashboard') }}"
                   class="menu-link {{ request()->routeIs('aluno.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i>
                    <span>Início</span>
                </a>
            </div>
            <div class="menu-item">
                <a href="{{ route('aluno.salas.index') }}"
                   class="menu-link {{ request()->routeIs('aluno.salas.*') ? 'active' : '' }}">
                    <i class="fas fa-search"></i>
                    <span>Buscar Salas</span>
                </a>
            </div>
            <div class="menu-item">
                <a href="{{ route('aluno.historico') }}"
                   class="menu-link {{ request()->routeIs('aluno.historico*') ? 'active' : '' }}">
                    <i class="fas fa-book"></i>
                    <span>Minhas Aulas</span>
                </a>
            </div>
            <div class="menu-item">
                <a href="{{ route('aluno.simulados') }}"
                   class="menu-link {{ request()->routeIs('aluno.simulados*') ? 'active' : '' }}">
                    <i class="fas fa-file-alt"></i>
                    <span>Simulados</span>
                </a>
            </div>
        @endif

        <hr style="border-color: var(--border-color); margin: 20px 15px;">

        <div class="menu-item">
            <form id="logout-form" method="POST" action="{{ route('logout') }}" style="margin: 0;">
                @csrf
                <button type="button" onclick="openLogoutModal()"
                    class="menu-link"
                    style="width:100%; border:none; background:none; padding:12px 15px; text-align:left; cursor:pointer;">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Sair</span>
                </button>
            </form>
        </div>

    </nav>
</div>

{{-- Modal de logout --}}
<div id="logout-modal" class="logout-modal-overlay" onclick="closeLogoutModal(event)">
    <div class="logout-modal-box">
        <div class="logout-modal-icon">
            <i class="fas fa-sign-out-alt"></i>
        </div>
        <h3 class="logout-modal-title">Tem certeza que deseja sair?</h3>
        <br>
        <div class="logout-modal-actions">
            <button type="button" class="logout-btn-cancel" onclick="closeLogoutModal()">
                Cancelar
            </button>
            <button type="button" class="logout-btn-confirm" onclick="confirmLogout()">
                <i class="fas fa-sign-out-alt"></i>
                Sair
            </button>
        </div>
    </div>
</div>

<script>
    function openLogoutModal() {
        document.getElementById('logout-modal').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeLogoutModal(event) {
        if (event && event.target !== document.getElementById('logout-modal')) return;
        document.getElementById('logout-modal').classList.remove('active');
        document.body.style.overflow = '';
    }

    function confirmLogout() {
        document.getElementById('logout-form').submit();
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('logout-modal');
            if (modal.classList.contains('active')) {
                modal.classList.remove('active');
                document.body.style.overflow = '';
            }
        }
    });
</script>