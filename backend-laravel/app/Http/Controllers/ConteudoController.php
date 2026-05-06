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

    // ─── Helpers padrão ──────────────────────────────────────────

    private function authHeaders(): array
    {
        $token = session('api_token');
        return [
            'Accept'        => 'application/json',
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

            Log::warning("[ConteudoController] GET {$endpoint} retornou {$response->status()}", ['body' => $response->body()]);
            return null;
        } catch (\Exception $e) {
            Log::error("[ConteudoController] GET {$endpoint} falhou: " . $e->getMessage());
            return null;
        }
    }

    /**
     * POST com corpo de formulário (asForm).
     * A API .NET usa [FromForm] — não aceita JSON body.
     */
    private function apiPost(string $endpoint, array $data): ?array
    {
        try {
            $response = Http::withHeaders($this->authHeaders())
                ->asForm()
                ->timeout(15)
                ->post("{$this->baseUrl}/v1/{$endpoint}", $data);

            if ($response->successful()) {
                $json = $response->json();
                return is_array($json) ? $json : [];
            }

            Log::warning("[ConteudoController] POST {$endpoint} retornou {$response->status()}", ['body' => $response->body()]);
            return null;
        } catch (\Exception $e) {
            Log::error("[ConteudoController] POST {$endpoint} falhou: " . $e->getMessage());
            return null;
        }
    }

    /**
     * PUT com parâmetros na query string.
     * A API .NET usa [FromQuery] nos endpoints de atualização.
     */
    private function apiPut(string $endpoint, array $data): ?array
    {
        try {
            $url      = "{$this->baseUrl}/v1/{$endpoint}?" . http_build_query($data);
            $response = Http::withHeaders($this->authHeaders())->timeout(15)->send('PUT', $url);

            if ($response->successful()) {
                $json = $response->json();
                return is_array($json) ? $json : [];
            }

            Log::warning("[ConteudoController] PUT {$endpoint} retornou {$response->status()}", ['body' => $response->body()]);
            return null;
        } catch (\Exception $e) {
            Log::error("[ConteudoController] PUT {$endpoint} falhou: " . $e->getMessage());
            return null;
        }
    }

    private function apiDelete(string $endpoint): bool
    {
        try {
            $response = Http::withHeaders($this->authHeaders())
                ->timeout(15)
                ->delete("{$this->baseUrl}/v1/{$endpoint}");

            if ($response->successful()) return true;

            Log::warning("[ConteudoController] DELETE {$endpoint} retornou {$response->status()}", ['body' => $response->body()]);
            return false;
        } catch (\Exception $e) {
            Log::error("[ConteudoController] DELETE {$endpoint} falhou: " . $e->getMessage());
            return false;
        }
    }

    // ─── Helper privado de montagem de payload ────────────────────

    private function buildConteudoParams(Request $request, array $extra = []): array
    {
        $situacao = $request->boolean('situacao', true);
        $tipo     = strtolower($request->input('type'));

        return array_merge([
            'Titulo'    => $request->input('titulo'),
            'IdUsuario' => Auth::id(),
            'Descricao' => $request->input('descricao', ''),
            'IdMateria' => (int) $request->input('materia_id'),
            'Tipo'      => $tipo,
            'Situacao'  => $situacao ? 'true' : 'false',
            'Url'       => $request->input('file_url', ''),
        ], $extra);
    }

    // ─── Actions ──────────────────────────────────────────────────

    public function index()
    {
        $conteudos = $this->apiGet("Conteudo/ListarConteudos") ?? [];
        $title     = '<i class="fas fa-folder-open"></i> Conteúdos';
        $subtitle  = 'Gerencie os materiais e conteúdos das suas salas de aula';

        return view('professor.conteudo.index', compact('conteudos', 'title', 'subtitle'));
    }

    public function create()
    {
        $materias     = $this->apiGet("Materia/ListarMaterias") ?? [];
        $ultimapagina = "<a href='" . route('professor.conteudo.index') . "' class='back-link'><i class='fas fa-arrow-left'></i> Voltar</a>";
        $title        = '<i class="fas fa-plus"></i> Novo Conteúdo';
        $subtitle     = 'Adicione um conteúdo de apoio para a sala';

        return view('professor.conteudo.create', compact('title', 'subtitle', 'ultimapagina', 'materias'));
    }

    public function store(Request $request)
    {
        Log::info('[ConteudoController] store()', [
            'payload'  => $request->except(['file_path', '_token']),
            'has_file' => $request->hasFile('file_path') ? 'yes' : 'no',
        ]);

        $request->validate([
            'titulo'     => 'required|string|max:255',
            'descricao'  => 'nullable|string',
            'materia_id' => 'required|integer',
            'type'       => 'required|string|in:pdf,slide,link,document,other',
            'situacao'   => 'nullable|boolean',
            'file_url'   => 'nullable|url|required_if:type,link',
            'file_path'  => [
                'nullable', 'file', 'max:51200',
                'mimes:pdf,pptx,ppt,docx,doc,mp4,avi,mov',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->input('type') !== 'link'
                        && empty($request->input('file_url'))
                        && !$value) {
                        $fail('Envie um arquivo ou informe uma URL.');
                    }
                },
            ],
        ]);

        try {
            if ($request->hasFile('file_path') && $request->file('file_path')->isValid()) {
                // ── Com arquivo: multipart (Http direto) ──
                $arquivo  = $request->file('file_path');
                $nomeOrig = pathinfo($arquivo->getClientOriginalName(), PATHINFO_FILENAME);
                $extensao = '.' . $arquivo->getClientOriginalExtension();

                $params = $this->buildConteudoParams($request, [
                    'NomeArquivo'     => $nomeOrig,
                    'ExtensaoArquivo' => $extensao,
                ]);

                Log::info('[ConteudoController] store() com arquivo', [
                    'nome' => $nomeOrig, 'ext' => $extensao, 'size' => $arquivo->getSize(),
                ]);

                $response = Http::withToken(session('api_token'))
                    ->timeout(60)
                    ->asMultipart()
                    ->attach('Arquivo', file_get_contents($arquivo->getRealPath()), $arquivo->getClientOriginalName())
                    ->post("{$this->baseUrl}/v1/Conteudo/CadastrarConteudo", $params);

                if (!$response->successful()) {
                    Log::warning('[ConteudoController] store() multipart recusado', [
                        'status' => $response->status(), 'body' => $response->body(),
                    ]);
                    return back()->withInput()->with('error', $response->json('message') ?? 'Erro ao cadastrar conteúdo.');
                }

            } else {
                // ── Sem arquivo (link): apiPost com asForm ──
                $params = $this->buildConteudoParams($request, [
                    'NomeArquivo'     => '',
                    'ExtensaoArquivo' => '',
                ]);

                Log::info('[ConteudoController] store() sem arquivo, params:', $params);

                $result = $this->apiPost('Conteudo/CadastrarConteudo', $params);

                if ($result === null) {
                    return back()->withInput()->with('error', 'Erro ao cadastrar conteúdo.');
                }
            }
        } catch (\Exception $e) {
            Log::error('[ConteudoController] store() erro: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Erro ao conectar com o servidor.');
        }

        return redirect()->route('professor.conteudo.index')->with('success', 'Conteúdo cadastrado com sucesso!');
    }

    public function show($id)
    {
        return redirect()->route('professor.conteudo.edit', $id);
    }

    public function edit($id)
    {
        $conteudo = $this->apiGet("Conteudo/RetornaConteudoPorId/{$id}");
        if ($conteudo === null) {
            return redirect()->route('professor.conteudo.index')->with('error', 'Conteúdo não encontrado.');
        }

        $materias     = $this->apiGet("Materia/ListarMaterias") ?? [];
        $ultimapagina = "<a href='" . route('professor.conteudo.index') . "' class='back-link'><i class='fas fa-arrow-left'></i> Voltar</a>";
        $title        = '<i class="fas fa-edit"></i> Editar Conteúdo';
        $subtitle     = 'Atualize as informações do conteúdo';

        return view('professor.conteudo.edit', compact('conteudo', 'materias', 'title', 'subtitle', 'ultimapagina'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'titulo'     => 'required|string|max:255',
            'descricao'  => 'nullable|string',
            'materia_id' => 'required|integer',
            'type'       => 'required|string|in:pdf,slide,link,document,other',
            'situacao'   => 'nullable|boolean',
            'file_url'   => 'nullable|url|required_if:type,link',
            'file_path'  => ['nullable', 'file', 'max:51200', 'mimes:pdf,pptx,ppt,docx,doc,mp4,avi,mov'],
        ], [
            'titulo.required'      => 'O título é obrigatório.',
            'materia_id.required'  => 'Selecione uma matéria.',
            'type.required'        => 'Selecione o tipo do conteúdo.',
            'file_url.required_if' => 'Informe a URL para conteúdos do tipo Link.',
            'file_url.url'         => 'A URL informada não é válida.',
            'file_path.max'        => 'O arquivo deve ter no máximo 50 MB.',
            'file_path.mimes'      => 'Formato de arquivo não permitido.',
        ]);

        if ($request->hasFile('file_path') && $request->file('file_path')->isValid()) {
            // ── Com arquivo: PUT multipart + query string ──
            $arquivo  = $request->file('file_path');
            $nomeOrig = pathinfo($arquivo->getClientOriginalName(), PATHINFO_FILENAME);
            $extensao = '.' . $arquivo->getClientOriginalExtension();

            $params = $this->buildConteudoParams($request, [
                'IdConteudo'      => (int) $id,
                'NomeArquivo'     => $nomeOrig,
                'ExtensaoArquivo' => $extensao,
            ]);

            try {
                $response = Http::withToken(session('api_token'))
                    ->timeout(60)
                    ->attach('Arquivo', file_get_contents($arquivo->getRealPath()), $arquivo->getClientOriginalName())
                    ->put("{$this->baseUrl}/v1/Conteudo/AtualizarConteudo?" . http_build_query($params));
            } catch (\Exception $e) {
                Log::error('[ConteudoController] update() multipart: ' . $e->getMessage());
                return back()->withInput()->with('error', 'Erro ao conectar com o servidor.');
            }

            if (!$response->successful()) {
                Log::warning('[ConteudoController] update() multipart recusado', [
                    'status' => $response->status(), 'body' => $response->body(),
                ]);
                return back()->withInput()->with('error', $response->json('message') ?? 'Erro ao atualizar conteúdo.');
            }

        } else {
            // ── Sem arquivo: apiPut com query string ──
            $params = $this->buildConteudoParams($request, [
                'IdConteudo'      => (int) $id,
                'NomeArquivo'     => '',
                'ExtensaoArquivo' => '',
            ]);

            $result = $this->apiPut('Conteudo/AtualizarConteudo', $params);

            if ($result === null) {
                return back()->withInput()->with('error', 'Não foi possível atualizar o conteúdo.');
            }
        }

        return redirect()->route('professor.conteudo.index')->with('success', 'Conteúdo atualizado com sucesso!');
    }

    public function destroy($id)
    {
        $result = $this->apiDelete("Conteudo/DeletarConteudo/{$id}");

        if ($result) {
            return redirect()->route('professor.conteudo.index')->with('success', 'Conteúdo deletado com sucesso!');
        }

        return redirect()->route('professor.conteudo.index')->with('error', 'Erro ao deletar conteúdo.');
    }

    public function toggle($id)
    {
        $conteudo = $this->apiGet("Conteudo/RetornaConteudoPorId/{$id}");
        if ($conteudo === null) {
            return response()->json(['error' => 'Conteúdo não encontrado.'], 404);
        }

        $novaSituacao = !$conteudo['situacao'];

        $result = $this->apiPut('Conteudo/AtualizarConteudo', [
            'IdConteudo'      => (int) $id,
            'Situacao'        => $novaSituacao ? 'true' : 'false',
            'Titulo'          => $conteudo['titulo']          ?? '',
            'IdUsuario'       => $conteudo['idUsuario']       ?? 0,
            'Descricao'       => $conteudo['descricao']       ?? '',
            'IdMateria'       => $conteudo['idMateria']       ?? 0,
            'Tipo'            => $conteudo['tipo']            ?? '',
            'NomeArquivo'     => $conteudo['nomeArquivo']     ?? '',
            'ExtensaoArquivo' => $conteudo['extensaoArquivo'] ?? '',
            'Url'             => $conteudo['url']             ?? '',
        ]);

        if ($result !== null) {
            return response()->json([
                'success'  => true,
                'situacao' => $novaSituacao,
                'message'  => 'Situação alterada com sucesso!',
            ]);
        }

        return response()->json(['error' => 'Erro ao alterar situação.'], 500);
    }

    public function download($id)
    {
        try {
            $response = Http::withHeaders($this->authHeaders())
                ->timeout(60)
                ->get("{$this->baseUrl}/v1/Conteudo/DownloadArquivoConteudo/{$id}");

            if ($response->successful() && strlen($response->body()) > 0) {
                $dados       = $this->apiGet("Conteudo/RetornaDadosDoArquivo/{$id}");
                $nomeArquivo = $dados
                    ? (($dados['nomeArquivo'] ?? 'conteudo') . ($dados['extensaoArquivo'] ?? ''))
                    : "conteudo_{$id}";

                $contentType = $response->header('Content-Type') ?? 'application/octet-stream';

                Log::info("[ConteudoController] Download {$id}: type={$contentType}, nome={$nomeArquivo}");

                return response($response->body())
                    ->header('Content-Type', $contentType)
                    ->header('Content-Disposition', 'inline; filename="' . $nomeArquivo . '"')
                    ->header('Cache-Control', 'public, max-age=3600')
                    ->header('Accept-Ranges', 'bytes');
            }

            Log::warning("[ConteudoController] Download {$id}: status {$response->status()} ou body vazio");
            return response('Arquivo não encontrado', 404);

        } catch (\Exception $e) {
            Log::error("[ConteudoController] Download {$id} falhou: " . $e->getMessage());
            return response('Erro ao conectar com o servidor', 500);
        }
    }
}