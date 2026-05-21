<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Cargo;

class AuthController extends Controller
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('DOTNET_API_URL', 'http://profeluno_dotnet:9000');
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function autenticar(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        try {
            $response = Http::post("{$this->baseUrl}/v1/User/Login", [
                'email'    => $request->input('email'),
                'password' => md5($request->input('password')),
            ]);

            if ($response->successful() && !empty($response->json('autorizacao'))) {
                $data = $response->json();

                $user = \App\Models\User::firstOrCreate(
                    ['email' => $request->input('email')],
                    [
                        'nome_usuario' => explode('@', $request->input('email'))[0],
                        'password'     => md5($request->input('password')),
                        'cargo_id'     => $data['idCargo'] ?? null,
                    ]
                );

                $user->update([
                    'password' => md5($request->input('password')),
                    'cargo_id' => $data['idCargo'] ?? $user->cargo_id,
                ]);

                $user->load('cargo');

                Auth::login($user);

                session([
                    'user_id'    => $user->id,
                    'user_nome'  => $user->nome_usuario,
                    'user_email' => $user->email,
                    'user_cargo' => strtolower($user->cargo?->nome_cargo ?? 'aluno'),
                    'cargo_id'   => $user->cargo_id,
                    'api_token'  => $data['autorizacao'],
                ]);

                return match (session('user_cargo')) {
                    'professor' => redirect()->route('professor.dashboard'),
                    default     => redirect()->route('aluno.dashboard'),
                };
            }

        } catch (\Throwable $e) {
            Log::error('AuthController::autenticar', ['exception' => $e]);
        }

        return redirect()
            ->route('login')
            ->withErrors(['email' => 'Email ou senha inválidos.'])
            ->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function showRegister()
    {
        $cargos        = Cargo::whereNotIn('nome_cargo', ['admin', 'Admin'])->get();
        $escolaridades = collect();
        $areas         = collect();

        try {
            $resEsc = Http::get("{$this->baseUrl}/v1/Escolaridade/RetornaTodasEscolaridades");
            if ($resEsc->successful()) {
                $escolaridades = collect($resEsc->json());
            }

            $resArea = Http::get("{$this->baseUrl}/v1/Area/RetornaTodasAreas");
            if ($resArea->successful()) {
                $areas = collect($resArea->json());
            }
        } catch (\Throwable $e) {
            Log::error('AuthController::showRegister listas', ['exception' => $e]);
        }

        return view('auth.register', compact('cargos', 'escolaridades', 'areas'));
    }

    public function registrar(Request $request)
    {
        $cargo     = Cargo::find($request->input('cargo_id'));
        $cargoNome = strtolower($cargo?->nome_cargo ?? '');

        $rules = [
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email',
            'cargo_id'              => 'required|exists:cargos,id',
            'password'              => 'required|string|min:6',
            'password_confirmation' => 'required|same:password',
        ];

        if ($cargoNome === 'aluno') {
            $rules['escolaridade_id'] = 'required|integer';
            $rules['area_id']         = 'required|integer';
        }

        if ($cargoNome === 'professor') {
            $rules['escolaridade_id'] = 'required|integer';
            $rules['area_id']         = 'required|integer';
        }

        $request->validate($rules);

        // O .NET aceita tudo em /v1/User/CadastrarUsuario
        $payload = [
            'nome'    => $request->input('name'),
            'email'   => $request->input('email'),
            'senha'   => md5($request->input('password')),
            'idCargo' => (int) $request->input('cargo_id'),
        ];

        if ($cargoNome === 'aluno') {
            $payload['periodoAluno']        = $request->input('periodo') ?? '';
            $payload['idEscolaridadeAluno'] = (int) $request->input('escolaridade_id');
            $payload['idAreaAluno']         = (int) $request->input('area_id');
        }

        if ($cargoNome === 'professor') {
            $payload['formacaoProfessor']       = $request->input('formacao') ?? '';
            $payload['idEscolaridadeProfessor'] = (int) $request->input('escolaridade_id');
            $payload['idAreaProfessor']         = (int) $request->input('area_id');
        }

        // Chamada ao .NET 
        try {
            Log::debug('AuthController::registrar payload', ['payload' => $payload]);

            $response = Http::post("{$this->baseUrl}/v1/User/CadastrarUsuario", $payload);

            Log::debug('AuthController::registrar response', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            if ($response->successful()) {
                \App\Models\User::updateOrCreate(
                    ['email' => $request->input('email')],
                    [
                        'nome_usuario' => $request->input('name'),
                        'password'     => md5($request->input('password')),
                        'cargo_id'     => (int) $request->input('cargo_id'),
                    ]
                );

                return redirect()
                    ->route('login')
                    ->with('success', 'Conta criada com sucesso! Faça login para continuar.');
            }

            // Tenta extrair mensagem de erro do .NET para exibir ao usuário
            $apiMsg = $response->json('message') ?? 'Ocorreu um erro ao registrar. Tente novamente.';

            return redirect()
                ->route('register')
                ->withErrors(['email' => $apiMsg])
                ->withInput($request->only('name', 'email', 'cargo_id'));

        } catch (\Throwable $e) {
            Log::error('AuthController::registrar exception', ['exception' => $e]);

            return redirect()
                ->route('register')
                ->withErrors(['email' => 'Erro de conexão. Tente novamente mais tarde.'])
                ->withInput($request->only('name', 'email', 'cargo_id'));
        }
    }
}