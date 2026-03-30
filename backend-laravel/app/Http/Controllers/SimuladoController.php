<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SimuladoController extends Controller
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('DOTNET_API_URL', 'http://profeluno_dotnet:9000');
    }

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

            Log::warning("[SimuladoController] GET {$endpoint} retornou {$response->status()}", [
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error("[SimuladoController] GET {$endpoint} falhou: " . $e->getMessage());
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
                // API pode retornar JSON ou string vazia (ex: 200/201 sem body)
                $json = $response->json();
                return is_array($json) ? $json : [];
            }

            Log::warning("[SimuladoController] POST {$endpoint} retornou {$response->status()}", [
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error("[SimuladoController] POST {$endpoint} falhou: " . $e->getMessage());
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
                // API pode retornar JSON ou string vazia (ex: 200/201 sem body)
                $json = $response->json();
                return is_array($json) ? $json : [];
            }

            Log::warning("[SimuladoController] PUT {$endpoint} retornou {$response->status()}", [
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error("[SimuladoController] PUT {$endpoint} falhou: " . $e->getMessage());
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

            Log::warning("[SimuladoController] DELETE {$endpoint} retornou {$response->status()}", [
                'body' => $response->body(),
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error("[SimuladoController] DELETE {$endpoint} falhou: " . $e->getMessage());
            return false;
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CRUD
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Lista todos os simulados do professor autenticado.
     *
     * GET /api/simulados?professor_id={id}
     */
    public function index()
    {
        $professorId = Auth::id();
        // ── Chamada à API .NET ──────────────────────────────────────────────
        $data = $this->apiGet("Simulado/RetornaSimuladosPorUsuario/{$professorId}");

        // ── Fallback: dados fictícios enquanto a API não está disponível ────
        // TODO: remover após integração completa
        if (is_null($data)) {
            $data = [
                [
                    'id'           => 1,
                    'titulo'       => 'Simulado — Equações do 2º Grau',
                    'descricao'    => 'Avaliação com 15 questões de múltipla escolha',
                    'sala'         => 'Aula de Matemática',
                    'materia'      => 'Matemática',
                    'qtd_questoes' => 15,
                    'situacao'     => 1,
                    'criado_em'    => '2026-03-20T10:00:00',
                ],
                [
                    'id'           => 2,
                    'titulo'       => 'Simulado — Leis de Newton',
                    'descricao'    => '10 questões sobre dinâmica e cinemática',
                    'sala'         => 'Aula de Física',
                    'materia'      => 'Física',
                    'qtd_questoes' => 10,
                    'situacao'     => 1,
                    'criado_em'    => '2026-03-21T14:30:00',
                ],
                [
                    'id'           => 3,
                    'titulo'       => 'Simulado — Segunda Guerra Mundial',
                    'descricao'    => '20 questões sobre o contexto histórico',
                    'sala'         => 'Aula de História',
                    'materia'      => 'História',
                    'qtd_questoes' => 20,
                    'situacao'     => 1,
                    'criado_em'    => '2026-03-19T09:00:00',
                ],
                [
                    'id'           => 4,
                    'titulo'       => 'Simulado — Tabela Periódica',
                    'descricao'    => 'Questões sobre elementos e ligações químicas',
                    'sala'         => 'Aula de Química',
                    'materia'      => 'Química',
                    'qtd_questoes' => 8,
                    'situacao'     => 0,
                    'criado_em'    => '2026-03-17T11:00:00',
                ],
                [
                    'id'           => 5,
                    'titulo'       => 'Simulado — Análise Sintática',
                    'descricao'    => 'Exercícios de gramática e interpretação',
                    'sala'         => 'Aula de Português',
                    'materia'      => 'Português',
                    'qtd_questoes' => 12,
                    'situacao'     => 1,
                    'criado_em'    => '2026-03-22T16:00:00',
                ],
            ];
        }

        $simulados = $data;

        $title    = '<i class="fas fa-list-ol"></i> Simulados';
        $subtitle = 'Gerencie os simulados vinculados às suas salas de aula';

        return view('professor.simulado.index', compact('simulados', 'title', 'subtitle'));
    }

    /**
     * Exibe o formulário de criação de simulado.
     * Busca as materias disponíveis do professor para preencher o select.
     *
     * GET /api/materias?professor_id={id}
     */
    public function create()
    {
        $materias = $this->apiGet("Materia/ListarMaterias") ?? [];

        $ultimapagina = "<a href='" . route('professor.simulados.index') . "' class='back-link'>
            <i class='fas fa-arrow-left'></i>
            Voltar
        </a>";

        $title    = '<i class="fas fa-plus"></i> Criar Simulado';
        $subtitle = 'Preencha os detalhes para criar um novo simulado';

        return view('professor.simulado.create', compact('materias', 'title', 'subtitle', 'ultimapagina'));
    }

    /**
     * Salva o novo simulado via API.
     *
     * POST /api/simulados
     *
     * Payload esperado pela API:
     * {
     *   "titulo":       "string",
     *   "descricao":    "string|null",
     *   "materia_id":   int,
     *   "professor_id": int,
     *   "situacao":     bool,
     *   "questoes": [
     *     {
     *       "ordem":           int,
     *       "enunciado":       "string",
     *       "questao_a":       "string",
     *       "questao_b":       "string",
     *       "questao_c":       "string",
     *       "questao_d":       "string",
     *       "questao_e":       "string|null",
     *       "questao_correta": int  // 1=A 2=B 3=C 4=D 5=E
     *     }
     *   ]
     * }
     */
    public function store(Request $request)
    {
        $request->validate([
            'titulo'                            => 'required|string|max:255',
            'descricao'                         => 'nullable|string',
            'materia_id'                        => 'required|integer',
            'questoes'                          => 'required|array|min:1',
            'questoes.*.enunciado'              => 'required|string',
            'questoes.*.questao_a'              => 'required|string',
            'questoes.*.questao_b'              => 'required|string',
            'questoes.*.questao_c'              => 'required|string',
            'questoes.*.questao_d'              => 'required|string',
            'questoes.*.questao_e'              => 'nullable|string',
            'questoes.*.questao_correta'        => 'required|integer|between:1,5',
        ]);

        // Normaliza questões com a ordem correta
        $questoes = collect($request->questoes)
            ->values()
            ->map(function ($q, $index) {
                return [
                    'ordem'           => $index + 1,
                    'enunciado'       => $q['enunciado'],
                    'questaoA'       => $q['questao_a'],
                    'questaoB'       => $q['questao_b'],
                    'questaoC'       => $q['questao_c'],
                    'questaoD'       => $q['questao_d'],
                    'questaoE'       => $q['questao_e'] ?? null,
                    'questaoCorreta' => (int) $q['questao_correta'],
                ];
            })
            ->toArray();

        $payload = [
            'titulo'       => $request->titulo,
            'descricao'    => $request->descricao,
            'idMateria'    => (int) $request->materia_id,
            'idUser'       => Auth::id(),
            'situacao'     => true,
            'simuladoQuestoesRequests'     => $questoes,
        ];
        // dd($payload);
        // ── Chamada à API .NET ──────────────────────────────────────────────
        // $resultado = $this->apiPost('Simulado/CadastrarSimulado', $payload);
        $resultado = $this->apiPost('Simulado/CadastrarSimulado', [$payload]);
        if (is_null($resultado)) {
            return back()
                ->withInput()
                ->with('error', 'Não foi possível criar o simulado. Tente novamente.');
        }

        return redirect()
            ->route('professor.simulados.index')
            ->with('success', 'Simulado criado com sucesso!');
    }

    /**
     * Exibe as questões de um simulado.
     *
     */
    public function show(int $id)
    {
        $professorId = Auth::id();
        $simulado = $this->apiGet("Simulado/RetornaSimuladoPorId/{$id}/{$professorId}");

        if (is_null($simulado)) {
            return redirect()
                ->route('professor.simulados.index')
                ->with('error', 'Simulado não encontrado.');
        }

        $title    = '<i class="fas fa-eye"></i> Ver Simulado';
        $subtitle = $simulado['titulo'] ?? 'Detalhes do simulado';

        $ultimapagina = "<a href='" . route('professor.simulados.index') . "' class='back-link'>
            <i class='fas fa-arrow-left'></i>
            Voltar
        </a>";

        return view('professor.simulado.show', compact('simulado', 'title', 'subtitle', 'ultimapagina'));
    }

    /**
     * Exibe o formulário de edição de um simulado.
     *
     */
    public function edit(int $id)
    {
        $professorId = Auth::id();
        $simulado = $this->apiGet("Simulado/RetornaSimuladoPorId/{$id}/{$professorId}");

        if (is_null($simulado)) {
            return redirect()
                ->route('professor.simulados.index')
                ->with('error', 'Simulado não encontrado.');
        }

        $materias = $this->apiGet("Materia/ListarMaterias") ?? [];

        $title    = '<i class="fas fa-pen"></i> Editar Simulado';
        $subtitle = 'Atualize os dados do simulado';

        $ultimapagina = "<a href='" . route('professor.simulados.index') . "' class='back-link'>
            <i class='fas fa-arrow-left'></i>
            Voltar
        </a>";

        return view('professor.simulado.edit', compact('simulado', 'materias', 'title', 'subtitle', 'ultimapagina'));
    }

    /**
     * Atualiza um simulado via API.
     *
     * PUT /api/simulado/{id}
     */
    public function update(Request $request, int $id)
    {
        $request->validate([
            'titulo'                            => 'required|string|max:255',
            'descricao'                         => 'nullable|string',
            'materia_id'                        => 'required|integer',
            'situacao'                          => 'boolean',
            'questoes'                          => 'required|array|min:1',
            'questoes.*.enunciado'              => 'required|string',
            'questoes.*.questao_a'              => 'required|string',
            'questoes.*.questao_b'              => 'required|string',
            'questoes.*.questao_c'              => 'required|string',
            'questoes.*.questao_d'              => 'required|string',
            'questoes.*.questao_e'              => 'nullable|string',
            'questoes.*.questao_correta'        => 'required|integer|between:1,5',
        ]);

        $questoes = collect($request->questoes)
            ->values()
            ->map(function ($q, $index) {
                return [
                    'ordem'           => $index + 1,
                    'enunciado'       => $q['enunciado'],
                    'questaoA'       => $q['questao_a'],
                    'questaoB'       => $q['questao_b'],
                    'questaoC'       => $q['questao_c'],
                    'questaoD'       => $q['questao_d'],
                    'questaoE'       => $q['questao_e'] ?? null,
                    'questaoCorreta' => (int) $q['questao_correta'],
                ];
            })
            ->toArray();

        $payload = [
            'idSimulado'   => $id,
            'titulo'       => $request->titulo,
            'descricao'    => $request->descricao,
            'idMateria'    => (int) $request->materia_id,
            'idUser'       => Auth::id(),
            'situacao'     => $request->boolean('situacao', true),
            'simuladoQuestoesRequests'     => $questoes,
        ];

        $resultado = $this->apiPut("Simulado/AtualizarSimulado", $payload);

        if (is_null($resultado)) {
            return back()
                ->withInput()
                ->with('error', 'Não foi possível atualizar o simulado. Tente novamente.');
        }

        return redirect()
            ->route('professor.simulados.index')
            ->with('success', 'Simulado atualizado com sucesso!');
    }

    /**
     * Remove um simulado via API.
     *
     * DELETE /api/simulado/{id}
     */
    public function destroy(int $id)
    {
        $sucesso = $this->apiDelete("Simulado/DeletarSimulado/{$id}");

        if (! $sucesso) {
            return redirect()
                ->route('professor.simulados.index')
                ->with('error', 'Não foi possível excluir o simulado. Tente novamente.');
        }

        return redirect()
            ->route('professor.simulados.index')
            ->with('success', 'Simulado excluído com sucesso!');
    }

    /**
     * Ativa ou desativa um simulado.
     *
     * PATCH /api/simulado/{id}/situacao
     */
    public function toggleSituacao(int $id)
    {
        // Primeiro busca o simulado para saber a situação atual
        $simulado = $this->apiGet("/Simulado/BuscarSimuladoPorId/{$id}");

        if (is_null($simulado)) {
            return response()->json(['error' => 'Simulado não encontrado'], 404);
        }

        $novaSituacao = ! ($simulado['situacao'] ?? true);

        try {
            $response = Http::withHeaders($this->authHeaders())
                ->timeout(15)
                ->patch("{$this->baseUrl}/api/simulado/{$id}/situacao", [
                    'situacao' => $novaSituacao,
                ]);

            if ($response->successful()) {
                return response()->json(['situacao' => $novaSituacao]);
            }

            return response()->json(['error' => 'Falha ao atualizar situação'], 500);
        } catch (\Exception $e) {
            Log::error("[SimuladoController] PATCH /api/simulado/{$id}/situacao falhou: " . $e->getMessage());
            return response()->json(['error' => 'Erro interno'], 500);
        }
    }
}