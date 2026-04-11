using backend_dotnet.Data;
using backend_dotnet.Models;
using backend_dotnet.Models.Requests;
using backend_dotnet.Services.Interfaces;
using Microsoft.EntityFrameworkCore;

namespace backend_dotnet.Services
{
    public class SalaAulaService : ISalaAulaService
    {
        private ProfelunoContext _context;
        public SalaAulaService(ProfelunoContext context)
        {
            _context = context;
        }

        public async Task<IEnumerable<SalaAula>> RetornaTodasSalasAula()
        {
            return await _context.SalaAulas.AsNoTracking().ToListAsync();
        }

        public async Task<SalaAula> RetornaSalaAulaPorId(int idSalaAula)
        {
            return await _context.SalaAulas.FirstOrDefaultAsync(x => x.IdSalaAula == idSalaAula);
        }

        public async Task<IEnumerable<SalaAula>> RetornaSalaAulaPorProfessor(int idProfessor)
        {
            return await _context.SalaAulas.Where(x => x.IdProfessor == idProfessor).ToListAsync();
        }

        public async Task<int> CadastraSalaAula(CadastrarSalaAulaRequest request)
        {
            SalaAula newSalaAula = new SalaAula
            {
                Titulo = request.Titulo,
                Descricao = request.Descricao,
                IdMateria = request.IdMateria,
                IdProfessor = request.IdProfessor,
                MaxAlunos = request.MaxAlunos,
                DataHoraInicio = request.DataHoraInicio,
                DataHoraFim = request.DataHoraFim,
                Status = request.Status,
                IdConteudo = request.IdConteudo == null || request.IdConteudo == 0 ? null : request.IdConteudo,
                IdSimulado = request.IdSimulado == null || request.IdSimulado == 0 ? null : request.IdSimulado,
                Url = request.Url,
                NomeSala = request.NomeSala,
                CreatedAt = DateTime.UtcNow
            };

            await _context.SalaAulas.AddAsync(newSalaAula);
            await _context.SaveChangesAsync();
            
            return newSalaAula.IdSalaAula;
        }

        public async Task<bool> AtualizaSalaAula(AtualizarSalaAulaRequest request)
        {
            var salaAula = await _context.SalaAulas.FirstOrDefaultAsync(x => x.IdSalaAula == request.IdSalaAula);

            if(salaAula == null) return false;

            salaAula.Titulo = request.Titulo;
            salaAula.Descricao = request.Descricao;
            salaAula.IdMateria = request.IdMateria;
            salaAula.IdProfessor = request.IdProfessor;
            salaAula.MaxAlunos = request.MaxAlunos;
            salaAula.DataHoraInicio = request.DataHoraInicio;
            salaAula.DataHoraFim = request.DataHoraFim;
            salaAula.Status = request.Status;
            salaAula.IdConteudo = request.IdConteudo == null || request.IdConteudo == 0 ? null : request.IdConteudo;
            salaAula.IdSimulado = request.IdSimulado == null || request.IdSimulado == 0 ? null : request.IdSimulado;
            salaAula.UpdatedAt = DateTime.Now;

            await _context.SaveChangesAsync();

            return true;
        }
    }
}