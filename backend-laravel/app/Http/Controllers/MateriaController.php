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

    public function index()
    {
        $materias = collect();

        try {
            $response = Http::get("{$this->baseUrl}/v1/Materia/ListarMaterias");
            if ($response->successful()) {
                $materias = collect($response->json());
            } else {
                Log::warning('MateriaController::index falha na API', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('MateriaController::index erro', ['exception' => $e]);
        }

        return view('admin.materias.index', compact('materias'));
    }

    public function create()
    {
        return view('admin.materias.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome_materia' => 'required|string|max:255',
        ]);

        $situacao = $request->has('situacao_materia') ? 1 : 0;

        try {
            $response = Http::post("{$this->baseUrl}/v1/Materia/CadastrarMateria", [
                'nomeMateria'    => $request->input('nome_materia'),
                'situacaoMateria' => $situacao,
            ]);

            Log::debug('MateriaController::store dotnet response', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            if ($response->successful()) {
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
            $response = Http::get("{$this->baseUrl}/v1/Materia/BuscarMateriaPorId/{$id}");
            // dd($response->json());

            if ($response->successful()) {
                $materia = (object) $response->json();
            } else {
                Log::warning('MateriaController::edit matéria não encontrada', [
                    'id'     => $id,
                    'status' => $response->status(),
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

        return view('admin.materias.edit', compact('materia'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'nome_materia' => 'required|string|max:255',
        ]);

        $situacao = $request->has('situacao_materia') ? 1 : 0;

        try {
            $response = Http::put("{$this->baseUrl}/v1/Materia/AtualizarMateria/{$id}", [
                'id'             => $id,
                'nomeMateria'    => $request->input('nome_materia'),
                'situacaoMateria' => $situacao,
            ]);

            Log::debug('MateriaController::update dotnet response', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            if ($response->successful()) {
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
            $response = Http::delete("{$this->baseUrl}/v1/Materia/DeletarMateria/{$id}");

            Log::debug('MateriaController::destroy dotnet response', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            if ($response->successful()) {
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
            // Primeiro busca o estado atual
            $responseGet = Http::get("{$this->baseUrl}/v1/Materia/BuscarMateriaPorId/{$id}");

            if (!$responseGet->successful()) {
                return redirect()
                    ->route('admin.materias.index')
                    ->with('error', 'Matéria não encontrada.');
            }

            $materia      = $responseGet->json();
            $novaSituacao = isset($materia['situacaoMateria']) ? ($materia['situacaoMateria'] ? 0 : 1) : 0;

            $responsePatch = Http::put("{$this->baseUrl}/v1/Materia/AtualizarMateria/{$id}", [
                'id'              => $id,
                'nomeMateria'     => $materia['nomeMateria'] ?? $materia['nome_materia'],
                'situacaoMateria' => $novaSituacao,
            ]);

            if ($responsePatch->successful()) {
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