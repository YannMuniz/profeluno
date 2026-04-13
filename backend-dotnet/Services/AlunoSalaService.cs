using backend_dotnet.Data;
using backend_dotnet.Models;
using backend_dotnet.Models.Requests;
using backend_dotnet.Models.Responses;
using backend_dotnet.Services.Interfaces;
using Microsoft.EntityFrameworkCore;

namespace backend_dotnet.Services
{
    public class AlunoSalaService : IAlunoSalaService
    {
        private readonly ProfelunoContext _context;
        public AlunoSalaService(ProfelunoContext context)
        {
            _context = context;
        }

        public async Task<IEnumerable<AlunoSala>> RetornaTodosAlunoSala()
        {
            return await _context.AlunoSalas.ToListAsync();
        }
        public async Task<AlunoSala> RetornaAlunoSalaPorId(int idAlunoSala)
        {
            return await _context.AlunoSalas.FirstOrDefaultAsync(x => x.IdAlunoSala == idAlunoSala);
        }

        public async Task<IEnumerable<AlunoSala>> RetornarAlunoSalaPorIdAluno(int idAluno)
        {
            return await _context.AlunoSalas.Where(x => x.IdAluno == idAluno).ToListAsync();
        }

        public async Task<QuantidadeAlunosSalaResponse> RetornaQtdAlunosSala(int idSalaAula)
        {
            var totalAlunos = await _context.AlunoSalas
                .Where(x => x.IdSalaAula == idSalaAula)
                .CountAsync();
            var dadosSala = await _context.SalaAulas.FirstOrDefaultAsync(x => x.IdSalaAula == idSalaAula);

            return new QuantidadeAlunosSalaResponse
            {
                QtdAlunosSala = totalAlunos,
                DataHoraInicio = dadosSala.DataHoraInicio,
                DataHoraFim = dadosSala.DataHoraFim,
            };
        }

        public async Task<int> CadastraAlunoSala(CadastraAlunoSalaRequest request)
        {
            AlunoSala newAlunoSala = new AlunoSala
            {
                IdAluno = request.IdAluno,
                IdSalaAula = request.IdSalaAula,
                JoinedAt = request.JoinedAt,
                LeftAt = request.LeftAt,
                CreatedAt = DateTime.Now
            };

            await _context.AlunoSalas.AddAsync(newAlunoSala);
            await _context.SaveChangesAsync();

            return newAlunoSala.IdAlunoSala;
        }

        public async Task<int> AtualizarAlunoSala(AtualizarAlunoSalaRequest request)
        {
            var response = await _context.AlunoSalas.FirstOrDefaultAsync(x => x.IdAlunoSala == request.IdAlunoSala);

            response.IdAluno = request.IdAluno;
            response.IdSalaAula = request.IdSalaAula;
            response.JoinedAt = request.JoinedAt;
            response.LeftAt = request.LeftAt;
            response.UpdatedAt = DateTime.Now;
            await _context.SaveChangesAsync();

            return request.IdAlunoSala;
        }

        public async Task<bool> DeletarAlunoSala(int idAlunoSala)
        {
            var alunoSala = await _context.AlunoSalas.FirstOrDefaultAsync(x => x.IdAlunoSala == idAlunoSala);
            if(alunoSala == null) return false;
            _context.AlunoSalas.Remove(alunoSala);
            await _context.SaveChangesAsync();

            return true;
        }
    }
}
