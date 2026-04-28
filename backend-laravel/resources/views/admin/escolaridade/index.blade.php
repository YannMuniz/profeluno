{{-- resources/views/admin/escolaridades/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Gerenciar Escolaridades')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/forms.css') }}">
@endpush

@section('content')

<div class="page-header">
    <div class="page-header-info">
        <h2><i class="fas fa-school"></i> Escolaridades</h2>
        <p>Gerencie as escolaridades disponíveis na plataforma</p>
    </div>
    <a href="{{ route('admin.escolaridades.create') }}" class="btn-create">
        <i class="fas fa-plus"></i>
        Nova Escolaridade
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
            <input type="text" id="searchInput" placeholder="Buscar escolaridade...">
            <i class="fas fa-search"></i>
        </div>
        <span class="table-count">
            {{ $escolaridades->count() }} escolaridade(s) encontrada(s)
        </span>
    </div>

    <table class="data-table" id="escolaridadesTable">
        <thead>
            <tr>
                <th>#</th>
                <th>Nome da Escolaridade</th>
                <th>Situação</th>
                <th>Criada em</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($escolaridades as $escolaridade)
            @php
                $id        = $escolaridade['id']               ?? $escolaridade['idEscolaridade']              ?? '—';
                $nome      = $escolaridade['nome_escolaridade']      ?? $escolaridade['nomeEscolaridade']     ?? $escolaridade['Nome'] ?? '—';
                $situacao  = $escolaridade['situacao_escolaridade']  ?? $escolaridade['situacaoEscolaridade'] ?? 1;
                $criadoEm  = $escolaridade['createdAt']        ?? $escolaridade['criadoEm']        ?? null;
            @endphp
            <tr>
                <td>{{ $id }}</td>
                <td>
                    <div style="display:flex; align-items:center; gap:10px;">
                        <div class="avatar-initial" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                            <i class="fas fa-book" style="font-size:14px;"></i>
                        </div>
                        <strong>{{ $nome }}</strong>
                    </div>
                </td>
                <td>
                    <span class="badge {{ $situacao ? 'badge-ativo' : 'badge-inativo' }}">
                        <i class="fas fa-{{ $situacao ? 'check-circle' : 'times-circle' }}"></i>
                        {{ $situacao ? 'Ativa' : 'Inativa' }}
                    </span>
                </td>
                <td>
                    {{ $criadoEm ? \Carbon\Carbon::parse($criadoEm)->format('d/m/Y') : '—' }}
                </td>
                <td>
                    <div class="td-actions">
                        {{-- Toggle rápido de situação --}}
                        <form method="POST" action="{{ route('admin.escolaridades.toggle', $id) }}" style="display:inline;">
                            @csrf
                            @method('PATCH')
                            <button
                                type="submit"
                                class="btn-edit"
                                title="{{ $situacao ? 'Desativar' : 'Ativar' }}"
                                style="{{ $situacao ? '' : 'background:rgba(40,199,111,0.12);color:#28c76f;border-color:rgba(40,199,111,0.3);' }}"
                            >
                                <i class="fas fa-{{ $situacao ? 'toggle-on' : 'toggle-off' }}"></i>
                                {{ $situacao ? 'Desativar' : 'Ativar' }}
                            </button>
                        </form>

                        <a href="{{ route('admin.escolaridades.edit', $id) }}" class="btn-edit">
                            <i class="fas fa-pen"></i>
                            Editar
                        </a>

                        <button class="btn-delete"
                            onclick="openDeleteModal('{{ $id }}', '{{ addslashes($nome) }}')"
                        >
                            <i class="fas fa-trash-alt"></i>
                            Excluir
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5">
                    <div class="table-empty">
                        <i class="fas fa-book-open"></i>
                        <p>Nenhuma escolaridade cadastrada ainda.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if(isset($escolaridades) && method_exists($escolaridades, 'links'))
<div class="pagination-wrapper">
    {{ $escolaridades->links() }}
</div>
@endif

{{-- Modal de exclusão --}}
<div class="modal-overlay" id="deleteModal">
    <div class="modal-box">
        <div class="modal-icon">
            <i class="fas fa-trash-alt"></i>
        </div>
        <h4>Excluir Escolaridade</h4>
        <p>Tem certeza que deseja excluir a escolaridade <strong id="deleteEscolaridadeName"></strong>? Esta ação não pode ser desfeita.</p>
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
document.getElementById('searchInput').addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#escolaridadesTable tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});

function openDeleteModal(id, nome) {
    document.getElementById('deleteEscolaridadeName').textContent = nome;
    document.getElementById('deleteForm').action = `/admin/escolaridades/${id}`;
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