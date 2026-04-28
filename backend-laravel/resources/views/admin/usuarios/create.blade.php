{{-- resources/views/admin/usuarios/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Novo Usuário')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/forms.css') }}">
@endpush

@section('content')

<div class="form-card">
    <div class="form-card-header">
        <div class="form-card-header-icon">
            <i class="fas fa-user-plus"></i>
        </div>
        <div>
            <h3>Dados do Usuário</h3>
            <p>Campos marcados com <span style="color:#ea5455;">*</span> são obrigatórios</p>
        </div>
    </div>

    @include('admin.usuarios._form', [
        'action' => route('admin.usuarios.store'),
        'method' => 'POST',
        'cargos' => $cargos,
    ])
</div>

@endsection