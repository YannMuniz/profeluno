<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SimuladoController extends Controller
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('DOTNET_API_URL', 'http://profeluno_dotnet:9000');
    }

    public function index()
    {
        // ── Dados fictícios (substituir por chamada à API .NET futuramente) ──
        $simulados = [
            [
                'id'           => 1,
                'titulo'       => 'Simulado — Equações do 2º Grau',
                'descricao'    => 'Avaliação com 15 questões de múltipla escolha',
                'sala'         => 'Aula de Matemática',
                'materia'      => 'Matemática',
                'qtd_questoes' => 15,
                'situacao'     => 1,
                'criado_em'    => '2026-03-20T10:00:00',
            ],
            [
                'id'           => 2,
                'titulo'       => 'Simulado — Leis de Newton',
                'descricao'    => '10 questões sobre dinâmica e cinemática',
                'sala'         => 'Aula de Física',
                'materia'      => 'Física',
                'qtd_questoes' => 10,
                'situacao'     => 1,
                'criado_em'    => '2026-03-21T14:30:00',
            ],
            [
                'id'           => 3,
                'titulo'       => 'Simulado — Segunda Guerra Mundial',
                'descricao'    => '20 questões sobre o contexto histórico',
                'sala'         => 'Aula de História',
                'materia'      => 'História',
                'qtd_questoes' => 20,
                'situacao'     => 1,
                'criado_em'    => '2026-03-19T09:00:00',
            ],
            [
                'id'           => 4,
                'titulo'       => 'Simulado — Tabela Periódica',
                'descricao'    => 'Questões sobre elementos e ligações químicas',
                'sala'         => 'Aula de Química',
                'materia'      => 'Química',
                'qtd_questoes' => 8,
                'situacao'     => 0,
                'criado_em'    => '2026-03-17T11:00:00',
            ],
            [
                'id'           => 5,
                'titulo'       => 'Simulado — Análise Sintática',
                'descricao'    => 'Exercícios de gramática e interpretação',
                'sala'         => 'Aula de Português',
                'materia'      => 'Português',
                'qtd_questoes' => 12,
                'situacao'     => 1,
                'criado_em'    => '2026-03-22T16:00:00',
            ],
        ];

        $title = '<i class="fas fa-list-ol"></i> Simulados';
        $subtitle = 'Gerencie os simulados vinculados às suas salas de aula';

        return view('professor.simulado.index', compact('simulados', 'title', 'subtitle'));
    }

    public function create()
    {
        $ultimapagina = "<a href='" . route('professor.simulados.index') . "' class='back-link'>
            <i class='fas fa-arrow-left'></i>
            Voltar
        </a>";
        $title = '<i class="fas fa-plus"></i> Criar Simulado';
        $subtitle = 'Preencha os detalhes para criar um novo simulado';
        return view('professor.simulado.create', compact('title', 'subtitle', 'ultimapagina'));
    }

    public function store() 
    {
        // Lógica para salvar o simulado (futura integração com API .NET)
        return redirect()->route('professor.simulados.index')->with('success', 'Simulado criado com sucesso!');
    }
}