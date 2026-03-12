{{-- resources/views/partials/sidebar.blade.php --}}
<div class="sidebar">
    <div class="sidebar-header">
        <a href="{{ route('home') }}" class="logo">
            <div class="logo-icon">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div class="logo-text">
                <h2>ProfeLuno</h2>
                <p>Sistema de Aulas</p>
            </div>
        </a>
    </div>

    <nav class="sidebar-menu">
        @if (Auth::user()->cargo?->nome_cargo === 'professor')
            <div class="menu-item">
                <a href="{{ route('professor.dashboard') }}" class="menu-link {{ request()->routeIs('professor.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i>
                    <span>Dashboard</span>
                </a>
            </div>
            <div class="menu-item">
                <a href="{{ route('professor.classrooms') }}" class="menu-link {{ request()->routeIs('professor.*') ? 'active' : '' }}">
                    <i class="fas fa-chalkboard-user"></i>
                    <span>Minhas Salas</span>
                </a>
            </div>
            <div class="menu-item">
                <a href="#" class="menu-link">
                    <i class="fas fa-book"></i>
                    <span>Conteúdos</span>
                </a>
            </div>
            <div class="menu-item">
                <a href="#" class="menu-link">
                    <i class="fas fa-clipboard-check"></i>
                    <span>Avaliações</span>
                </a>
            </div>
        @else
            <div class="menu-item">
                <a href="{{ route('aluno.dashboard') }}" class="menu-link {{ request()->routeIs('aluno.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i>
                    <span>Início</span>
                </a>
            </div>
            <div class="menu-item">
                <a href="{{ route('aluno.browse') }}" class="menu-link {{ request()->routeIs('aluno.browse') ? 'active' : '' }}">
                    <i class="fas fa-search"></i>
                    <span>Buscar Salas</span>
                </a>
            </div>
            <div class="menu-item">
                <a href="#" class="menu-link">
                    <i class="fas fa-book"></i>
                    <span>Minhas Aulas</span>
                </a>
            </div>
            <div class="menu-item">
                <a href="#" class="menu-link">
                    <i class="fas fa-history"></i>
                    <span>Histórico</span>
                </a>
            </div>
        @endif

        <hr style="border-color: var(--border-color); margin: 20px 15px;">

        <div class="menu-item">
            <a href="#" class="menu-link">
                <i class="fas fa-cog"></i>
                <span>Configurações</span>
            </a>
        </div>
        <div class="menu-item">
            <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                @csrf
                <button type="submit" class="menu-link" style="width: 100%; border: none; background: none; padding: 12px 15px; text-align: left; cursor: pointer;">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Sair</span>
                </button>
            </form>
        </div>
    </nav>
</div>