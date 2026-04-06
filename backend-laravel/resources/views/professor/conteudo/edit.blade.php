{{-- resources/views/professor/conteudo/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Editar Conteúdo')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/forms.css') }}">
<link rel="stylesheet" href="{{ asset('css/sala-professor.css') }}">
@endpush

@section('content')

@include('professor.conteudo._form', [
    'conteudo' => $conteudo,
    'materias' => $materias,
    {{-- CORRIGIDO: API retorna 'idConteudo', não 'id' --}}
    'action'   => route('professor.conteudo.update', $conteudo['idConteudo']),
    'method'   => 'PUT',
])

@endsection