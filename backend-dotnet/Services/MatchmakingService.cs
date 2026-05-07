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

        public async Task<List<SalaAula>> AcharMelhorProfessor(int idMateria, int idArea)
        {
            var professoresCandidatos = await _context.Users
                .Include(d => d.ProfessorPerfil)
                    .ThenInclude(d => d.Area)
                        .ThenInclude(d => d.ProfessorMateria)
                            .ThenInclude(d => d.Materias)
                .Where(u => u.ProfessorPerfil.Area.ProfessorMateria.Any(am => am.IdMateria == idMateria) || u.ProfessorPerfil.IdArea == idArea)
                .ToListAsync();

            if(!professoresCandidatos.Any()) return new List<SalaAula>();

            var listaRanqueada = professoresCandidatos.Select(p => new
            {
                Professor = p,
                Score = CalcularScore(p, idMateria)
            })
            .OrderByDescending(x => x.Score)
            .ToList();

            var idsProfessoresRanqueados = listaRanqueada.Select(x => x.Professor.IdUser).ToList();

            var salasDisponiveis = await _context.SalaAulas
                .Where(x => idsProfessoresRanqueados.Contains((int)x.IdProfessor) && x.IdMateria == idMateria)
                .ToListAsync();

            var salasOrdenadas = salasDisponiveis
                .OrderBy(sala => idsProfessoresRanqueados.IndexOf((int)sala.IdProfessor))
                .ToList();

            return salasOrdenadas;
        }

        private double CalcularScore(User professor, int idMateria)
        {
            double score = 0;

            score += (professor.ProfessorPerfil.Avalicao * 6);

            // Professores com mais tempo de casa ganham bônus
            if(professor.CreatedAt < DateTime.Now.AddYears(-1)) score += 10;

            if(professor.ProfessorPerfil.Area.ProfessorMateria.Any(x => x.IdMateria == idMateria)) score += 20;

            score += new Random().NextDouble() * 3;

            return score;
        }
    }
}
