<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class SalaAulaProfessorController extends Controller
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('DOTNET_API_URL', 'http://profeluno_dotnet:9000');
    }

    // HELPERS DE API
    private function authHeaders(): array
    {
        $token = session('api_token');
        return [
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json',
            'Authorization' => "Bearer {$token}",
        ];
    }

    private function apiGet(string $endpoint): ?array
    {
        try {
            $response = Http::withHeaders($this->authHeaders())
                ->timeout(15)
                ->get("{$this->baseUrl}/v1/{$endpoint}");

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning("[SalaAulaController] GET {$endpoint} retornou {$response->status()}", [
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error("[SalaAulaController] GET {$endpoint} falhou: " . $e->getMessage());
            return null;
        }
    }

    private function apiPost(string $endpoint, array $data): ?array
    {
        try {
            $response = Http::withHeaders($this->authHeaders())
                ->timeout(15)
                ->post("{$this->baseUrl}/v1/{$endpoint}", $data);

            if ($response->successful()) {
                $json = $response->json();
                return is_array($json) ? $json : [];
            }

            Log::warning("[SalaAulaController] POST {$endpoint} retornou {$response->status()}", [
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error("[SalaAulaController] POST {$endpoint} falhou: " . $e->getMessage());
            return null;
        }
    }

    private function apiPut(string $endpoint, array $data): ?array
    {
        try {
            $response = Http::withHeaders($this->authHeaders())
                ->timeout(15)
                ->put("{$this->baseUrl}/v1/{$endpoint}", $data);

            if ($response->successful()) {
                $json = $response->json();
                return is_array($json) ? $json : [];
            }

            Log::warning("[SalaAulaController] PUT {$endpoint} retornou {$response->status()}", [
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error("[SalaAulaController] PUT {$endpoint} falhou: " . $e->getMessage());
            return null;
        }
    }

    private function apiDelete(string $endpoint): bool
    {
        try {
            $response = Http::withHeaders($this->authHeaders())
                ->timeout(15)
                ->delete("{$this->baseUrl}/v1/{$endpoint}");

            if ($response->successful()) {
                return true;
            }

            Log::warning("[SalaAulaController] DELETE {$endpoint} retornou {$response->status()}", [
                'body' => $response->body(),
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error("[SalaAulaController] DELETE {$endpoint} falhou: " . $e->getMessage());
            return false;
        }
    }

    // NORMALIZE
    private function normalizeSala(array $item): object
    {
        $sala = (object) $item;

        $sala->id = $sala->idSalaAula ?? null;

        $sala->data_hora_inicio = !empty($sala->dataHoraInicio)
            ? Carbon::parse($sala->dataHoraInicio)
            : null;

        $sala->data_hora_fim = !empty($sala->dataHoraFim)
            ? Carbon::parse($sala->dataHoraFim)
            : null;

        $sala->qtd_alunos = $sala->maxAlunos ?? 0;
        $sala->materia    = $sala->materia   ?? '—';
        $sala->avaliacao  = $sala->avaliacao ?? null;
        $sala->descricao  = $sala->descricao ?? null;
        $sala->status     = $sala->status    ?? 'pending';
        $sala->material   = $sala->material  ?? false;

        return $sala;
    }

    // INDEX
    public function index(Request $request)
    {
        $page    = (int) $request->get('page', 1);
        $perPage = 10;

        $data     = $this->apiGet("SalaAula/RetornaSalaAulaPorProfessor/" . Auth::id());
        $materias = $this->apiGet("Materia/ListarMaterias") ?? [];

        $materiasMap = collect($materias)->keyBy('idMateria');

        if (is_null($data)) {
            session()->flash('error', 'Não foi possível carregar as salas. Tente novamente.');

            $salas = new LengthAwarePaginator(
                collect(), 0, $perPage, $page,
                ['path' => $request->url()]
            );

            return view('professor.salas.index', [
                'salas'           => $salas,
                'salasAtivas'     => collect(),
                'salasAgendadas'  => collect(),
                'salasConcluidas' => collect(),
                'salaAtiva'       => null,
                'materias'        => $materias,
            ]);
        }

        $items = collect($data)->map(function ($i) use ($materiasMap) {
            $sala = $this->normalizeSala($i);

            if (isset($sala->idMateria) && $materiasMap->has($sala->idMateria)) {
                $sala->materia = $materiasMap->get($sala->idMateria)['nomeMateria'] ?? '—';
            }

            return $sala;
        });

        $salas = new LengthAwarePaginator(
            $items->forPage($page, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            ['path' => $request->url()]
        );

        $salasAtivas     = $items->where('status', 'active')->values();
        $salasAgendadas  = $items->where('status', 'pending')->values();
        $salasConcluidas = $items->where('status', 'completed')->values();
        $salaAtiva       = $salasAtivas->first();

        return view('professor.salas.index', compact(
            'salas', 'salasAtivas', 'salasAgendadas',
            'salasConcluidas', 'salaAtiva', 'materias',
        ));
    }

    // CREATE
    public function create()
    {
        $materias  = $this->apiGet('Materia/ListarMaterias') ?? [];
        $conteudos = $this->apiGet('Conteudo/RetornaConteudoPorIdProfessor/' . Auth::id()) ?? [];
        $conteudos = is_array($conteudos) && isset($conteudos[0])
            ? $conteudos
            : (isset($conteudos['idConteudo']) ? [$conteudos] : []);

        $simulados = $this->apiGet("Simulado/RetornaSimuladosPorUsuario/" . Auth::id()) ?? [];
        $simulados = is_array($simulados) && isset($simulados[0])
            ? $simulados
            : (isset($simulados['idSimulado']) ? [$simulados] : []);

        if (!$materias) {
            session()->flash('warning', 'Não foi possível carregar as matérias.');
        }

        return view('professor.salas.create', compact('materias', 'conteudos', 'simulados'));
    }

    // STORE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo'           => 'required|string|max:255',
            'descricao'        => 'nullable|string',
            'materia_id'       => 'required|integer',
            'max_alunos'       => 'required|integer|min:1|max:500',
            'status'           => 'required|in:active,pending',
            // inicio: obrigatório só se agendada (pending)
            'data_hora_inicio' => 'nullable|date',
            'data_hora_fim'    => 'nullable|date',
            'conteudo_id'      => 'nullable|integer',
            'simulado_id'      => 'nullable|integer',

            'questoes'                   => 'nullable|array|min:1',
            'questoes.*.enunciado'       => 'required_with:questoes|string',
            'questoes.*.questao_a'       => 'required_with:questoes|string',
            'questoes.*.questao_b'       => 'required_with:questoes|string',
            'questoes.*.questao_c'       => 'required_with:questoes|string',
            'questoes.*.questao_d'       => 'required_with:questoes|string',
            'questoes.*.questao_e'       => 'nullable|string',
            'questoes.*.questao_correta' => 'required_with:questoes|integer|between:1,5',
        ], [
            'titulo.required'     => 'O título é obrigatório.',
            'materia_id.required' => 'Selecione uma matéria.',
            'max_alunos.required' => 'Informe a quantidade máxima de alunos.',
            'status.in'           => 'Status inválido.',
        ]);

        // Se for ao vivo (active), dataHoraInicio = agora
        if ($validated['status'] === 'active') {
            $dataHoraInicio = now()->toIso8601String();
        } else {
            $dataHoraInicio = $validated['data_hora_inicio'] ?? null;
        }

        $dataHoraFim = $validated['data_hora_fim'] ?? null;

        $simuladoId = $validated['simulado_id'] ?? null;

        if (!empty($validated['questoes']) && empty($simuladoId)) {
            $simuladoCriado = $this->apiPost('Simulado/CadastrarSimulado', [
                'titulo'    => $validated['titulo'] . ' — Simulado',
                'idMateria' => $validated['materia_id'],
                'situacao'  => true,
                'questoes'  => array_values($validated['questoes']),
            ]);

            if (is_null($simuladoCriado)) {
                return back()->withInput()
                    ->withErrors(['simulado' => 'Falha ao criar o simulado. Tente novamente.']);
            }

            $simuladoId = $simuladoCriado['idSimulado'] ?? null;
        }

        $sala = $this->apiPost('SalaAula/CadastrarSalaAula', [
            'titulo'         => $validated['titulo'],
            'descricao'      => $validated['descricao'] ?? null,
            'idProfessor'    => Auth::id(),
            'maxAlunos'      => (int) $validated['max_alunos'],
            'dataHoraInicio' => $dataHoraInicio,
            'dataHoraFim'    => $dataHoraFim,
            'idMateria'      => (int) $validated['materia_id'],
            'status'         => $validated['status'],
            'idConteudo'     => $validated['conteudo_id'] ? (int) $validated['conteudo_id'] : null,
            'idSimulado'     => $simuladoId               ? (int) $simuladoId               : null,
        ]);

        if (is_null($sala)) {
            return back()->withInput()
                ->withErrors(['api' => 'Falha ao criar a sala. Tente novamente.']);
        }

        // Se foi criada como ao vivo, redireciona direto para a sala
        if ($validated['status'] === 'active') {
            $salaId = $sala['idSalaAula'] ?? null;
            if ($salaId) {
                return redirect()
                    ->route('professor.salas.video-aula', $salaId)
                    ->with('success', 'Sala criada e iniciada!');
            }
        }

        return redirect()
            ->route('professor.salas.index')
            ->with('success', 'Sala criada com sucesso!');
    }

    // SHOW
    public function show(int $id)
    {
        $data = $this->apiGet("SalaAula/RetornaSalaAulaPorId/{$id}");

        if (is_null($data)) {
            return redirect()->route('professor.salas.index')
                ->with('error', 'Sala não encontrada.');
        }

        $sala = $this->normalizeSala($data);

        if (isset($sala->idMateria)) {
            $materias      = $this->apiGet('Materia/ListarMaterias') ?? [];
            $materia       = collect($materias)->firstWhere('idMateria', $sala->idMateria);
            $sala->materia = $materia['nomeMateria'] ?? '—';
        }

        return view('professor.salas.show', compact('sala'));
    }

    // EDIT
    public function edit(int $id)
    {
        $data = $this->apiGet("SalaAula/RetornaSalaAulaPorId/{$id}");

        if (is_null($data)) {
            return redirect()->route('professor.salas.index')
                ->with('error', 'Sala não encontrada.');
        }

        $sala      = $this->normalizeSala($data);
        $materias  = $this->apiGet('Materia/ListarMaterias') ?? [];
        $conteudos = $this->apiGet('Conteudo/RetornaConteudoPorIdProfessor/' . Auth::id()) ?? [];
        $conteudos = is_array($conteudos) && isset($conteudos[0])
            ? $conteudos
            : (isset($conteudos['idConteudo']) ? [$conteudos] : []);

        $simulados = $this->apiGet("Simulado/RetornaSimuladosPorUsuario/" . Auth::id()) ?? [];
        $simulados = is_array($simulados) && isset($simulados[0])
            ? $simulados
            : (isset($simulados['idSimulado']) ? [$simulados] : []);

        return view('professor.salas.edit', compact('sala', 'materias', 'conteudos', 'simulados'));
    }

    // UPDATE
    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'titulo'           => 'required|string|max:255',
            'descricao'        => 'nullable|string',
            'materia_id'       => 'required|integer',
            'max_alunos'       => 'required|integer|min:1|max:500',
            'status'           => 'required|in:active,completed,pending',
            'data_hora_inicio' => 'nullable|date',
            'data_hora_fim'    => 'nullable|date',
            'conteudo_id'      => 'nullable|integer',
            'simulado_id'      => 'nullable|integer',
        ], [
            'titulo.required'     => 'O título é obrigatório.',
            'materia_id.required' => 'Selecione uma matéria.',
            'max_alunos.required' => 'Informe a quantidade máxima de alunos.',
            'status.in'           => 'Status inválido.',
        ]);

        $dadosAtuais = $this->apiGet("SalaAula/RetornaSalaAulaPorId/{$id}");

        if (is_null($dadosAtuais)) {
            return back()->withInput()
                ->withErrors(['api' => 'Não foi possível recuperar os dados da sala.']);
        }

        // Se mudou para active e não tinha início definido, usar agora
        if ($validated['status'] === 'active') {
            $dataHoraInicio = now()->toIso8601String();
        } else {
            $dataHoraInicio = $validated['data_hora_inicio']
                ?? $dadosAtuais['dataHoraInicio']
                ?? null;
        }

        $resultado = $this->apiPut('SalaAula/AtualizarSalaAula', [
            'idSalaAula'     => $id,
            'titulo'         => $validated['titulo'],
            'descricao'      => $validated['descricao'] ?? null,
            'idProfessor'    => Auth::id(),
            'maxAlunos'      => (int) $validated['max_alunos'],
            'dataHoraInicio' => $dataHoraInicio,
            'dataHoraFim'    => $validated['data_hora_fim'] ?? $dadosAtuais['dataHoraFim'] ?? null,
            'idMateria'      => (int) $validated['materia_id'],
            'status'         => $validated['status'],
            'idConteudo'     => $validated['conteudo_id'] ? (int) $validated['conteudo_id'] : null,
            'idSimulado'     => $validated['simulado_id'] ? (int) $validated['simulado_id'] : null,
            'url'            => $dadosAtuais['url']      ?? null,
            'nomeSala'       => $dadosAtuais['nomeSala'] ?? null,
        ]);

        if (is_null($resultado)) {
            return back()->withInput()
                ->withErrors(['api' => 'Falha ao atualizar a sala. Tente novamente.']);
        }

        return redirect()->route('professor.salas.index')
            ->with('success', 'Sala atualizada com sucesso!');
    }

    // DESTROY
    public function destroy(int $id)
    {
        // Verifica se a sala pertence ao professor antes de deletar
        $data = $this->apiGet("SalaAula/RetornaSalaAulaPorId/{$id}");

        if (is_null($data)) {
            return redirect()->route('professor.salas.index')
                ->with('error', 'Sala não encontrada.');
        }

        // Não permite deletar sala ativa
        if (($data['status'] ?? '') === 'active') {
            return redirect()->route('professor.salas.index')
                ->with('error', 'Não é possível deletar uma sala que está ao vivo. Encerre-a primeiro.');
        }

        $ok = $this->apiDelete("SalaAula/DeletarSalaAula/{$id}");

        if (!$ok) {
            return redirect()->route('professor.salas.index')
                ->with('error', 'Não foi possível deletar a sala. Tente novamente.');
        }

        return redirect()->route('professor.salas.index')
            ->with('success', 'Sala deletada com sucesso!');
    }

    // INICIAR
    public function iniciar(int $id)
    {
        // Verifica se já existe uma sala ativa para este professor
        $todasSalas = $this->apiGet("SalaAula/RetornaSalaAulaPorProfessor/" . Auth::id());

        if (!is_null($todasSalas)) {
            $salaAtivaExistente = collect($todasSalas)
                ->first(fn($s) => ($s['status'] ?? '') === 'active' && ($s['idSalaAula'] ?? 0) !== $id);

            if ($salaAtivaExistente) {
                return redirect()->route('professor.salas.index')
                    ->with('error', 'Você já possui uma aula ao vivo ativa. Encerre-a antes de iniciar outra.');
            }
        }

        $dadosAtuais = $this->apiGet("SalaAula/RetornaSalaAulaPorId/{$id}");

        if (is_null($dadosAtuais)) {
            return redirect()->route('professor.salas.index')
                ->with('error', 'Sala não encontrada.');
        }

        if (($dadosAtuais['status'] ?? '') !== 'pending') {
            return redirect()->route('professor.salas.index')
                ->with('error', 'Esta sala não pode ser iniciada.');
        }

        $resultado = $this->apiPut('SalaAula/AtualizarSalaAula', [
            'idSalaAula'     => $id,
            'titulo'         => $dadosAtuais['titulo'],
            'descricao'      => $dadosAtuais['descricao']      ?? null,
            'idProfessor'    => $dadosAtuais['idProfessor'],
            'maxAlunos'      => $dadosAtuais['maxAlunos'],
            'dataHoraInicio' => now()->toIso8601String(), // marca o início real agora
            'dataHoraFim'    => $dadosAtuais['dataHoraFim']    ?? null,
            'idMateria'      => $dadosAtuais['idMateria'],
            'status'         => 'active',
            'idConteudo'     => $dadosAtuais['idConteudo']     ?? null,
            'idSimulado'     => $dadosAtuais['idSimulado']     ?? null,
            'url'            => $dadosAtuais['url']            ?? null,
            'nomeSala'       => $dadosAtuais['nomeSala']       ?? null,
        ]);

        if (is_null($resultado)) {
            return redirect()->route('professor.salas.index')
                ->with('error', 'Não foi possível iniciar a sala. Tente novamente.');
        }

        // Redireciona para a sala de vídeo
        return redirect()->route('professor.salas.video-aula', $id)
            ->with('success', 'Aula iniciada!');
    }

    // ENCERRAR
    public function encerrar(int $id)
    {
        $dadosAtuais = $this->apiGet("SalaAula/RetornaSalaAulaPorId/{$id}");

        if (is_null($dadosAtuais)) {
            return redirect()->route('professor.salas.index')
                ->with('error', 'Sala não encontrada.');
        }

        if (($dadosAtuais['status'] ?? '') !== 'active') {
            return redirect()->route('professor.salas.index')
                ->with('error', 'Esta sala não está ativa.');
        }

        $resultado = $this->apiPut('SalaAula/AtualizarSalaAula', [
            'idSalaAula'     => $id,
            'titulo'         => $dadosAtuais['titulo'],
            'descricao'      => $dadosAtuais['descricao']      ?? null,
            'idProfessor'    => $dadosAtuais['idProfessor'],
            'maxAlunos'      => $dadosAtuais['maxAlunos'],
            'dataHoraInicio' => $dadosAtuais['dataHoraInicio'] ?? null,
            'dataHoraFim'    => now()->toIso8601String(), // marca o fim real agora
            'idMateria'      => $dadosAtuais['idMateria'],
            'status'         => 'completed',
            'idConteudo'     => $dadosAtuais['idConteudo']     ?? null,
            'idSimulado'     => $dadosAtuais['idSimulado']     ?? null,
            'url'            => $dadosAtuais['url']            ?? null,
            'nomeSala'       => $dadosAtuais['nomeSala']       ?? null,
        ]);

        if (is_null($resultado)) {
            return redirect()->route('professor.salas.index')
                ->with('error', 'Não foi possível encerrar a sala. Tente novamente.');
        }

        return redirect()->route('professor.salas.index')
            ->with('success', 'Aula encerrada com sucesso!');
    }

    // VIDEO AULA (professor)
    public function videoAula(int $id)
    {
        $data = $this->apiGet("SalaAula/RetornaSalaAulaPorId/{$id}");

        if (is_null($data)) {
            return redirect()->route('professor.salas.index')
                ->with('error', 'Sala não encontrada.');
        }

        $sala = $this->normalizeSala($data);

        // Só professor dono pode entrar
        if (($data['idProfessor'] ?? null) != Auth::id()) {
            return redirect()->route('professor.salas.index')
                ->with('error', 'Acesso não autorizado.');
        }

        // Sala precisa estar ativa
        if ($sala->status !== 'active') {
            return redirect()->route('professor.salas.index')
                ->with('error', 'Esta sala não está ao vivo.');
        }

        // Resolve nome da matéria
        if (isset($sala->idMateria)) {
            $materias      = $this->apiGet('Materia/ListarMaterias') ?? [];
            $materia       = collect($materias)->firstWhere('idMateria', $sala->idMateria);
            $sala->materia = $materia['nomeMateria'] ?? '—';
        }

        return view('professor.salas.video-aula', compact('sala'));
    }
}