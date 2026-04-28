{{-- resources/views/admin/usuarios/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Editar Usuário')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/forms.css') }}">
@endpush

@section('content')

<div class="form-card">
    <div class="form-card-header">
        <div class="form-card-header-icon">
            <i class="fas fa-user-edit"></i>
        </div>
        <div>
            <h3>Editar Dados</h3>
            <p>Deixe a senha em branco para não alterá-la</p>
        </div>
    </div>

    @include('admin.usuarios._form', [
        'action'  => route('admin.usuarios.update', $usuario['idUser'] ?? ''),
        'method'  => 'PUT',
        'cargos'  => $cargos,
        'usuario' => $usuario,
        'current_password' => $usuario['password'] ?? '',  // passa a senha atual
    ])
</div>

@endsection