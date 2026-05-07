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

        return view('aluno.salas.index', compact('salas', 'materias'));
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

        return view('aluno.salas.show', compact('sala', 'professor'));
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
        $liberada = Cache::get("sala_{$id}_liberada", false);
        return response()->json(['liberada' => (bool) $liberada]);
    }

    // ─── JOIN - CRIAR REGISTRO E ENTRAR NA SALA ─────────────────────────────

    public function join(Request $request, int $id)
    {
        // Verifica se a sala está liberada pelo professor
        if (!Cache::get("sala_{$id}_liberada", false)) {
            return redirect()->route('aluno.salas.aguardando', $id)
                ->with('error', 'O professor ainda não liberou a entrada.');
        }

        $idAluno = Auth::id();
        $enrollments = $this->apiGet("AlunoSala/RetornarAlunoSalaPorIdAluno/{$idAluno}");
        $existing = collect($enrollments)->firstWhere('idSalaAula', $id);

        if (!empty($existing['idAlunoSala'])) {
            return redirect()->route('aluno.salas.video', $id)
                ->with('success', 'Você já está nessa aula.');
        }
        $dataHoraAtual = date('Y-m-d H:i:s');
        $resultado = $this->apiPost('AlunoSala/CadastraAlunoSala', [
            'idAluno'    => $idAluno,
            'idSalaAula' => $id,
            'joinedAt' => $dataHoraAtual,
        ]);

        if (is_null($resultado)) {
            return redirect()->route('aluno.salas.aguardando', $id)
                ->with('error', 'Não foi possível entrar na sala. Tente novamente.');
        }

        return redirect()->route('aluno.salas.video', $id)
            ->with('success', 'Você entrou na aula!');
    }

    // ─── VIDEO AULA - ASSISTIR AULA AO VIVO ─────────────────────────────────

    public function video(int $id)
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

        // Busca conteúdo vinculado à sala
        $conteudo = null;
        if (!empty($data['idConteudo'])) {
            $conteudo = $this->apiGet("Conteudo/RetornaConteudoPorId/{$data['idConteudo']}");
            if (is_null($conteudo)) {
                Log::warning("[SalaAulaAlunoController] Conteúdo {$data['idConteudo']} não encontrado para sala {$id}");
            }
        }

        // Quantidade de alunos na sala
        $qtdAlunos = 0;
        $qtdData   = $this->apiGet("AlunoSala/RetornaQtdAlunosSala/{$id}");
        if (!is_null($qtdData)) {
            if (is_array($qtdData)) {
                $qtdAlunos = (int) ($qtdData['quantidade'] ?? $qtdData['count'] ?? 0);
            } else {
                $qtdAlunos = (int) $qtdData;
            }
        }

        return view('aluno.salas.video-aula', compact('sala', 'nomeProfessor', 'conteudo', 'qtdAlunos'));
    }

    // ─── LEAVE - SAIR DA SALA ───────────────────────────────────────────────

    public function leave(int $id)
    {
        $idAluno = Auth::id();

        $enrollments = $this->apiGet("AlunoSala/RetornarAlunoSalaPorIdAluno/{$idAluno}");
        $alunoSala   = collect($enrollments)->firstWhere('idSalaAula', $id);

        if (empty($alunoSala['idAlunoSala'])) {
            return redirect()->route('aluno.dashboard')
                ->with('error', 'Não foi possível sair da sala. Matrícula não encontrada.');
        }

        $ok = $this->apiDelete("AlunoSala/DeletarAlunoSala/{$alunoSala['idAlunoSala']}");

        if (!$ok) {
            return redirect()->route('aluno.dashboard')
                ->with('error', 'Não foi possível sair da sala. Tente novamente.');
        }

        return redirect()->route('aluno.dashboard')
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

    // ─── HISTÓRICO ──────────────────────────────────────────────────────────

    public function historico(Request $request)
    {
        $idAluno = Auth::id();
        $page    = (int) $request->get('page', 1);
        $perPage = 10;

        $data     = $this->apiGet("AlunoSala/RetornarAlunoSalaPorIdAluno/{$idAluno}");
        $materias = $this->apiGet('Materia/ListarMaterias') ?? [];

        if (is_null($data)) {
            session()->flash('error', 'Não foi possível carregar o histórico. Tente novamente.');

            $aulas = new LengthAwarePaginator(
                collect(), 0, $perPage, $page,
                ['path' => $request->url()]
            );

            return view('aluno.historico.index', compact('aulas'));
        }

        $materiasMap = collect($materias)->keyBy('idMateria');

        $items = collect($data)->map(function ($item) use ($materiasMap) {
            $sala = $this->normalizeSala($item);
            if (isset($sala->idMateria) && $materiasMap->has($sala->idMateria)) {
                $sala->materia = $materiasMap->get($sala->idMateria)['nomeMateria'] ?? '—';
            }
            return $sala;
        });

        $aulas = new LengthAwarePaginator(
            $items->forPage($page, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            ['path' => $request->url()]
        );

        return view('aluno.historico.index', compact('aulas'));
    }

    // ─── HISTÓRICO SHOW ─────────────────────────────────────────────────────

    public function historicoShow(int $id)
    {
        $data = $this->apiGet("SalaAula/RetornaSalaAulaPorId/{$id}");

        if (is_null($data)) {
            return redirect()->route('aluno.historico')
                ->with('error', 'Registro não encontrado.');
        }

        $sala = $this->normalizeSala($data);

        if (!empty($data['idMateria'])) {
            $materias      = $this->apiGet('Materia/ListarMaterias') ?? [];
            $materia       = collect($materias)->firstWhere('idMateria', $data['idMateria']);
            $sala->materia = $materia['nomeMateria'] ?? '—';
        }

        return view('aluno.historico.show', compact('sala'));
    }

    // ─── SIMULADOS ──────────────────────────────────────────────────────────

    public function simulados(Request $request)
    {
        $idAluno = Auth::id();
        $page    = (int) $request->get('page', 1);
        $perPage = 10;

        $data      = $this->apiGet("Simulado/RetornaSimuladosPorUsuario/{$idAluno}");
        $simulados = [];

        if (!is_null($data)) {
            $simulados = is_array($data) && isset($data[0])
                ? $data
                : (isset($data['idSimulado']) ? [$data] : []);
        } else {
            session()->flash('error', 'Não foi possível carregar os simulados. Tente novamente.');
        }

        $items = collect($simulados);

        $simulados = new LengthAwarePaginator(
            $items->forPage($page, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            ['path' => $request->url()]
        );

        return view('aluno.simulados.index', compact('simulados'));
    }

    // ─── SIMULADO SHOW ──────────────────────────────────────────────────────

    public function simuladoShow(int $id)
    {
        $data = $this->apiGet("Simulado/RetornaSimuladoPorId/{$id}");

        if (is_null($data)) {
            return redirect()->route('aluno.simulados')
                ->with('error', 'Simulado não encontrado.');
        }

        $simulado = (object) $data;

        if (!empty($data['idMateria'])) {
            $materias          = $this->apiGet('Materia/ListarMaterias') ?? [];
            $materia           = collect($materias)->firstWhere('idMateria', $data['idMateria']);
            $simulado->materia = $materia['nomeMateria'] ?? '—';
        }

        return view('aluno.simulados.show', compact('simulado'));
    }
}