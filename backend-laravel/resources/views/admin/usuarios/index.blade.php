{{-- resources/views/admin/usuarios/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Gerenciar Usuários')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/forms.css') }}">
@endpush

@section('content')

{{-- Header --}}
<div class="page-header">
    <a href="{{ route('admin.usuarios.create') }}" class="btn-create">
        <i class="fas fa-user-plus"></i>
        Novo Usuário
    </a>
</div>

{{-- Alertas flash --}}
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

{{-- Tabela --}}
<div class="table-wrapper">
    <div class="table-toolbar">
        <div class="table-search">
            <input type="text" id="searchInput" placeholder="Buscar por nome ou e-mail...">
            <i class="fas fa-search"></i>
        </div>
        <span class="table-count">
            {{ $usuarios->count() }} usuário(s) encontrado(s)
        </span>
    </div>

    <table class="data-table" id="usuariosTable">
        <thead>
            <tr>
                <th>#</th>
                <th>Usuário</th>
                <th>E-mail</th>
                <th>Cargo</th>
                <th>Criado em</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($usuarios as $usuario)
            @php
                $mapaCargos = [
                    1 => 'aluno',
                    2 => 'professor',
                    3 => 'admin',
                ];
                $id       = $usuario['id']           ?? $usuario['idUser']        ?? '—';
                $nome     = $usuario['nome_Usuario']  ?? $usuario['nome']      ?? $usuario['Nome'] ?? '—';
                $email    = $usuario['email']         ?? $usuario['Email']     ?? '—';
                $cargoId = $usuario['idCargo'] ?? null;
                $cargo = $mapaCargos[$cargoId] ?? 'aluno';
                $criadoEm = $usuario['createdAt']    ?? $usuario['criadoEm'] ?? null;
            @endphp
            <tr>
                <td>{{ $id }}</td>
                <td>
                    <div class="td-user">
                        <div class="avatar-initial">
                            {{ strtoupper(substr($nome, 0, 1)) }}
                        </div>
                        <div class="td-user-info">
                            {{ $nome }}
                        </div>
                    </div>
                </td>
                <td>{{ $email }}</td>
                <td>
                    <span class="badge badge-{{ $cargo }}">
                        <i class="fas fa-{{ $cargo === 'admin' ? 'shield-alt' : ($cargo === 'professor' ? 'chalkboard-teacher' : 'user-graduate') }}"></i>
                        {{ ucfirst($cargo) }}
                    </span>
                </td>
                <td>
                    {{ $criadoEm ? \Carbon\Carbon::parse($criadoEm)->format('d/m/Y') : '—' }}
                </td>
                <td>
                    <div class="td-actions">
                        <a href="{{ route('admin.usuarios.edit', $id) }}" class="btn-edit">
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
                <td colspan="6">
                    <div class="table-empty">
                        <i class="fas fa-users-slash"></i>
                        <p>Nenhum usuário encontrado.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Paginação --}}
@if(isset($usuarios) && method_exists($usuarios, 'links'))
<div class="pagination-wrapper">
    {{ $usuarios->links() }}
</div>
@endif

{{-- Modal de exclusão --}}
<div class="modal-overlay" id="deleteModal">
    <div class="modal-box">
        <div class="modal-icon">
            <i class="fas fa-trash-alt"></i>
        </div>
        <h4>Excluir Usuário</h4>
        <p>Tem certeza que deseja excluir o usuário <strong id="deleteUserName"></strong>? Esta ação não pode ser desfeita.</p>
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
// Busca na tabela
document.getElementById('searchInput').addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#usuariosTable tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});

// Modal de exclusão
function openDeleteModal(id, nome) {
    document.getElementById('deleteUserName').textContent = nome;
    document.getElementById('deleteForm').action = `/admin/usuarios/${id}`;
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