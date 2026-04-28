{{-- resources/views/admin/escolaridades/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Nova Escolaridade')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/forms.css') }}">
@endpush

@section('content')

<div class="form-card">
    <div class="form-card-header">
        <div class="form-card-header-icon">
            <i class="fas fa-school"></i>
        </div>
        <div>
            <h3>Dados da Escolaridade</h3>
            <p>Campos marcados com <span style="color:#ea5455;">*</span> são obrigatórios</p>
        </div>
    </div>

    @include('admin.escolaridades._form', [
        'action' => route('admin.escolaridades.store'),
        'method' => 'POST',
    ])
</div>

@endsection