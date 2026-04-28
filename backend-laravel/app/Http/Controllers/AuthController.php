<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Cargo;

class AuthController extends Controller
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('DOTNET_API_URL', 'http://profeluno_dotnet:9000');
    }

    public function autenticar(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $url = "{$this->baseUrl}/v1/User/Login";

        try {
            $response = Http::post($url, [
                'email' => $request->input('email'),
                'password' => md5($request->input('password')),
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if (!empty($data['autorizacao'])) {
                    $user = \App\Models\User::where('email', $request->input('email'))
                        ->with('cargo') // eager load para não fazer query extra
                        ->firstOrFail();

                    Auth::login($user);

                    // Salva na sessão — sem aparecer na URL
                    session([
                        'user_id'    => $user->id,
                        'user_nome'  => $user->nome_usuario,
                        'user_email' => $user->email,
                        'user_cargo' => strtolower($user->cargo?->nome_cargo ?? 'aluno'),
                        'cargo_id'   => $user->cargo_id,
                        'api_token'  => $data['autorizacao'],
                    ]);

                    $role = session('user_cargo');

                    if ($role === 'admin') {
                        return redirect()->route('admin.dashboard');
                    } elseif ($role === 'professor') {
                        return redirect()->route('professor.dashboard');
                    } else {
                        return redirect()->route('aluno.dashboard');
                    }
                }
            }

            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Email ou senha inválidos.'])
                ->withInput($request->only('email'));

        } catch (\Throwable $e) {
            Log::error('AuthController::autenticar error', ['exception' => $e]);

            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Ocorreu um erro ao tentar fazer login.'])
                ->withInput($request->only('email'));
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function showLogin() {
        return view('auth.login');
    }

    public function showRegister()
    {
        $cargos        = Cargo::all();
        $escolaridades = collect();
        $areas         = collect();

        try {
            $resEsc = Http::get("{$this->baseUrl}/v1/Escolaridade/ListarEscolaridades");
            if ($resEsc->successful()) {
                $escolaridades = collect($resEsc->json());
            }

            $resArea = Http::get("{$this->baseUrl}/v1/Area/ListarAreas");
            if ($resArea->successful()) {
                $areas = collect($resArea->json());
            }
        } catch (\Throwable $e) {
            Log::error('AuthController::showRegister erro ao buscar listas', ['exception' => $e]);
        }

        return view('auth.register', compact('cargos', 'escolaridades', 'areas'));
    }

    public function registrar(Request $request)
    {
        $cargo     = Cargo::find($request->input('cargo_id'));
        $cargoNome = strtolower($cargo?->nome_cargo ?? '');

        $rules = [
            'name'                  => 'required|string',
            'email'                 => 'required|email',
            'cargo_id'              => 'required|exists:cargos,id',
            'password'              => 'required|string',
            'password_confirmation' => 'required|string|same:password',
        ];

        if (in_array($cargoNome, ['aluno', 'professor'])) {
            $rules['escolaridade_id'] = 'required';
            $rules['area_id']         = 'required';
        }

        $request->validate($rules);

        $url = "{$this->baseUrl}/v1/User/CadastrarUsuario";

        try {
            Log::debug('AuthController::registrar dotnet request', [
                'url'     => $url,
                'payload' => [
                    'nome'    => $request->input('name'),
                    'email'   => $request->input('email'),
                    'idCargo' => $request->input('cargo_id'),
                ],
            ]);

            $response = Http::post($url, [
                'nome'    => $request->input('name'),
                'email'   => $request->input('email'),
                'senha'   => md5($request->input('password')),
                'idCargo' => $request->input('cargo_id'),
            ]);

            Log::debug('AuthController::registrar dotnet response', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            if ($response->successful()) {
                $user = \App\Models\User::updateOrCreate(
                    ['email' => $request->input('email')],
                    [
                        'nome_usuario' => $request->input('name'),
                        'password'     => md5($request->input('password')),
                        'cargo_id'     => $request->input('cargo_id'),
                    ]
                );

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
                        $resPerfil = Http::post("{$this->baseUrl}/v1/{$modulo}/SalvarPerfil", $perfilPayload);

                        Log::debug("AuthController::registrar {$modulo} dotnet", [
                            'status' => $resPerfil->status(),
                            'body'   => $resPerfil->body(),
                        ]);

                        // Não bloqueia o registro se o perfil falhar — endpoint ainda não existe
                        if (! $resPerfil->successful()) {
                            Log::warning("AuthController::registrar {$modulo} falhou", [
                                'status' => $resPerfil->status(),
                            ]);
                        }
                    } catch (\Throwable $e) {
                        Log::error("AuthController::registrar {$modulo} erro", ['exception' => $e]);
                    }
                }

                return redirect()->route('login')->with('success', 'Registro bem-sucedido. Faça login para continuar.');
            }

            return redirect()
                ->route('register')
                ->withErrors(['email' => 'Ocorreu um erro ao tentar registrar.'])
                ->withInput($request->only('name', 'email', 'cargo_id'));

        } catch (\Throwable $e) {
            Log::error('AuthController::registrar error', ['exception' => $e]);

            return redirect()
                ->route('register')
                ->withErrors(['email' => 'Ocorreu um erro ao tentar registrar.'])
                ->withInput($request->only('name', 'email', 'cargo_id'));
        }
    }
}
