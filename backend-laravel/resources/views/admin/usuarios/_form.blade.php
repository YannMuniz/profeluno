{{-- resources/views/admin/usuarios/_form.blade.php --}}
{{--
    Variáveis esperadas:
    $usuario  — (opcional) instância de User para edição
    $cargos   — coleção de Cargo
    $action   — URL de destino do form (POST ou PUT)
    $method   — 'POST' ou 'PUT'
--}}

<form method="POST" action="{{ $action }}">
    @csrf
    @if($method === 'PUT')
        @method('PUT')
    @endif

    <div class="form-card-body">

        {{-- Erros de validação --}}
        @if($errors->any())
        <div class="alert alert-danger" style="margin-bottom: 22px;">
            <i class="fas fa-exclamation-circle"></i>
            <div>
                <strong>Corrija os erros abaixo:</strong>
                <ul style="margin: 6px 0 0; padding-left: 16px;">
                    @foreach($errors->all() as $error)
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
            <input
                type="text"
                id="nome_usuario"
                name="nome_usuario"
                class="form-control {{ $errors->has('nome_usuario') ? 'is-invalid' : '' }}"
                value="{{ old('nome_Usuario', is_array($usuario ?? null) ? ($usuario['nome_Usuario'] ?? $usuario['nome'] ?? $usuario['Nome'] ?? '') : ($usuario->nome_Usuario ?? '')) }}"
                required
            >
            @error('nome_usuario')
                <span class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
            @enderror
        </div>

        {{-- E-mail --}}
        <div class="form-group">
            <label class="form-label" for="email">
                E-mail <span class="required">*</span>
            </label>
            <input
                type="email"
                id="email"
                name="email"
                class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                placeholder="email@exemplo.com"
                value="{{ old('email', is_array($usuario ?? null) ? ($usuario['email'] ?? $usuario['Email'] ?? '') : ($usuario->email ?? '')) }}"
                required
            >
            @error('email')
                <span class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
            @enderror
        </div>

        {{-- Cargo --}}
        <div class="form-group">
            <label class="form-label" for="cargo_id">
                Cargo <span class="required">*</span>
            </label>
            <select
                id="cargo_id"
                name="cargo_id"
                class="form-control {{ $errors->has('cargo_id') ? 'is-invalid' : '' }}"
                required
            >
                <option value="">Selecione um cargo...</option>
                @foreach($cargos as $cargo)
                @php
                    $cargoId   = is_array($cargo) ? ($cargo['id'] ?? $cargo['Id'] ?? '') : $cargo->id;
                    $cargoNome = is_array($cargo) ? ($cargo['nome_cargo'] ?? $cargo['nomeCargo'] ?? $cargo['Nome'] ?? $cargo['nome'] ?? '') : $cargo->nome_cargo;

                    // Pega o cargo atual do usuário (array ou objeto)
                    $usuarioCargoId = old('cargo_id',
                        is_array($usuario ?? null)
                            ? ($usuario['idCargo'] ?? $usuario['cargo_id'] ?? '')
                            : ($usuario->idCargo ?? $usuario->cargo_id ?? '')
                    );

                    $selected = (string) $usuarioCargoId === (string) $cargoId ? 'selected' : '';
                @endphp
                    <option value="{{ $cargoId }}" {{ $selected }}>
                        {{ $cargoNome }}
                    </option>
                @endforeach
            </select>
            @error('cargo_id')
                <span class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
            @enderror
        </div>

        {{-- Senha (só exibe campo de senha em criação; em edição é opcional) --}}
        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="password">
                    Senha
                    @if(isset($usuario)) <span style="font-weight:400; text-transform:none; letter-spacing:0;">(deixe em branco para manter)</span> @else <span class="required">*</span> @endif
                </label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                    placeholder="{{ isset($usuario) ? '••••••••' : 'Mínimo 6 caracteres' }}"
                    {{ isset($usuario) ? '' : 'required' }}
                >
                @error('password')
                    <span class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="password_confirmation">
                    Confirmar senha
                    @if(!isset($usuario)) <span class="required">*</span> @endif
                </label>
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    class="form-control {{ $errors->has('password_confirmation') ? 'is-invalid' : '' }}"
                    placeholder="Repita a senha"
                    {{ isset($usuario) ? '' : 'required' }}
                >
                @error('password_confirmation')
                    <span class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                @enderror
            </div>
        </div>

    </div>

    <div class="form-footer">
        <a href="{{ route('admin.usuarios.index') }}" class="btn-cancel">
            <i class="fas fa-arrow-left"></i>
            Cancelar
        </a>
        <button type="submit" class="btn-save">
            <i class="fas fa-{{ isset($usuario) ? 'save' : 'user-plus' }}"></i>
            {{ isset($usuario) ? 'Salvar Alterações' : 'Criar Usuário' }}
        </button>
    </div>
</form>