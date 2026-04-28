<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
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

            Log::warning("[ProfileController] GET {$endpoint} retornou {$response->status()}", [
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error("[ProfileController] GET {$endpoint} falhou: " . $e->getMessage());
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

            Log::warning("[ProfileController] POST {$endpoint} retornou {$response->status()}", [
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error("[ProfileController] POST {$endpoint} falhou: " . $e->getMessage());
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

            Log::warning("[ProfileController] PUT {$endpoint} retornou {$response->status()}", [
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error("[ProfileController] PUT {$endpoint} falhou: " . $e->getMessage());
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

            Log::warning("[ProfileController] DELETE {$endpoint} retornou {$response->status()}", [
                'body' => $response->body(),
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error("[ProfileController] DELETE {$endpoint} falhou: " . $e->getMessage());
            return false;
        }
    }

    public function edit()
    {
        $user      = Auth::user()->load('cargo');
        $cargoNome = strtolower($user->cargo?->nome_cargo ?? '');
        $perfil    = null;
        $escolaridades = collect();
        $areas         = collect();

        // Busca listas auxiliares
        $escolaridades = collect($this->apiGet('Escolaridade/ListarEscolaridades') ?? []);
        $areas         = collect($this->apiGet('Area/ListarAreas') ?? []);

        // Busca perfil específico do cargo
        if (in_array($cargoNome, ['aluno', 'professor'])) {
            $modulo = $cargoNome === 'aluno' ? 'AlunoPerfil' : 'ProfessorPerfil';

            try {
                $resPerfil = Http::get("{$this->baseUrl}/v1/{$modulo}/BuscarPerfil/{$user->id}");

                if ($resPerfil->successful()) {
                    $perfil = $resPerfil->json();
                } else {
                    Log::warning("ProfileController::edit perfil não encontrado", [
                        'cargo'  => $cargoNome,
                        'status' => $resPerfil->status(),
                    ]);
                }
            } catch (\Throwable $e) {
                Log::error('ProfileController::edit erro ao buscar perfil', ['exception' => $e]);
            }
        }

        $title    = '<i class="fas fa-user"></i> Meu Perfil';
        $subtitle = 'Atualize seus dados e informações de perfil';

        return view('profile.edit', compact('user', 'cargoNome', 'escolaridades', 'areas', 'perfil', 'title', 'subtitle'));
    }

    public function update(Request $request)
    {
        $user      = Auth::user()->load('cargo');
        $cargoNome = strtolower($user->cargo?->nome_cargo ?? '');

        // Validação
        $rules = [
            'nome_usuario' => 'required|string|max:255',
        ];

        if ($request->filled('password')) {
            $rules['password']              = 'string|min:6|confirmed';
            $rules['password_confirmation'] = 'required|string';
        }

        if ($cargoNome === 'aluno') {
            $rules['escolaridade_id'] = 'required';
            $rules['area_id']         = 'required';
        } elseif ($cargoNome === 'professor') {
            $rules['escolaridade_id'] = 'required';
            $rules['area_id']         = 'required';
        }

        $request->validate($rules);

        // 1) Atualiza dados básicos do usuário no .NET
        try {
            $payload = [
                'idUser'       => $user->id,
                'nome_Usuario' => $request->input('nome_usuario'),
                'email'        => $user->email,
                'idCargo'      => $user->cargo_id,
                'password'     => $request->filled('password')
                                    ? md5($request->input('password'))
                                    : $user->password,
            ];

            $response = $this->apiPut('User/AtualizarUsuario', $payload);

            if (is_null($response)) {
                return back()
                    ->withErrors(['nome_usuario' => 'Erro ao atualizar dados na API.'])
                    ->withInput();
            }
        } catch (\Throwable $e) {
            Log::error('ProfileController::update user error', ['exception' => $e]);

            return back()
                ->withErrors(['nome_usuario' => 'Ocorreu um erro inesperado.'])
                ->withInput();
        }

        // 2) Atualiza perfil específico no .NET
        if (in_array($cargoNome, ['aluno', 'professor'])) {
            $modulo = $cargoNome === 'aluno' ? 'AlunoPerfil' : 'ProfessorPerfil';

            $perfilPayload = $cargoNome === 'aluno'
                ? [
                    'idUser'         => $user->id,
                    'periodo'        => $request->input('periodo'),
                    'idEscolaridade' => $request->input('escolaridade_id'),
                    'idArea'         => $request->input('area_id'),
                ]
                : [
                    'idUser'         => $user->id,
                    'formacao'       => $request->input('formacao'),
                    'idEscolaridade' => $request->input('escolaridade_id'),
                    'idArea'         => $request->input('area_id'),
                ];

            try {
                // PUT se já existe perfil, POST se é novo
                $resPerfil = Http::put("{$this->baseUrl}/v1/{$modulo}/AtualizarPerfil", $perfilPayload);

                // Se o .NET retornar 404 (perfil ainda não existe), tenta criar
                if ($resPerfil->status() === 404) {
                    $resPerfil = Http::post("{$this->baseUrl}/v1/{$modulo}/SalvarPerfil", $perfilPayload);
                }

                Log::debug("ProfileController::update {$modulo} dotnet", [
                    'status' => $resPerfil->status(),
                    'body'   => $resPerfil->body(),
                ]);

                if (! $resPerfil->successful()) {
                    return back()
                        ->withErrors(['area_id' => 'Erro ao atualizar perfil na API.'])
                        ->withInput();
                }
            } catch (\Throwable $e) {
                Log::error("ProfileController::update {$modulo} error", ['exception' => $e]);

                return back()
                    ->withErrors(['area_id' => 'Ocorreu um erro inesperado ao salvar o perfil.'])
                    ->withInput();
            }
        }

        // 3) Atualiza localmente só o que é necessário para Auth/sessão
        $localData = ['nome_usuario' => $request->input('nome_usuario')];
        if ($request->filled('password')) {
            $localData['password'] = md5($request->input('password'));
        }
        $user->update($localData);
        session(['user_nome' => $request->input('nome_usuario')]);

        return back()->with('success', 'Perfil atualizado com sucesso!');
    }
}