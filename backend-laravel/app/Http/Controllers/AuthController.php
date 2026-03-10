<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    public function verifyUser(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $dotnetBaseUrl = env('DOTNET_API_URL', 'http://profeluno_dotnet:5000');

        try {
            $response = Http::get("{$dotnetBaseUrl}/v1/User/Login", [
                'email' => $request->input('email'),
                'password' => md5($request->input('password')),
            ]);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'data' => $response->json(),
                ]);
            }

            return response()->json([
                'success' => false,
                'status' => $response->status(),
                'error' => $response->body(),
            ], $response->status());
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
