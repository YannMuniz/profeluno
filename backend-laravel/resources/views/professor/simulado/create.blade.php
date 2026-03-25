{{-- resources/views/professor/simulado/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Novo Simulado')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/sala-professor.css') }}">
@endpush

@section('content')

<form action="{{ route('professor.simulados.store') }}" method="POST" id="formSimulado">
    @csrf
    @include('professor.simulado._form', ['salas' => $salas])
</form>

@endsection

@push('scripts')
<script src="{{ asset('js/sala-professor.js') }}"></script>
@endpush