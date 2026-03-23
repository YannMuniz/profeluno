{{-- resources/views/admin/materias/_form.blade.php --}}
{{--
    Variáveis esperadas:
    $materia  — (opcional) instância de Materia para edição
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
            <label class="form-label" for="nome_materia">
                Nome da Matéria <span class="required">*</span>
            </label>
            <input
                type="text"
                id="nome_materia"
                name="nome_materia"
                class="form-control {{ $errors->has('nome_materia') ? 'is-invalid' : '' }}"
                placeholder="Ex.: Matemática, Física, Química..."
                value="{{ old('nomeMateria', is_array($materia ?? null) ? ($materia['nomeMateria'] ?? $materia['nomeMateria'] ?? '') : ($materia->nomeMateria ?? '')) }}"
                required
            >
            <span class="form-hint">O nome da matéria deve ser único na plataforma.</span>
            @error('nome_materia')
                <span class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
            @enderror
        </div>

        {{-- Situação --}}
        <div class="form-group">
            <label class="form-label">Situação</label>
            <label class="toggle-group" for="situacao_materia">
                <div class="toggle-switch">
                    <input
                        type="checkbox"
                        id="situacao_materia"
                        name="situacao_materia"
                        value="1"
                        {{ old('situacaoMateria', is_array($materia ?? null) ? ($materia['situacaoMateria'] ?? $materia['situacaoMateria'] ?? 1) : ($materia->situacaoMateria ?? 1)) ? 'checked' : '' }}
                    >
                    <span class="toggle-slider"></span>
                </div>
                <div class="toggle-label">
                    Matéria ativa
                    <small>Matérias ativas ficam visíveis para alunos e professores</small>
                </div>
            </label>
            @error('situacao_materia')
                <span class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
            @enderror
        </div>

    </div>

    <div class="form-footer">
        <a href="{{ route('admin.materias.index') }}" class="btn-cancel">
            <i class="fas fa-arrow-left"></i>
            Cancelar
        </a>
        <button type="submit" class="btn-save">
            <i class="fas fa-{{ isset($materia) ? 'save' : 'plus' }}"></i>
            {{ isset($materia) ? 'Salvar Alterações' : 'Criar Matéria' }}
        </button>
    </div>
</form>