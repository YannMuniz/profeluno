<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConteudoController extends Controller
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('DOTNET_API_URL', 'http://profeluno_dotnet:9000');
    }

    public function index()
    {
        // ── Dados fictícios (substituir por chamada à API .NET futuramente) ──
        $conteudos = [
            [
                'id'        => 1,
                'titulo'    => 'Apostila de Equações do 2º Grau',
                'descricao' => 'Material completo com exercícios resolvidos',
                'sala'      => 'Aula de Matemática',
                'tipo'      => 'pdf',
                'situacao'  => 1,
                'criado_em' => '2026-03-20T10:00:00',
            ],
            [
                'id'        => 2,
                'titulo'    => 'Slides — Leis de Newton',
                'descricao' => 'Apresentação com exemplos do cotidiano',
                'sala'      => 'Aula de Física',
                'tipo'      => 'slide',
                'situacao'  => 1,
                'criado_em' => '2026-03-21T14:30:00',
            ],
            [
                'id'        => 3,
                'titulo'    => 'Videoaula: Segunda Guerra Mundial',
                'descricao' => 'Resumo em vídeo com linha do tempo interativa',
                'sala'      => 'Aula de História',
                'tipo'      => 'video',
                'situacao'  => 1,
                'criado_em' => '2026-03-19T09:00:00',
            ],
            [
                'id'        => 4,
                'titulo'    => 'Simulado — Tabela Periódica',
                'descricao' => '10 questões de múltipla escolha',
                'sala'      => 'Aula de Química',
                'tipo'      => 'simulado',
                'situacao'  => 1,
                'criado_em' => '2026-03-22T16:00:00',
            ],
            [
                'id'        => 5,
                'titulo'    => 'Exercícios de Análise Sintática',
                'descricao' => 'Lista de exercícios para fixação',
                'sala'      => 'Aula de Português',
                'tipo'      => 'document',
                'situacao'  => 0,
                'criado_em' => '2026-03-17T11:00:00',
            ],
        ];

        $title = '<i class="fas fa-folder-open"></i> Conteúdos';
        $subtitle = 'Gerencie os materiais e simulados das suas salas de aula';
        return view('professor.conteudo.index', compact('conteudos', 'title', 'subtitle'));
    }

    public function create()
    {
        $ultimapagina = "<a href='" . route('professor.conteudo.index') . "' class='back-link'>
            <i class='fas fa-arrow-left'></i>
            Voltar
        </a>";
        $title = '<i class="fas fa-plus"></i> Novo Conteúdo';
        $subtitle = 'Adicione um conteúdo de apoio para a sala';
        return view('professor.conteudo.create', compact('title', 'subtitle', 'ultimapagina'));
    }

    public function store() {
        // Lógica para salvar o conteúdo (futuro: chamada à API .NET)
        return redirect()->route('professor.conteudo.index')->with('success', 'Conteúdo criado com sucesso!');
    }
}