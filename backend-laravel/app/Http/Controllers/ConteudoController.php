<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ConteudoController extends Controller
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

            Log::warning("[SimuladoController] GET {$endpoint} retornou {$response->status()}", [
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error("[SimuladoController] GET {$endpoint} falhou: " . $e->getMessage());
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
                // API pode retornar JSON ou string vazia (ex: 200/201 sem body)
                $json = $response->json();
                return is_array($json) ? $json : [];
            }

            Log::warning("[SimuladoController] POST {$endpoint} retornou {$response->status()}", [
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error("[SimuladoController] POST {$endpoint} falhou: " . $e->getMessage());
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
                // API pode retornar JSON ou string vazia (ex: 200/201 sem body)
                $json = $response->json();
                return is_array($json) ? $json : [];
            }

            Log::warning("[SimuladoController] PUT {$endpoint} retornou {$response->status()}", [
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error("[SimuladoController] PUT {$endpoint} falhou: " . $e->getMessage());
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

            Log::warning("[SimuladoController] DELETE {$endpoint} retornou {$response->status()}", [
                'body' => $response->body(),
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error("[SimuladoController] DELETE {$endpoint} falhou: " . $e->getMessage());
            return false;
        }
    }
    
    public function index()
    {
        $professorId = Auth::id();
        $conteudos = $this->apiGet("Conteudo/RetornaConteudoPorUsuario/{$professorId}");
        // ── Dados fictícios (substituir por chamada à API .NET futuramente) ──
        // $conteudos = [
        //     [
        //         'id'        => 1,
        //         'titulo'    => 'Apostila de Equações do 2º Grau',
        //         'descricao' => 'Material completo com exercícios resolvidos',
        //         'materia'   => 'Matemática',
        //         'tipo'      => 'pdf',
        //         'situacao'  => 1,
        //         'criado_em' => '2026-03-20T10:00:00',
        //     ],
        //     [
        //         'id'        => 2,
        //         'titulo'    => 'Slides — Leis de Newton',
        //         'descricao' => 'Apresentação com exemplos do cotidiano',
        //         'materia'   => 'Física',
        //         'tipo'      => 'slide',
        //         'situacao'  => 1,
        //         'criado_em' => '2026-03-21T14:30:00',
        //     ],
        //     [
        //         'id'        => 3,
        //         'titulo'    => 'Videoaula: Segunda Guerra Mundial',
        //         'descricao' => 'Resumo em vídeo com linha do tempo interativa',
        //         'materia'   => 'História',
        //         'tipo'      => 'video',
        //         'situacao'  => 1,
        //         'criado_em' => '2026-03-19T09:00:00',
        //     ],
        //     [
        //         'id'        => 4,
        //         'titulo'    => 'Simulado — Tabela Periódica',
        //         'descricao' => '10 questões de múltipla escolha',
        //         'materia'   => 'Química',
        //         'tipo'      => 'simulado',
        //         'situacao'  => 1,
        //         'criado_em' => '2026-03-22T16:00:00',
        //     ],
        //     [
        //         'id'        => 5,
        //         'titulo'    => 'Exercícios de Análise Sintática',
        //         'descricao' => 'Lista de exercícios para fixação',
        //         'materia'   => 'Português',
        //         'tipo'      => 'document',
        //         'situacao'  => 0,
        //         'criado_em' => '2026-03-17T11:00:00',
        //     ],
        // ];
        $conteudos = $conteudos ?? [];

        $title = '<i class="fas fa-folder-open"></i> Conteúdos';
        $subtitle = 'Gerencie os materiais e conteudos das suas salas de aula';
        return view('professor.conteudo.index', compact('conteudos', 'title', 'subtitle'));
    }

    public function create()
    {
        $materias = $this->apiGet("Materia/ListarMaterias") ?? [];

        $ultimapagina = "<a href='" . route('professor.conteudo.index') . "' class='back-link'>
            <i class='fas fa-arrow-left'></i>
            Voltar
        </a>";
        $title = '<i class="fas fa-plus"></i> Novo Conteúdo';
        $subtitle = 'Adicione um conteúdo de apoio para a sala';
        return view('professor.conteudo.create', compact('title', 'subtitle', 'ultimapagina', 'materias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo'      => 'required|string|max:255',
            'descricao'   => 'nullable|string',
            'materia_id'  => 'required|integer',
            'type'        => 'required|string|in:pdf,slide,link,document,other',
            'situacao'    => 'nullable|boolean',
            'file_url'    => 'nullable|url|required_if:type,link',
            'file_path'   => [
                'nullable',
                'file',
                'max:51200',
                'mimes:pdf,pptx,ppt,docx,doc,mp4,avi,mov',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->input('type') !== 'link' && empty($request->input('file_url')) && !$value) {
                        $fail('Envie um arquivo ou informe uma URL.');
                    }
                },
            ],
        ], [
            'titulo.required'      => 'O título é obrigatório.',
            'materia_id.required'  => 'Selecione uma matéria.',
            'type.required'        => 'Selecione o tipo do conteúdo.',
            'file_url.required_if' => 'Informe a URL para conteúdos do tipo Link.',
            'file_url.url'         => 'A URL informada não é válida.',
            'file_path.max'        => 'O arquivo deve ter no máximo 50 MB.',
            'file_path.mimes'      => 'Formato de arquivo não permitido.',
        ]);

        $professorId = Auth::id();
        $tipo        = $request->input('type');
        $fileUrl     = $request->input('file_url', '');

        // ── Com arquivo físico → multipart/form-data ──────────────────────────
        if ($request->hasFile('file_path') && $request->file('file_path')->isValid()) {
            $arquivo  = $request->file('file_path');
            $nomeOrig = pathinfo($arquivo->getClientOriginalName(), PATHINFO_FILENAME);
            $extensao = '.' . $arquivo->getClientOriginalExtension();

            try {
                $response = Http::withToken(session('api_token'))
                    ->timeout(60)
                    ->attach(
                        'Arquivo',                                       // <-- campo C#
                        file_get_contents($arquivo->getRealPath()),
                        $arquivo->getClientOriginalName()
                    )
                    ->post("{$this->baseUrl}/v1/Conteudo/CadastrarConteudo", [
                        'Titulo'           => $request->input('titulo'),
                        'IdUsuario'        => $professorId,
                        'Descricao'        => $request->input('descricao', ''),
                        'IdMateria'        => $request->input('materia_id'),
                        'Tipo'             => $tipo,
                        'Situacao'         => $request->boolean('situacao', true),
                        'NomeArquivo'      => $nomeOrig,
                        'ExtensaoArquivo'  => $extensao,
                        'Url'              => $fileUrl,
                    ]);

            } catch (\Exception $e) {
                Log::error('[ConteudoController] store() multipart falhou: ' . $e->getMessage());
                return back()->withInput()->with('error', 'Erro ao conectar com o servidor.');
            }

            if (!$response->successful()) {
                Log::warning('[ConteudoController] API recusou cadastro', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return back()->withInput()->with('error', $response->json('message') ?? 'Erro ao cadastrar conteúdo.');
            }

        // ── Sem arquivo (link/URL externa) → JSON ─────────────────────────────
        } else {
            $result = $this->apiPost('Conteudo/CadastrarConteudo', [
                'Titulo'           => $request->input('titulo'),
                'IdUsuario'        => $professorId,
                'Descricao'        => $request->input('descricao', ''),
                'IdMateria'        => $request->input('materia_id'),
                'Tipo'             => $tipo,
                'Situacao'         => $request->boolean('situacao', true),
                'NomeArquivo'      => '',
                'ExtensaoArquivo'  => '',
                'Url'              => $fileUrl,
            ]);

            if ($result === null) {
                return back()->withInput()->with('error', 'Não foi possível cadastrar o conteúdo.');
            }
        }

        return redirect()
            ->route('professor.conteudo.index')
            ->with('success', 'Conteúdo cadastrado com sucesso!');
    }

    public function show($id)
    {
        return redirect()->route('professor.conteudo.edit', $id);
    }

    public function edit($id)
    {
        $conteudo = $this->apiGet("Conteudo/{$id}");
        if ($conteudo === null) {
            return redirect()->route('professor.conteudo.index')->with('error', 'Conteúdo não encontrado.');
        }

        $materias = $this->apiGet("Materia/ListarMaterias") ?? [];

        $ultimapagina = "<a href='" . route('professor.conteudo.index') . "' class='back-link'>
            <i class='fas fa-arrow-left'></i>
            Voltar
        </a>";
        $title = '<i class="fas fa-edit"></i> Editar Conteúdo';
        $subtitle = 'Atualize as informações do conteúdo';
        return view('professor.conteudo.edit', compact('conteudo', 'materias', 'title', 'subtitle', 'ultimapagina'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'titulo'      => 'required|string|max:255',
            'descricao'   => 'nullable|string',
            'materia_id'  => 'required|integer',
            'type'        => 'required|string|in:pdf,slide,link,document,other',
            'situacao'    => 'nullable|boolean',
            'file_url'    => 'nullable|url|required_if:type,link',
            'file_path'   => [
                'nullable',
                'file',
                'max:51200',
                'mimes:pdf,pptx,ppt,docx,doc,mp4,avi,mov',
            ],
        ], [
            'titulo.required'      => 'O título é obrigatório.',
            'materia_id.required'  => 'Selecione uma matéria.',
            'type.required'        => 'Selecione o tipo do conteúdo.',
            'file_url.required_if' => 'Informe a URL para conteúdos do tipo Link.',
            'file_url.url'         => 'A URL informada não é válida.',
            'file_path.max'        => 'O arquivo deve ter no máximo 50 MB.',
            'file_path.mimes'      => 'Formato de arquivo não permitido.',
        ]);

        $professorId = Auth::id();
        $tipo        = $request->input('type');
        $fileUrl     = $request->input('file_url', '');

        // ── Com arquivo físico → multipart/form-data ──────────────────────────
        if ($request->hasFile('file_path') && $request->file('file_path')->isValid()) {
            $arquivo  = $request->file('file_path');
            $nomeOrig = pathinfo($arquivo->getClientOriginalName(), PATHINFO_FILENAME);
            $extensao = '.' . $arquivo->getClientOriginalExtension();

            try {
                $response = Http::withToken(session('api_token'))
                    ->timeout(60)
                    ->attach(
                        'Arquivo',
                        file_get_contents($arquivo->getRealPath()),
                        $arquivo->getClientOriginalName()
                    )
                    ->put("{$this->baseUrl}/v1/Conteudo/{$id}", [
                        'Titulo'           => $request->input('titulo'),
                        'IdUsuario'        => $professorId,
                        'Descricao'        => $request->input('descricao', ''),
                        'IdMateria'        => $request->input('materia_id'),
                        'Tipo'             => $tipo,
                        'Situacao'         => $request->boolean('situacao', true),
                        'NomeArquivo'      => $nomeOrig,
                        'ExtensaoArquivo'  => $extensao,
                        'Url'              => $fileUrl,
                    ]);

            } catch (\Exception $e) {
                Log::error('[ConteudoController] update() multipart falhou: ' . $e->getMessage());
                return back()->withInput()->with('error', 'Erro ao conectar com o servidor.');
            }

            if (!$response->successful()) {
                Log::warning('[ConteudoController] API recusou atualização', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return back()->withInput()->with('error', $response->json('message') ?? 'Erro ao atualizar conteúdo.');
            }

        // ── Sem arquivo → JSON ─────────────────────────────
        } else {
            $result = $this->apiPut("Conteudo/{$id}", [
                'Titulo'           => $request->input('titulo'),
                'IdUsuario'        => $professorId,
                'Descricao'        => $request->input('descricao', ''),
                'IdMateria'        => $request->input('materia_id'),
                'Tipo'             => $tipo,
                'Situacao'         => $request->boolean('situacao', true),
                'NomeArquivo'      => '',
                'ExtensaoArquivo'  => '',
                'Url'              => $fileUrl,
            ]);

            if ($result === null) {
                return back()->withInput()->with('error', 'Não foi possível atualizar o conteúdo.');
            }
        }

        return redirect()
            ->route('professor.conteudo.index')
            ->with('success', 'Conteúdo atualizado com sucesso!');
    }
    
    public function destroy($id)
    {
        $result = $this->apiDelete("Conteudo/{$id}");

        if ($result) {
            return redirect()->route('professor.conteudo.index')->with('success', 'Conteúdo deletado com sucesso!');
        } else {
            return redirect()->route('professor.conteudo.index')->with('error', 'Erro ao deletar conteúdo.');
        }
    }

    public function toggle($id)
    {
        $conteudo = $this->apiGet("Conteudo/{$id}");
        if ($conteudo === null) {
            return response()->json(['error' => 'Conteúdo não encontrado.'], 404);
        }

        $novaSituacao = !$conteudo['situacao'];

        $result = $this->apiPut("Conteudo/{$id}/Toggle", [
            'Situacao' => $novaSituacao,
        ]);

        if ($result !== null) {
            return response()->json([
                'success' => true,
                'situacao' => $novaSituacao,
                'message' => 'Situação alterada com sucesso!'
            ]);
        } else {
            return response()->json(['error' => 'Erro ao alterar situação.'], 500);
        }
    }

    public function download($id)
    {
        try {
            $response = Http::withHeaders($this->authHeaders())
                ->timeout(60)
                ->get("{$this->baseUrl}/v1/conteudo/DownloadArquivoConteudo/{$id}");

            if ($response->successful()) {
                $conteudo = $this->apiGet("Conteudo/{$id}");
                $nomeArquivo = $conteudo ? ($conteudo['nomeArquivo'] . $conteudo['extensaoArquivo']) : "conteudo_{$id}";

                return response($response->body())
                    ->header('Content-Type', $response->header('Content-Type') ?? 'application/octet-stream')
                    ->header('Content-Disposition', 'attachment; filename="' . $nomeArquivo . '"');
            } else {
                Log::warning("[ConteudoController] Download {$id} retornou {$response->status()}", [
                    'body' => $response->body(),
                ]);
                return redirect()->back()->with('error', 'Arquivo não encontrado ou erro no download.');
            }
        } catch (\Exception $e) {
            Log::error("[ConteudoController] Download {$id} falhou: " . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao conectar com o servidor.');
        }
    }
}