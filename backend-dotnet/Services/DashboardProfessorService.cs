using backend_dotnet.Data;
using backend_dotnet.Services.Interfaces;
using Microsoft.EntityFrameworkCore;

namespace backend_dotnet.Services
{
    public class DashboardProfessorService : IDashboardProfessorService
    {
        private readonly ProfelunoContext _context;

        public DashboardProfessorService(ProfelunoContext profeluno)
        {
            _context = profeluno;
        }

        public async Task<int> TotalAulas(int idProfessor)
        {
            return await _context.SalaAulas.Where(x => x.IdProfessor == idProfessor).CountAsync();
        }

        public async Task<int> AulasAtivas(int idProfessor)
        {
            return await _context.SalaAulas.Where(x => x.IdProfessor == idProfessor && x.Status == "active" ).CountAsync();
        }

        public async Task<int> AulasPendentes(int idProfessor)
        {
            return await _context.SalaAulas.Where(x => x.IdProfessor == idProfessor && x.Status == "pending").CountAsync();
        }

        public async Task<int> AulasConcluidas(int idProfessor)
        {
            return await _context.SalaAulas.Where(x => x.IdProfessor == idProfessor && x.Status == "completed").CountAsync();
        }

        public async Task<int> ConteudosCriados(int idProfessor)
        {
            return await _context.Conteudos.Where(x => x.IdUsuario == idProfessor).CountAsync();
        }

        public async Task<int> SimuladoCriado(int idProfessor)
        {
            return await _context.Simulados.Where(x => x.IdUser == idProfessor).CountAsync();
        }
    }
}