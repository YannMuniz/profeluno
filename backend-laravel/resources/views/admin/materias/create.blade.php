{{-- resources/views/admin/materias/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Nova Matéria')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/forms.css') }}">
@endpush

@section('content')

<div class="form-card">
    <div class="form-card-header">
        <div class="form-card-header-icon">
            <i class="fas fa-book"></i>
        </div>
        <div>
            <h3>Dados da Matéria</h3>
            <p>Campos marcados com <span style="color:#ea5455;">*</span> são obrigatórios</p>
        </div>
    </div>

    @include('admin.materias._form', [
        'action' => route('admin.materias.store'),
        'method' => 'POST',
    ])
</div>

@endsection