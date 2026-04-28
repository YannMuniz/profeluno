{{-- resources/views/admin/materias/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Editar Matéria')

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
            <p>Atualize o nome ou a situação da matéria</p>
        </div>
    </div>

    @include('admin.materias._form', [
        'action' => route('admin.materias.update', $materia->idMateria),
        'method' => 'PUT',
        'materia' => $materia,
    ])
</div>

@endsection