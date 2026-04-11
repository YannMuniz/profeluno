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

    /**
     * POST com body JSON — usado onde a API aceita body normal.
     */
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

    /**
     * PUT com body JSON — usado onde a API aceita body normal.
     */
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

    // NORMALIZA UM ITEM DA API → objeto com Carbon nas datas
    private function normalizeSala(array $item): object
    {
        $sala = (object) $item;

        $sala->data_hora_inicio = !empty($sala->data_hora_inicio)
            ? Carbon::parse($sala->data_hora_inicio)
            : null;

        $sala->data_hora_fim = !empty($sala->data_hora_fim)
            ? Carbon::parse($sala->data_hora_fim)
            : null;

        // Garante campos que a view usa, mesmo que a API não retorne
        $sala->avaliacao  = $sala->avaliacao  ?? null;
        $sala->qtd_alunos = $sala->qtd_alunos ?? 0;
        $sala->material   = $sala->material   ?? false;
        $sala->status     = $sala->status     ?? 'pending';
        $sala->materia    = $sala->materia    ?? '—';
        $sala->descricao  = $sala->descricao  ?? null;

        // Aliases para facilitar o acesso nas views
        // A API retorna idSalaAula — expõe como id também
        $sala->id         = $sala->idSalaAula ?? $sala->id ?? null;

        return $sala;
    }

    // INDEX — lista paginada de salas do professor
    public function index(Request $request)
    {
        $page    = (int) $request->get('page', 1);
        $perPage = 10;

        $data     = $this->apiGet("SalaAula/ListarSalasPorProfessor/" . Auth::id());
        $materias = $this->apiGet("Materia/ListarMaterias") ?? [];

        if (is_null($data)) {
            session()->flash('error', 'Não foi possível carregar as salas. Tente novamente.');

            $salas = new LengthAwarePaginator(
                collect(),
                0,
                $perPage,
                $page,
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

        // Suporte a dois formatos: { data: [...], total: N } ou [ ... ]
        if (isset($data['data']) && is_array($data['data'])) {
            $items = collect($data['data'])->map(fn($i) => $this->normalizeSala($i));
            $total = $data['total'] ?? $items->count();
        } else {
            $items = collect($data)->map(fn($i) => $this->normalizeSala($i));
            $total = $items->count();
        }

        $salas = new LengthAwarePaginator(
            $items->forPage($page, $perPage)->values(),
            $total,
            $perPage,
            $page,
            ['path' => $request->url()]
        );

        $salasAtivas     = $items->where('status', 'active')->values();
        $salasAgendadas  = $items->where('status', 'pending')->values();
        $salasConcluidas = $items->where('status', 'completed')->values();
        $salaAtiva       = $salasAtivas->first();

        return view('professor.salas.index', compact(
            'salas',
            'salasAtivas',
            'salasAgendadas',
            'salasConcluidas',
            'salaAtiva',
            'materias',
        ));
    }

    // CREATE — formulário de criação (steps)
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

    // STORE — salva nova sala via API
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo'           => 'required|string|max:255',
            'descricao'        => 'nullable|string',
            'materia_id'       => 'required|integer',
            'max_alunos'       => 'required|integer|min:1|max:500',
            'data_hora_inicio' => 'nullable|date',
            'data_hora_fim'    => 'nullable|date|after_or_equal:data_hora_inicio',
            'status'           => 'required|in:active,pending',
            'conteudo_id'      => 'nullable|integer',
            'simulado_id'      => 'nullable|integer',

            // Simulado inline (criação no momento — Step 3)
            'questoes'                   => 'nullable|array|min:1',
            'questoes.*.enunciado'       => 'required_with:questoes|string',
            'questoes.*.questao_a'       => 'required_with:questoes|string',
            'questoes.*.questao_b'       => 'required_with:questoes|string',
            'questoes.*.questao_c'       => 'required_with:questoes|string',
            'questoes.*.questao_d'       => 'required_with:questoes|string',
            'questoes.*.questao_e'       => 'nullable|string',
            'questoes.*.questao_correta' => 'required_with:questoes|integer|between:1,5',
        ], [
            'titulo.required'              => 'O título é obrigatório.',
            'materia_id.required'          => 'Selecione uma matéria.',
            'max_alunos.required'          => 'Informe a quantidade máxima de alunos.',
            'data_hora_fim.after_or_equal' => 'O fim deve ser após o início.',
            'status.in'                    => 'Status inválido.',
        ]);

        // Gera sala Jitsi automática
        $roomName = Str::uuid()->toString();
        $jitsiUrl = env('JITSI_BASE_URL', 'https://meet.jit.si') . '/' . $roomName;

        // Se veio simulado inline, cria primeiro
        $simuladoId = $validated['simulado_id'] ?? null;

        if (!empty($validated['questoes']) && empty($simuladoId)) {
            $simuladoPayload = [
                'titulo'    => ($validated['titulo'] ?? 'Simulado') . ' — Simulado',
                'idMateria' => $validated['materia_id'],
                'situacao'  => true,
                'questoes'  => array_values($validated['questoes']),
            ];

            $simuladoCriado = $this->apiPost('Simulado/CadastrarSimulado', $simuladoPayload);

            if (is_null($simuladoCriado)) {
                return back()
                    ->withInput()
                    ->withErrors(['simulado' => 'Falha ao criar o simulado. Tente novamente.']);
            }

            $simuladoId = $simuladoCriado['idSimulado'] ?? null;
        }

        // Monta payload conforme contrato da API CadastrarSalaAula
        $payload = [
            'titulo'          => $validated['titulo'],
            'descricao'       => $validated['descricao'] ?? null,
            'idProfessor'     => Auth::id(),
            'maxAlunos'       => (int) $validated['max_alunos'],
            'dataHoraInicio'  => $validated['data_hora_inicio'] ?? null,
            'dataHoraFim'     => $validated['data_hora_fim']    ?? null,
            'idMateria'       => (int) $validated['materia_id'],
            'status'          => $validated['status'],
            'idConteudo'      => $validated['conteudo_id'] ? (int) $validated['conteudo_id'] : null,
            'idSimulado'      => $simuladoId               ? (int) $simuladoId               : null,
            'url'             => $jitsiUrl,
            'nomeSala'        => $roomName,
        ];

        $sala = $this->apiPost('SalaAula/CadastrarSalaAula', $payload);

        if (is_null($sala)) {
            return back()
                ->withInput()
                ->withErrors(['api' => 'Falha ao criar a sala. Tente novamente.']);
        }

        // A API pode retornar o id como idSalaAula
        $salaId = $sala['idSalaAula'] ?? $sala['id'] ?? null;

        if ($salaId) {
            return redirect()
                ->route('professor.salas.show', $salaId)
                ->with('success', 'Sala criada com sucesso!');
        }

        return redirect()
            ->route('professor.salas.index')
            ->with('success', 'Sala criada com sucesso!');
    }

    // SHOW — detalhes de uma sala
    public function show(int $id)
    {
        $data = $this->apiGet("SalaAula/RetornaSalaAulaPorId/{$id}");

        if (is_null($data)) {
            return redirect()
                ->route('professor.salas.index')
                ->with('error', 'Sala não encontrada.');
        }

        $sala = $this->normalizeSala($data);

        return view('professor.salas.show', compact('sala'));
    }

    // EDIT — formulário de edição
    public function edit(int $id)
    {
        $data = $this->apiGet("SalaAula/RetornaSalaAulaPorId/{$id}");

        if (is_null($data)) {
            return redirect()
                ->route('professor.salas.index')
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

    // UPDATE — atualiza sala via API
    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'titulo'           => 'required|string|max:255',
            'descricao'        => 'nullable|string',
            'materia_id'       => 'required|integer',
            'max_alunos'       => 'required|integer|min:1|max:500',
            'data_hora_inicio' => 'nullable|date',
            'data_hora_fim'    => 'nullable|date|after_or_equal:data_hora_inicio',
            'status'           => 'required|in:active,completed,pending',
            'conteudo_id'      => 'nullable|integer',
            'simulado_id'      => 'nullable|integer',
        ], [
            'titulo.required'              => 'O título é obrigatório.',
            'materia_id.required'          => 'Selecione uma matéria.',
            'max_alunos.required'          => 'Informe a quantidade máxima de alunos.',
            'data_hora_fim.after_or_equal' => 'O fim deve ser após o início.',
            'status.in'                    => 'Status inválido.',
        ]);

        // Monta payload conforme contrato da API AtualizarSalaAula
        $payload = [
            'idSalaAula'      => $id,
            'titulo'          => $validated['titulo'],
            'descricao'       => $validated['descricao'] ?? null,
            'idProfessor'     => Auth::id(),
            'maxAlunos'       => (int) $validated['max_alunos'],
            'dataHoraInicio'  => $validated['data_hora_inicio'] ?? null,
            'dataHoraFim'     => $validated['data_hora_fim']    ?? null,
            'idMateria'       => (int) $validated['materia_id'],
            'status'          => $validated['status'],
            'idConteudo'      => $validated['conteudo_id'] ? (int) $validated['conteudo_id'] : null,
            'idSimulado'      => $validated['simulado_id'] ? (int) $validated['simulado_id'] : null,
        ];

        $resultado = $this->apiPut('SalaAula/AtualizarSalaAula', $payload);

        if (is_null($resultado)) {
            return back()
                ->withInput()
                ->withErrors(['api' => 'Falha ao atualizar a sala. Tente novamente.']);
        }

        return redirect()
            ->route('professor.salas.show', $id)
            ->with('success', 'Sala atualizada com sucesso!');
    }

    // DESTROY — deleta sala via API
    public function destroy(int $id)
    {
        $ok = $this->apiDelete("SalaAula/DeletarSalaAula/{$id}");

        if (!$ok) {
            return redirect()
                ->route('professor.salas.index')
                ->with('error', 'Não foi possível deletar a sala. Tente novamente.');
        }

        return redirect()
            ->route('professor.salas.index')
            ->with('success', 'Sala deletada com sucesso!');
    }

    // INICIAR — muda status para active
    public function iniciar(int $id)
    {
        // Primeiro busca os dados atuais da sala para reenviar tudo
        $data = $this->apiGet("SalaAula/RetornaSalaAulaPorId/{$id}");

        if (is_null($data)) {
            return redirect()
                ->route('professor.salas.index')
                ->with('error', 'Sala não encontrada.');
        }

        $payload = array_merge($data, [
            'idSalaAula' => $id,
            'status'     => 'active',
        ]);

        $resultado = $this->apiPut('SalaAula/AtualizarSalaAula', $payload);

        if (is_null($resultado)) {
            return redirect()
                ->route('professor.salas.index')
                ->with('error', 'Não foi possível iniciar a sala. Tente novamente.');
        }

        return redirect()
            ->route('professor.salas.show', $id)
            ->with('success', 'Aula iniciada!');
    }
}