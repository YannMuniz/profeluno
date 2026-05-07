<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
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

            Log::warning("[SalaAulaAlunoController] GET {$endpoint} retornou {$response->status()}", [
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error("[SalaAulaAlunoController] GET {$endpoint} falhou: " . $e->getMessage());
            return null;
        }
    }

    public function DashboardProfessor()
    {
        $dashboard = $this->apiGet(
            "Dashboard/Professor/" . Auth::id()
        );

        $classrooms = collect($dashboard['ultimasSalas'] ?? []);

        return view('professor.dashboard', [
            'totalAlunos'      => $dashboard['totalAlunos'] ?? 0,
            'totalClasses'     => $dashboard['totalAulas'] ?? 0,
            'activeClasses'    => $dashboard['aulasAtivas'] ?? 0,
            'completedClasses' => $dashboard['aulasConcluidas'] ?? 0,
            'totalConteudos'   => $dashboard['conteudosCriados'] ?? 0,
            'totalSimulados'   => $dashboard['simuladosCriados'] ?? 0,
            'classrooms'       => $classrooms,
        ]);
    }
        
    public function DashboardAluno()
    {
        $dashboard = $this->apiGet(
            "Dashboard/Aluno/" . Auth::id()
        );

        return view('aluno.dashboard', [
            'totalClasses' => $dashboard['aulasDisponiveis'] ?? 0,
            'completedClasses' => $dashboard['aulasConcluidas'] ?? 0,
            'classrooms' => collect($dashboard['ultimasAulas'] ?? []),
        ]);
    }

    public function DashboardAdmin()
    {
        return view('admin.dashboard');
    }
}
