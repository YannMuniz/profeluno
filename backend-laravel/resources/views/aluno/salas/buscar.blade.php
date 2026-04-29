{{-- resources/views/aluno/salas/buscar.blade.php --}}
@extends('layouts.app')

@section('title', 'Buscar Sala de Aula')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/buscar-sala.css') }}">
@endpush

@section('content')

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Search Section --}}
<div class="search-section">
    <h2 class="search-title">Encontre sua Sala de Aula</h2>
    <form class="search-form" method="GET" action="{{ route('aluno.salas.index') }}">
        <div class="search-input-group">
            <input type="text" name="q"
                   placeholder="Buscar por título ou professor..."
                   value="{{ request('q') }}">
            <i class="fas fa-search"></i>
        </div>
        <div class="search-input-group">
            <select name="materia">
                <option value="">Todas as Matérias</option>
                {{-- $materias é array de arrays vindos da API --}}
                @foreach($materias as $mat)
                    <option value="{{ $mat['idMateria'] }}"
                        {{ request('materia') == $mat['idMateria'] ? 'selected' : '' }}>
                        {{ $mat['nomeMateria'] }}
                    </option>
                @endforeach
            </select>
            <i class="fas fa-book"></i>
        </div>
        <button type="submit" class="btn-search">
            <i class="fas fa-search"></i>
            <span>Buscar</span>
        </button>
    </form>
</div>

{{-- Filter Chips --}}
<div class="filter-chips">
    <div class="chip {{ !request('filtro') ? 'active' : '' }}" data-filter="">
        <i class="fas fa-list"></i> Todas
    </div>
    <div class="chip {{ request('filtro') == 'ao-vivo' ? 'active' : '' }}" data-filter="ao-vivo">
        <i class="fas fa-circle" style="font-size:8px;color:var(--success-color)"></i> Ao Vivo Agora
    </div>
    <div class="chip {{ request('filtro') == 'agendadas' ? 'active' : '' }}" data-filter="agendadas">
        <i class="fas fa-calendar"></i> Agendadas
    </div>
</div>

{{-- Results Header --}}
<div class="results-header">
    <div>
        <h2 class="section-title">
            <i class="fas fa-chalkboard-teacher"></i>
            Salas Disponíveis
        </h2>
        <p class="results-count">{{ count($salas) }} sala(s) encontrada(s)</p>
    </div>
    <select class="sort-select" id="sortSelect">
        <option value="recentes" {{ request('ordenar', 'recentes') == 'recentes' ? 'selected' : '' }}>Mais Recentes</option>
        <option value="ao-vivo"  {{ request('ordenar') == 'ao-vivo'  ? 'selected' : '' }}>Ao Vivo Primeiro</option>
        <option value="alunos"   {{ request('ordenar') == 'alunos'   ? 'selected' : '' }}>Mais Alunos</option>
    </select>
</div>

{{-- Teachers Grid --}}
<div class="teachers-grid">
    @forelse($salas as $sala)
    <div class="teacher-card">

        @if($sala->status === 'active')
        <div class="live-badge">
            <i class="fas fa-circle"></i> AO VIVO
        </div>
        @endif

        <div class="teacher-header">
            <div class="teacher-avatar">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <div class="teacher-info">
                <h3>{{ $sala->titulo }}</h3>
                <p class="teacher-subject">{{ $sala->materia }}</p>
            </div>
        </div>

        <div class="teacher-details">
            <div class="detail-row">
                <i class="fas fa-users"></i>
                <span>Até {{ $sala->qtd_alunos }} alunos</span>
            </div>
            <div class="detail-row">
                <i class="fas fa-{{ $sala->status === 'active' ? 'circle' : 'calendar' }}"></i>
                <span>
                    @if($sala->status === 'active')
                        Aula ao vivo agora
                    @elseif($sala->data_hora_inicio)
                        {{ $sala->data_hora_inicio->format('d/m/Y \à\s H:i') }}
                    @else
                        Data a definir
                    @endif
                </span>
            </div>
            @if($sala->descricao)
            <div class="detail-row">
                <i class="fas fa-info-circle"></i>
                <span>{{ Str::limit($sala->descricao, 60) }}</span>
            </div>
            @endif
        </div>

        <div class="teacher-tags">
            <span class="tag">{{ $sala->materia }}</span>
            @if($sala->status === 'active')
                <span class="tag" style="background:rgba(40,199,111,.15);color:#28c76f">Ao Vivo</span>
            @elseif($sala->status === 'pending')
                <span class="tag" style="background:rgba(255,159,67,.15);color:#ff9f43">Agendada</span>
            @endif
        </div>

        <div class="teacher-footer">
            @if($sala->status === 'active')
                <form action="{{ route('aluno.join', $sala->id) }}" method="POST" style="flex:1">
                    @csrf
                    <button type="submit" class="btn-primary" style="width:100%">
                        <i class="fas fa-sign-in-alt"></i> Entrar na Aula
                    </button>
                </form>
            @else
                <a href="{{ route('aluno.show', $sala->id) }}"
                   class="btn-primary" style="flex:1;text-align:center">
                    <i class="fas fa-clock"></i> Ver Detalhes
                </a>
            @endif
            <a href="{{ route('aluno.show', $sala->id) }}" class="btn-secondary">
                <i class="fas fa-info-circle"></i>
            </a>
        </div>

    </div>
    @empty
    <div class="empty-state" style="grid-column:1/-1">
        <i class="fas fa-search"></i>
        <h3>Nenhuma sala encontrada</h3>
        <p>Tente ajustar os filtros de busca.</p>
    </div>
    @endforelse
</div>

<div class="pagination-wrapper"></div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // Chips de filtro
    document.querySelectorAll('.chip').forEach(chip => {
        chip.addEventListener('click', function () {
            const filter = this.dataset.filter;
            const params = new URLSearchParams(window.location.search);
            if (filter) {
                params.set('filtro', filter);
            } else {
                params.delete('filtro');
            }
            window.location.href = `{{ route('aluno.salas.index') }}?${params.toString()}`;
        });
    });

    // Ordenação
    document.getElementById('sortSelect')?.addEventListener('change', function () {
        const params = new URLSearchParams(window.location.search);
        params.set('ordenar', this.value);
        window.location.href = `{{ route('aluno.buscar-sala') }}?${params.toString()}`;
    });

});
</script>
@endpush