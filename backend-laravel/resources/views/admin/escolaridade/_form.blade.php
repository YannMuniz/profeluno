{{-- resources/views/admin/escolaridades/_form.blade.php --}}
{{--
    Variáveis esperadas:
    $escolaridade  — (opcional) instância de Escolaridade para edição
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

        {{-- Nome da escolaridade --}}
        <div class="form-group">
            <label class="form-label" for="nome_escolaridade">
                Nome da Escolaridade <span class="required">*</span>
            </label>
            <input
                type="text"
                id="nome_escolaridade"
                name="nome_escolaridade"
                class="form-control {{ $errors->has('nome_escolaridade') ? 'is-invalid' : '' }}"
                placeholder="Ex.: Ensino Fundamental, Ensino Médio..."
                value="{{ old('nomeEscolaridade', is_array($escolaridade ?? null) ? ($escolaridade['nomeEscolaridade'] ?? $escolaridade['nomeEscolaridade'] ?? '') : ($escolaridade->nomeEscolaridade ?? '')) }}"
                required
            >
            <span class="form-hint">O nome da escolaridade deve ser único na plataforma.</span>
            @error('nome_escolaridade')
                <span class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
            @enderror
        </div>

        {{-- Situação --}}
        <div class="form-group">
            <label class="form-label">Situação</label>
            <label class="toggle-group" for="situacao_escolaridade">
                <div class="toggle-switch">
                    <input
                        type="checkbox"
                        id="situacao_escolaridade"
                        name="situacao_escolaridade"
                        value="1"
                        {{ old('situacaoEscolaridade', is_array($escolaridade ?? null) ? ($escolaridade['situacaoEscolaridade'] ?? $escolaridade['situacaoEscolaridade'] ?? 1) : ($escolaridade->situacaoEscolaridade ?? 1)) ? 'checked' : '' }}
                    >
                    <span class="toggle-slider"></span>
                </div>
                <div class="toggle-label">
                    Escolaridade ativa
                    <small>Escolaridades ativas ficam visíveis para alunos e professores</small>
                </div>
            </label>
            @error('situacao_escolaridade')
                <span class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
            @enderror
        </div>

    </div>

    <div class="form-footer">
        <a href="{{ route('admin.escolaridades.index') }}" class="btn-cancel">
            <i class="fas fa-arrow-left"></i>
            Cancelar
        </a>
        <button type="submit" class="btn-save">
            <i class="fas fa-{{ isset($escolaridade) ? 'save' : 'plus' }}"></i>
            {{ isset($escolaridade) ? 'Salvar Alterações' : 'Criar Escolaridade' }}
        </button>
    </div>
</form>