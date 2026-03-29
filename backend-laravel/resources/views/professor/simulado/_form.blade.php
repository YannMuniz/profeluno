{{-- resources/views/professor/simulado/_form.blade.php --}}
{{-- Variáveis esperadas:
     $materias    : array de materias disponíveis
     $simulado : array|null — preenchido no edit, null no create
--}}

@php
    $isEdit    = isset($simulado) && !empty($simulado);
    $titulo    = $isEdit ? ($simulado['titulo']    ?? '') : old('titulo',     '');
    $descricao = $isEdit ? ($simulado['descricao'] ?? '') : old('descricao',  '');
    $materiaId = $isEdit ? ($simulado['idMateria'] ?? '') : old('materia_id', ''); // ✅ era materia_id
    $situacao  = $isEdit ? ($simulado['situacao']  ?? 1)  : old('situacao',   1);

    // ✅ era $simulado['questoes'] com snake_case — API retorna simuladoQuestao em camelCase
    $questoes = [];
    if ($isEdit) {
        foreach (($simulado['simuladoQuestao'] ?? []) as $q) {
            $questoes[] = [
                'enunciado'       => $q['enunciado']      ?? '',
                'questao_a'       => $q['questaoA']       ?? '',
                'questao_b'       => $q['questaoB']       ?? '',
                'questao_c'       => $q['questaoC']       ?? '',
                'questao_d'       => $q['questaoD']       ?? '',
                'questao_e'       => $q['questaoE']       ?? null,
                'questao_correta' => $q['questaoCorreta'] ?? null,
            ];
        }
    }
@endphp

<div class="form-grid-two">

    {{-- ── Coluna principal ──────────────────────────────────────── --}}
    <div class="form-col-main">

        {{-- Card: dados gerais --}}
        <div class="questao-block" style="margin-bottom: 24px;">
            <div class="questao-header">
                <div class="questao-num-wrap">
                    <span class="questao-num-label" style="font-size: 15px; font-weight: 600;">
                        <i class="fas fa-info-circle" style="color: var(--primary-color); margin-right: 6px;"></i>
                        Informações do Simulado
                    </span>
                </div>

                {{-- Toggle de situação (apenas no edit) --}}
                @if($isEdit)
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span style="font-size: 13px; color: var(--text-secondary);">Situação:</span>
                    <label class="toggle-switch" title="{{ $situacao ? 'Desativar' : 'Ativar' }} simulado">
                        <input type="hidden"   name="situacao" value="0">
                        <input type="checkbox" name="situacao" value="1" {{ $situacao ? 'checked' : '' }}
                               onchange="this.previousElementSibling.value = this.checked ? 1 : 0">
                        <span class="toggle-slider"></span>
                    </label>
                    <span id="situacaoLabel" style="font-size: 13px; font-weight: 600;
                          color: {{ $situacao ? 'var(--success-color)' : 'var(--danger-color)' }};">
                        {{ $situacao ? 'Ativo' : 'Inativo' }}
                    </span>
                </div>
                @endif
            </div>

            <div class="questao-body">
                {{-- Título --}}
                <div class="form-group">
                    <label class="form-label" for="titulo">
                        Título <span class="required">*</span>
                    </label>
                    <input
                        type="text"
                        id="titulo"
                        name="titulo"
                        class="form-control @error('titulo') is-invalid @enderror"
                        placeholder="Ex: Simulado — Equações do 2º Grau"
                        value="{{ $titulo }}"
                        required
                    >
                    @error('titulo')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Descrição --}}
                <div class="form-group">
                    <label class="form-label" for="descricao">
                        Descrição
                        <span style="font-size: 11px; font-weight: 400; color: var(--text-secondary);">(opcional)</span>
                    </label>
                    <textarea
                        id="descricao"
                        name="descricao"
                        class="form-control @error('descricao') is-invalid @enderror"
                        rows="2"
                        placeholder="Descreva brevemente o objetivo do simulado..."
                    >{{ $descricao }}</textarea>
                    @error('descricao')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Materia --}}
                <div class="form-group">
                    <label class="form-label" for="materia_id">
                        Matéria <span class="required">*</span>
                    </label>
                    <select
                        id="materia_id"
                        name="materia_id"
                        class="form-control @error('materia_id') is-invalid @enderror"
                        required
                    >
                        <option value="">— Selecione uma matéria —</option>
                        @foreach($materias as $materia)
                            <option
                                value="{{ $materia['idMateria'] }}"
                                {{ $materiaId == $materia['idMateria'] ? 'selected' : '' }}
                            >
                                {{ $materia['nomeMateria'] }}
                                @if(!empty($materia['descricao']))— {{ $materia['descricao'] }}@endif
                            </option>
                        @endforeach
                    </select>
                    @error('materia_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Container dinâmico de questões --}}
        <div id="questoesContainer">
            {{-- Questões existentes (edit) são injetadas pelo JS via window.questoesIniciais --}}
        </div>

        <div class="add-questao-area">
            <button type="button" class="btn-add-questao-lg" id="addQuestao">
                <span class="add-questao-inner">
                    <i class="fas fa-plus-circle"></i>
                    Adicionar Nova Questão
                </span>
            </button>
        </div>

        {{-- Erros de validação --}}
        @if($errors->any())
        <div class="alert alert-danger" style="margin-top: 16px;">
            <ul style="margin:0; padding-left: 18px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="form-actions">
            <a href="{{ route('professor.simulados.index') }}" class="btn-form-cancel">
                <i class="fas fa-times"></i> Cancelar
            </a>
            <button type="submit" class="btn-form-submit" id="submitSimulado">
                <i class="fas fa-save"></i>
                {{ $isEdit ? 'Atualizar Simulado' : 'Salvar Simulado' }}
            </button>
        </div>
    </div>

    {{-- ── Lateral ──────────────────────────────────────────────── --}}
    <div class="form-col-side">
        <div class="preview-card">
            <div class="preview-card-header">
                <i class="fas fa-chart-bar"></i>
                Resumo do Simulado
            </div>
            <div class="preview-card-body">
                <div class="simulado-summary">
                    <div class="summary-item">
                        <strong id="summaryTotal">0</strong>
                        <span>Questões</span>
                    </div>
                    <div class="summary-item">
                        <strong id="summaryCompletas">0</strong>
                        <span>Completas</span>
                    </div>
                    <div class="summary-item">
                        <strong id="summaryFaltando">0</strong>
                        <span>Incompletas</span>
                    </div>
                </div>
                <div class="progress-bar-wrap">
                    <div class="progress-bar-fill" id="progressFill"></div>
                </div>
                <p class="progress-label" id="progressLabel">Preencha as questões</p>
            </div>
        </div>

        <div class="tips-card">
            <div class="tips-card-header">
                <i class="fas fa-lightbulb"></i>
                Como usar
            </div>
            <ul class="tips-list">
                <li>Cada questão precisa de enunciado, alternativas A–D e a resposta correta</li>
                <li>A alternativa E é opcional</li>
                <li>Você pode adicionar quantas questões quiser</li>
                <li>O simulado ficará disponível aos alunos após ser publicado</li>
                <li>Use o índice lateral para navegar rapidamente entre questões</li>
            </ul>
        </div>

        <div class="tips-card">
            <div class="tips-card-header">
                <i class="fas fa-map-signs"></i>
                Navegação
            </div>
            <div class="questao-nav" id="questaoNav">
                <p class="nav-empty">Nenhuma questão ainda.</p>
            </div>
        </div>
    </div>

</div>

{{-- Template de questão (oculto, clonado pelo JS) --}}
<template id="questaoTemplate">
    <div class="questao-block" data-index="__INDEX__" id="questao-block-__INDEX__">
        <div class="questao-header">
            <div class="questao-num-wrap">
                <span class="questao-num-badge">__NUM__</span>
                <span class="questao-num-label">Questão __NUM__</span>
            </div>
            <div class="questao-header-actions">
                <button type="button" class="btn-move-up" title="Mover para cima" data-index="__INDEX__">
                    <i class="fas fa-chevron-up"></i>
                </button>
                <button type="button" class="btn-move-down" title="Mover para baixo" data-index="__INDEX__">
                    <i class="fas fa-chevron-down"></i>
                </button>
                <button type="button" class="btn-remove-questao" title="Remover questão" data-index="__INDEX__">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
        </div>

        <div class="questao-body">
            <div class="form-group">
                <label class="form-label">
                    Enunciado <span class="required">*</span>
                </label>
                <textarea
                    name="questoes[__INDEX__][enunciado]"
                    class="form-control questao-enunciado"
                    rows="3"
                    placeholder="Digite o enunciado da questão..."
                    required
                ></textarea>
            </div>

            <div class="alternativas-section">
                <p class="form-label">Alternativas <span class="required">*</span></p>
                <div class="alternativas-grid">
                    <div class="alternativa-item">
                        <label class="alt-radio">
                            <input type="radio" name="questoes[__INDEX__][questao_correta]" value="1" required>
                            <span class="alt-radio-custom"></span>
                        </label>
                        <span class="alt-label" data-alt="A">A</span>
                        <input type="text" name="questoes[__INDEX__][questao_a]" class="form-control alt-input" placeholder="Alternativa A" required>
                    </div>
                    <div class="alternativa-item">
                        <label class="alt-radio">
                            <input type="radio" name="questoes[__INDEX__][questao_correta]" value="2">
                            <span class="alt-radio-custom"></span>
                        </label>
                        <span class="alt-label" data-alt="B">B</span>
                        <input type="text" name="questoes[__INDEX__][questao_b]" class="form-control alt-input" placeholder="Alternativa B" required>
                    </div>
                    <div class="alternativa-item">
                        <label class="alt-radio">
                            <input type="radio" name="questoes[__INDEX__][questao_correta]" value="3">
                            <span class="alt-radio-custom"></span>
                        </label>
                        <span class="alt-label" data-alt="C">C</span>
                        <input type="text" name="questoes[__INDEX__][questao_c]" class="form-control alt-input" placeholder="Alternativa C" required>
                    </div>
                    <div class="alternativa-item">
                        <label class="alt-radio">
                            <input type="radio" name="questoes[__INDEX__][questao_correta]" value="4">
                            <span class="alt-radio-custom"></span>
                        </label>
                        <span class="alt-label" data-alt="D">D</span>
                        <input type="text" name="questoes[__INDEX__][questao_d]" class="form-control alt-input" placeholder="Alternativa D" required>
                    </div>
                    <div class="alternativa-item optional-alt">
                        <label class="alt-radio">
                            <input type="radio" name="questoes[__INDEX__][questao_correta]" value="5">
                            <span class="alt-radio-custom"></span>
                        </label>
                        <span class="alt-label" data-alt="E">E</span>
                        <input type="text" name="questoes[__INDEX__][questao_e]" class="form-control alt-input" placeholder="Alternativa E (opcional)">
                    </div>
                </div>
                <p class="alt-hint">
                    <i class="fas fa-info-circle"></i> Marque o botão ao lado da alternativa correta
                </p>
            </div>
        </div>
    </div>
</template>

{{-- Questões existentes para o JS popular no edit --}}
<script>
    window.questoesIniciais = @json($questoes);
</script>