<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EscolaridadeController extends Controller
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

            Log::warning("[EscolaridadeController] GET {$endpoint} retornou {$response->status()}", [
                'body' => $response->body(),
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error("[EscolaridadeController] GET {$endpoint} falhou: " . $e->getMessage());
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

            Log::warning("[EscolaridadeController] POST {$endpoint} retornou {$response->status()}", [
                'body' => $response->body(),
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error("[EscolaridadeController] POST {$endpoint} falhou: " . $e->getMessage());
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

            Log::warning("[EscolaridadeController] PUT {$endpoint} retornou {$response->status()}", [
                'body' => $response->body(),
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error("[EscolaridadeController] PUT {$endpoint} falhou: " . $e->getMessage());
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

            Log::warning("[EscolaridadeController] DELETE {$endpoint} retornou {$response->status()}", [
                'body' => $response->body(),
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error("[EscolaridadeController] DELETE {$endpoint} falhou: " . $e->getMessage());
            return false;
        }
    }

    public function index()
    {
        $escolaridades = collect($this->apiGet('Escolaridade/ListarEscolaridades') ?? []);
        $title    = '<i class="fas fa-graduation-cap"></i> Escolaridades';
        $subtitle = 'Gerencie os níveis de escolaridade';

        return view('admin.escolaridade.index', compact('escolaridades', 'title', 'subtitle'));
    }

    public function create()
    {
        $title    = '<i class="fas fa-plus"></i> Nova Escolaridade';
        $subtitle = 'Cadastre um novo nível de escolaridade';

        return view('admin.escolaridade.create', compact('title', 'subtitle'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome_escolaridade' => 'required|string|max:255',
        ]);

        $situacao = $request->input('situacao_escolaridade', 0);
        
        try {
            $response = $this->apiPost('Escolaridade/CadastrarEscolaridade', [
                'nomeEscolaridade'    => $request->input('nome_escolaridade'),
                'situacaoEscolaridade' => $situacao,
            ]);

            if (! is_null($response)) {
                return redirect()
                    ->route('admin.escolaridades.index')
                    ->with('success', 'Escolaridade cadastrada com sucesso!');
            }

            return back()
                ->withErrors(['nome_escolaridade' => 'Erro ao cadastrar escolaridade na API. Verifique se o nome já existe.'])
                ->withInput();

        } catch (\Throwable $e) {
            Log::error('EscolaridadeController::store erro', ['exception' => $e]);

            return back()
                ->withErrors(['nome_escolaridade' => 'Ocorreu um erro inesperado. Tente novamente.'])
                ->withInput();
        }
    }

    public function edit(string $id)
    {
        $escolaridade = null;

        try {
            $response = $this->apiGet("Escolaridade/BuscarEscolaridadePorId/{$id}");

            if (! is_null($response)) {
                $escolaridade = (object) $response;
            } else {
                Log::warning('EscolaridadeController::edit escolaridade não encontrada', [
                    'id' => $id,
                ]);

                return redirect()
                    ->route('admin.escolaridades.index')
                    ->with('error', 'Escolaridade não encontrada.');
            }
        } catch (\Throwable $e) {
            Log::error('EscolaridadeController::edit erro', ['exception' => $e]);

            return redirect()
                ->route('admin.escolaridades.index')
                ->with('error', 'Erro ao buscar dados da escolaridade.');
        }

        $title    = '<i class="fas fa-pen"></i> Editar Escolaridade';
        $subtitle = 'Atualize os dados desta escolaridade';

        return view('admin.escolaridade.edit', compact('escolaridade', 'title', 'subtitle'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'nome_escolaridade' => 'required|string|max:255',
        ]);

        $situacao = $request->has('situacao_escolaridade') ? 1 : 0;

        try {
            $response = $this->apiPut('Escolaridade/AtualizarEscolaridade', [
                'idEscolaridade'       => (int) $id,
                'nomeEscolaridade'     => $request->input('nome_escolaridade'),
                'situacaoEscolaridade' => $situacao,
            ]);

            if (! is_null($response)) {
                return redirect()
                    ->route('admin.escolaridades.index')
                    ->with('success', 'Escolaridade atualizada com sucesso!');
            }

            return back()
                ->withErrors(['nome_escolaridade' => 'Erro ao atualizar escolaridade na API. Tente novamente.'])
                ->withInput();

        } catch (\Throwable $e) {
            Log::error('EscolaridadeController::update erro', ['exception' => $e]);

            return back()
                ->withErrors(['nome_escolaridade' => 'Ocorreu um erro inesperado. Tente novamente.'])
                ->withInput();
        }
    }

    public function destroy(string $id)
    {
        try {
            $ok = $this->apiDelete("Escolaridade/DeletarEscolaridade/{$id}");

            if ($ok) {
                return redirect()
                    ->route('admin.escolaridades.index')
                    ->with('success', 'Escolaridade excluída com sucesso!');
            }

            return redirect()
                ->route('admin.escolaridades.index')
                ->with('error', 'Erro ao excluir escolaridade na API. Tente novamente.');

        } catch (\Throwable $e) {
            Log::error('EscolaridadeController::destroy erro', ['exception' => $e]);

            return redirect()
                ->route('admin.escolaridades.index')
                ->with('error', 'Ocorreu um erro inesperado ao excluir.');
        }
    }

    public function toggle(string $id)
    {
        try {
            $escolaridade = $this->apiGet("Escolaridade/BuscarEscolaridadePorId/{$id}");

            if (is_null($escolaridade)) {
                return redirect()
                    ->route('admin.escolaridades.index')
                    ->with('error', 'Escolaridade não encontrada.');
            }

            $novaSituacao = isset($escolaridade['situacaoEscolaridade']) ? ($escolaridade['situacaoEscolaridade'] ? 0 : 1) : 0;

            $responsePatch = $this->apiPut('Escolaridade/AtualizarEscolaridade', [
                'idEscolaridade'       => (int) $id,
                'nomeEscolaridade'     => $escolaridade['nomeEscolaridade'] ?? $escolaridade['nome_escolaridade'],
                'situacaoEscolaridade' => $novaSituacao,
            ]);

            if (! is_null($responsePatch)) {
                $msg = $novaSituacao ? 'Escolaridade ativada com sucesso!' : 'Escolaridade desativada com sucesso!';
                return redirect()->route('admin.escolaridades.index')->with('success', $msg);
            }

            return redirect()
                ->route('admin.escolaridades.index')
                ->with('error', 'Erro ao alterar situação da escolaridade.');

        } catch (\Throwable $e) {
            Log::error('EscolaridadeController::toggle erro', ['exception' => $e]);

            return redirect()
                ->route('admin.escolaridades.index')
                ->with('error', 'Ocorreu um erro inesperado.');
        }
    }
}