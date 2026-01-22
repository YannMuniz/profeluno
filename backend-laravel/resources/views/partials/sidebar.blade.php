{{-- resources/views/partials/sidebar.blade.php --}}
<div class="sidebar">
    <div class="sidebar-header">
        <a href="{{ route('dashboard') }}" class="logo">
            <div class="logo-icon">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div class="logo-text">
                <h2>ProfeLuno</h2>
                <p>{{ $userType ?? 'Dashboard' }}</p>
            </div>
        </a>
    </div>
    
    <nav class="sidebar-menu">
        @foreach($menuItems as $item)
            <div class="menu-item" @if($item['separator'] ?? false) style="margin-top: 30px;" @endif>
                <a href="{{ $item['route'] }}" class="menu-link {{ $item['active'] ?? false ? 'active' : '' }}">
                    <i class="{{ $item['icon'] }}"></i>
                    <span>{{ $item['label'] }}</span>
                </a>
            </div>
        @endforeach
    </nav>
</div>