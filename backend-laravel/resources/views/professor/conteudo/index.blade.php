{{-- resources/views/professor/conteudo/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Meus Conteúdos')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/forms.css') }}">
@endpush

@section('content')

<div class="page-header">
    {{-- <div class="page-header-info">
        <h2><i class="fas fa-folder-open"></i> Conteúdos</h2>
        <p>Gerencie os materiais e simulados das suas salas de aula</p>
    </div> --}}
    <a href="{{ route('professor.conteudo.create') }}" class="btn-create">
        <i class="fas fa-plus"></i>
        Novo Conteúdo
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
            <input type="text" id="searchInput" placeholder="Buscar conteúdo, matéria, sala...">
            <i class="fas fa-search"></i>
        </div>
        <div style="display: flex; align-items: center; gap: 12px;">
            <select id="filterTipo" class="filter-select" style="height: 38px; font-size: 13px;">
                <option value="">Todos os tipos</option>
                <option value="pdf">📄 PDF</option>
                <option value="slide">🖥️ Slide</option>
                <option value="video">🎬 Vídeo</option>
                <option value="document">📝 Documento</option>
                <option value="simulado">📋 Simulado</option>
                <option value="other">📦 Outro</option>
            </select>
            <span class="table-count" id="tableCount">
                {{ count($conteudos) }} conteúdo(s) encontrado(s)
            </span>
        </div>
    </div>

    <table class="data-table" id="conteudosTable">
        <thead>
            <tr>
                <th>#</th>
                <th>Título</th>
                <th>Sala de Aula</th>
                <th>Tipo</th>
                <th>Situação</th>
                <th>Criado em</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($conteudos as $conteudo)
            @php
                $id        = $conteudo['id']         ?? '—';
                $titulo    = $conteudo['titulo']      ?? '—';
                $sala      = $conteudo['sala']        ?? '—';
                $tipo      = $conteudo['tipo']        ?? 'other';
                $situacao  = $conteudo['situacao']    ?? 1;
                $criadoEm  = $conteudo['criado_em']   ?? null;

                $tipoIcones = [
                    'pdf'      => ['icon' => 'fa-file-pdf',   'cor' => '#ea5455', 'bg' => 'rgba(234,84,85,0.12)',    'label' => 'PDF'],
                    'slide'    => ['icon' => 'fa-desktop',    'cor' => '#ff9f43', 'bg' => 'rgba(255,159,67,0.12)',   'label' => 'Slide'],
                    'video'    => ['icon' => 'fa-film',       'cor' => '#00cfe8', 'bg' => 'rgba(0,207,232,0.12)',    'label' => 'Vídeo'],
                    'document' => ['icon' => 'fa-file-word',  'cor' => '#7367f0', 'bg' => 'rgba(115,103,240,0.12)', 'label' => 'Documento'],
                    'simulado' => ['icon' => 'fa-list-ol',    'cor' => '#28c76f', 'bg' => 'rgba(40,199,111,0.12)',  'label' => 'Simulado'],
                    'other'    => ['icon' => 'fa-file',       'cor' => '#82868b', 'bg' => 'rgba(130,134,139,0.12)', 'label' => 'Outro'],
                ];

                $info = $tipoIcones[$tipo] ?? $tipoIcones['other'];
            @endphp
            <tr data-tipo="{{ $tipo }}">
                <td>{{ $id }}</td>
                <td>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div class="avatar-initial"
                             style="background: {{ $info['bg'] }}; color: {{ $info['cor'] }}; border: 1px solid {{ $info['cor'] }}33;">
                            <i class="fas {{ $info['icon'] }}" style="font-size: 14px;"></i>
                        </div>
                        <div>
                            <strong>{{ $titulo }}</strong>
                            @if(!empty($conteudo['descricao']))
                            <p style="font-size: 12px; color: var(--text-secondary); margin: 2px 0 0;">
                                {{ Str::limit($conteudo['descricao'], 55) }}
                            </p>
                            @endif
                        </div>
                    </div>
                </td>
                <td>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-chalkboard-teacher"
                           style="color: var(--primary-color); font-size: 13px;"></i>
                        <span>{{ $sala }}</span>
                    </div>
                </td>
                <td>
                    <span class="badge"
                          style="background: {{ $info['bg'] }}; color: {{ $info['cor'] }}; border: 1px solid {{ $info['cor'] }}33;">
                        <i class="fas {{ $info['icon'] }}"></i>
                        {{ $info['label'] }}
                    </span>
                </td>
                <td>
                    <span class="badge {{ $situacao ? 'badge-ativo' : 'badge-inativo' }}">
                        <i class="fas fa-{{ $situacao ? 'check-circle' : 'times-circle' }}"></i>
                        {{ $situacao ? 'Ativo' : 'Inativo' }}
                    </span>
                </td>
                <td>
                    {{ $criadoEm ? \Carbon\Carbon::parse($criadoEm)->format('d/m/Y') : '—' }}
                </td>
                <td>
                    <div class="td-actions">
                        <a href="#" class="btn-edit" title="Visualizar">
                            <i class="fas fa-eye"></i>
                            Ver
                        </a>
                        <a href="#" class="btn-edit" title="Editar">
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
                <td colspan="7">
                    <div class="table-empty">
                        <i class="fas fa-folder-open"></i>
                        <p>Nenhum conteúdo cadastrado ainda.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if(isset($conteudos) && is_object($conteudos) && method_exists($conteudos, 'links'))
<div class="pagination-wrapper">
    {{ $conteudos->links() }}
</div>
@endif

{{-- Modal de exclusão --}}
<div class="modal-overlay" id="deleteModal">
    <div class="modal-box">
        <div class="modal-icon">
            <i class="fas fa-trash-alt"></i>
        </div>
        <h4>Excluir Conteúdo</h4>
        <p>Tem certeza que deseja excluir <strong id="deleteConteudoName"></strong>? Esta ação não pode ser desfeita.</p>
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
    // ── Busca por texto ──────────────────────────────────────────
    document.getElementById('searchInput').addEventListener('input', applyFilters);
    document.getElementById('filterTipo').addEventListener('change', applyFilters);

    function applyFilters() {
        const q    = document.getElementById('searchInput').value.toLowerCase();
        const tipo = document.getElementById('filterTipo').value.toLowerCase();
        let visible = 0;

        document.querySelectorAll('#conteudosTable tbody tr:not(#emptyRow)').forEach(row => {
            const text     = row.textContent.toLowerCase();
            const rowTipo  = (row.dataset.tipo || '').toLowerCase();

            const matchQ    = !q    || text.includes(q);
            const matchTipo = !tipo || rowTipo === tipo;

            row.style.display = (matchQ && matchTipo) ? '' : 'none';
            if (matchQ && matchTipo) visible++;
        });

        document.getElementById('tableCount').textContent =
            `${visible} conteúdo(s) encontrado(s)`;
    }

    // ── Modal de exclusão ────────────────────────────────────────
    function openDeleteModal(id, nome) {
        document.getElementById('deleteConteudoName').textContent = nome;
        document.getElementById('deleteForm').action = `/professor/conteudos/${id}`;
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