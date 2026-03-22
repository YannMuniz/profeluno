{{-- resources/views/admin/materias/_form.blade.php --}}
{{--
    Variáveis esperadas:
    $cargo  — (opcional) instância de Cargo para edição
    $action   — URL de destino do form
    $method   — 'POST' ou 'PUT'
--}}

<form method="POST" action="{{ $action }}">
    @csrf
    @if($method === 'PUT')
        @method('PUT')
    @endif

    <div class="form-card-body">

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

        {{-- Nome da matéria --}}
        <div class="form-group">
            <label class="form-label" for="nome_cargo">
                Nome do Cargo <span class="required">*</span>
            </label>
            <input
                type="text"
                id="nome_cargo"
                name="nome_cargo"
                class="form-control {{ $errors->has('nome_cargo') ? 'is-invalid' : '' }}"
                placeholder="Ex.: Aluno, Professor..."
                value="{{ old('nome_cargo', is_array($cargo ?? null) ? ($cargo['nome_cargo'] ?? $cargo['nome_cargo'] ?? '') : ($cargo->nome_cargo ?? '')) }}"
                required
            >
            <span class="form-hint">O nome do cargo deve ser único na plataforma.</span>
            @error('nome_cargo')
                <span class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
            @enderror
        </div>

    </div>

    <div class="form-footer">
        <a href="{{ route('admin.cargos.index') }}" class="btn-cancel">
            <i class="fas fa-arrow-left"></i>
            Cancelar
        </a>
        <button type="submit" class="btn-save">
            <i class="fas fa-{{ isset($cargo) ? 'save' : 'plus' }}"></i>
            {{ isset($cargo) ? 'Salvar Alterações' : 'Criar Cargo' }}
        </button>
    </div>
</form>