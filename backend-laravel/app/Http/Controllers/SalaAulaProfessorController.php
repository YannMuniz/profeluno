<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class SalaAulaProfessorController extends Controller
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('DOTNET_API_URL', 'http://profeluno_dotnet:9000');
    }

    // HELPERS DE API
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

            Log::warning("[SalaAulaController] GET {$endpoint} retornou {$response->status()}", [
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error("[SalaAulaController] GET {$endpoint} falhou: " . $e->getMessage());
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

            Log::warning("[SalaAulaController] POST {$endpoint} retornou {$response->status()}", [
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error("[SalaAulaController] POST {$endpoint} falhou: " . $e->getMessage());
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

            Log::warning("[SalaAulaController] PUT {$endpoint} retornou {$response->status()}", [
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error("[SalaAulaController] PUT {$endpoint} falhou: " . $e->getMessage());
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

            Log::warning("[SalaAulaController] DELETE {$endpoint} retornou {$response->status()}", [
                'body' => $response->body(),
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error("[SalaAulaController] DELETE {$endpoint} falhou: " . $e->getMessage());
            return false;
        }
    }

    // NORMALIZE
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
        $sala->avaliacao  = $sala->avaliacao ?? null;
        $sala->descricao  = $sala->descricao ?? null;
        $sala->status     = $sala->status    ?? 'pending';
        $sala->material   = $sala->material  ?? false;

        return $sala;
    }

    // INDEX
    public function index(Request $request)
    {
        $page    = (int) $request->get('page', 1);
        $perPage = 10;

        $data     = $this->apiGet("SalaAula/RetornaSalaAulaPorProfessor/" . Auth::id());
        $materias = $this->apiGet("Materia/ListarMaterias") ?? [];

        $materiasMap = collect($materias)->keyBy('idMateria');

        if (is_null($data)) {
            session()->flash('error', 'Não foi possível carregar as salas. Tente novamente.');

            $salas = new LengthAwarePaginator(
                collect(), 0, $perPage, $page,
                ['path' => $request->url()]
            );

            return view('professor.salas.index', [
                'salas'           => $salas,
                'salasAtivas'     => collect(),
                'salasAgendadas'  => collect(),
                'salasConcluidas' => collect(),
                'salaAtiva'       => null,
                'materias'        => $materias,
            ]);
        }

        $items = collect($data)->map(function ($i) use ($materiasMap) {
            $sala = $this->normalizeSala($i);

            if (isset($sala->idMateria) && $materiasMap->has($sala->idMateria)) {
                $sala->materia = $materiasMap->get($sala->idMateria)['nomeMateria'] ?? '—';
            }

            return $sala;
        });

        $salas = new LengthAwarePaginator(
            $items->forPage($page, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            ['path' => $request->url()]
        );

        $salasAtivas     = $items->where('status', 'active')->values();
        $salasAgendadas  = $items->where('status', 'pending')->values();
        $salasConcluidas = $items->where('status', 'completed')->values();
        $salaAtiva       = $salasAtivas->first();

        $title    = '<i class="fas fa-chalkboard-teacher"></i> Sala de aula';
        $subtitle = 'Gerencie suas salas de aula, inicie aulas ao vivo e acompanhe o desempenho dos alunos';

        return view('professor.salas.index', compact(
            'salas', 'salasAtivas', 'salasAgendadas',
            'salasConcluidas', 'salaAtiva', 'materias', 'title', 'subtitle'
        ));
    }

    // CREATE
    public function create()
    {
        $materias  = $this->apiGet('Materia/ListarMaterias') ?? [];
        $conteudos = $this->apiGet('Conteudo/RetornaConteudoPorIdProfessor/' . Auth::id()) ?? [];
        $conteudos = is_array($conteudos) && isset($conteudos[0])
            ? $conteudos
            : (isset($conteudos['idConteudo']) ? [$conteudos] : []);

        $simulados = $this->apiGet("Simulado/RetornaSimuladosPorUsuario/" . Auth::id()) ?? [];
        $simulados = is_array($simulados) && isset($simulados[0])
            ? $simulados
            : (isset($simulados['idSimulado']) ? [$simulados] : []);

        if (!$materias) {
            session()->flash('warning', 'Não foi possível carregar as matérias.');
        }
        $title    = '<i class="fas fa-chalkboard-teacher"></i> Sala de aula';
        $subtitle = 'Crie uma nova sala de aula, defina a matéria, adicione conteúdo ou simulado e prepare-se para ensinar!';
        return view('professor.salas.create', compact('materias', 'conteudos', 'simulados', 'title', 'subtitle'));
    }

    // STORE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo'           => 'required|string|max:255',
            'descricao'        => 'nullable|string',
            'materia_id'       => 'required|numeric',
            'max_alunos'       => 'required|integer|min:1|max:500',
            'status'           => 'required|in:active,pending',
            'data_hora_inicio' => 'nullable|date',
            'data_hora_fim'    => 'nullable|date',
            // Removido nullable|integer — strings vazias causavam falha
            'conteudo_id'      => 'nullable',
            'simulado_id'      => 'nullable',
        ], [
            'titulo.required'     => 'O título é obrigatório.',
            'materia_id.required' => 'Selecione uma matéria.',
            'max_alunos.required' => 'Informe a quantidade máxima de alunos.',
            'status.in'           => 'Status inválido.',
        ]);
    
        // Normaliza valores vazios para null
        $conteudoId = !empty($validated['conteudo_id']) ? (int) $validated['conteudo_id'] : null;
        $simuladoId = !empty($validated['simulado_id']) ? (int) $validated['simulado_id'] : null;
    
        // Se for ao vivo (active), dataHoraInicio = agora
        if ($validated['status'] === 'active') {
            $dataHoraInicio = now()->toIso8601String();
        } else {
            $dataHoraInicio = !empty($validated['data_hora_inicio'])
                ? $validated['data_hora_inicio']
                : null;
        }
    
        $dataHoraFim = !empty($validated['data_hora_fim']) ? $validated['data_hora_fim'] : null;
    
        $sala = $this->apiPost('SalaAula/CadastrarSalaAula', [
            'titulo'         => $validated['titulo'],
            'descricao'      => $validated['descricao'] ?? null,
            'idProfessor'    => Auth::id(),
            'maxAlunos'      => (int) $validated['max_alunos'],
            'dataHoraInicio' => $dataHoraInicio,
            'dataHoraFim'    => $dataHoraFim,
            'idMateria'      => (int) $validated['materia_id'],
            'status'         => $validated['status'],
            'idConteudo'     => $conteudoId,
            'idSimulado'     => $simuladoId,
        ]);
    
        if (is_null($sala)) {
            return back()->withInput()
                ->withErrors(['api' => 'Falha ao criar a sala. Tente novamente.']);
        }
    
        if ($validated['status'] === 'active') {
            $salaId = $sala['idSalaAula'] ?? null;
            if ($salaId) {
                return redirect()
                    ->route('professor.salas.video-aula', $salaId)
                    ->with('success', 'Sala criada e iniciada!');
            }
        }
    
        return redirect()
            ->route('professor.salas.index')
            ->with('success', 'Sala criada com sucesso!');
    }

    // SHOW
    public function show(int $id)
    {
        $data = $this->apiGet("SalaAula/RetornaSalaAulaPorId/{$id}");

        if (is_null($data)) {
            return redirect()->route('professor.salas.index')
                ->with('error', 'Sala não encontrada.');
        }

        $sala = $this->normalizeSala($data);

        if (isset($sala->idMateria)) {
            $materias      = $this->apiGet('Materia/ListarMaterias') ?? [];
            $materia       = collect($materias)->firstWhere('idMateria', $sala->idMateria);
            $sala->materia = $materia['nomeMateria'] ?? '—';
        }
        $title    = '<i class="fas fa-chalkboard-teacher"></i> Sala de aula';
        $subtitle = 'Gerencie suas salas de aula, inicie aulas ao vivo e acompanhe o desempenho dos alunos';

        return view('professor.salas.show', compact('sala', 'title', 'subtitle'));
    }

    // EDIT
    public function edit(int $id)
    {
        $data = $this->apiGet("SalaAula/RetornaSalaAulaPorId/{$id}");

        if (is_null($data)) {
            return redirect()->route('professor.salas.index')
                ->with('error', 'Sala não encontrada.');
        }

        $sala      = $this->normalizeSala($data);
        $materias  = $this->apiGet('Materia/ListarMaterias') ?? [];
        $conteudos = $this->apiGet('Conteudo/RetornaConteudoPorIdProfessor/' . Auth::id()) ?? [];
        $conteudos = is_array($conteudos) && isset($conteudos[0])
            ? $conteudos
            : (isset($conteudos['idConteudo']) ? [$conteudos] : []);

        $simulados = $this->apiGet("Simulado/RetornaSimuladosPorUsuario/" . Auth::id()) ?? [];
        $simulados = is_array($simulados) && isset($simulados[0])
            ? $simulados
            : (isset($simulados['idSimulado']) ? [$simulados] : []);

        return view('professor.salas.edit', compact('sala', 'materias', 'conteudos', 'simulados'));
    }

    // UPDATE
    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'titulo'           => 'required|string|max:255',
            'descricao'        => 'nullable|string',
            'materia_id'       => 'required|integer',
            'max_alunos'       => 'required|integer|min:1|max:500',
            'status'           => 'required|in:active,completed,pending',
            'data_hora_inicio' => 'nullable|date',
            'data_hora_fim'    => 'nullable|date',
            'conteudo_id'      => 'nullable|integer',
            'simulado_id'      => 'nullable|integer',
        ], [
            'titulo.required'     => 'O título é obrigatório.',
            'materia_id.required' => 'Selecione uma matéria.',
            'max_alunos.required' => 'Informe a quantidade máxima de alunos.',
            'status.in'           => 'Status inválido.',
        ]);

        $dadosAtuais = $this->apiGet("SalaAula/RetornaSalaAulaPorId/{$id}");

        if (is_null($dadosAtuais)) {
            return back()->withInput()
                ->withErrors(['api' => 'Não foi possível recuperar os dados da sala.']);
        }

        // Se mudou para active e não tinha início definido, usar agora
        if ($validated['status'] === 'active') {
            $dataHoraInicio = now()->toIso8601String();
        } else {
            $dataHoraInicio = $validated['data_hora_inicio']
                ?? $dadosAtuais['dataHoraInicio']
                ?? null;
        }

        $resultado = $this->apiPut('SalaAula/AtualizarSalaAula', [
            'idSalaAula'     => $id,
            'titulo'         => $validated['titulo'],
            'descricao'      => $validated['descricao'] ?? null,
            'idProfessor'    => Auth::id(),
            'maxAlunos'      => (int) $validated['max_alunos'],
            'dataHoraInicio' => $dataHoraInicio,
            'dataHoraFim'    => $validated['data_hora_fim'] ?? $dadosAtuais['dataHoraFim'] ?? null,
            'idMateria'      => (int) $validated['materia_id'],
            'status'         => $validated['status'],
            'idConteudo'     => $validated['conteudo_id'] ? (int) $validated['conteudo_id'] : null,
            'idSimulado'     => $validated['simulado_id'] ? (int) $validated['simulado_id'] : null,
            'url'            => $dadosAtuais['url']      ?? null,
            'nomeSala'       => $dadosAtuais['nomeSala'] ?? null,
        ]);

        if (is_null($resultado)) {
            return back()->withInput()
                ->withErrors(['api' => 'Falha ao atualizar a sala. Tente novamente.']);
        }

        return redirect()->route('professor.salas.index')
            ->with('success', 'Sala atualizada com sucesso!');
    }

    // DESTROY
    public function destroy(int $id)
    {
        // Verifica se a sala pertence ao professor antes de deletar
        $data = $this->apiGet("SalaAula/RetornaSalaAulaPorId/{$id}");

        if (is_null($data)) {
            return redirect()->route('professor.salas.index')
                ->with('error', 'Sala não encontrada.');
        }

        // Não permite deletar sala ativa
        if (($data['status'] ?? '') === 'active') {
            return redirect()->route('professor.salas.index')
                ->with('error', 'Não é possível deletar uma sala que está ao vivo. Encerre-a primeiro.');
        }

        $ok = $this->apiDelete("SalaAula/DeletarSalaAula/{$id}");

        if (!$ok) {
            return redirect()->route('professor.salas.index')
                ->with('error', 'Não foi possível deletar a sala. Tente novamente.');
        }

        return redirect()->route('professor.salas.index')
            ->with('success', 'Sala deletada com sucesso!');
    }

    // INICIAR
    public function iniciar(int $id)
    {
        // Verifica se já existe uma sala ativa para este professor
        $todasSalas = $this->apiGet("SalaAula/RetornaSalaAulaPorProfessor/" . Auth::id());

        if (!is_null($todasSalas)) {
            $salaAtivaExistente = collect($todasSalas)
                ->first(fn($s) => ($s['status'] ?? '') === 'active' && ($s['idSalaAula'] ?? 0) !== $id);

            if ($salaAtivaExistente) {
                return redirect()->route('professor.salas.index')
                    ->with('error', 'Você já possui uma aula ao vivo ativa. Encerre-a antes de iniciar outra.');
            }
        }

        $dadosAtuais = $this->apiGet("SalaAula/RetornaSalaAulaPorId/{$id}");

        if (is_null($dadosAtuais)) {
            return redirect()->route('professor.salas.index')
                ->with('error', 'Sala não encontrada.');
        }

        if (($dadosAtuais['status'] ?? '') !== 'pending') {
            return redirect()->route('professor.salas.index')
                ->with('error', 'Esta sala não pode ser iniciada.');
        }

        $resultado = $this->apiPut('SalaAula/AtualizarSalaAula', [
            'idSalaAula'     => $id,
            'titulo'         => $dadosAtuais['titulo'],
            'descricao'      => $dadosAtuais['descricao']      ?? null,
            'idProfessor'    => $dadosAtuais['idProfessor'],
            'maxAlunos'      => $dadosAtuais['maxAlunos'],
            'dataHoraInicio' => now()->toIso8601String(), // marca o início real agora
            'dataHoraFim'    => $dadosAtuais['dataHoraFim']    ?? null,
            'idMateria'      => $dadosAtuais['idMateria'],
            'status'         => 'active',
            'idConteudo'     => $dadosAtuais['idConteudo']     ?? null,
            'idSimulado'     => $dadosAtuais['idSimulado']     ?? null,
            'url'            => $dadosAtuais['url']            ?? null,
            'nomeSala'       => $dadosAtuais['nomeSala']       ?? null,
        ]);

        if (is_null($resultado)) {
            return redirect()->route('professor.salas.index')
                ->with('error', 'Não foi possível iniciar a sala. Tente novamente.');
        }

        // Redireciona para a sala de vídeo
        return redirect()->route('professor.salas.video-aula', $id)
            ->with('success', 'Aula iniciada!');
    }

    // ENCERRAR
    public function encerrar(int $id)
    {
        $dadosAtuais = $this->apiGet("SalaAula/RetornaSalaAulaPorId/{$id}");

        if (is_null($dadosAtuais)) {
            return redirect()->route('professor.salas.index')
                ->with('error', 'Sala não encontrada.');
        }

        if (($dadosAtuais['status'] ?? '') !== 'active') {
            return redirect()->route('professor.salas.index')
                ->with('error', 'Esta sala não está ativa.');
        }

        $resultado = $this->apiPut('SalaAula/AtualizarSalaAula', [
            'idSalaAula'     => $id,
            'titulo'         => $dadosAtuais['titulo'],
            'descricao'      => $dadosAtuais['descricao']      ?? null,
            'idProfessor'    => $dadosAtuais['idProfessor'],
            'maxAlunos'      => $dadosAtuais['maxAlunos'],
            'dataHoraInicio' => $dadosAtuais['dataHoraInicio'] ?? null,
            'dataHoraFim'    => now()->toIso8601String(), // marca o fim real agora
            'idMateria'      => $dadosAtuais['idMateria'],
            'status'         => 'completed',
            'idConteudo'     => $dadosAtuais['idConteudo']     ?? null,
            'idSimulado'     => $dadosAtuais['idSimulado']     ?? null,
            'url'            => $dadosAtuais['url']            ?? null,
            'nomeSala'       => $dadosAtuais['nomeSala']       ?? null,
        ]);

        if (is_null($resultado)) {
            return redirect()->route('professor.salas.index')
                ->with('error', 'Não foi possível encerrar a sala. Tente novamente.');
        }

        return redirect()->route('professor.salas.index')
            ->with('success', 'Aula encerrada com sucesso!');
    }

     // VIDEO AULA (professor)
    public function videoAula(int $id)
    {
        $data = $this->apiGet("SalaAula/RetornaSalaAulaPorId/{$id}");
 
        if (is_null($data)) {
            return redirect()->route('professor.salas.index')
                ->with('error', 'Sala não encontrada.');
        }
 
        $sala = $this->normalizeSala($data);
 
        // Só professor dono pode entrar
        if (($data['idProfessor'] ?? null) != Auth::id()) {
            return redirect()->route('professor.salas.index')
                ->with('error', 'Acesso não autorizado.');
        }
 
        // Sala precisa estar ativa
        if ($sala->status !== 'active') {
            return redirect()->route('professor.salas.index')
                ->with('error', 'Esta sala não está ao vivo.');
        }
 
        // Resolve nome da matéria
        if (isset($sala->idMateria)) {
            $materias      = $this->apiGet('Materia/ListarMaterias') ?? [];
            $materia       = collect($materias)->firstWhere('idMateria', $sala->idMateria);
            $sala->materia = $materia['nomeMateria'] ?? '—';
        }
 
        // Busca conteúdo vinculado
        $conteudo = null;
        if (!empty($data['idConteudo'])) {
            $conteudo = $this->apiGet("Conteudo/RetornaConteudoPorId/{$data['idConteudo']}");
            if (is_null($conteudo)) {
                Log::warning("[SalaAulaProfessorController] Conteúdo {$data['idConteudo']} não encontrado para sala {$id}");
            }
        }
        // Verifica se o professor já liberou os alunos
        $liberada = Cache::get("sala_{$id}_liberada", false);
 
        return view('professor.salas.video-aula', compact('sala', 'conteudo', 'liberada'));
    }

    // LIBERAR ALUNOS - armazena flag no cache (validade: 6h)
    public function liberarAlunos(int $id)
    {
        $data = $this->apiGet("SalaAula/RetornaSalaAulaPorId/{$id}");

        if (is_null($data)) {
            Log::error("[SalaAulaProfessorController] Tentativa de liberar sala inexistente: {$id}");
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Sala não encontrada.'], 404);
            }
            return redirect()->route('professor.salas.index')->with('error', 'Sala não encontrada.');
        }

        if (($data['idProfessor'] ?? null) != Auth::id()) {
            Log::warning("[SalaAulaProfessorController] Tentativa não autorizada de liberar sala {$id} por usuário " . Auth::id());
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Acesso não autorizado.'], 403);
            }
            return redirect()->route('professor.salas.index')->with('error', 'Acesso não autorizado.');
        }

        // Verificar se já está liberada
        $jaLiberada = Cache::get("sala_{$id}_liberada", false);
        if ($jaLiberada) {
            Log::info("[SalaAulaProfessorController] Sala {$id} já estava liberada");
        } else {
            Log::info("[SalaAulaProfessorController] Liberando sala {$id} para alunos");
        }

        Cache::put("sala_{$id}_liberada", true, now()->addHours(6));

        // Verificar se foi setado corretamente
        $verificar = Cache::get("sala_{$id}_liberada", false);
        Log::info("[SalaAulaProfessorController] Cache verificado após set: " . ($verificar ? 'true' : 'false'));

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Alunos liberados!']);
        }

        return redirect()->route('professor.salas.video-aula', $id)
            ->with('success', 'Alunos liberados para entrar na sala!');
    }
    
    // CONTAGEM DE ALUNOS NA SALA - endpoint AJAX
    public function contagemAlunos(int $id): \Illuminate\Http\JsonResponse
    {
        $data = $this->apiGet("AlunoSala/RetornaQtdAlunosSala/{$id}");
 
        if (is_null($data)) {
            return response()->json(['count' => 0]);
        }
 
        // A API pode retornar o número diretamente ou num campo
        $count = is_array($data)
            ? ($data['quantidade'] ?? $data['count'] ?? $data['total'] ?? 0)
            : (int) $data;
 
        return response()->json(['count' => $count]);
    }

    // MEMBROS — retorna professor + alunos na sala (AJAX)
    public function membros(int $id): \Illuminate\Http\JsonResponse
    {
        $alunosSala = $this->apiGet("AlunoSala/RetornaAlunoSalaPorIdSalaAula/{$id}") ?? [];
    
        // Normaliza para array de arrays
        if (!is_array($alunosSala) || (count($alunosSala) && !isset($alunosSala[0]))) {
            $alunosSala = isset($alunosSala['idAlunoSala']) ? [$alunosSala] : [];
        }
    
        return response()->json([
            'professor' => [
                'id'   => Auth::id(),
                'nome' => session('user_nome') ?? (Auth::user()->name ?? 'Professor'),
                'role' => 'professor',
            ],
            'alunos' => collect($alunosSala)->map(fn($a) => [
                'id'   => $a['idAluno']   ?? null,
                'nome' => $a['nomeAluno'] ?? 'Aluno',
                'role' => 'aluno',
            ])->values(),
            'total' => count($alunosSala),
        ]);
    }

    // REFRESH CONTEÚDOS — retorna JSON para refresh AJAX no create
    public function refreshConteudos(): \Illuminate\Http\JsonResponse
    {
        $conteudos = $this->apiGet('Conteudo/RetornaConteudoPorIdProfessor/' . Auth::id()) ?? [];
        $conteudos = is_array($conteudos) && isset($conteudos[0])
            ? $conteudos
            : (isset($conteudos['idConteudo']) ? [$conteudos] : []);
    
        return response()->json($conteudos);
    }
    
    // REFRESH SIMULADOS — retorna JSON para refresh AJAX no create
    public function refreshSimulados(): \Illuminate\Http\JsonResponse
    {
        $simulados = $this->apiGet("Simulado/RetornaSimuladosPorUsuario/" . Auth::id()) ?? [];
        $simulados = is_array($simulados) && isset($simulados[0])
            ? $simulados
            : (isset($simulados['idSimulado']) ? [$simulados] : []);
    
        return response()->json($simulados);
    }
}