{{-- resources/views/professor/conteudo/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Novo Conteúdo')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/forms.css') }}">
<link rel="stylesheet" href="{{ asset('css/sala-professor.css') }}">
@endpush

@section('content')

@include('professor.conteudo._form', [
    'conteudo' => null,
    'materias' => $materias,
    'action'   => route('professor.conteudo.store'),
    'method'   => 'POST',
])

@endsection