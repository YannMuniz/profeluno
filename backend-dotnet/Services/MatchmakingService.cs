using backend_dotnet.Data;
using backend_dotnet.Models;
using Microsoft.EntityFrameworkCore;

namespace backend_dotnet.Services
{
    public class MatchmakingService
    {
        private readonly ProfelunoContext _context;

        public MatchmakingService(ProfelunoContext context)
        {
            _context = context;
        }

        public async Task<SalaAula> AcharMelhorProfessor(int alunoId, int idArea)
        {
            // 1. Buscar professores que lecionam essa matéria e estão online/disponíveis
            // Simulando que você tenha uma tabela ou relação de Professor x Materia
            var professoresCandidatos = await _context.Users
                .Where(u => u.MateriaId == idArea)
                .ToListAsync();

            if(!professoresCandidatos.Any()) return null;

            var listaRanqueada = professoresCandidatos.Select(p => new
            {
                Professor = p,
                // CÁLCULO DO SCORE
                Score = CalcularScore(p)
            })
            .OrderByDescending(x => x.Score)
            .ToList();

            var melhorMatch = listaRanqueada.First();

            return new ProfessorMatchResponse
            {
                ProfessorNome = melhorMatch.Professor.Nome_Usuario,
                ScoreObtido = melhorMatch.Score,
                UrlJitsi = GerarUrlJitsi(alunoId, melhorMatch.Professor.IdUser)
            };
        }

        private double CalcularScore(User professor)
        {
            double score = 0;

            // Exemplo 1: Rating (Avaliação) - Se o prof tem nota 4.5 de 5.0, ganha 27 pontos (4.5 * 6)
            score += (professor.ProfessorUsers.First().Rating * 6);

            // Exemplo 2: Experiência - Professores com mais tempo de casa ganham bônus
            if(professor.CreatedAt < DateTime.Now.AddYears(-1)) score += 10;

            // Exemplo 3: Proximidade ou Preferência (Se o aluno já teve aula com ele e gostou)
            // Aqui você faria um JOIN com uma tabela de histórico

            return score;
        }
    }
}
