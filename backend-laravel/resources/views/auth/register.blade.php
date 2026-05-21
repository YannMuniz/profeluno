@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection

@section('content')
<div class="auth-container">
    <div class="auth-card">

        <div class="auth-header">
            <div class="logo">
                <div class="logo-icon"><i class="fas fa-graduation-cap"></i></div>
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

        @if (session('success'))
            <div class="alert alert-success">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        <form method="POST" action="{{ route('registrar') }}">
            @csrf

            {{-- ── Dados básicos ──────────────────────────────── --}}
            <div class="form-group">
                <label for="name">Nome Completo</label>
                <input type="text" name="name" id="name"
                    class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name') }}" required autofocus>
                @error('name')<span class="error-message">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email"
                    class="form-control @error('email') is-invalid @enderror"
                    value="{{ old('email') }}" required>
                @error('email')<span class="error-message">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label for="cargo_id">Tipo de Usuário</label>
                <select name="cargo_id" id="cargo_id"
                    class="form-control @error('cargo_id') is-invalid @enderror" required>
                    <option value="">Selecione...</option>
                    @foreach($cargos as $cargo)
                        <option value="{{ $cargo->id }}"
                            data-nome="{{ strtolower($cargo->nome_cargo) }}"
                            {{ old('cargo_id') == $cargo->id ? 'selected' : '' }}>
                            {{ ucfirst($cargo->nome_cargo) }}
                        </option>
                    @endforeach
                </select>
                @error('cargo_id')<span class="error-message">{{ $message }}</span>@enderror
            </div>

            {{-- ── Campos exclusivos de Aluno ─────────────────── --}}
            <div id="aluno-fields" style="display:none;">

                <div class="form-group">
                    <label for="periodo">Período <span class="text-muted">(opcional)</span></label>
                    <input type="text" name="periodo" id="periodo"
                        class="form-control @error('periodo') is-invalid @enderror"
                        value="{{ old('periodo') }}" placeholder="Ex: 3º semestre">
                    @error('periodo')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="escolaridade_id_aluno">Escolaridade</label>
                    <select name="escolaridade_id" id="escolaridade_id_aluno"
                        class="form-control @error('escolaridade_id') is-invalid @enderror">
                        <option value="">Selecione...</option>
                        @foreach($escolaridades as $esc)
                            <option value="{{ $esc['idEscolaridade'] }}"
                                {{ old('escolaridade_id') == $esc['idEscolaridade'] ? 'selected' : '' }}>
                                {{ $esc['nomeEscolaridade'] }}
                            </option>
                        @endforeach
                    </select>
                    @error('escolaridade_id')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="area_id_aluno">Área de Interesse</label>
                    <select name="area_id" id="area_id_aluno"
                        class="form-control @error('area_id') is-invalid @enderror">
                        <option value="">Selecione...</option>
                        @foreach($areas as $area)
                            <option value="{{ $area['idArea'] }}"
                                {{ old('area_id') == $area['idArea'] ? 'selected' : '' }}>
                                {{ $area['nomeArea'] }}
                            </option>
                        @endforeach
                    </select>
                    @error('area_id')<span class="error-message">{{ $message }}</span>@enderror
                </div>

            </div>

            {{-- ── Campos exclusivos de Professor ──────────────── --}}
            <div id="professor-fields" style="display:none;">

                <div class="form-group">
                    <label for="formacao">Formação <span class="text-muted">(opcional)</span></label>
                    <input type="text" name="formacao" id="formacao"
                        class="form-control @error('formacao') is-invalid @enderror"
                        value="{{ old('formacao') }}" placeholder="Ex: Licenciatura em Matemática">
                    @error('formacao')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="escolaridade_id_prof">Titulação</label>
                    <select name="escolaridade_id" id="escolaridade_id_prof"
                        class="form-control @error('escolaridade_id') is-invalid @enderror">
                        <option value="">Selecione...</option>
                        @foreach($escolaridades as $esc)
                            <option value="{{ $esc['idEscolaridade'] }}"
                                {{ old('escolaridade_id') == $esc['idEscolaridade'] ? 'selected' : '' }}>
                                {{ $esc['nomeEscolaridade'] }}
                            </option>
                        @endforeach
                    </select>
                    @error('escolaridade_id')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="area_id_prof">Área de Atuação</label>
                    <select name="area_id" id="area_id_prof"
                        class="form-control @error('area_id') is-invalid @enderror">
                        <option value="">Selecione...</option>
                        @foreach($areas as $area)
                            <option value="{{ $area['idArea'] }}"
                                {{ old('area_id') == $area['idArea'] ? 'selected' : '' }}>
                                {{ $area['nomeArea'] }}
                            </option>
                        @endforeach
                    </select>
                    @error('area_id')<span class="error-message">{{ $message }}</span>@enderror
                </div>

            </div>

            {{-- ── Senha ───────────────────────────────────────── --}}
            <div class="form-group">
                <label for="password">Senha</label>
                <input type="password" name="password" id="password"
                    class="form-control @error('password') is-invalid @enderror" required>
                @error('password')<span class="error-message">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirmar Senha</label>
                <input type="password" name="password_confirmation" id="password_confirmation"
                    class="form-control" required>
            </div>

            <button type="submit" class="btn-primary">Registrar</button>
        </form>

        <div class="auth-footer">
            <p>Já tem uma conta? <a href="{{ route('login') }}">Faça login aqui</a></p>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const cargoSelect = document.getElementById('cargo_id');
    const alunoFields = document.getElementById('aluno-fields');
    const profFields  = document.getElementById('professor-fields');

    function updateFields() {
        const opt  = cargoSelect.options[cargoSelect.selectedIndex];
        const nome = opt ? (opt.dataset.nome || '') : '';

        const isAluno = nome === 'aluno';
        const isProf  = nome === 'professor';

        alunoFields.style.display = isAluno ? 'block' : 'none';
        profFields.style.display  = isProf  ? 'block' : 'none';

        // required dinâmico
        alunoFields.querySelectorAll('input,select').forEach(el => el.required = isAluno);
        profFields.querySelectorAll('input,select').forEach(el => el.required = isProf);
    }

    // ── Desabilita campos ocultos antes do submit ──────────────────────
    // Campos disabled não são enviados ao servidor, evitando duplicatas
    // de escolaridade_id e area_id que confundem a validação do Laravel.
    document.querySelector('form').addEventListener('submit', function () {
        const opt  = cargoSelect.options[cargoSelect.selectedIndex];
        const nome = opt ? (opt.dataset.nome || '') : '';

        if (nome !== 'aluno') {
            alunoFields.querySelectorAll('input,select').forEach(el => el.disabled = true);
        }
        if (nome !== 'professor') {
            profFields.querySelectorAll('input,select').forEach(el => el.disabled = true);
        }
    });

    cargoSelect.addEventListener('change', updateFields);
    updateFields();
})();
</script>
@endpush