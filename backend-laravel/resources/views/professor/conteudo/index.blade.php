{{-- resources/views/professor/conteudo/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Meus Conteúdos')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/forms.css') }}">
<link rel="stylesheet" href="{{ asset('css/conteudo-form.css') }}">
@endpush
@section('content')

<div class="page-header">
    <a href="{{ route('professor.conteudo.create') }}" class="btn-create">
        <i class="fas fa-plus"></i>
        Novo Conteúdo
    </a>
</div>

@if(session('success'))
<div class="alert alert-success">
    <i class="fas fa-check-circle"></i>
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert alert-danger">
    <i class="fas fa-exclamation-circle"></i>
    {{ session('error') }}
</div>
@endif

<div class="table-wrapper">
    <div class="table-toolbar">
        <div class="table-search">
            <input type="text" id="searchInput" placeholder="Buscar conteúdo, matéria, sala...">
            <i class="fas fa-search"></i>
        </div>
        <div style="display: flex; align-items: center; gap: 12px;">
            <select id="filterTipo" class="filter-select" style="height: 38px; font-size: 13px;">
                <option value="">Todos os tipos</option>
                <option value="pdf">📄 PDF</option>
                <option value="slide">🖥️ Slide</option>
                <option value="link">🎬 Link</option>
                <option value="document">📝 Documento</option>
                <option value="other">📦 Outro</option>
            </select>
            <span class="table-count" id="tableCount">
                {{ count($conteudos) }} conteúdo(s) encontrado(s)
            </span>
        </div>
    </div>

    <table class="data-table" id="conteudosTable">
        <thead>
            <tr>
                <th>#</th>
                <th>Título</th>
                <th>Matéria</th>
                <th>Tipo</th>
                <th>Situação</th>
                <th>Criado em</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($conteudos as $conteudo)
            @php
                $id          = $conteudo['idConteudo']             ?? '—';
                $titulo      = $conteudo['titulo']                 ?? '—';
                $nomeMateria = $conteudo['materia']['nomeMateria'] ?? '—';
                $tipo        = strtolower($conteudo['tipo']        ?? 'other');
                $situacao    = $conteudo['situacao']               ?? true;
                $criadoEm    = $conteudo['createdAt']              ?? null;
                $fileUrl     = $conteudo['url']                    ?? null;
                $nomeArquivo = $conteudo['nomeArquivo']            ?? null;

                $tipoIcones = [
                    'pdf'      => ['icon' => 'fa-file-pdf',  'cor' => '#ea5455', 'bg' => 'rgba(234,84,85,0.12)',    'label' => 'PDF'],
                    'slide'    => ['icon' => 'fa-desktop',   'cor' => '#ff9f43', 'bg' => 'rgba(255,159,67,0.12)',   'label' => 'Slide'],
                    'link'     => ['icon' => 'fa-film',      'cor' => '#00cfe8', 'bg' => 'rgba(0,207,232,0.12)',    'label' => 'Link'],
                    'document' => ['icon' => 'fa-file-word', 'cor' => '#7367f0', 'bg' => 'rgba(115,103,240,0.12)', 'label' => 'Documento'],
                    'other'    => ['icon' => 'fa-file',      'cor' => '#82868b', 'bg' => 'rgba(130,134,139,0.12)', 'label' => 'Outro'],
                ];

                $info    = $tipoIcones[$tipo] ?? $tipoIcones['other'];
                $hasLink = !empty($fileUrl)     && $tipo === 'link';
                $hasFile = !empty($nomeArquivo) && in_array($tipo, ['pdf', 'slide', 'document', 'other']);

                // Rota de exclusão gerada pelo Laravel (evita hardcode)
                $destroyUrl = route('professor.conteudo.destroy', $id);
            @endphp
            <tr data-tipo="{{ $tipo }}">
                <td>{{ $id }}</td>

                <td>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div class="avatar-initial"
                             style="background: {{ $info['bg'] }}; color: {{ $info['cor'] }}; border: 1px solid {{ $info['cor'] }}33;">
                            <i class="fas {{ $info['icon'] }}" style="font-size: 14px;"></i>
                        </div>
                        <div>
                            @if($hasLink)
                                <strong>
                                    <a href="#"
                                       onclick="openLinkModal({{ json_encode($titulo) }}, {{ json_encode($fileUrl) }}); return false;"
                                       style="color: inherit; text-decoration: none;"
                                       title="Visualizar link">
                                        {{ $titulo }}
                                        <i class="fas fa-external-link-alt"
                                           style="font-size: 10px; opacity: .6; margin-left: 4px;"></i>
                                    </a>
                                </strong>
                            @else
                                <strong>{{ $titulo }}</strong>
                            @endif

                            @if(!empty($conteudo['descricao']))
                            <p style="font-size: 12px; color: var(--text-secondary); margin: 2px 0 0;">
                                {{ Str::limit($conteudo['descricao'], 55) }}
                            </p>
                            @endif
                        </div>
                    </div>
                </td>

                <td>
                    <span class="badge"
                          style="background: rgba(0,207,232,0.1); color: var(--info-color); border: 1px solid rgba(0,207,232,0.25);">
                        <i class="fas fa-book"></i>
                        {{ $nomeMateria }}
                    </span>
                </td>

                <td>
                    <span class="badge"
                          style="background: {{ $info['bg'] }}; color: {{ $info['cor'] }}; border: 1px solid {{ $info['cor'] }}33;">
                        <i class="fas {{ $info['icon'] }}"></i>
                        {{ $info['label'] }}
                    </span>
                </td>

                <td>
                    <span class="badge {{ $situacao ? 'badge-ativo' : 'badge-inativo' }}">
                        <i class="fas fa-{{ $situacao ? 'check-circle' : 'times-circle' }}"></i>
                        {{ $situacao ? 'Ativo' : 'Inativo' }}
                    </span>
                </td>

                <td>
                    {{ $criadoEm ? \Carbon\Carbon::parse($criadoEm)->format('d/m/Y') : '—' }}
                </td>

                <td>
                    <div class="td-actions" style="flex-wrap: wrap; gap: 6px;">

                        @if($hasLink)
                        <button
                            class="btn-action-link"
                            title="Visualizar link"
                            onclick="openLinkModal({{ json_encode($titulo) }}, {{ json_encode($fileUrl) }})"
                        >
                            <i class="fas fa-play-circle"></i>
                            Abrir
                        </button>
                        @endif

                        @if($hasFile)
                        <a
                            href="{{ route('professor.conteudo.download', $id) }}"
                            class="btn-action-download"
                            title="Baixar arquivo"
                            download
                        >
                            <i class="fas fa-download"></i>
                            Baixar
                        </a>
                        @endif

                        <a href="{{ route('professor.conteudo.edit', $id) }}" class="btn-edit" title="Editar">
                            <i class="fas fa-pen"></i>
                            Editar
                        </a>

                        {{-- Passa a URL gerada pelo Laravel, sem hardcode --}}
                        <button
                            class="btn-delete"
                            onclick="openDeleteModal('{{ $destroyUrl }}', '{{ addslashes($titulo) }}')"
                        >
                            <i class="fas fa-trash-alt"></i>
                            Excluir
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr id="emptyRow">
                <td colspan="7">
                    <div class="table-empty">
                        <i class="fas fa-folder-open"></i>
                        <p>Nenhum conteúdo cadastrado ainda.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if(isset($conteudos) && is_object($conteudos) && method_exists($conteudos, 'links'))
<div class="pagination-wrapper">
    {{ $conteudos->links() }}
</div>
@endif

{{-- Modal: Visualizar Link --}}
<div class="modal-overlay" id="linkViewModal">
    <div class="modal-box" style="padding:0; max-width: 900px; width: 95vw;">
        <div class="modal-view-header">
            <i class="fas fa-external-link-alt" style="color: var(--primary-color);"></i>
            <span id="linkViewTitle">—</span>
            <a id="linkViewExternal" href="#" target="_blank" rel="noopener">
                <i class="fas fa-external-link-alt"></i> Abrir em nova aba
            </a>
            <button
                style="background:none;border:none;cursor:pointer;font-size:16px;color:var(--text-secondary);"
                onclick="closeLinkModal()"
            ><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-view-body" id="linkViewBody"
             style="min-height: 520px; height: 60vh;"></div>
    </div>
</div>

{{-- Modal: Excluir --}}
<div class="modal-overlay" id="deleteModal">
    <div class="modal-box">
        <div class="modal-icon">
            <i class="fas fa-trash-alt"></i>
        </div>
        <h4>Excluir Conteúdo</h4>
        <p>Tem certeza que deseja excluir <strong id="deleteConteudoName"></strong>? Esta ação não pode ser desfeita.</p>
        <div class="modal-actions">
            <button class="btn-cancel" onclick="closeDeleteModal()">
                <i class="fas fa-times"></i>
                Cancelar
            </button>
            <form id="deleteForm" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-delete" style="padding: 13px 22px;">
                    <i class="fas fa-trash-alt"></i>
                    Confirmar Exclusão
                </button>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ── Filtros de busca ─────────────────────────────────────────────
document.getElementById('searchInput').addEventListener('input', applyFilters);
document.getElementById('filterTipo').addEventListener('change', applyFilters);

function applyFilters() {
    const q    = document.getElementById('searchInput').value.toLowerCase();
    const tipo = document.getElementById('filterTipo').value.toLowerCase();
    let visible = 0;

    document.querySelectorAll('#conteudosTable tbody tr:not(#emptyRow)').forEach(row => {
        const text    = row.textContent.toLowerCase();
        const rowTipo = (row.dataset.tipo || '').toLowerCase();
        const matchQ    = !q    || text.includes(q);
        const matchTipo = !tipo || rowTipo === tipo;
        row.style.display = (matchQ && matchTipo) ? '' : 'none';
        if (matchQ && matchTipo) visible++;
    });

    document.getElementById('tableCount').textContent = `${visible} conteúdo(s) encontrado(s)`;
}

// ── Modal: Excluir ────────────────────────────────────────────────
// Recebe a URL completa já gerada pelo Laravel via route() no Blade
function openDeleteModal(url, nome) {
    document.getElementById('deleteConteudoName').textContent = nome;
    document.getElementById('deleteForm').action = url;
    document.getElementById('deleteModal').classList.add('active');
}
function closeDeleteModal() {
    document.getElementById('deleteModal').classList.remove('active');
}
document.getElementById('deleteModal').addEventListener('click', function (e) {
    if (e.target === this) closeDeleteModal();
});

// ── Modal: Visualizar Link ────────────────────────────────────────
function getYouTubeId(url) {
    const m = url.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([^&?/]+)/);
    return m ? m[1] : null;
}
function getGDriveId(url) {
    const m = url.match(/drive\.google\.com\/file\/d\/([^/]+)/);
    return m ? m[1] : null;
}

function openLinkModal(titulo, url) {
    document.getElementById('linkViewTitle').textContent = titulo;
    document.getElementById('linkViewExternal').href = url;

    const body = document.getElementById('linkViewBody');

    const ytId = getYouTubeId(url);
    if (ytId) {
        body.innerHTML = `<iframe
            src="https://www.youtube.com/embed/${ytId}"
            style="width:100%;height:100%;min-height:520px;border:none;"
            allowfullscreen></iframe>`;
    } else {
        const gdId = getGDriveId(url);
        if (gdId) {
            body.innerHTML = `<iframe
                src="https://drive.google.com/file/d/${gdId}/preview"
                style="width:100%;height:100%;min-height:520px;border:none;"></iframe>`;
        } else {
            body.innerHTML = `
                <div class="generic-link-view" style="padding: 40px; text-align: center;">
                    <i class="fas fa-external-link-alt" style="font-size: 48px; opacity:.4; margin-bottom: 16px;"></i>
                    <p style="margin: 8px 0 4px; font-weight:600; font-size: 16px;">${titulo}</p>
                    <a href="${url}" target="_blank" rel="noopener"
                       style="word-break: break-all; color: var(--primary-color);">${url}</a>
                    <br><br>
                    <a href="${url}" target="_blank" rel="noopener" class="btn-form-submit" style="display:inline-flex;gap:6px;">
                        <i class="fas fa-external-link-alt"></i> Abrir link
                    </a>
                </div>`;
        }
    }

    document.getElementById('linkViewModal').classList.add('active');
}

function closeLinkModal() {
    document.getElementById('linkViewModal').classList.remove('active');
    document.getElementById('linkViewBody').innerHTML = '';
}
document.getElementById('linkViewModal').addEventListener('click', function (e) {
    if (e.target === this) closeLinkModal();
});
</script>
@endpush