{{-- resources/views/professor/simulado-criar.blade.php --}}
@extends('layouts.app')

@section('title', 'Novo Simulado')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/sala-professor.css') }}">
@endsection

@section('content')

<form action="{{ route('professor.simulados.store') }}" method="POST" id="formSimulado">
    @csrf
    <input type="hidden" name="sala_aula_id" value="{{-- $sala->id --}}">

    <div class="form-grid-two">

        {{-- Coluna principal: questões --}}
        <div class="form-col-main">

            {{-- Container dinâmico de questões --}}
            <div id="questoesContainer">
                {{-- questões geradas pelo JS --}}
            </div>

            <div class="add-questao-area">
                <button type="button" class="btn-add-questao-lg" id="addQuestao">
                    <span class="add-questao-inner">
                        <i class="fas fa-plus-circle"></i>
                        Adicionar Nova Questão
                    </span>
                </button>
            </div>

            {{-- Validação --}}
            @if($errors->any())
            <div class="alert alert-danger">
                <ul style="margin:0; padding-left: 18px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="form-actions">
                <a href="{{-- route('professor.sala.show', $sala->id) --}}" class="btn-form-cancel">
                    <i class="fas fa-times"></i> Cancelar
                </a>
                <button type="submit" class="btn-form-submit" id="submitSimulado">
                    <i class="fas fa-save"></i>
                    Salvar Simulado
                </button>
            </div>
        </div>

        {{-- Lateral --}}
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

            {{-- Índice de navegação das questões --}}
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
</form>

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
                        <span class="alt-label correct-indicator" data-alt="A">A</span>
                        <input
                            type="text"
                            name="questoes[__INDEX__][questao_a]"
                            class="form-control alt-input"
                            placeholder="Alternativa A"
                            required
                        >
                    </div>
                    <div class="alternativa-item">
                        <label class="alt-radio">
                            <input type="radio" name="questoes[__INDEX__][questao_correta]" value="2">
                            <span class="alt-radio-custom"></span>
                        </label>
                        <span class="alt-label" data-alt="B">B</span>
                        <input
                            type="text"
                            name="questoes[__INDEX__][questao_b]"
                            class="form-control alt-input"
                            placeholder="Alternativa B"
                            required
                        >
                    </div>
                    <div class="alternativa-item">
                        <label class="alt-radio">
                            <input type="radio" name="questoes[__INDEX__][questao_correta]" value="3">
                            <span class="alt-radio-custom"></span>
                        </label>
                        <span class="alt-label" data-alt="C">C</span>
                        <input
                            type="text"
                            name="questoes[__INDEX__][questao_c]"
                            class="form-control alt-input"
                            placeholder="Alternativa C"
                            required
                        >
                    </div>
                    <div class="alternativa-item">
                        <label class="alt-radio">
                            <input type="radio" name="questoes[__INDEX__][questao_correta]" value="4">
                            <span class="alt-radio-custom"></span>
                        </label>
                        <span class="alt-label" data-alt="D">D</span>
                        <input
                            type="text"
                            name="questoes[__INDEX__][questao_d]"
                            class="form-control alt-input"
                            placeholder="Alternativa D"
                            required
                        >
                    </div>
                    <div class="alternativa-item optional-alt">
                        <label class="alt-radio">
                            <input type="radio" name="questoes[__INDEX__][questao_correta]" value="5">
                            <span class="alt-radio-custom"></span>
                        </label>
                        <span class="alt-label" data-alt="E">E</span>
                        <input
                            type="text"
                            name="questoes[__INDEX__][questao_e]"
                            class="form-control alt-input"
                            placeholder="Alternativa E (opcional)"
                        >
                    </div>
                </div>
                <p class="alt-hint"><i class="fas fa-info-circle"></i> Marque o botão ao lado da alternativa correta</p>
            </div>
        </div>
    </div>
</template>

@endsection

@section('scripts')
<script src="{{ asset('js/sala-professor.js') }}"></script>
@endsection