<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CargoController extends Controller
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

            Log::warning("[CargoController] GET {$endpoint} retornou {$response->status()}", [
                'body' => $response->body(),
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error("[CargoController] GET {$endpoint} falhou: " . $e->getMessage());
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

            Log::warning("[CargoController] POST {$endpoint} retornou {$response->status()}", [
                'body' => $response->body(),
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error("[CargoController] POST {$endpoint} falhou: " . $e->getMessage());
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

            Log::warning("[CargoController] PUT {$endpoint} retornou {$response->status()}", [
                'body' => $response->body(),
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error("[CargoController] PUT {$endpoint} falhou: " . $e->getMessage());
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

            Log::warning("[CargoController] DELETE {$endpoint} retornou {$response->status()}", [
                'body' => $response->body(),
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error("[CargoController] DELETE {$endpoint} falhou: " . $e->getMessage());
            return false;
        }
    }

    public function index()
    {
        $cargos = collect($this->apiGet('Cargo/ListarCargos') ?? []);
        $title    = '<i class="fas fa-briefcase"></i> Cargos';
        $subtitle = 'Gerencie os cargos do sistema';

        return view('admin.cargos.index', compact('cargos', 'title', 'subtitle'));
    }

    public function create()
    {
        $title    = '<i class="fas fa-plus"></i> Novo Cargo';
        $subtitle = 'Cadastre um novo cargo para o sistema';

        return view('admin.cargos.create', compact('title', 'subtitle'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome_cargo' => 'required|string|max:255',
        ]);

        try {
            $response = $this->apiPost('Cargo/CadastrarCargo', [
                'nome'    => $request->input('nome_cargo'),
            ]);

            if (! is_null($response)) {
                return redirect()
                    ->route('admin.cargos.index')
                    ->with('success', 'Cargo cadastrado com sucesso!');
            }

            return back()
                ->withErrors(['nome_cargo' => 'Erro ao cadastrar cargo na API. Verifique se o nome já existe.'])
                ->withInput();

        } catch (\Throwable $e) {
            Log::error('CargoController::store erro', ['exception' => $e]);

            return back()
                ->withErrors(['nome_cargo' => 'Ocorreu um erro inesperado. Tente novamente.'])
                ->withInput();
        }
    }

    public function edit(string $id)
    {
        $cargo = null;

        try {
            $response = $this->apiGet("Cargo/RetornaCargoPorId/{$id}");

            if (! is_null($response)) {
                $cargo = (object) $response;
            } else {
                Log::warning('CargoController::edit cargo não encontrado', [
                    'id' => $id,
                ]);

                return redirect()
                    ->route('admin.cargos.index')
                    ->with('error', 'Cargo não encontrado.');
            }
        } catch (\Throwable $e) {
            Log::error('CargoController::edit erro', ['exception' => $e]);

            return redirect()
                ->route('admin.cargos.index')
                ->with('error', 'Erro ao buscar dados do cargo.');
        }

        $title    = '<i class="fas fa-pen"></i> Editar Cargo';
        $subtitle = 'Atualize os dados deste cargo';

        return view('admin.cargos.edit', compact('cargo', 'title', 'subtitle'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'nome_cargo' => 'required|string|max:255',
        ]);

        $situacao = $request->has('situacao_cargo') ? 1 : 0;

        try {
            $response = $this->apiPut('Cargo/AtualizarCargo', [
                'idCargo'       => (int) $id,
                'nomeCargo'     => $request->input('nome_cargo'),
                'situacaoCargo' => $situacao,
            ]);

            if (! is_null($response)) {
                return redirect()
                    ->route('admin.cargos.index')
                    ->with('success', 'Cargo atualizado com sucesso!');
            }

            return back()
                ->withErrors(['nome_cargo' => 'Erro ao atualizar cargo na API. Tente novamente.'])
                ->withInput();

        } catch (\Throwable $e) {
            Log::error('CargoController::update erro', ['exception' => $e]);

            return back()
                ->withErrors(['nome_cargo' => 'Ocorreu um erro inesperado. Tente novamente.'])
                ->withInput();
        }
    }

    public function destroy(string $id)
    {
        try {
            $ok = $this->apiDelete("Cargo/DeletarCargo/{$id}");

            if ($ok) {
                return redirect()
                    ->route('admin.cargos.index')
                    ->with('success', 'Cargo excluído com sucesso!');
            }

            return redirect()
                ->route('admin.cargos.index')
                ->with('error', 'Erro ao excluir cargo na API. Tente novamente.');

        } catch (\Throwable $e) {
            Log::error('CargoController::destroy erro', ['exception' => $e]);

            return redirect()
                ->route('admin.cargos.index')
                ->with('error', 'Ocorreu um erro inesperado ao excluir.');
        }
    }

    // public function toggle(string $id)
    // {
    //     try {
    //         // Primeiro busca o estado atual
    //         $responseGet = Http::get("{$this->baseUrl}/v1/Cargo/BuscarCargoPorId/{$id}");

    //         if (!$responseGet->successful()) {
    //             return redirect()
    //                 ->route('admin.cargos.index')
    //                 ->with('error', 'Cargo não encontrado.');
    //         }

    //         $cargo      = $responseGet->json();

    //         $responsePatch = Http::put("{$this->baseUrl}/v1/Cargo/AtualizarCargo", [
    //             'idCargo'       => (int) $id,
    //             'nomeCargo'     => $cargo['nomeCargo'] ?? $cargo['nome_cargo'],
    //         ]);

    //         if ($responsePatch->successful()) {
    //             $msg = $novaSituacao ? 'Cargo ativado com sucesso!' : 'Cargo desativado com sucesso!';
    //             return redirect()->route('admin.cargos.index')->with('success', $msg);
    //         }

    //         return redirect()
    //             ->route('admin.cargos.index')
    //             ->with('error', 'Erro ao alterar situação do cargo.');

    //     } catch (\Throwable $e) {
    //         Log::error('CargoController::toggle erro', ['exception' => $e]);

    //         return redirect()
    //             ->route('admin.materias.index')
    //             ->with('error', 'Ocorreu um erro inesperado.');
    //     }
    // }
}