{{-- resources/views/partials/header.blade.php --}}
<div class="header">
    <div class="header-left">
        <h1>{{ $title ?? 'Dashboard' }}</h1>
        <p>{{ $subtitle ?? 'Bem-vindo ao sistema' }}</p>
    </div>
    <div class="header-right">
        {{-- <button class="notification-btn" id="notificationBtn">
            <i class="fas fa-bell"></i>
            @if(isset($notificationCount) && $notificationCount > 0)
                <span class="notification-badge">{{ $notificationCount }}</span>
            @endif
        </button> --}}
        <button class="profile-btn" id="profileBtn">
            <i class="fas fa-user"></i>
            @if(session('user_nome'))
                <span class="profile-name">{{ session('user_nome') }}</span>
            @endif
        </button>
    </div>
</div>