<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class UserController extends Controller
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('DOTNET_API_URL', 'http://profeluno_dotnet:9000');
    }

    public function index()
    {
        $usuarios = collect();
        $cargosMap = [];

        try {
            // Busca cargos primeiro para montar o mapa id => nome
            $cargosResponse = Http::get("{$this->baseUrl}/v1/Cargo/ListarCargos");
            if ($cargosResponse->successful()) {
                $cargos = collect($cargosResponse->json());
                $cargosMap = $cargos->keyBy(function ($cargo) {
                    return $cargo['id'] ?? $cargo['Id'] ?? $cargo['idCargo'] ?? null;
                })->map(function ($cargo) {
                    return $cargo['nome'] ?? $cargo['Nome'] ?? $cargo['nomeCargo'] ?? '—';
                })->toArray();
            }

            // Busca usuários
            $response = Http::get("{$this->baseUrl}/v1/User/ListarUsuarios");
            if ($response->successful()) {
                $usuarios = collect($response->json());
            } else {
                Log::warning('UserController::index falha na API', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('UserController::index erro', ['exception' => $e]);
        }

        return view('admin.usuarios.index', compact('usuarios', 'cargosMap'));
    }

    public function create()
    {
        $cargos = collect();

        try {
            $response = Http::get("{$this->baseUrl}/v1/Cargo/ListarCargos");
            if ($response->successful()) {
                $cargos = collect($response->json());
            } else {
                Log::warning('UserController::create falha ao buscar cargos', [
                    'status' => $response->status(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('UserController::create erro ao buscar cargos', ['exception' => $e]);
        }

        return view('admin.usuarios.create', compact('cargos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome_usuario' => 'required|string|max:255',
            'email'        => 'required|email|max:255',
            'cargo_id'     => 'required|exists:cargos,id',
            'password'     => 'required|string|min:6',
            'password_confirmation' => 'required|string|same:password',
        ]);

        $password = md5($request->input('password'));

        try {
            Log::debug('UserController::store dotnet request', [
                'payload' => [
                    'nome'    => $request->input('nome_usuario'),
                    'email'   => $request->input('email'),
                    'senha'   => $password,
                    'idCargo' => $request->input('cargo_id'),
                ],
            ]);

            $response = Http::post("{$this->baseUrl}/v1/User/CadastrarUsuario", [
                'nome'    => $request->input('nome_usuario'),
                'email'   => $request->input('email'),
                'senha'   => $password,
                'idCargo' => $request->input('cargo_id'),
            ]);

            Log::debug('UserController::store dotnet response', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            if ($response->successful()) {
                User::updateOrCreate(
                    ['email' => $request->input('email')],
                    [
                        'nome_usuario' => $request->input('nome_usuario'),
                        'password'     => md5($request->input('password')),
                        'cargo_id'     => $request->input('cargo_id'),
                    ]
                );

                return redirect()
                    ->route('admin.usuarios.index')
                    ->with('success', 'Usuário cadastrado com sucesso!');
            }

            return back()
                ->withErrors(['email' => 'Erro ao cadastrar usuário na API. Tente novamente.'])
                ->withInput();

        } catch (\Throwable $e) {
            Log::error('UserController::store erro', ['exception' => $e]);

            return back()
                ->withErrors(['email' => 'Ocorreu um erro inesperado. Tente novamente.'])
                ->withInput();
        }
    }

    public function edit(string $id)
    {
        $usuario = null;
        $cargos  = collect();

        try {
            // Busca dados do usuário
            $responseUsuario = Http::get("{$this->baseUrl}/v1/User/BuscarUsuario/{$id}");
            
            if ($responseUsuario->successful()) {
                $data    = $responseUsuario->json();

                $usuario = (object) [
                    'id'          => $data['id']          ?? $data['Id']         ?? null,
                    'nome_Usuario'=> $data['nome_Usuario'] ?? $data['nome']       ?? $data['Nome'] ?? '',
                    'email'       => $data['email']        ?? $data['Email']      ?? '',
                    'idCargo'     => $data['idCargo']      ?? $data['cargo_id']   ?? null,
                    'createdAt'   => $data['createdAt']    ?? $data['criadoEm']   ?? null,
                ];
            } else {
                Log::warning('UserController::edit usuário não encontrado', [
                    'id'     => $id,
                    'status' => $responseUsuario->status(),
                ]);

                return redirect()
                    ->route('admin.usuarios.index')
                    ->with('error', 'Usuário não encontrado.');
            }

            // Busca cargos para o select
            $responseCargos = Http::get("{$this->baseUrl}/v1/Cargo/ListarCargos");

            if ($responseCargos->successful()) {
                $cargos = collect($responseCargos->json());
            }

        } catch (\Throwable $e) {
            Log::error('UserController::edit erro', ['exception' => $e]);

            return redirect()
                ->route('admin.usuarios.index')
                ->with('error', 'Erro ao buscar dados do usuário.');
        }

        return view('admin.usuarios.edit', compact('usuario', 'cargos'));
    }

    public function update(Request $request, string $id)
    {
        $rules = [
            'nome_usuario' => 'required|string|max:255',
            'email'        => 'required|email|max:255',
            'cargo_id'     => 'required|exists:cargos,id',
        ];

        if ($request->filled('password')) {
            $rules['password']              = 'string|min:6';
            $rules['password_confirmation'] = 'required|string|same:password';
        }

        $request->validate($rules);

        $payload = [
            'id'      => $id,
            'nome'    => $request->input('nome_usuario'),
            'email'   => $request->input('email'),
            'idCargo' => $request->input('cargo_id'),
        ];

        if ($request->filled('password')) {
            $payload['senha'] = md5($request->input('password'));
        }

        try {
            Log::debug('UserController::update dotnet request', [
                'id'      => $id,
                'payload' => $payload,
            ]);

            $response = Http::put("{$this->baseUrl}/v1/User/AtualizarUsuario", $payload);

            Log::debug('UserController::update dotnet response', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            if ($response->successful()) {
                $localData = [
                    'nome_usuario' => $request->input('nome_usuario'),
                    'cargo_id'     => $request->input('cargo_id'),
                ];

                if ($request->filled('password')) {
                    $localData['password'] = md5($request->input('password'));
                }

                User::where('email', $request->input('email'))->update($localData);

                return redirect()
                    ->route('admin.usuarios.index')
                    ->with('success', 'Usuário atualizado com sucesso!');
            }

            return back()
                ->withErrors(['email' => 'Erro ao atualizar usuário na API. Tente novamente.'])
                ->withInput();

        } catch (\Throwable $e) {
            Log::error('UserController::update erro', ['exception' => $e]);

            return back()
                ->withErrors(['email' => 'Ocorreu um erro inesperado. Tente novamente.'])
                ->withInput();
        }
    }

    public function destroy(string $id)
    {
        try {
            $response = Http::delete("{$this->baseUrl}/v1/User/DeletarUsuario/{$id}");

            Log::debug('UserController::destroy dotnet response', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            if ($response->successful()) {
                // Remove também do banco local
                User::find($id)?->delete();

                return redirect()
                    ->route('admin.usuarios.index')
                    ->with('success', 'Usuário excluído com sucesso!');
            }

            return redirect()
                ->route('admin.usuarios.index')
                ->with('error', 'Erro ao excluir usuário na API. Tente novamente.');

        } catch (\Throwable $e) {
            Log::error('UserController::destroy erro', ['exception' => $e]);

            return redirect()
                ->route('admin.usuarios.index')
                ->with('error', 'Ocorreu um erro inesperado ao excluir.');
        }
    }
}