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

    public function index()
    {
        $cargos = collect();

        try {
            $response = Http::get("{$this->baseUrl}/v1/Cargo/ListarCargos");

            if ($response->successful()) {
                $cargos = collect($response->json());
            } else {
                Log::warning('CargoController::index falha na API', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('CargoController::index erro', ['exception' => $e]);
        }

        return view('admin.cargos.index', compact('cargos'));
    }

    public function create()
    {
        return view('admin.cargos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome_cargo' => 'required|string|max:255',
        ]);

        try {
            $response = Http::post("{$this->baseUrl}/v1/Cargo/CadastrarCargo", [
                'nome'    => $request->input('nome_cargo'),
            ]);

            Log::debug('CargoController::store dotnet response', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            if ($response->successful()) {
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
        $cargo = null; // ← singular

        try {
            $response = Http::get("{$this->baseUrl}/v1/Cargo/RetornaCargoPorId/{$id}");

            // dd($response->json()); ← remova isto

            if ($response->successful()) {
                $cargo = (object) $response->json(); // ← singular
            } else {
                Log::warning('CargoController::edit cargo não encontrado', [
                    'id'     => $id,
                    'status' => $response->status(),
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

        return view('admin.cargos.edit', compact('cargo')); // ← singular
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'nome_cargo' => 'required|string|max:255',
        ]);

        $situacao = $request->has('situacao_cargo') ? 1 : 0;

        try {
            $response = Http::put("{$this->baseUrl}/v1/Cargo/AtualizarCargo", [
                'idCargo'       => (int) $id,
                'nomeCargo'     => $request->input('nome_cargo'),
                'situacaoCargo' => $situacao,
            ]);

            Log::debug('CargoController::update dotnet response', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            if ($response->successful()) {
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
            $response = Http::delete("{$this->baseUrl}/v1/Cargo/DeletarCargo/{$id}");

            Log::debug('CargoController::destroy dotnet response', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            if ($response->successful()) {
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