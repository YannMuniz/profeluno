{{-- resources/views/aluno/simulados/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Simulados')

@section('content')

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="page-header d-flex align-items-center justify-content-between mb-4">
    <div>
        <h1>Meus Simulados</h1>
        <p class="text-muted">Simulados que você realizou</p>
    </div>
    <a href="{{ route('aluno.salas.index') }}" class="btn-primary">
        <i class="fas fa-search"></i> Buscar Salas
    </a>
</div>

@if($simulados->count() > 0)

<div class="simulados-grid">
    @foreach($simulados as $simulado)
    <div class="simulado-card">
        <div class="simulado-header">
            <div class="simulado-icon">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="simulado-info">
                <h3>{{ $simulado['titulo'] ?? 'Simulado sem título' }}</h3>
                <p class="text-muted small">
                    @if(isset($simulado['idMateria']))
                        <i class="fas fa-book me-1"></i>{{ $simulado['materia'] ?? 'Matéria' }}
                    @endif
                </p>
            </div>
        </div>

        <div class="simulado-details">
            @if(isset($simulado['questoes']))
            <div class="detail-row">
                <i class="fas fa-list"></i>
                <span>{{ count($simulado['questoes']) }} questão(ões)</span>
            </div>
            @endif
            @if(isset($simulado['situacao']))
            <div class="detail-row">
                <i class="fas fa-{{ $simulado['situacao'] ? 'check-circle' : 'clock' }}"></i>
                <span>{{ $simulado['situacao'] ? 'Disponível' : 'Indisponível' }}</span>
            </div>
            @endif
        </div>

        <div class="simulado-footer">
            <a href="{{ route('aluno.simulados.show', isset($simulado['idSimulado']) ? $simulado['idSimulado'] : $simulado['id']) }}"
               class="btn-primary" style="flex:1;text-align:center">
                <i class="fas fa-play"></i> Realizar
            </a>
            <a href="{{ route('aluno.simulados.show', isset($simulado['idSimulado']) ? $simulado['idSimulado'] : $simulado['id']) }}"
               class="btn-secondary">
                <i class="fas fa-info-circle"></i>
            </a>
        </div>
    </div>
    @endforeach
</div>

{{-- Paginação --}}
<div class="pagination-wrapper mt-4">
    {{ $simulados->links() }}
</div>

@else

<div class="empty-state">
    <i class="fas fa-file-alt"></i>
    <h3>Nenhum simulado disponível</h3>
    <p>Você ainda não tem simulados para realizar.</p>
    <a href="{{ route('aluno.salas.index') }}" class="btn-primary mt-3">
        <i class="fas fa-search"></i> Buscar Salas
    </a>
</div>

@endif

<style>
.simulados-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
    margin-top: 30px;
}

.simulado-card {
    background: var(--card-bg, #fff);
    border: 1px solid var(--border-color, #e0e0e0);
    border-radius: 12px;
    padding: 20px;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
}

.simulado-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    border-color: var(--primary-color, #7367f0);
}

.simulado-header {
    display: flex;
    gap: 15px;
    margin-bottom: 15px;
}

.simulado-icon {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    background: rgba(115, 103, 240, 0.15);
    color: #7367f0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    flex-shrink: 0;
}

.simulado-info h3 {
    font-size: 16px;
    font-weight: 600;
    margin: 0 0 4px 0;
    color: var(--text-color, #2b2b40);
}

.simulado-details {
    flex: 1;
    margin: 15px 0;
}

.detail-row {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 13px;
    color: var(--text-muted, #666);
    margin-bottom: 8px;
}

.simulado-footer {
    display: flex;
    gap: 10px;
    padding-top: 15px;
    border-top: 1px solid var(--border-color, #e0e0e0);
}

.btn-primary, .btn-secondary {
    padding: 10px 16px;
    border-radius: 8px;
    border: none;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    font-size: 13px;
}

.btn-primary {
    background: #7367f0;
    color: white;
}

.btn-primary:hover {
    background: #6258d3;
}

.btn-secondary {
    background: #f0f0f0;
    color: #666;
}

.btn-secondary:hover {
    background: #e8e8e8;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: var(--text-muted, #666);
}

.empty-state i {
    font-size: 64px;
    margin-bottom: 20px;
    opacity: 0.3;
}

.empty-state h3 {
    font-size: 24px;
    font-weight: 600;
    margin: 20px 0 10px;
    color: var(--text-color, #2b2b40);
}

.empty-state p {
    font-size: 14px;
    margin-bottom: 20px;
}

.pagination-wrapper {
    display: flex;
    justify-content: center;
}

.pagination-wrapper .pagination {
    display: flex;
    gap: 5px;
    list-style: none;
    padding: 0;
    margin: 0;
}

.pagination-wrapper .pagination a,
.pagination-wrapper .pagination span {
    padding: 8px 12px;
    border: 1px solid var(--border-color, #e0e0e0);
    border-radius: 6px;
    text-decoration: none;
    color: var(--text-color, #2b2b40);
    font-weight: 500;
    transition: all 0.3s ease;
}

.pagination-wrapper .pagination a:hover {
    background: #7367f0;
    color: white;
    border-color: #7367f0;
}

.pagination-wrapper .pagination .active span {
    background: #7367f0;
    color: white;
    border-color: #7367f0;
}
</style>

@endsection
