{{-- resources/views/partials/header.blade.php --}}
<div class="header">
    <div class="header-left">
        <br>
        @if(isset($ultimapagina))
            {!! $ultimapagina !!}
        @endif
        <h1>{!! $title ?? 'Dashboard' !!}</h1>
        <p>{{ $subtitle ?? 'Bem-vindo ao sistema' }}</p>
    </div>
    <div class="header-right">
        <a href="{{ route('perfil.edit') }}" class="profile-btn" id="profileBtn">
            <i class="fas fa-user"></i>
            @if(session('user_nome'))
                <span class="profile-name">{{ session('user_nome') }}</span>
            @endif
        </a>
    </div>
</div>