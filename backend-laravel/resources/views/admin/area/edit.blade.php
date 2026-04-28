{{-- resources/views/admin/area/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Editar Área')

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
            <p>Atualize o nome ou a situação da área</p>
        </div>
    </div>

    @include('admin.area._form', [
        'action' => route('admin.areas.update', $area->idArea),
        'method' => 'PUT',
        'area' => $area,
    ])
</div>

@endsection