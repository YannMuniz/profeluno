<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class HistoricoAlunoController extends Controller
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('DOTNET_API_URL', 'http://profeluno_dotnet:9000');
    }

    // ─── HELPERS ────────────────────────────────────────────────────────────

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

            Log::warning("[HistoricoAlunoController] GET {$endpoint} retornou {$response->status()}", [
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error("[HistoricoAlunoController] GET {$endpoint} falhou: " . $e->getMessage());
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

            Log::warning("[HistoricoAlunoController] POST {$endpoint} retornou {$response->status()}", [
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error("[HistoricoAlunoController] POST {$endpoint} falhou: " . $e->getMessage());
            return null;
        }
    }

    // ─── NORMALIZE ──────────────────────────────────────────────────────────

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

        $sala->qtd_alunos = $sala->maxAlunos  ?? 0;
        $sala->materia    = $sala->materia     ?? '—';
        $sala->status     = $sala->status      ?? 'pending';
        $sala->descricao  = $sala->descricao   ?? null;
        $sala->titulo     = $sala->titulo      ?? 'Sem título';
        $sala->idMateria  = $sala->idMateria   ?? null;
        $sala->idConteudo = $sala->idConteudo  ?? null;
        $sala->idSimulado = $sala->idSimulado  ?? null;
        $sala->idProfessor= $sala->idProfessor ?? null;

        return $sala;
    }

    // ─── INDEX ──────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $idAluno = Auth::id();
        $page    = (int) $request->get('page', 1);
        $perPage = 10;

        // Endpoint correto: retorna aulas concluídas com salaAula aninhada
        $data     = $this->apiGet("AlunoSala/RetornaAulasConcluidasIdAluno/{$idAluno}");
        $materias = $this->apiGet('Materia/ListarMaterias') ?? [];

        if (is_null($data)) {
            session()->flash('error', 'Não foi possível carregar o histórico. Tente novamente.');

            $aulas = new LengthAwarePaginator(
                collect(), 0, $perPage, $page,
                ['path' => $request->url()]
            );

            return view('aluno.historico.index', compact('aulas'));
        }

        $materiasMap = collect($materias)->keyBy('idMateria');

        $items = collect($data)->map(function ($item) use ($materiasMap) {
            // A API retorna { idAlunoSala, salaAula: { ... } }
            $salaData = $item['salaAula'] ?? $item;
            $sala = $this->normalizeSala($salaData);

            // Meta da relação aluno↔sala
            $sala->joinedAt = !empty($item['joinedAt'])
                ? Carbon::parse($item['joinedAt'])
                : null;
            $sala->leftAt = !empty($item['leftAt'])
                ? Carbon::parse($item['leftAt'])
                : null;

            // Resolve nome da matéria
            if ($sala->idMateria && $materiasMap->has($sala->idMateria)) {
                $sala->materia = $materiasMap->get($sala->idMateria)['nomeMateria'] ?? '—';
            }

            return $sala;
        });

        $aulas = new LengthAwarePaginator(
            $items->forPage($page, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            ['path' => $request->url()]
        );

        return view('aluno.historico.index', compact('aulas'));
    }

    // ─── SHOW ───────────────────────────────────────────────────────────────

    public function show(int $id)
    {
        $data = $this->apiGet("SalaAula/RetornaSalaAulaPorId/{$id}");

        if (is_null($data)) {
            return redirect()->route('aluno.historico')
                ->with('error', 'Registro não encontrado.');
        }

        $sala = $this->normalizeSala($data);

        // Resolve matéria
        if ($sala->idMateria) {
            $materias      = $this->apiGet('Materia/ListarMaterias') ?? [];
            $materia       = collect($materias)->firstWhere('idMateria', $sala->idMateria);
            $sala->materia = $materia['nomeMateria'] ?? '—';
        }

        // Resolve professor
        $professor = null;
        if ($sala->idProfessor) {
            $professor = $this->apiGet("Professor/RetornaProfessorPorId/{$sala->idProfessor}");
            if (is_null($professor)) {
                Log::warning("[HistoricoAlunoController] Professor {$sala->idProfessor} não encontrado.");
            }
        }

        // Resolve conteúdo
        $conteudo = null;
        if ($sala->idConteudo) {
            $conteudo = $this->apiGet("Conteudo/RetornaConteudoPorId/{$sala->idConteudo}");
            if (is_null($conteudo)) {
                Log::warning("[HistoricoAlunoController] Conteúdo {$sala->idConteudo} não encontrado.");
            }
        }

        // Resolve simulado (apenas metadados; respostas ficam na view do simulado)
        $simulado = null;
        if ($sala->idSimulado) {
            $simulado = $this->apiGet("Simulado/RetornaSimuladoPorId/{$sala->idSimulado}");
            if (is_null($simulado)) {
                Log::warning("[HistoricoAlunoController] Simulado {$sala->idSimulado} não encontrado.");
            }
        }

        return view('aluno.historico.show', compact('sala', 'professor', 'conteudo', 'simulado'));
    }

    // ─── SIMULADO ───────────────────────────────────────────────────────────

    /**
     * Exibe o simulado para o aluno responder.
     * Pode ser acessado múltiplas vezes (retry livre).
     */
    public function simulado(int $salaId)
    {
        $salaData = $this->apiGet("SalaAula/RetornaSalaAulaPorId/{$salaId}");

        if (is_null($salaData) || empty($salaData['idSimulado'])) {
            return redirect()->route('aluno.historico.show', $salaId)
                ->with('error', 'Esta sala não possui simulado disponível.');
        }

        $simulado = $this->apiGet("Simulado/RetornaSimuladoPorId/{$salaData['idSimulado']}");

        if (is_null($simulado)) {
            return redirect()->route('aluno.historico.show', $salaId)
                ->with('error', 'Não foi possível carregar o simulado.');
        }

        $sala = $this->normalizeSala($salaData);

        return view('aluno.simulado.show', compact('simulado', 'sala', 'salaId'));
    }

    /**
     * Processa as respostas do aluno e retorna a correção.
     * O aluno pode repetir o simulado quantas vezes quiser.
     */
    public function simuladoSubmit(Request $request, int $salaId)
    {
        $salaData = $this->apiGet("SalaAula/RetornaSalaAulaPorId/{$salaId}");

        if (is_null($salaData) || empty($salaData['idSimulado'])) {
            return redirect()->route('aluno.historico.show', $salaId)
                ->with('error', 'Simulado não disponível.');
        }

        $simulado = $this->apiGet("Simulado/RetornaSimuladoPorId/{$salaData['idSimulado']}");

        if (is_null($simulado)) {
            return redirect()->route('aluno.historico.show', $salaId)
                ->with('error', 'Não foi possível carregar o simulado.');
        }

        $sala      = $this->normalizeSala($salaData);
        $respostas = $request->input('respostas', []); // ['0' => '2', '1' => '1', ...]
        $questoes  = $simulado['simuladoQuestao'] ?? [];

        // Correção
        $total    = count($questoes);
        $acertos  = 0;
        $resultado = [];

        foreach ($questoes as $i => $q) {
            $correta    = (int) ($q['questaoCorreta'] ?? 0);
            $respondida = isset($respostas[$i]) ? (int) $respostas[$i] : null;
            $acertou    = ($respondida !== null && $respondida === $correta);

            if ($acertou) {
                $acertos++;
            }

            $resultado[] = [
                'questao'    => $q,
                'respondida' => $respondida,
                'correta'    => $correta,
                'acertou'    => $acertou,
            ];
        }

        $percentual = $total > 0 ? round(($acertos / $total) * 100) : 0;

        return view('aluno.simulado.show', compact(
            'simulado', 'sala', 'salaId', 'resultado', 'acertos', 'total', 'percentual'
        ));
    }
}