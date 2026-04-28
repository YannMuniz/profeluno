{{-- resources/views/admin/area/_form.blade.php --}}
{{--
    Variáveis esperadas:
    $area  — (opcional) instância de Area para edição
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

        {{-- Nome da área --}}
        <div class="form-group">
            <label class="form-label" for="nome_area">
                Nome da Área <span class="required">*</span>
            </label>
            <input
                type="text"
                id="nome_area"
                name="nome_area"
                class="form-control {{ $errors->has('nome_area') ? 'is-invalid' : '' }}"
                placeholder="Ex.: Saúde, Educação, Tecnologia..."
                value="{{ old('nomeArea', is_array($area ?? null) ? ($area['nomeArea'] ?? $area['nomeArea'] ?? '') : ($area->nomeArea ?? '')) }}"
                required
            >
            <span class="form-hint">O nome da área deve ser único na plataforma.</span>
            @error('nome_area')
                <span class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
            @enderror
        </div>

        {{-- Situação --}}
        <div class="form-group">
            <label class="form-label">Situação</label>
            <label class="toggle-group" for="situacao_area">
                <div class="toggle-switch">
                    <input
                        type="checkbox"
                        id="situacao_area"
                        name="situacao_area"
                        value="1"
                        {{ old('situacaoArea', is_array($area ?? null) ? ($area['situacaoArea'] ?? $area['situacaoArea'] ?? 1) : ($area->situacaoArea ?? 1)) ? 'checked' : '' }}
                    >
                    <span class="toggle-slider"></span>
                </div>
                <div class="toggle-label">
                    Área ativa
                    <small>Áreas ativas ficam visíveis para alunos e professores</small>
                </div>
            </label>
            @error('situacao_area')
                <span class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
            @enderror
        </div>

    </div>

    <div class="form-footer">
        <a href="{{ route('admin.areas.index') }}" class="btn-cancel">
            <i class="fas fa-arrow-left"></i>
            Cancelar
        </a>
        <button type="submit" class="btn-save">
            <i class="fas fa-{{ isset($area) ? 'save' : 'plus' }}"></i>
            {{ isset($area) ? 'Salvar Alterações' : 'Criar Área' }}
        </button>
    </div>
</form>