<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ConteudoController extends Controller
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
    
    public function index()
    {
        $professorId = Auth::id();
        $conteudos = $this->apiGet("Conteudo/RetornaConteudoPorUsuario/{$professorId}");
        // ── Dados fictícios (substituir por chamada à API .NET futuramente) ──
        // $conteudos = [
        //     [
        //         'id'        => 1,
        //         'titulo'    => 'Apostila de Equações do 2º Grau',
        //         'descricao' => 'Material completo com exercícios resolvidos',
        //         'materia'   => 'Matemática',
        //         'tipo'      => 'pdf',
        //         'situacao'  => 1,
        //         'criado_em' => '2026-03-20T10:00:00',
        //     ],
        //     [
        //         'id'        => 2,
        //         'titulo'    => 'Slides — Leis de Newton',
        //         'descricao' => 'Apresentação com exemplos do cotidiano',
        //         'materia'   => 'Física',
        //         'tipo'      => 'slide',
        //         'situacao'  => 1,
        //         'criado_em' => '2026-03-21T14:30:00',
        //     ],
        //     [
        //         'id'        => 3,
        //         'titulo'    => 'Videoaula: Segunda Guerra Mundial',
        //         'descricao' => 'Resumo em vídeo com linha do tempo interativa',
        //         'materia'   => 'História',
        //         'tipo'      => 'video',
        //         'situacao'  => 1,
        //         'criado_em' => '2026-03-19T09:00:00',
        //     ],
        //     [
        //         'id'        => 4,
        //         'titulo'    => 'Simulado — Tabela Periódica',
        //         'descricao' => '10 questões de múltipla escolha',
        //         'materia'   => 'Química',
        //         'tipo'      => 'simulado',
        //         'situacao'  => 1,
        //         'criado_em' => '2026-03-22T16:00:00',
        //     ],
        //     [
        //         'id'        => 5,
        //         'titulo'    => 'Exercícios de Análise Sintática',
        //         'descricao' => 'Lista de exercícios para fixação',
        //         'materia'   => 'Português',
        //         'tipo'      => 'document',
        //         'situacao'  => 0,
        //         'criado_em' => '2026-03-17T11:00:00',
        //     ],
        // ];
        $conteudos = $conteudos ?? [];

        $title = '<i class="fas fa-folder-open"></i> Conteúdos';
        $subtitle = 'Gerencie os materiais e conteudos das suas salas de aula';
        return view('professor.conteudo.index', compact('conteudos', 'title', 'subtitle'));
    }

    public function create()
    {
        $materias = $this->apiGet("Materia/ListarMaterias") ?? [];

        $ultimapagina = "<a href='" . route('professor.conteudo.index') . "' class='back-link'>
            <i class='fas fa-arrow-left'></i>
            Voltar
        </a>";
        $title = '<i class="fas fa-plus"></i> Novo Conteúdo';
        $subtitle = 'Adicione um conteúdo de apoio para a sala';
        return view('professor.conteudo.create', compact('title', 'subtitle', 'ultimapagina', 'materias'));
    }

    public function store() {
        // $file = $request->file('arquivo'); // UploadedFile
        // // Conteúdo binário
        // $binaryContent = file_get_contents($file->getRealPath());
        // // Nome e tamanho (se quiser salvar em colunas extras)
        // $filename = $file->getClientOriginalName();
        // $filesize = $file->getSize();

        // Lógica para salvar o conteúdo (futuro: chamada à API .NET)
        return redirect()->route('professor.conteudo.index')->with('success', 'Conteúdo criado com sucesso!');
    }

    public function edit() {
        //
    }

    public function update() {
        //
    }
    
    public function destroy() {
        //
    }

    public function toggle() {
        //
    }

    public function download() {
        //
    }
}