{{-- resources/views/admin/usuarios/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Editar Usuário')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/forms.css') }}">
@endpush

@section('content')

<div class="page-header">
    <div class="page-header-info">
        <h2><i class="fas fa-user-edit"></i> Editar Usuário</h2>
        <p>Atualize os dados de <strong>{{ $usuario->nome_Usuario }}</strong></p>
    </div>
    <a href="{{ route('admin.usuarios.index') }}" class="btn-cancel">
        <i class="fas fa-arrow-left"></i>
        Voltar
    </a>
</div>

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
        'action' => route('admin.usuarios.update', $usuario->id),
        'method' => 'PUT',
        'cargos' => $cargos,
        'usuario' => $usuario,
    ])
</div>

@endsection