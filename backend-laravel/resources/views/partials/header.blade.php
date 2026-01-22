{{-- resources/views/partials/header.blade.php --}}
<div class="header">
    <div class="header-left">
        <h1>{{ $title ?? 'Dashboard' }}</h1>
        <p>{{ $subtitle ?? 'Bem-vindo ao sistema' }}</p>
    </div>
    <div class="header-right">
        <button class="notification-btn" id="notificationBtn">
            <i class="fas fa-bell"></i>
            @if(isset($notificationCount) && $notificationCount > 0)
                <span class="notification-badge">{{ $notificationCount }}</span>
            @endif
        </button>
        <button class="profile-btn" id="profileBtn">
            <i class="fas fa-user"></i>
        </button>
    </div>
</div>