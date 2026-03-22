@extends('layouts.app')
@section('content')
<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <div class="logo">
                <div class="logo-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="logo-text">
                    <h1>ProfeLuno</h1>
                    <p>Sistema de Aulas Virtuais</p>
                </div>
            </div>
        </div>

        <h2>Criar Conta</h2>
        <p>Registre-se para começar</p>

        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('registrar') }}">
            @csrf
            
            <div class="form-group">
                <label for="name">Nome Completo</label>
                <input 
                    type="text" 
                    name="name" 
                    id="name" 
                    class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name') }}"
                    required 
                    autofocus
                >
                @error('name')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input 
                    type="email" 
                    name="email" 
                    id="email" 
                    class="form-control @error('email') is-invalid @enderror"
                    value="{{ old('email') }}"
                    required
                >
                @error('email')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="cargo_id">Tipo de Usuário</label>
                <select 
                    name="cargo_id" 
                    id="cargo_id" 
                    class="form-control @error('cargo_id') is-invalid @enderror"
                    required
                >
                    <option value="">Selecione...</option>
                    @foreach($cargos as $cargo)
                    @php
                        $cargoId   = is_array($cargo) ? ($cargo['id']         ?? $cargo['Id']        ?? '') : $cargo->id;
                        $cargoNome = is_array($cargo) ? ($cargo['nome_cargo']  ?? $cargo['nomeCargo'] ?? $cargo['Nome'] ?? '') : $cargo->nome_cargo;
                        $selected  = old('cargo_id', is_array($usuario ?? null)
                            ? ($usuario['cargo_id'] ?? $usuario['idCargo'] ?? '')
                            : ($usuario->cargo_id ?? '')) == $cargoId ? 'selected' : '';
                    @endphp
                        <option value="{{ $cargoId }}" {{ $selected }}>
                            {{ $cargoNome }}
                        </option>
                    @endforeach
                </select>
                @error('cargo_id')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Senha</label>
                <input 
                    type="password" 
                    name="password" 
                    id="password" 
                    class="form-control @error('password') is-invalid @enderror"
                    required
                >
                @error('password')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirmar Senha</label>
                <input 
                    type="password" 
                    name="password_confirmation" 
                    id="password_confirmation" 
                    class="form-control"
                    required
                >
            </div>

            <button type="submit" class="btn-primary">Registrar</button>
        </form>

        <div class="auth-footer">
            <p>Já tem uma conta? <a href="{{ route('login') }}">Faça login aqui</a></p>
        </div>
    </div>
</div>
@endsection

@section('styles')
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection
