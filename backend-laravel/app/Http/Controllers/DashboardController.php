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

    private function apiGet(string $endpoint): mixed
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
        return view('professor.dashboard', [
            'totalAulas'           => $this->apiGet("DashboardProfessor/TotalAulas/" . Auth::id()) ?? 0,
            'totalAulasAtivas'       => $this->apiGet("DashboardProfessor/AulasAtivas/" . Auth::id()) ?? 0,
            'AulasPendentes'    => $this->apiGet("DashboardProfessor/AulasPendentes/" . Auth::id()) ?? 0,
            'AulasCompletas'        => $this->apiGet("DashboardProfessor/AulasConcluidas/" . Auth::id()) ?? 0,
            'totalConteudos'         => $this->apiGet("DashboardProfessor/ConteudosCriados/" . Auth::id()) ?? 0,
            'totalSimulados'         => $this->apiGet("DashboardProfessor/SimuladosCriados/" . Auth::id()) ?? 0,
            'classrooms'             => collect($this->apiGet("DashboardProfessor/UltimasAulas/" . Auth::id()) ?? []),
        ]);
    }
        
    public function DashboardAluno()
    {
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
