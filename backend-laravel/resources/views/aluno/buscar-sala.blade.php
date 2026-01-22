{{-- resources/views/aluno/buscar-sala.blade.php --}}
@extends('layouts.app')

@section('title', 'Buscar Sala de Aula')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/buscar-sala.css') }}">
@endpush

@section('content')
<!-- Search Section -->
<div class="search-section">
    <h2 class="search-title">Encontre seu Professor Ideal</h2>
    <form class="search-form" method="GET" action="{{ route('aluno.buscar-sala') }}">
        <div class="search-input-group">
            <input type="text" name="q" placeholder="Buscar por professor ou tópico..." value="{{ request('q') }}">
            <i class="fas fa-search"></i>
        </div>
        <div class="search-input-group">
            <select name="materia">
                <option value="">Todas as Matérias</option>
                @foreach($materias ?? ['Matemática', 'Física', 'Química', 'Biologia', 'História', 'Geografia', 'Literatura', 'Inglês'] as $materia)
                <option value="{{ $materia }}" {{ request('materia') == $materia ? 'selected' : '' }}>
                    {{ $materia }}
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

<!-- Filter Chips -->
<div class="filter-chips">
    <div class="chip {{ request('filtro') == 'ao-vivo' ? 'active' : '' }}" data-filter="ao-vivo">
        <i class="fas fa-circle" style="font-size: 8px; color: var(--success-color);"></i> Ao Vivo Agora
    </div>
    <div class="chip {{ request('filtro') == 'melhor-avaliados' ? 'active' : '' }}" data-filter="melhor-avaliados">
        <i class="fas fa-star"></i> Melhor Avaliados
    </div>
    <div class="chip {{ request('filtro') == 'mais-alunos' ? 'active' : '' }}" data-filter="mais-alunos">
        <i class="fas fa-users"></i> Mais Alunos
    </div>
    <div class="chip {{ request('filtro') == 'disponivel-hoje' ? 'active' : '' }}" data-filter="disponivel-hoje">
        <i class="fas fa-clock"></i> Disponível Hoje
    </div>
    <div class="chip {{ request('filtro') == 'certificados' ? 'active' : '' }}" data-filter="certificados">
        <i class="fas fa-certificate"></i> Certificados
    </div>
</div>

<!-- Results Section -->
<div class="results-header">
    <div>
        <h2 class="section-title">
            <i class="fas fa-chalkboard-teacher"></i>
            Professores Disponíveis
        </h2>
        <p class="results-count">{{ count($professores ?? []) }} professores encontrados</p>
    </div>
    <select class="sort-select" name="ordenar">
        <option value="relevante" {{ request('ordenar') == 'relevante' ? 'selected' : '' }}>Mais Relevantes</option>
        <option value="avaliacao" {{ request('ordenar') == 'avaliacao' ? 'selected' : '' }}>Melhor Avaliação</option>
        <option value="alunos" {{ request('ordenar') == 'alunos' ? 'selected' : '' }}>Mais Alunos</option>
        <option value="ao-vivo" {{ request('ordenar') == 'ao-vivo' ? 'selected' : '' }}>Ao Vivo Agora</option>
    </select>
</div>

<!-- Teachers Grid -->
<div class="teachers-grid">
    @forelse($professores ?? [] as $professor)
    <div class="teacher-card">
        @if($professor->ao_vivo ?? false)
        <div class="live-badge">
            <i class="fas fa-circle"></i>
            AO VIVO
        </div>
        @endif
        
        <div class="teacher-header">
            <div class="teacher-avatar" style="background: {{ $professor->avatar_color ?? 'linear-gradient(135deg, var(--primary-color), #9f8cfe)' }};">
                @if($professor->foto)
                <img src="{{ asset('storage/' . $professor->foto) }}" alt="{{ $professor->nome }}">
                @else
                <i class="fas fa-user-tie"></i>
                @endif
            </div>
            <div class="teacher-info">
                <h3>{{ $professor->nome }}</h3>
                <p class="teacher-subject">{{ $professor->materia_principal }}</p>
                <div class="teacher-rating">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= floor($professor->avaliacao_media))
                        <i class="fas fa-star"></i>
                        @elseif($i - 0.5 <= $professor->avaliacao_media)
                        <i class="fas fa-star-half-alt"></i>
                        @else
                        <i class="far fa-star"></i>
                        @endif
                    @endfor
                    <strong>{{ number_format($professor->avaliacao_media, 1) }}</strong>
                    <span>({{ $professor->total_avaliacoes }} avaliações)</span>
                </div>
            </div>
        </div>

        <div class="teacher-details">
            <div class="detail-row">
                <i class="fas fa-graduation-cap"></i>
                <span>{{ $professor->especialidade }}</span>
            </div>
            <div class="detail-row">
                <i class="fas fa-users"></i>
                <span>{{ $professor->total_alunos }} alunos {{ $professor->ao_vivo ? 'online agora' : 'já estudaram' }}</span>
            </div>
            <div class="detail-row">
                <i class="fas fa-{{ $professor->ao_vivo ? 'clock' : 'calendar' }}"></i>
                <span>{{ $professor->ao_vivo ? 'Aula ao vivo: ' . $professor->aula_atual : 'Próxima aula: ' . $professor->proxima_aula }}</span>
            </div>
        </div>

        <div class="teacher-tags">
            @foreach($professor->tags ?? [] as $tag)
            <span class="tag">{{ $tag }}</span>
            @endforeach
        </div>

        <div class="teacher-footer">
            <button class="btn-primary" onclick="window.location.href='{{ route('sala.entrar', $professor->id) }}'">
                <i class="fas fa-{{ $professor->ao_vivo ? 'sign-in-alt' : 'calendar-plus' }}"></i>
                {{ $professor->ao_vivo ? 'Entrar na Sala' : 'Agendar Aula' }}
            </button>
            <button class="btn-secondary" onclick="window.location.href='{{ route('professor.detalhes', $professor->id) }}'">
                <i class="fas fa-info-circle"></i>
            </button>
        </div>
    </div>
    @empty
    {{-- Dados de exemplo caso não haja professores do banco --}}
    <div class="teacher-card">
        <div class="live-badge">
            <i class="fas fa-circle"></i>
            AO VIVO
        </div>
        <div class="teacher-header">
            <div class="teacher-avatar">
                <i class="fas fa-user-tie"></i>
            </div>
            <div class="teacher-info">
                <h3>Prof. João Silva</h3>
                <p class="teacher-subject">Matemática Avançada</p>
                <div class="teacher-rating">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <strong>4.9</strong>
                    <span>(127 avaliações)</span>
                </div>
            </div>
        </div>
        <div class="teacher-details">
            <div class="detail-row">
                <i class="fas fa-graduation-cap"></i>
                <span>Especialista em Cálculo e Trigonometria</span>
            </div>
            <div class="detail-row">
                <i class="fas fa-users"></i>
                <span>24 alunos online agora</span>
            </div>
            <div class="detail-row">
                <i class="fas fa-clock"></i>
                <span>Aula ao vivo: Trigonometria Avançada</span>
            </div>
        </div>
        <div class="teacher-tags">
            <span class="tag">Cálculo</span>
            <span class="tag">Trigonometria</span>
            <span class="tag">Funções</span>
            <span class="tag">Vestibular</span>
        </div>
        <div class="teacher-footer">
            <button class="btn-primary">
                <i class="fas fa-sign-in-alt"></i>
                Entrar na Sala
            </button>
            <button class="btn-secondary">
                <i class="fas fa-info-circle"></i>
            </button>
        </div>
    </div>
    @endforelse
</div>

@if(isset($professores) && $professores->hasPages())
<div class="pagination-wrapper">
    {{ $professores->links() }}
</div>
@endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filtros por chip
    const chips = document.querySelectorAll('.chip');
    chips.forEach(chip => {
        chip.addEventListener('click', function() {
            const filter = this.dataset.filter;
            window.location.href = `{{ route('aluno.buscar-sala') }}?filtro=${filter}`;
        });
    });
    
    // Ordenação
    const sortSelect = document.querySelector('.sort-select');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            const params = new URLSearchParams(window.location.search);
            params.set('ordenar', this.value);
            window.location.href = `{{ route('aluno.buscar-sala') }}?${params.toString()}`;
        });
    }
});
</script>
@endpush