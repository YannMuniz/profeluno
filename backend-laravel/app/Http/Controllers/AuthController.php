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
    public function autenticar(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $dotnetBaseUrl = env('DOTNET_API_URL', 'http://profeluno_dotnet:2912');
        $url = "{$dotnetBaseUrl}/v1/User/Login"; 

        $password = md5($request->input('password'));
        try {
            $response = Http::post($url, [
                'email' => $request->input('email'),
                'password' => $password,
            ]);

            Log::debug('AuthController::autenticar dotnet response', [
                'url' => $url,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if (!empty($data['autorizacao'])) {
                    $cargoValue = $data['cargo'] ?? null;
                    $cargoId = null;

                    if (is_numeric($cargoValue)) {
                        $cargoId = (int) $cargoValue;
                    } elseif (is_string($cargoValue)) {
                        $cargoId = Cargo::whereRaw('LOWER(nome_cargo) = ?', [strtolower($cargoValue)])->value('id');
                    }

                    $cargoId = $cargoId ?: Cargo::whereRaw('LOWER(nome_cargo) = ?', ['aluno'])->value('id');

                    $user = \App\Models\User::firstOrCreate(
                        ['email' => $request->input('email')],
                        [
                            'password' => bcrypt(Str::random(32)),
                            'cargo_id' => $cargoId,
                            'nome_usuario' => $request->input('email'),
                        ]
                    );

                    if ($user->cargo_id !== $cargoId) {
                        $user->update(['cargo_id' => $cargoId]);
                    }

                    Auth::login($user);

                    $role = strtolower($user->cargo?->nome_cargo ?? 'aluno');

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

    public function showRegister() {
        $cargos = Cargo::all();
        return view('auth.register', compact('cargos'));
    }

    public function registrar(Request $request) {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'cargo_id' => 'required|exists:cargos,id',
            'password' => 'required|string',
            'password_confirmation' => 'required|string|same:password',
        ]);

        $password = md5($request->input('password'));

        $dotnetBaseUrl = env('DOTNET_API_URL', 'http://profeluno_dotnet:2912');
        $url = "{$dotnetBaseUrl}/v1/User/CadastrarUsuario"; 
        try {
            if ($request->input('password') !== $request->input('password_confirmation')) {
                return redirect()
                    ->route('register')
                    ->withErrors(['password_confirmation' => 'As senhas não coincidem.'])
                    ->withInput($request->only('name', 'email', 'cargo_id'));
            }

            Log::debug('AuthController::registrar dotnet request', [
                'url' => $url,
                'payload' => [
                    'nome' => $request->input('name'),
                    'email' => $request->input('email'),
                    'senha' => md5($request->input('password')),
                    'idCargo' => $request->input('cargo_id'),
                ],
            ]);

            $response = Http::post($url, [
                'nome' => $request->input('name'),
                'email' => $request->input('email'),
                'senha' => $password,
                'idCargo' => $request->input('cargo_id'),
            ]);

            Log::debug('AuthController::registrar dotnet response', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            if ($response->successful()) {
                // Cria/atualiza o usuário localmente para manter o cargo e permitir redirecionamento
                \App\Models\User::updateOrCreate(
                    ['email' => $request->input('email')],
                    [
                        'nome_usuario' => $request->input('name'),
                        'password' => md5($request->password),
                        'cargo_id' => $request->input('cargo_id'),
                    ]
                );

                return redirect()->route('login')->with('success', 'Registro bem-sucedido. Faça login para continuar.');
            }

            return redirect()
                ->route('register')
                ->withErrors(['email' => 'Ocorreu um erro ao tentar registrar.'])
                ->withInput($request->only('name', 'email', 'cargo_id'));
        } catch (\Throwable $e) {
            return redirect()
                ->route('register')
                ->withErrors(['email' => 'Ocorreu um erro ao tentar registrar.'])
                ->withInput($request->only('name', 'email', 'cargo_id'));
        }
    }
}
