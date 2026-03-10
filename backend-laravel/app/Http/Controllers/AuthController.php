<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
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

        $dotnetBaseUrl = env('DOTNET_API_URL', 'http://profeluno_dotnet:5000');

        try {
            $response = Http::post("{$dotnetBaseUrl}/v1/User/Login", [
                'email' => $request->input('email'),
                'password' => md5($request->input('password')),
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if (!empty($data['autorizacao'])) {
                    $user = \App\Models\User::firstOrCreate(
                        ['email' => $request->input('email')],
                        ['password' => bcrypt(Str::random(32))]
                    );

                    Auth::login($user);

                    $role = strtolower($data['cargo'] ?? 'aluno');

                    if ($role === 'admin') {
                        return view('admin.dashboard');
                    } elseif ($role === 'professor') {
                        return redirect()->route('professor.dashboard');
                    } elseif ($role === 'aluno') {
                        return redirect()->route('aluno.dashboard');
                    }
                }
            }

            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Email ou senha inválidos.'])
                ->withInput($request->only('email'));
        } catch (\Throwable $e) {
            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Ocorreu um erro ao tentar fazer login.'])
                ->withInput($request->only('email'));
        }
    }

    public function showLogin() {
        return view('auth.login');
    }

    public function showRegister() {
        $cargos = Cargo::all();
        return view('auth.register', compact('cargos'));
    }

    public function registrar() {
        return 'registro';
    }
}
