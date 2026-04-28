@extends('layouts.app')

@section('title', 'Meu Perfil')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/forms.css') }}">
@endpush

@section('content')

<div class="form-card">
    <div class="form-card-header">
        <div class="form-card-header-icon">
            <i class="fas fa-user-edit"></i>
        </div>
        <div>
            <h3>Editar Perfil</h3>
            <p>O e-mail não pode ser alterado. Deixe a senha em branco para mantê-la.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('perfil.update') }}">
        @csrf
        @method('PUT')

        <div class="form-card-body">

            @if (session('success'))
                <div class="alert alert-success" style="margin-bottom: 22px;">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger" style="margin-bottom: 22px;">
                    <i class="fas fa-exclamation-circle"></i>
                    <div>
                        <strong>Corrija os erros abaixo:</strong>
                        <ul style="margin: 6px 0 0; padding-left: 16px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            {{-- Nome --}}
            <div class="form-group">
                <label class="form-label" for="nome_usuario">
                    Nome completo <span class="required">*</span>
                </label>
                <input type="text" id="nome_usuario" name="nome_usuario"
                    class="form-control {{ $errors->has('nome_usuario') ? 'is-invalid' : '' }}"
                    value="{{ old('nome_usuario', $user->nome_usuario) }}" required>
                @error('nome_usuario')
                    <span class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                @enderror
            </div>

            {{-- Email (somente leitura) --}}
            <div class="form-group">
                <label class="form-label">E-mail</label>
                <input type="email" class="form-control form-control-readonly"
                    value="{{ $user->email }}" disabled>
                <small class="form-hint">O e-mail não pode ser alterado.</small>
            </div>

            {{-- Cargo (somente leitura) --}}
            <div class="form-group">
                <label class="form-label">Cargo</label>
                <input type="text" class="form-control form-control-readonly"
                    value="{{ ucfirst($user->cargo?->nome_cargo ?? '—') }}" disabled>
            </div>

            {{-- Campos de Aluno --}}
            @if($cargoNome === 'aluno')
                <div class="form-group">
                    <label class="form-label" for="periodo">Período</label>
                    <input type="text" id="periodo" name="periodo"
                        class="form-control {{ $errors->has('periodo') ? 'is-invalid' : '' }}"
                        value="{{ old('periodo', $perfil?->periodo) }}"
                        placeholder="Ex: 3º semestre">
                    @error('periodo')
                        <span class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="escolaridade_id">Escolaridade <span class="required">*</span></label>
                    <select id="escolaridade_id" name="escolaridade_id"
                        class="form-control {{ $errors->has('escolaridade_id') ? 'is-invalid' : '' }}" required>
                        <option value="">Selecione...</option>
                        @foreach($escolaridades as $esc)
                            <option value="{{ $esc->id }}"
                                {{ old('escolaridade_id', $perfil?->escolaridade_id) == $esc->id ? 'selected' : '' }}>
                                {{ $esc->nome_escolaridade }}
                            </option>
                        @endforeach
                    </select>
                    @error('escolaridade_id')
                        <span class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="area_id">Área de Interesse <span class="required">*</span></label>
                    <select id="area_id" name="area_id"
                        class="form-control {{ $errors->has('area_id') ? 'is-invalid' : '' }}" required>
                        <option value="">Selecione...</option>
                        @foreach($areas as $area)
                            <option value="{{ $area->id }}"
                                {{ old('area_id', $perfil?->area_id) == $area->id ? 'selected' : '' }}>
                                {{ $area->nome_area }}
                            </option>
                        @endforeach
                    </select>
                    @error('area_id')
                        <span class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                    @enderror
                </div>
            @endif

            {{-- Campos de Professor --}}
            @if($cargoNome === 'professor')
                <div class="form-group">
                    <label class="form-label" for="formacao">Formação</label>
                    <input type="text" id="formacao" name="formacao"
                        class="form-control {{ $errors->has('formacao') ? 'is-invalid' : '' }}"
                        value="{{ old('formacao', $perfil?->formacao) }}"
                        placeholder="Ex: Licenciatura em Matemática">
                    @error('formacao')
                        <span class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="escolaridade_id">Titulação <span class="required">*</span></label>
                    <select id="escolaridade_id" name="escolaridade_id"
                        class="form-control {{ $errors->has('escolaridade_id') ? 'is-invalid' : '' }}" required>
                        <option value="">Selecione...</option>
                        @foreach($escolaridades as $esc)
                            <option value="{{ $esc->id }}"
                                {{ old('escolaridade_id', $perfil?->escolaridade_id) == $esc->id ? 'selected' : '' }}>
                                {{ $esc->nome_escolaridade }}
                            </option>
                        @endforeach
                    </select>
                    @error('escolaridade_id')
                        <span class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="area_id">Área de Atuação <span class="required">*</span></label>
                    <select id="area_id" name="area_id"
                        class="form-control {{ $errors->has('area_id') ? 'is-invalid' : '' }}" required>
                        <option value="">Selecione...</option>
                        @foreach($areas as $area)
                            <option value="{{ $area->id }}"
                                {{ old('area_id', $perfil?->area_id) == $area->id ? 'selected' : '' }}>
                                {{ $area->nome_area }}
                            </option>
                        @endforeach
                    </select>
                    @error('area_id')
                        <span class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                    @enderror
                </div>
            @endif

            {{-- Senha --}}
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="password">
                        Nova Senha <span style="font-weight:400;">(opcional)</span>
                    </label>
                    <input type="password" id="password" name="password"
                        class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                        placeholder="Mínimo 6 caracteres">
                    @error('password')
                        <span class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="password_confirmation">Confirmar Nova Senha</label>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                        class="form-control {{ $errors->has('password_confirmation') ? 'is-invalid' : '' }}"
                        placeholder="Repita a nova senha">
                    @error('password_confirmation')
                        <span class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                    @enderror
                </div>
            </div>

        </div>

        <div class="form-footer">
            <button type="submit" class="btn-save">
                <i class="fas fa-save"></i> Salvar Alterações
            </button>
        </div>
    </form>
</div>

@endsection