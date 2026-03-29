{{-- resources/views/professor/simulado/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Editar Simulado')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/sala-professor.css') }}">
@endpush

@section('content')

<form action="{{ route('professor.simulados.update', $simulado['idSimulado']) }}" method="POST" id="formSimulado">
    @csrf
    @method('PUT')
    @include('professor.simulado._form', ['simulado' => $simulado])
</form>

@endsection

@push('scripts')
<script src="{{ asset('js/sala-professor.js') }}"></script>
@endpush