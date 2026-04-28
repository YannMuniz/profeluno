<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MateriaController extends Controller
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

            Log::warning("[MateriaController] GET {$endpoint} retornou {$response->status()}", [
                'body' => $response->body(),
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error("[MateriaController] GET {$endpoint} falhou: " . $e->getMessage());
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

            Log::warning("[MateriaController] POST {$endpoint} retornou {$response->status()}", [
                'body' => $response->body(),
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error("[MateriaController] POST {$endpoint} falhou: " . $e->getMessage());
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

            Log::warning("[MateriaController] PUT {$endpoint} retornou {$response->status()}", [
                'body' => $response->body(),
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error("[MateriaController] PUT {$endpoint} falhou: " . $e->getMessage());
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

            Log::warning("[MateriaController] DELETE {$endpoint} retornou {$response->status()}", [
                'body' => $response->body(),
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error("[MateriaController] DELETE {$endpoint} falhou: " . $e->getMessage());
            return false;
        }
    }

    public function index()
    {
        $materias = collect($this->apiGet('Materia/ListarMaterias') ?? []);
        $title    = '<i class="fas fa-book"></i> Matérias';
        $subtitle = 'Gerencie as disciplinas disponíveis';

        return view('admin.materias.index', compact('materias', 'title', 'subtitle'));
    }

    public function create()
    {
        $title    = '<i class="fas fa-plus"></i> Nova Matéria';
        $subtitle = 'Cadastre uma nova matéria para o sistema';

        return view('admin.materias.create', compact('title', 'subtitle'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome_materia' => 'required|string|max:255',
        ]);

        $situacao = $request->input('situacao_materia', 0);
        
        try {
            $response = $this->apiPost('Materia/CadastrarMateria', [
                'nomeMateria'    => $request->input('nome_materia'),
                'situacaoMateria' => $situacao,
            ]);

            if (! is_null($response)) {
                return redirect()
                    ->route('admin.materias.index')
                    ->with('success', 'Matéria cadastrada com sucesso!');
            }

            return back()
                ->withErrors(['nome_materia' => 'Erro ao cadastrar matéria na API. Verifique se o nome já existe.'])
                ->withInput();

        } catch (\Throwable $e) {
            Log::error('MateriaController::store erro', ['exception' => $e]);

            return back()
                ->withErrors(['nome_materia' => 'Ocorreu um erro inesperado. Tente novamente.'])
                ->withInput();
        }
    }

    public function edit(string $id)
    {
        $materia = null;

        try {
            $response = $this->apiGet("Materia/BuscarMateriaPorId/{$id}");

            if (! is_null($response)) {
                $materia = (object) $response;
            } else {
                Log::warning('MateriaController::edit matéria não encontrada', [
                    'id' => $id,
                ]);

                return redirect()
                    ->route('admin.materias.index')
                    ->with('error', 'Matéria não encontrada.');
            }
        } catch (\Throwable $e) {
            Log::error('MateriaController::edit erro', ['exception' => $e]);

            return redirect()
                ->route('admin.materias.index')
                ->with('error', 'Erro ao buscar dados da matéria.');
        }

        $title    = '<i class="fas fa-pen"></i> Editar Matéria';
        $subtitle = 'Atualize os dados desta matéria';

        return view('admin.materias.edit', compact('materia', 'title', 'subtitle'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'nome_materia' => 'required|string|max:255',
        ]);

        $situacao = $request->has('situacao_materia') ? 1 : 0;

        try {
            $response = $this->apiPut('Materia/AtualizarMateria', [
                'idMateria'       => (int) $id,
                'nomeMateria'     => $request->input('nome_materia'),
                'situacaoMateria' => $situacao,
            ]);

            if (! is_null($response)) {
                return redirect()
                    ->route('admin.materias.index')
                    ->with('success', 'Matéria atualizada com sucesso!');
            }

            return back()
                ->withErrors(['nome_materia' => 'Erro ao atualizar matéria na API. Tente novamente.'])
                ->withInput();

        } catch (\Throwable $e) {
            Log::error('MateriaController::update erro', ['exception' => $e]);

            return back()
                ->withErrors(['nome_materia' => 'Ocorreu um erro inesperado. Tente novamente.'])
                ->withInput();
        }
    }

    public function destroy(string $id)
    {
        try {
            $ok = $this->apiDelete("Materia/DeletarMateria/{$id}");

            if ($ok) {
                return redirect()
                    ->route('admin.materias.index')
                    ->with('success', 'Matéria excluída com sucesso!');
            }

            return redirect()
                ->route('admin.materias.index')
                ->with('error', 'Erro ao excluir matéria na API. Tente novamente.');

        } catch (\Throwable $e) {
            Log::error('MateriaController::destroy erro', ['exception' => $e]);

            return redirect()
                ->route('admin.materias.index')
                ->with('error', 'Ocorreu um erro inesperado ao excluir.');
        }
    }

    public function toggle(string $id)
    {
        try {
            $materia = $this->apiGet("Materia/BuscarMateriaPorId/{$id}");

            if (is_null($materia)) {
                return redirect()
                    ->route('admin.materias.index')
                    ->with('error', 'Matéria não encontrada.');
            }

            $novaSituacao = isset($materia['situacaoMateria']) ? ($materia['situacaoMateria'] ? 0 : 1) : 0;

            $responsePatch = $this->apiPut('Materia/AtualizarMateria', [
                'idMateria'       => (int) $id,
                'nomeMateria'     => $materia['nomeMateria'] ?? $materia['nome_materia'],
                'situacaoMateria' => $novaSituacao,
            ]);

            if (! is_null($responsePatch)) {
                $msg = $novaSituacao ? 'Matéria ativada com sucesso!' : 'Matéria desativada com sucesso!';
                return redirect()->route('admin.materias.index')->with('success', $msg);
            }

            return redirect()
                ->route('admin.materias.index')
                ->with('error', 'Erro ao alterar situação da matéria.');

        } catch (\Throwable $e) {
            Log::error('MateriaController::toggle erro', ['exception' => $e]);

            return redirect()
                ->route('admin.materias.index')
                ->with('error', 'Ocorreu um erro inesperado.');
        }
    }
}