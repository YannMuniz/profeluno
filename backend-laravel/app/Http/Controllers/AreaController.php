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

    public function index()
    {
        $areas = collect();

        try {
            $response = Http::get("{$this->baseUrl}/v1/Area/ListarAreas");

            if ($response->successful()) {
                $areas = collect($response->json());
            } else {
                Log::warning('AreaController::index falha na API', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('AreaController::index erro', ['exception' => $e]);
        }

        return view('admin.area.index', compact('areas'));
    }

    public function create()
    {
        return view('admin.area.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome_area' => 'required|string|max:255',
        ]);

        $situacao = $request->input('situacao_area', 0);
        
        try {
            $response = Http::post("{$this->baseUrl}/v1/Area/CadastrarArea", [
                'nomeArea'    => $request->input('nome_area'),
                'situacaoArea' => $situacao,
            ]);

            Log::debug('AreaController::store dotnet response', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            if ($response->successful()) {
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
            $response = Http::get("{$this->baseUrl}/v1/Area/BuscarAreaPorId/{$id}");

            if ($response->successful()) {
                $area = (object) $response->json();
            } else {
                Log::warning('AreaController::edit área não encontrada', [
                    'id'     => $id,
                    'status' => $response->status(),
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

        return view('admin.area.edit', compact('area'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'nome_area' => 'required|string|max:255',
        ]);

        $situacao = $request->has('situacao_area') ? 1 : 0;

        try {
            $response = Http::put("{$this->baseUrl}/v1/Area/AtualizarArea", [
                'idArea'       => (int) $id,
                'nomeArea'     => $request->input('nome_area'),
                'situacaoArea' => $situacao,
            ]);

            Log::debug('AreaController::update dotnet response', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            if ($response->successful()) {
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
            $response = Http::delete("{$this->baseUrl}/v1/Area/DeletarArea/{$id}");

            Log::debug('AreaController::destroy dotnet response', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            if ($response->successful()) {
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
            // Primeiro busca o estado atual
            $responseGet = Http::get("{$this->baseUrl}/v1/Area/BuscarAreaPorId/{$id}");

            if (!$responseGet->successful()) {
                return redirect()
                    ->route('admin.areas.index')
                    ->with('error', 'Área não encontrada.');
            }

            $area      = $responseGet->json();
            $novaSituacao = isset($area['situacaoArea']) ? ($area['situacaoArea'] ? 0 : 1) : 0;

            $responsePatch = Http::put("{$this->baseUrl}/v1/Area/AtualizarArea", [
                'idArea'       => (int) $id,
                'nomeArea'     => $area['nomeArea'] ?? $area['nome_area'],
                'situacaoArea' => $novaSituacao,
            ]);

            if ($responsePatch->successful()) {
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