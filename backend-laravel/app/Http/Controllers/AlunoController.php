<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Professor;
use App\Models\Aula;
use App\Models\Simulado;

class AlunoController extends Controller
{
    /**
     * Dashboard do Aluno
     */
    public function dashboard()
    {
        // Dados para o header
        $title = 'Bem-vindo de volta!';
        $subtitle = 'Continue seu aprendizado e alcance seus objetivos';
        $notificationCount = 5;
        
        // Itens do menu
        $menuItems = [
            [
                'label' => 'Dashboard',
                'route' => route('aluno.dashboard'),
                'icon' => 'fas fa-home',
                'active' => true
            ],
            [
                'label' => 'Sala de Aula',
                'route' => route('aluno.buscar-sala'),
                'icon' => 'fas fa-chalkboard-teacher',
                'active' => false
            ],
            [
                'label' => 'Meus Simulados',
                'route' => route('aluno.simulados'),
                'icon' => 'fas fa-file-alt',
                'active' => false
            ],
            [
                'label' => 'Conteúdo das Aulas',
                'route' => route('aluno.conteudos'),
                'icon' => 'fas fa-book',
                'active' => false
            ],
            [
                'label' => 'Configurações',
                'route' => route('aluno.configuracoes'),
                'icon' => 'fas fa-cog',
                'active' => false,
                'separator' => true
            ],
            [
                'label' => 'Sair',
                'route' => route('logout'),
                'icon' => 'fas fa-sign-out-alt',
                'active' => false
            ]
        ];
        
        // Estatísticas
        $stats = [
            'aulasEmAndamento' => Aula::where('aluno_id', auth()->id())
                ->where('status', 'em_andamento')
                ->count(),
            'simuladosConcluidos' => Simulado::where('aluno_id', auth()->id())
                ->where('status', 'concluido')
                ->count(),
            'simuladosPendentes' => Simulado::where('aluno_id', auth()->id())
                ->where('status', 'pendente')
                ->count(),
            'taxaAproveitamento' => 87 // Calcular baseado em métricas reais
        ];
        
        // Aulas recentes
        $aulasRecentes = $this->getAulasRecentes();
        
        return view('aluno.dashboard', compact(
            'title',
            'subtitle',
            'notificationCount',
            'menuItems',
            'userType',
            'stats',
            'aulasRecentes'
        ))->with('userType', 'Aluno Dashboard');
    }
    
    /**
     * Buscar Sala de Aula
     */
    public function buscarSala(Request $request)
    {
        // Dados para o header
        $title = 'Buscar Sala de Aula';
        $subtitle = 'Encontre professores disponíveis para suas matérias de interesse';
        $notificationCount = 5;
        
        // Itens do menu
        $menuItems = $this->getMenuItems('buscar-sala');
        
        // Matérias disponíveis
        $materias = [
            'Matemática',
            'Física',
            'Química',
            'Biologia',
            'História',
            'Geografia',
            'Literatura',
            'Inglês',
            'Português',
            'Filosofia'
        ];
        
        // Buscar professores
        $query = Professor::with(['avaliacoes', 'materias']);
        
        // Filtro de busca
        if ($request->filled('q')) {
            $query->where(function($q) use ($request) {
                $q->where('nome', 'like', '%' . $request->q . '%')
                  ->orWhere('especialidade', 'like', '%' . $request->q . '%');
            });
        }
        
        // Filtro de matéria
        if ($request->filled('materia')) {
            $query->whereHas('materias', function($q) use ($request) {
                $q->where('nome', $request->materia);
            });
        }
        
        // Filtros especiais
        if ($request->filled('filtro')) {
            switch ($request->filtro) {
                case 'ao-vivo':
                    $query->where('ao_vivo', true);
                    break;
                case 'melhor-avaliados':
                    $query->orderBy('avaliacao_media', 'desc');
                    break;
                case 'mais-alunos':
                    $query->orderBy('total_alunos', 'desc');
                    break;
                case 'disponivel-hoje':
                    $query->whereHas('disponibilidades', function($q) {
                        $q->whereDate('data', today());
                    });
                    break;
                case 'certificados':
                    $query->where('certificado', true);
                    break;
            }
        }
        
        // Ordenação
        if ($request->filled('ordenar')) {
            switch ($request->ordenar) {
                case 'avaliacao':
                    $query->orderBy('avaliacao_media', 'desc');
                    break;
                case 'alunos':
                    $query->orderBy('total_alunos', 'desc');
                    break;
                case 'ao-vivo':
                    $query->orderBy('ao_vivo', 'desc');
                    break;
                default:
                    // Relevante - pode usar algoritmo mais complexo
                    $query->orderBy('ao_vivo', 'desc')
                          ->orderBy('avaliacao_media', 'desc');
            }
        }
        
        $professores = $query->paginate(12);
        
        return view('aluno.buscar-sala', compact(
            'title',
            'subtitle',
            'notificationCount',
            'menuItems',
            'materias',
            'professores'
        ))->with('userType', 'Aluno Dashboard');
    }
    
    /**
     * Helper: Get menu items
     */
    private function getMenuItems($active = 'dashboard')
    {
        return [
            [
                'label' => 'Dashboard',
                'route' => route('aluno.dashboard'),
                'icon' => 'fas fa-home',
                'active' => $active === 'dashboard'
            ],
            [
                'label' => 'Sala de Aula',
                'route' => route('aluno.buscar-sala'),
                'icon' => 'fas fa-chalkboard-teacher',
                'active' => $active === 'buscar-sala'
            ],
            [
                'label' => 'Meus Simulados',
                'route' => route('aluno.simulados'),
                'icon' => 'fas fa-file-alt',
                'active' => $active === 'simulados'
            ],
            [
                'label' => 'Conteúdo das Aulas',
                'route' => route('aluno.conteudos'),
                'icon' => 'fas fa-book',
                'active' => $active === 'conteudos'
            ],
            [
                'label' => 'Configurações',
                'route' => route('aluno.configuracoes'),
                'icon' => 'fas fa-cog',
                'active' => $active === 'configuracoes',
                'separator' => true
            ],
            [
                'label' => 'Sair',
                'route' => route('logout'),
                'icon' => 'fas fa-sign-out-alt',
                'active' => false
            ]
        ];
    }
    
    /**
     * Helper: Get aulas recentes
     */
    private function getAulasRecentes()
    {
        // Aqui você buscaria do banco de dados
        // Por enquanto retorno dados de exemplo
        return [
            [
                'titulo' => 'Matemática Avançada',
                'professor' => 'Prof. João Silva',
                'topico' => 'Trigonometria e Funções',
                'icon' => 'fas fa-calculator',
                'iconBg' => 'rgba(115, 103, 240, 0.15)',
                'iconColor' => 'var(--primary-color)',
                'statusClass' => 'status-completed',
                'statusLabel' => 'Concluída',
                'actionLabel' => 'Ver Conteúdo',
                'url' => '#'
            ],
            [
                'titulo' => 'Química Orgânica',
                'professor' => 'Prof. Maria Santos',
                'topico' => 'Reações e Compostos',
                'icon' => 'fas fa-flask',
                'iconBg' => 'rgba(40, 199, 111, 0.15)',
                'iconColor' => 'var(--success-color)',
                'statusClass' => 'status-pending',
                'statusLabel' => 'Em Andamento',
                'actionLabel' => 'Acessar Sala',
                'url' => '#'
            ]
        ];
    }
}