using backend_dotnet.Data;
using backend_dotnet.Models;
using backend_dotnet.Services.Interfaces;
using Microsoft.EntityFrameworkCore;

namespace backend_dotnet.Services
{
    public class MatchmakingService : IMatchmakingService
    {
        private readonly ProfelunoContext _context;

        public MatchmakingService(ProfelunoContext context)
        {
            _context = context;
        }

        public async Task<SalaAula> AcharMelhorProfessor(int idAluno, int idMateria, int idArea)
        {
            var professoresCandidatos = await _context.Users
                .Include(d => d.ProfessorPerfil)
                    .ThenInclude(d => d.Area)
                        .ThenInclude(d => d.AreaMateria)
                        .ThenInclude(d => d.Materias)
                .Where(u => u.ProfessorPerfil.Area.AreaMateria.Any(am => am.IdArea == idArea || am.IdMateria == idMateria))
                .ToListAsync();

            if(!professoresCandidatos.Any()) return null;

            var listaRanqueada = professoresCandidatos.Select(p => new
            {
                Professor = p,
                Score = CalcularScore(p, idMateria)
            })
            .OrderByDescending(x => x.Score)
            .ToList();

            var melhorMatch = listaRanqueada.First();

            var melhorAula = await _context.SalaAulas.FirstOrDefaultAsync(x => x.IdMateria == idMateria && x.IdProfessor == melhorMatch.Professor.IdUser);

            return melhorAula;
        }

        private double CalcularScore(User professor, int idMateria)
        {
            double score = 0;

            // Exemplo 1: Rating (Avaliação) - Se o prof tem nota 4.5 de 5.0, ganha 27 pontos (4.5 * 6)
            score += (professor.ProfessorPerfil.Avalicao * 6);

            // Exemplo 2: Experiência - Professores com mais tempo de casa ganham bônus
            if(professor.CreatedAt < DateTime.Now.AddYears(-1)) score += 10;

            if(professor.ProfessorPerfil.Area.AreaMateria.Any(x => x.IdMateria == idMateria)) score += 20;

            score += new Random().NextDouble() * 3;

            return score;
        }
    }
}
