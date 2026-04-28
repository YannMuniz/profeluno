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

    public function index()
    {
        $escolaridades = collect();

        try {
            $response = Http::get("{$this->baseUrl}/v1/Escolaridade/ListarEscolaridades");

            if ($response->successful()) {
                $escolaridades = collect($response->json());
            } else {
                Log::warning('EscolaridadeController::index falha na API', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('EscolaridadeController::index erro', ['exception' => $e]);
        }

        return view('admin.escolaridade.index', compact('escolaridades'));
    }

    public function create()
    {
        return view('admin.escolaridade.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome_escolaridade' => 'required|string|max:255',
        ]);

        $situacao = $request->input('situacao_escolaridade', 0);
        
        try {
            $response = Http::post("{$this->baseUrl}/v1/Escolaridade/CadastrarEscolaridade", [
                'nomeEscolaridade'    => $request->input('nome_escolaridade'),
                'situacaoEscolaridade' => $situacao,
            ]);

            Log::debug('EscolaridadeController::store dotnet response', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            if ($response->successful()) {
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
            $response = Http::get("{$this->baseUrl}/v1/Escolaridade/BuscarEscolaridadePorId/{$id}");

            if ($response->successful()) {
                $escolaridade = (object) $response->json();
            } else {
                Log::warning('EscolaridadeController::edit escolaridade não encontrada', [
                    'id'     => $id,
                    'status' => $response->status(),
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

        return view('admin.escolaridade.edit', compact('escolaridade'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'nome_escolaridade' => 'required|string|max:255',
        ]);

        $situacao = $request->has('situacao_escolaridade') ? 1 : 0;

        try {
            $response = Http::put("{$this->baseUrl}/v1/Escolaridade/AtualizarEscolaridade", [
                'idEscolaridade'       => (int) $id,
                'nomeEscolaridade'     => $request->input('nome_escolaridade'),
                'situacaoEscolaridade' => $situacao,
            ]);

            Log::debug('EscolaridadeController::update dotnet response', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            if ($response->successful()) {
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
            $response = Http::delete("{$this->baseUrl}/v1/Escolaridade/DeletarEscolaridade/{$id}");

            Log::debug('EscolaridadeController::destroy dotnet response', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            if ($response->successful()) {
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
            // Primeiro busca o estado atual
            $responseGet = Http::get("{$this->baseUrl}/v1/Escolaridade/BuscarEscolaridadePorId/{$id}");

            if (!$responseGet->successful()) {
                return redirect()
                    ->route('admin.escolaridades.index')
                    ->with('error', 'Escolaridade não encontrada.');
            }

            $escolaridade      = $responseGet->json();
            $novaSituacao = isset($escolaridade['situacaoEscolaridade']) ? ($escolaridade['situacaoEscolaridade'] ? 0 : 1) : 0;

            $responsePatch = Http::put("{$this->baseUrl}/v1/Escolaridade/AtualizarEscolaridade", [
                'idEscolaridade'       => (int) $id,
                'nomeEscolaridade'     => $escolaridade['nomeEscolaridade'] ?? $escolaridade['nome_escolaridade'],
                'situacaoEscolaridade' => $novaSituacao,
            ]);

            if ($responsePatch->successful()) {
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