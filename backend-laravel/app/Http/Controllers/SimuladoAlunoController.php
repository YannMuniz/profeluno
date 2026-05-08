<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class SimuladoAlunoController extends Controller
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

            Log::warning("[SimuladoAlunoController] GET {$endpoint} retornou {$response->status()}", [
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error("[SimuladoAlunoController] GET {$endpoint} falhou: " . $e->getMessage());
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

            Log::warning("[SimuladoAlunoController] POST {$endpoint} retornou {$response->status()}", [
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error("[SimuladoAlunoController] POST {$endpoint} falhou: " . $e->getMessage());
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

            Log::warning("[SimuladoAlunoController] DELETE {$endpoint} retornou {$response->status()}", [
                'body' => $response->body(),
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error("[SimuladoAlunoController] DELETE {$endpoint} falhou: " . $e->getMessage());
            return false;
        }
    }

    // ─── SIMULADOS ──────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $idAluno = Auth::id();
        $page    = (int) $request->get('page', 1);
        $perPage = 10;

        $data      = $this->apiGet("Simulado/RetornaSimuladosPorUsuario/{$idAluno}");
        $simulados = [];

        if (!is_null($data)) {
            $simulados = is_array($data) && isset($data[0])
                ? $data
                : (isset($data['idSimulado']) ? [$data] : []);
        } else {
            session()->flash('error', 'Não foi possível carregar os simulados. Tente novamente.');
        }

        $items = collect($simulados);

        $simulados = new LengthAwarePaginator(
            $items->forPage($page, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            ['path' => $request->url()]
        );

        return view('aluno.simulados.index', compact('simulados'));
    }

    // ─── SIMULADO SHOW ──────────────────────────────────────────────────────

    public function show(int $id)
    {
        $data = $this->apiGet("Simulado/RetornaSimuladoPorId/{$id}");

        if (is_null($data)) {
            return redirect()->route('aluno.simulados')
                ->with('error', 'Simulado não encontrado.');
        }

        $simulado = (object) $data;

        if (!empty($data['idMateria'])) {
            $materias          = $this->apiGet('Materia/ListarMaterias') ?? [];
            $materia           = collect($materias)->firstWhere('idMateria', $data['idMateria']);
            $simulado->materia = $materia['nomeMateria'] ?? '—';
        }

        return view('aluno.simulados.show', compact('simulado'));
    }
}