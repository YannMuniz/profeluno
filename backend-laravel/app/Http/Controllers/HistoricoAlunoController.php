<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
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

    private function apiDelete(string $endpoint): bool
    {
        try {
            $response = Http::withHeaders($this->authHeaders())
                ->timeout(15)
                ->delete("{$this->baseUrl}/v1/{$endpoint}");

            if ($response->successful()) {
                return true;
            }

            Log::warning("[HistoricoAlunoController] DELETE {$endpoint} retornou {$response->status()}", [
                'body' => $response->body(),
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error("[HistoricoAlunoController] DELETE {$endpoint} falhou: " . $e->getMessage());
            return false;
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

        $sala->qtd_alunos = $sala->maxAlunos ?? 0;
        $sala->materia    = $sala->materia   ?? '—';
        $sala->status     = $sala->status    ?? 'pending';
        $sala->descricao  = $sala->descricao ?? null;
        $sala->titulo     = $sala->titulo    ?? 'Sem título';

        return $sala;
    }

    // ─── HISTÓRICO ──────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $idAluno = Auth::id();
        $page    = (int) $request->get('page', 1);
        $perPage = 10;

        $data     = $this->apiGet("AlunoSala/RetornarAlunoSalaPorIdAluno/{$idAluno}");
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
            $sala = $this->normalizeSala($item);
            if (isset($sala->idMateria) && $materiasMap->has($sala->idMateria)) {
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

    // ─── HISTÓRICO SHOW ─────────────────────────────────────────────────────

    public function show(int $id)
    {
        $data = $this->apiGet("SalaAula/RetornaSalaAulaPorId/{$id}");

        if (is_null($data)) {
            return redirect()->route('aluno.historico')
                ->with('error', 'Registro não encontrado.');
        }

        $sala = $this->normalizeSala($data);

        if (!empty($data['idMateria'])) {
            $materias      = $this->apiGet('Materia/ListarMaterias') ?? [];
            $materia       = collect($materias)->firstWhere('idMateria', $data['idMateria']);
            $sala->materia = $materia['nomeMateria'] ?? '—';
        }

        return view('aluno.historico.show', compact('sala'));
    }
}