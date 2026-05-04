<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AreaController extends Controller
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

            Log::warning("[AreaController] GET {$endpoint} retornou {$response->status()}", [
                'body' => $response->body(),
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error("[AreaController] GET {$endpoint} falhou: " . $e->getMessage());
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

            Log::warning("[AreaController] POST {$endpoint} retornou {$response->status()}", [
                'body' => $response->body(),
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error("[AreaController] POST {$endpoint} falhou: " . $e->getMessage());
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

            Log::warning("[AreaController] PUT {$endpoint} retornou {$response->status()}", [
                'body' => $response->body(),
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error("[AreaController] PUT {$endpoint} falhou: " . $e->getMessage());
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

            Log::warning("[AreaController] DELETE {$endpoint} retornou {$response->status()}", [
                'body' => $response->body(),
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error("[AreaController] DELETE {$endpoint} falhou: " . $e->getMessage());
            return false;
        }
    }

    public function index()
    {
        $areas = collect($this->apiGet('Area/RetornaTodasAreas') ?? []);
        $title    = '<i class="fas fa-layer-group"></i> Áreas';
        $subtitle = 'Gerencie as áreas disponíveis no sistema';

        return view('admin.area.index', compact('areas', 'title', 'subtitle'));
    }

    public function create()
    {
        $title    = '<i class="fas fa-plus"></i> Nova Área';
        $subtitle = 'Cadastre uma nova área para uso no sistema';

        return view('admin.area.create', compact('title', 'subtitle'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome_area' => 'required|string|max:255',
        ]);

        $situacao = $request->input('situacao_area', 0);
        
        try {
            $response = $this->apiPost('Area/CadastrarArea', [
                'nomeArea'    => $request->input('nome_area'),
                'situacaoArea' => $situacao,
            ]);

            if (! is_null($response)) {
                return redirect()
                    ->route('admin.areas.index')
                    ->with('success', 'Área cadastrada com sucesso!');
            }

            return back()
                ->withErrors(['nome_area' => 'Erro ao cadastrar área na API. Verifique se o nome já existe.'])
                ->withInput();

        } catch (\Throwable $e) {
            Log::error('AreaController::store erro', ['exception' => $e]);

            return back()
                ->withErrors(['nome_area' => 'Ocorreu um erro inesperado. Tente novamente.'])
                ->withInput();
        }
    }

    public function edit(string $id)
    {
        $area = null;

        try {
            $response = $this->apiGet("Area/RetornaAreaPorId/{$id}");

            if (! is_null($response)) {
                $area = (object) $response;
            } else {
                Log::warning('AreaController::edit área não encontrada', [
                    'id' => $id,
                ]);

                return redirect()
                    ->route('admin.areas.index')
                    ->with('error', 'Área não encontrada.');
            }
        } catch (\Throwable $e) {
            Log::error('AreaController::edit erro', ['exception' => $e]);

            return redirect()
                ->route('admin.areas.index')
                ->with('error', 'Erro ao buscar dados da área.');
        }

        $title    = '<i class="fas fa-pen"></i> Editar Área';
        $subtitle = 'Atualize os dados desta área';

        return view('admin.area.edit', compact('area', 'title', 'subtitle'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'nome_area' => 'required|string|max:255',
        ]);

        $situacao = $request->has('situacao_area') ? 1 : 0;

        try {
            $response = $this->apiPut('Area/AtualizarArea', [
                'idArea'       => (int) $id,
                'nomeArea'     => $request->input('nome_area'),
                'situacaoArea' => $situacao,
            ]);

            if (! is_null($response)) {
                return redirect()
                    ->route('admin.areas.index')
                    ->with('success', 'Área atualizada com sucesso!');
            }

            return back()
                ->withErrors(['nome_area' => 'Erro ao atualizar área na API. Tente novamente.'])
                ->withInput();

        } catch (\Throwable $e) {
            Log::error('AreaController::update erro', ['exception' => $e]);

            return back()
                ->withErrors(['nome_area' => 'Ocorreu um erro inesperado. Tente novamente.'])
                ->withInput();
        }
    }

    public function destroy(string $id)
    {
        try {
            $ok = $this->apiDelete("Area/DeletarArea/{$id}");

            if ($ok) {
                return redirect()
                    ->route('admin.areas.index')
                    ->with('success', 'Área excluída com sucesso!');
            }

            return redirect()
                ->route('admin.areas.index')
                    ->with('error', 'Erro ao excluir área na API. Tente novamente.');
        } catch (\Throwable $e) {
            Log::error('AreaController::destroy erro', ['exception' => $e]);

            return redirect()
                ->route('admin.areas.index')
                ->with('error', 'Ocorreu um erro inesperado ao excluir.');
        }
    }

    public function toggle(string $id)
    {
        try {
            $area = $this->apiGet("Area/BuscarAreaPorId/{$id}");

            if (is_null($area)) {
                return redirect()
                    ->route('admin.areas.index')
                    ->with('error', 'Área não encontrada.');
            }

            $novaSituacao = isset($area['situacaoArea']) ? ($area['situacaoArea'] ? 0 : 1) : 0;

            $responsePatch = $this->apiPut('Area/AtualizarArea', [
                'idArea'       => (int) $id,
                'nomeArea'     => $area['nomeArea'] ?? $area['nome_area'],
                'situacaoArea' => $novaSituacao,
            ]);

            if (! is_null($responsePatch)) {
                $msg = $novaSituacao ? 'Área ativada com sucesso!' : 'Área desativada com sucesso!';
                return redirect()->route('admin.areas.index')->with('success', $msg);
            }

            return redirect()
                ->route('admin.areas.index')
                ->with('error', 'Erro ao alterar situação da área.');

        } catch (\Throwable $e) {
            Log::error('AreaController::toggle erro', ['exception' => $e]);

            return redirect()
                ->route('admin.areas.index')
                ->with('error', 'Ocorreu um erro inesperado.');
        }
    }
}