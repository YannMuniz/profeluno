{{-- resources/views/admin/escolaridades/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Editar Escolaridade')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/forms.css') }}">
@endpush

@section('content')

<div class="page-header">
    <div class="page-header-info">
        <h2><i class="fas fa-school"></i> Editar Escolaridade</h2>
        <p>Atualize os dados de <strong>{{ $escolaridade->nomeEscolaridade }}</strong></p>
    </div>
    <a href="{{ route('admin.escolaridades.index') }}" class="btn-cancel">
        <i class="fas fa-arrow-left"></i>
        Voltar
    </a>
</div>

<div class="form-card">
    <div class="form-card-header">
        <div class="form-card-header-icon">
            <i class="fas fa-school"></i>
        </div>
        <div>
            <h3>Editar Dados</h3>
            <p>Atualize o nome ou a situação da escolaridade</p>
        </div>
    </div>

    @include('admin.escolaridades._form', [
        'action' => route('admin.escolaridades.update', $escolaridade->idEscolaridade),
        'method' => 'PUT',
        'escolaridade' => $escolaridade,
    ])
</div>

@endsection