{{-- resources/views/admin/materias/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Gerenciar Cargos')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/forms.css') }}">
@endpush

@section('content')

<div class="page-header">
    <a href="{{ route('admin.cargos.create') }}" class="btn-create">
        <i class="fas fa-plus"></i>
        Novo Cargo
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
            <input type="text" id="searchInput" placeholder="Buscar cargo...">
            <i class="fas fa-search"></i>
        </div>
        <span class="table-count">
            {{ $cargos->count() }} cargo(s) encontrado(s)
        </span>
    </div>

    <table class="data-table" id="cargosTable">
        <thead>
            <tr>
                <th>#</th>
                <th>Nome do Cargo</th>
                <th>Criado em</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($cargos as $cargo)
            @php
                $id        = $cargo['id']               ?? $cargo['idCargo']              ?? '—';
                $nome      = $cargo['nome_cargo']      ?? $cargo['nomeCargo']     ?? $cargo['Nome'] ?? '—';
                $criadoEm  = $cargo['createdAt']        ?? $cargo['criadoEm']        ?? null;
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
                    {{ $criadoEm ? \Carbon\Carbon::parse($criadoEm)->format('d/m/Y') : '—' }}
                </td>
                <td>
                    <div class="td-actions">
                        {{-- Toggle rápido de situação --}}
                        {{-- <form method="POST" action="{{ route('admin.cargos.toggle', $id) }}" style="display:inline;">
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
                        </form> --}}

                        <a href="{{ route('admin.cargos.edit', $id) }}" class="btn-edit">
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
                        <p>Nenhum cargo cadastrado ainda.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if(isset($cargos) && method_exists($cargos, 'links'))
<div class="pagination-wrapper">
    {{ $cargos->links() }}
</div>
@endif

{{-- Modal de exclusão --}}
<div class="modal-overlay" id="deleteModal">
    <div class="modal-box">
        <div class="modal-icon">
            <i class="fas fa-trash-alt"></i>
        </div>
        <h4>Excluir Cargo</h4>
        <p>Tem certeza que deseja excluir o cargo <strong id="deleteCargoName"></strong>? Esta ação não pode ser desfeita.</p>
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
    document.querySelectorAll('#cargosTable tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});

function openDeleteModal(id, nome) {
    document.getElementById('deleteCargoName').textContent = nome;
    document.getElementById('deleteForm').action = `/admin/cargos/${id}`;
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