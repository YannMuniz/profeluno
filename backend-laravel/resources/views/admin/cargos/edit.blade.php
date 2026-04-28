{{-- resources/views/admin/materias/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Editar Cargo')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/forms.css') }}">
@endpush

@section('content')

<div class="form-card">
    <div class="form-card-header">
        <div class="form-card-header-icon">
            <i class="fas fa-book-open"></i>
        </div>
        <div>
            <h3>Editar Dados</h3>
            <p>Atualize o nome do cargo</p>
        </div>
    </div>

    @include('admin.cargos._form', [
        'action' => route('admin.cargos.update', $cargo->idCargo),
        'method' => 'PUT',
        'cargo' => $cargo,
    ])
</div>

@endsection