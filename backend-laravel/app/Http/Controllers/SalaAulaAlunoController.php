<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class SalaAulaAlunoController extends Controller
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('DOTNET_API_URL', 'http://profeluno_dotnet:9000');
    }

    // ─── HELPERS ────────────────────────────────────────────────────────────

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

            Log::warning("[SalaAulaAlunoController] GET {$endpoint} retornou {$response->status()}", [
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error("[SalaAulaAlunoController] GET {$endpoint} falhou: " . $e->getMessage());
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

            Log::warning("[SalaAulaAlunoController] POST {$endpoint} retornou {$response->status()}", [
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error("[SalaAulaAlunoController] POST {$endpoint} falhou: " . $e->getMessage());
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

            Log::warning("[SalaAulaAlunoController] DELETE {$endpoint} retornou {$response->status()}", [
                'body' => $response->body(),
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error("[SalaAulaAlunoController] DELETE {$endpoint} falhou: " . $e->getMessage());
            return false;
        }
    }

    // ─── NORMALIZE ──────────────────────────────────────────────────────────

    private function normalizeSala(array $item): object
    {
        $sala = (object) $item;

        $sala->id = $sala->idSalaAula ?? null;

        $sala->data_hora_inicio = !empty($sala->dataHoraInicio)
            ? Carbon::parse($sala->dataHoraInicio)
            : null;

        $sala->data_hora_fim = !empty($sala->dataHoraFim)
            ? Carbon::parse($sala->dataHoraFim)
            : null;

        $sala->qtd_alunos = $sala->maxAlunos ?? 0;
        $sala->materia    = $sala->materia   ?? '—';
        $sala->status     = $sala->status    ?? 'pending';
        $sala->descricao  = $sala->descricao ?? null;
        $sala->titulo     = $sala->titulo    ?? 'Sem título';

        return $sala;
    }

    // ─── INDEX - BUSCAR SALAS DISPONÍVEIS ───────────────────────────────────

    public function index(Request $request)
    {
        $materias = $this->apiGet('Materia/ListarMaterias') ?? [];
        $filters  = [
            'q'         => trim($request->q ?? ''),
            'idMateria' => $request->materia,
            'filtro'    => $request->filtro,
            'ordenar'   => $request->ordenar,
        ];

        $data  = $this->apiGet('SalaAula/RetornaTodasSalasAula');
        $salas = [];

        if (!is_null($data)) {
            $materiasMap = collect($materias)->keyBy('idMateria');
            $salas = collect($data)->map(function ($item) use ($materiasMap) {
                $sala = $this->normalizeSala($item);
                if (isset($sala->idMateria) && $materiasMap->has($sala->idMateria)) {
                    $sala->materia = $materiasMap->get($sala->idMateria)['nomeMateria'] ?? '—';
                }
                return $sala;
            });

            if ($filters['idMateria']) {
                $salas = $salas->where('idMateria', $filters['idMateria']);
            }

            if ($filters['q']) {
                $salas = $salas->filter(function ($sala) use ($filters) {
                    $texto = mb_strtolower(($sala->titulo ?? '') . ' ' . ($sala->materia ?? ''));
                    return str_contains($texto, mb_strtolower($filters['q']));
                });
            }

            if ($filters['filtro'] === 'ao-vivo') {
                $salas = $salas->where('status', 'active');
            } elseif ($filters['filtro'] === 'agendadas') {
                $salas = $salas->where('status', 'pending');
            }

            if ($filters['ordenar'] === 'ao-vivo') {
                $salas = $salas->sortByDesc(fn($s) => $s->status === 'active');
            } elseif ($filters['ordenar'] === 'alunos') {
                $salas = $salas->sortByDesc('qtd_alunos');
            } else {
                $salas = $salas->sortByDesc('data_hora_inicio');
            }

            $salas = $salas->values()->all();
        } else {
            session()->flash('error', 'Não foi possível carregar as salas. Tente novamente.');
        }
        $title    = '<i class="fas fa-chalkboard-teacher"></i> Sala de aula';
        $subtitle = 'Encontre salas de aula ao vivo ou agendadas, filtre por matéria e participe das aulas que mais te interessam';
        return view('aluno.salas.index', compact('salas', 'materias', 'filters', 'title', 'subtitle'));
    }

    // ─── SHOW - DETALHES DE UMA SALA ────────────────────────────────────────

    public function show(int $id)
    {
        $data = $this->apiGet("SalaAula/RetornaSalaAulaPorId/{$id}");

        if (is_null($data)) {
            return redirect()->route('aluno.salas.index')
                ->with('error', 'Sala não encontrada.');
        }

        $sala      = $this->normalizeSala($data);
        $professor = null;

        if (!empty($data['idProfessor'])) {
            $professor = $this->apiGet("Usuario/RetornaUsuarioPorId/{$data['idProfessor']}");
        }

        if (!empty($data['idMateria'])) {
            $materias      = $this->apiGet('Materia/ListarMaterias') ?? [];
            $materia       = collect($materias)->firstWhere('idMateria', $data['idMateria']);
            $sala->materia = $materia['nomeMateria'] ?? '—';
        }

        // Quantidade atual de alunos na sala
        $qtdAlunos = 0;
        $qtdData   = $this->apiGet("AlunoSala/RetornaQtdAlunosSala/{$id}");
        if (!is_null($qtdData)) {
            if (is_array($qtdData)) {
                $qtdAlunos = (int) ($qtdData['quantidade'] ?? $qtdData['count'] ?? 0);
            } else {
                $qtdAlunos = (int) $qtdData;
            }
        }
        $sala->qtd_alunos_atual = $qtdAlunos;
        $title    = '<i class="fas fa-chalkboard-teacher"></i> Sala de aula';
        $subtitle = 'Veja os detalhes da sala, informações sobre o professor e a matéria, e participe das aulas que mais te interessam';
        return view('aluno.salas.show', compact('sala', 'professor', 'title', 'subtitle'));
    }

    // ─── AGUARDANDO - SALA DE ESPERA ────────────────────────────────────────

    public function aguardando(int $id)
    {
        $data = $this->apiGet("SalaAula/RetornaSalaAulaPorId/{$id}");

        if (is_null($data)) {
            return redirect()->route('aluno.salas.index')
                ->with('error', 'Sala não encontrada.');
        }

        $sala = $this->normalizeSala($data);

        if ($sala->status !== 'active') {
            return redirect()->route('aluno.salas.index')
                ->with('error', 'Esta sala não está ao vivo no momento.');
        }

        $nomeProfessor = null;
        if (!empty($data['idProfessor'])) {
            $professor     = $this->apiGet("Usuario/RetornaUsuarioPorId/{$data['idProfessor']}");
            $nomeProfessor = $professor['nome'] ?? $professor['name'] ?? 'Professor';
        }

        if (!empty($data['idMateria'])) {
            $materias      = $this->apiGet('Materia/ListarMaterias') ?? [];
            $materia       = collect($materias)->firstWhere('idMateria', $data['idMateria']);
            $sala->materia = $materia['nomeMateria'] ?? '—';
        }

        return view('aluno.salas.aguardando', compact('sala', 'nomeProfessor'));
    }

    // ─── CHECK LIBERADA - AJAX (polling da sala de espera) ──────────────────

    public function checkLiberada(int $id): \Illuminate\Http\JsonResponse
    {
        $data = $this->apiGet("SalaAula/RetornaSalaAulaPorId/{$id}");

        if (is_null($data) || ($data['status'] ?? '') !== 'active') {
            Log::info("[SalaAulaAlunoController] Check sala {$id} - Sala não existe ou não está ativa");
            return response()->json(['liberada' => false, 'encerrada' => true]);
        }

        $liberada = Cache::get("sala_{$id}_liberada", false);
        Log::info("[SalaAulaAlunoController] Check sala {$id} - Cache liberada: " . ($liberada ? 'true' : 'false'));

        return response()->json([
            'liberada' => (bool) $liberada,
            'encerrada' => false,
            'timestamp' => now()->toISOString()
        ]);
    }

    public function entrar(int $id): \Illuminate\Http\JsonResponse
    {
        $data = $this->apiGet("SalaAula/RetornaSalaAulaPorId/{$id}");
    
        if (is_null($data)) {
            return response()->json(['success' => false, 'message' => 'Sala não encontrada.'], 404);
        }
    
        $liberada = Cache::get("sala_{$id}_liberada", false);
        if (!$liberada) {
            return response()->json(['success' => false, 'message' => 'Sala ainda não liberada.'], 403);
        }
    
        // Verifica se já está cadastrado para evitar duplicata
        $registros = $this->apiGet("AlunoSala/RetornarAlunoSalaPorIdAluno/" . Auth::id());
        if ($registros) {
            $lista   = is_array($registros) && isset($registros[0]) ? $registros : [$registros];
            $existente = collect($lista)->first(fn($r) => ($r['idSalaAula'] ?? null) == $id);
            if ($existente) {
                return response()->json(['success' => true, 'already' => true]);
            }
        }
    
        $resultado = $this->apiPost('AlunoSala/CadastraAlunoSala', [
            'idAluno'     => Auth::id(),
            'idSalaAula'  => $id,
            'dataEntrada' => now()->toIso8601String(),
        ]);
    
        if (is_null($resultado)) {
            Log::warning("[SalaAulaAlunoController] Falha ao cadastrar AlunoSala para aluno " . Auth::id() . " sala {$id}");
        }
    
        return response()->json([
            'success' => true, // permite entrar mesmo se a API falhou (não bloqueia)
            'message' => 'Entrada registrada.',
        ]);
    }

    // ─── JOIN - CRIAR REGISTRO E ENTRAR NA SALA ─────────────────────────────

    public function join(Request $request, int $id)
    {
        Log::info("[SalaAulaAlunoController] Join iniciado - Sala: {$id}, User ID: " . (Auth::id() ?? 'null') . ", Session ID: " . session()->getId());

        // Verifica se a sala está liberada pelo professor
        $liberada = Cache::get("sala_{$id}_liberada", false);
        Log::info("[SalaAulaAlunoController] Join sala {$id} - Cache liberada: " . ($liberada ? 'true' : 'false'));

        if (!$liberada) {
            Log::warning("[SalaAulaAlunoController] Tentativa de join em sala não liberada: {$id}");
            return redirect()->route('aluno.salas.aguardando', $id)
                ->with('error', 'O professor ainda não liberou a entrada.');
        }

        $idAluno = Auth::id();
        if (!$idAluno) {
            Log::error("[SalaAulaAlunoController] Join sala {$id} - Usuário não autenticado");
            return redirect()->route('login')->with('error', 'Você precisa estar logado.');
        }

        Log::info("[SalaAulaAlunoController] Join sala {$id} - Verificando inscrições do aluno {$idAluno}");
        $enrollments = $this->apiGet("AlunoSala/RetornarAlunoSalaPorIdAluno/{$idAluno}");
        $existing = collect($enrollments)->firstWhere('idSalaAula', $id);

        if (!empty($existing['idAlunoSala'])) {
            Log::info("[SalaAulaAlunoController] Aluno {$idAluno} já está na sala {$id}");
            return redirect()->route('aluno.salas.video', $id)
                ->with('success', 'Você já está nessa aula.');
        }

        $dataHoraAtual = date('Y-m-d H:i:s');
        Log::info("[SalaAulaAlunoController] Join sala {$id} - Cadastrando aluno {$idAluno} na API");
        $resultado = $this->apiPost('AlunoSala/CadastraAlunoSala', [
            'idAluno'    => $idAluno,
            'idSalaAula' => $id,
            'joinedAt' => $dataHoraAtual,
        ]);

        if (is_null($resultado)) {
            Log::error("[SalaAulaAlunoController] Falha ao cadastrar AlunoSala para aluno {$idAluno} sala {$id}");
            return redirect()->route('aluno.salas.aguardando', $id)
                ->with('error', 'Não foi possível entrar na sala. Tente novamente.');
        }

        Log::info("[SalaAulaAlunoController] Aluno {$idAluno} entrou na sala {$id} com sucesso");
        return redirect()->route('aluno.salas.video', $id)
            ->with('success', 'Você entrou na aula!');
    }

    // ─── VIDEO AULA - ASSISTIR AULA AO VIVO ─────────────────────────────────

    public function videoAula(int $id)
    {
        $data = $this->apiGet("SalaAula/RetornaSalaAulaPorId/{$id}");
    
        if (is_null($data)) {
            return redirect()->route('aluno.salas.index')
                ->with('error', 'Sala não encontrada.');
        }
    
        $sala = $this->normalizeSala($data);
    
        if ($sala->status !== 'active') {
            return redirect()->route('aluno.salas.index')
                ->with('error', 'Esta sala não está ao vivo.');
        }
    
        // Nome da matéria
        if (isset($sala->idMateria)) {
            $materias      = $this->apiGet('Materia/ListarMaterias') ?? [];
            $materia       = collect($materias)->firstWhere('idMateria', $sala->idMateria);
            $sala->materia = $materia['nomeMateria'] ?? '—';
        }
    
        // Conteúdo vinculado
        $conteudo = null;
        if (!empty($data['idConteudo'])) {
            $conteudo = $this->apiGet("Conteudo/RetornaConteudoPorId/{$data['idConteudo']}");
        }
    
        // Liberação e nome do professor
        $liberada      = Cache::get("sala_{$id}_liberada", false);
        $nomeProfessor = $data['nomeProfessor'] ?? 'Professor';
    
        return view('aluno.salas.video-aula', compact('sala', 'conteudo', 'liberada', 'nomeProfessor'));
    }


    // ─── LEAVE - SAIR DA SALA ───────────────────────────────────────────────

    public function leave(int $id)
    {
        $registros = $this->apiGet("AlunoSala/RetornarAlunoSalaPorIdAluno/" . Auth::id());
    
        if ($registros) {
            $lista   = is_array($registros) && isset($registros[0]) ? $registros : [$registros];
            $registro = collect($lista)->first(fn($r) => ($r['idSalaAula'] ?? null) == $id);
    
            if ($registro && isset($registro['idAlunoSala'])) {
                $this->apiDelete("AlunoSala/DeletarAlunoSala/{$registro['idAlunoSala']}");
            }
        }
    
        return redirect()->route('aluno.salas.index')
            ->with('success', 'Você saiu da aula.');
    }

    // ─── RATING - AVALIAR PROFESSOR ─────────────────────────────────────────

    public function rating(Request $request, int $id)
    {
        $request->validate([
            'nota'       => 'required|integer|min:1|max:5',
            'comentario' => 'nullable|string|max:500',
        ], [
            'nota.required' => 'A nota é obrigatória.',
            'nota.min'      => 'A nota mínima é 1.',
            'nota.max'      => 'A nota máxima é 5.',
        ]);

        $idAluno   = Auth::id();
        $resultado = $this->apiPost(
            "Avaliacao/AvaliarProfessor"
            . "?idProfessor={$id}"
            . "&idAluno={$idAluno}"
            . "&nota={$request->nota}"
            . "&comentario=" . urlencode($request->comentario ?? ''),
            []
        );

        if (is_null($resultado)) {
            return back()->with('error', 'Não foi possível enviar a avaliação. Tente novamente.');
        }

        return back()->with('success', 'Avaliação enviada com sucesso!');
    }
    
    public function membros(int $id): \Illuminate\Http\JsonResponse
    {
        $alunosSala = $this->apiGet("AlunoSala/RetornaAlunoSalaPorIdSalaAula/{$id}") ?? [];
    
        if (!is_array($alunosSala) || (count($alunosSala) && !isset($alunosSala[0]))) {
            $alunosSala = isset($alunosSala['idAlunoSala']) ? [$alunosSala] : [];
        }
    
        // Busca info do professor via sala
        $sala      = $this->apiGet("SalaAula/RetornaSalaAulaPorId/{$id}");
        $professor = $sala ? [
            'id'   => $sala['idProfessor'] ?? null,
            'nome' => $sala['nomeProfessor'] ?? 'Professor',
            'role' => 'professor',
        ] : null;
    
        return response()->json([
            'professor' => $professor,
            'alunos'    => collect($alunosSala)->map(fn($a) => [
                'id'   => $a['idAluno']   ?? null,
                'nome' => $a['nomeAluno'] ?? 'Aluno',
                'role' => 'aluno',
            ])->values(),
            'total' => count($alunosSala),
        ]);
    }

}