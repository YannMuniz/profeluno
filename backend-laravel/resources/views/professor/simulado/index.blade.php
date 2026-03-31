{{-- resources/views/professor/simulado/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Meus Simulados')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/forms.css') }}">
@endpush

@section('content')

<div class="page-header">
    <a href="{{ route('professor.simulados.create') }}" class="btn-create">
        <i class="fas fa-plus"></i>
        Novo Simulado
    </a>
</div>

@if(session('success'))
<div class="alert alert-success">
    <i class="fas fa-check-circle"></i>
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert alert-danger">
    <i class="fas fa-exclamation-circle"></i>
    {{ session('error') }}
</div>
@endif

<div class="table-wrapper">
    <div class="table-toolbar">
        <div class="table-search">
            <input type="text" id="searchInput" placeholder="Buscar simulado, sala, matéria...">
            <i class="fas fa-search"></i>
        </div>
        <div style="display: flex; align-items: center; gap: 12px;">
            <select id="filterSituacao" class="filter-select" style="height: 38px; font-size: 13px;">
                <option value="">Todas as situações</option>
                <option value="1">Ativos</option>
                <option value="0">Inativos</option>
            </select>
            <span class="table-count" id="tableCount">
                {{ count($simulados ?? 0) }} simulado(s) encontrado(s)
            </span>
        </div>
    </div>

    <table class="data-table" id="simuladosTable">
        <thead>
            <tr>
                <th>#</th>
                <th>Simulado</th>
                <th>Descrição</th>
                <th>Matéria</th>
                <th>Questões</th>
                <th>Situação</th>
                <th>Criado em</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($simulados as $simulado)
            @php
                $id             = $simulado['idSimulado']          ?? '—';
                $titulo         = $simulado['titulo']       ?? '—';
                $descricao      = $simulado['descricao']    ?? null;
                $idMateria      = $simulado['idMateria']      ?? '—';
                $nomeMateria    = $simulado['materia']['nomeMateria']      ?? '—';
                $questoes       = $simulado['qtd_questoes'] ?? 0;
                $situacao       = $simulado['situacao']     ?? 1;
                $criadoEm       = $simulado['createdAt']    ?? null;

                // Cor da barra de progresso de questões
                $barCor = match(true) {
                    $questoes >= 20 => ['cor' => '#28c76f', 'bg' => 'rgba(40,199,111,0.12)'],
                    $questoes >= 10 => ['cor' => '#7367f0', 'bg' => 'rgba(115,103,240,0.12)'],
                    default         => ['cor' => '#ff9f43', 'bg' => 'rgba(255,159,67,0.12)'],
                };
            @endphp
            <tr data-situacao="{{ $situacao }}">
                <td>{{ $id }}</td>

                {{-- Título --}}
                <td>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div class="avatar-initial"
                             style="background: rgba(115,103,240,0.12); color: var(--primary-color); border: 1px solid rgba(115,103,240,0.25);">
                            <i class="fas fa-list-ol" style="font-size: 14px;"></i>
                        </div>
                        <strong>{{ $titulo }}</strong>
                    </div>
                </td>

                {{-- Descrição (coluna nova) --}}
                <td style="max-width: 220px;">
                    @if($descricao)
                        <span
                            style="font-size: 12px; color: var(--text-secondary); display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;"
                            title="{{ $descricao }}">
                            {{ $descricao }}
                        </span>
                    @else
                        <span style="color: var(--text-secondary); font-size: 12px;">—</span>
                    @endif
                </td>

                {{-- Matéria --}}
                <td>
                    <span class="badge"
                          style="background: rgba(0,207,232,0.1); color: var(--info-color); border: 1px solid rgba(0,207,232,0.25);">
                        <i class="fas fa-book"></i>
                        {{ $nomeMateria }}
                    </span>
                </td>

                {{-- Questões --}}
                <td>
                    <div style="display: flex; align-items: center; gap: 10px; min-width: 100px;">
                        <div style="flex: 1; height: 6px; background: var(--border-color); border-radius: 6px; overflow: hidden;">
                            <div style="height: 100%; width: {{ min($simulado['quantidadeQuestoes'] * 5, 100) }}%; background: {{ $barCor['cor'] }}; border-radius: 6px; transition: width 0.4s;"></div>
                        </div>
                        <span style="font-size: 13px; font-weight: 700; color: {{ $barCor['cor'] }}; white-space: nowrap;">
                            {{ $simulado['quantidadeQuestoes'] }}
                        </span>
                    </div>
                </td>


                {{-- Situação --}}
                <td>
                    <span class="badge {{ $situacao ? 'badge-ativo' : 'badge-inativo' }}">
                        <i class="fas fa-{{ $situacao ? 'check-circle' : 'times-circle' }}"></i>
                        {{ $situacao ? 'Ativo' : 'Inativo' }}
                    </span>
                </td>

                {{-- Data --}}
                <td>
                    {{ $criadoEm ? \Carbon\Carbon::parse($criadoEm)->format('d/m/Y') : '—' }}
                </td>

                {{-- Ações --}}
                <td>
                    <div class="td-actions">
                        <a href="{{ route('professor.simulados.show', $id) }}"
                           class="btn-edit" title="Visualizar questões">
                            <i class="fas fa-eye"></i>
                            Ver
                        </a>
                        <a href="{{ route('professor.simulados.edit', $id) }}"
                           class="btn-edit" title="Editar simulado">
                            <i class="fas fa-pen"></i>
                            Editar
                        </a>
                        <button class="btn-delete"
                            onclick="openDeleteModal('{{ $id }}', '{{ addslashes($titulo) }}')">
                            <i class="fas fa-trash-alt"></i>
                            Excluir
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr id="emptyRow">
                <td colspan="9">
                    <div class="table-empty">
                        <i class="fas fa-list-ol"></i>
                        <p>Nenhum simulado cadastrado ainda.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if(isset($simulados) && is_object($simulados) && method_exists($simulados, 'links'))
<div class="pagination-wrapper">
    {{ $simulados->links() }}
</div>
@endif

{{-- Modal de exclusão --}}
<div class="modal-overlay" id="deleteModal">
    <div class="modal-box">
        <div class="modal-icon">
            <i class="fas fa-trash-alt"></i>
        </div>
        <h4>Excluir Simulado</h4>
        <p>Tem certeza que deseja excluir <strong id="deleteSimuladoName"></strong>? Todas as questões vinculadas também serão removidas.</p>
        <div class="modal-actions">
            <button class="btn-cancel" onclick="closeDeleteModal()">
                <i class="fas fa-times"></i>
                Cancelar
            </button>
            <form id="deleteForm" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-delete" style="padding: 13px 22px;">
                    <i class="fas fa-trash-alt"></i>
                    Confirmar Exclusão
                </button>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // ── Filtros ──────────────────────────────────────────────────
    document.getElementById('searchInput').addEventListener('input', applyFilters);
    document.getElementById('filterSituacao').addEventListener('change', applyFilters);

    function applyFilters() {
        const q        = document.getElementById('searchInput').value.toLowerCase();
        const situacao = document.getElementById('filterSituacao').value;
        let visible    = 0;

        document.querySelectorAll('#simuladosTable tbody tr:not(#emptyRow)').forEach(row => {
            const text        = row.textContent.toLowerCase();
            const rowSituacao = row.dataset.situacao;

            const matchQ        = !q        || text.includes(q);
            const matchSituacao = !situacao  || rowSituacao === situacao;

            row.style.display = (matchQ && matchSituacao) ? '' : 'none';
            if (matchQ && matchSituacao) visible++;
        });

        document.getElementById('tableCount').textContent =
            `${visible} simulado(s) encontrado(s)`;
    }

    // ── Modal de exclusão ────────────────────────────────────────
    function openDeleteModal(id, nome) {
        document.getElementById('deleteSimuladoName').textContent = nome;
        document.getElementById('deleteForm').action = `/professor/simulados/${id}`;
        document.getElementById('deleteModal').classList.add('active');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.remove('active');
    }

    document.getElementById('deleteModal').addEventListener('click', function (e) {
        if (e.target === this) closeDeleteModal();
    });
</script>
@endpush