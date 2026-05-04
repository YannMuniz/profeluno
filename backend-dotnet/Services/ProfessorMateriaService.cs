using backend_dotnet.Data;
using backend_dotnet.Models;
using backend_dotnet.Models.Requests;
using backend_dotnet.Services.Interfaces;
using Microsoft.EntityFrameworkCore;

namespace backend_dotnet.Services
{
    public class ProfessorMateriaService : IProfessorMateriaService
    {
        private readonly ProfelunoContext _context;
        public ProfessorMateriaService(ProfelunoContext context)
        {
            _context = context;
        }

        public async Task<List<ProfessorMateria>> RetornaTodosProfessorMateria()
        {
            var professorMateria = await _context.ProfessorMateria.ToListAsync();
            return professorMateria;
        }

        public async Task<ProfessorMateria> RetornaProfessorMateriaPorId(int idProfessorMateria)
        {
            var ProfessorMateria = await _context.ProfessorMateria.FirstOrDefaultAsync(x => x.IdProfessorMateria == idProfessorMateria);
            return ProfessorMateria;
        }

        public async Task<ProfessorMateria> CadastraProfessorMateria(CadastrarProfessorMateriaRequest ProfessorMateria)
        {
            ProfessorMateria newProfessorMateria = new ProfessorMateria
            {
                IdProfessor = ProfessorMateria.IdProfessor,
                IdMateria = ProfessorMateria.IdMateria,
                SituacaoProfessorMateria = ProfessorMateria.SituacaoProfessorMateria,
                CreatedAt = DateTime.Now
            };

            _context.ProfessorMateria.Add(newProfessorMateria);
            await _context.SaveChangesAsync();
            return newProfessorMateria;
        }

        public async Task<ProfessorMateria> AtualizarProfessorMateria(AtualizarProfessorMateriaRequest ProfessorMateria)
        {
            var newProfessorMateria = await _context.ProfessorMateria.FirstOrDefaultAsync(x => x.IdProfessorMateria == ProfessorMateria.IdProfessorMateria);
            newProfessorMateria.IdProfessor = ProfessorMateria.IdArea;
            newProfessorMateria.IdMateria = ProfessorMateria.IdMateria;
            newProfessorMateria.SituacaoProfessorMateria = ProfessorMateria.SituacaoProfessorMateria;
            newProfessorMateria.UpdatedAt = DateTime.Now;
            await _context.SaveChangesAsync();
            return newProfessorMateria;
        }

        public async Task<bool> DeletarProfessorMateria(int idProfessorMateria)
        {
            var ProfessorMateria = await _context.ProfessorMateria.FirstOrDefaultAsync(x => x.IdProfessorMateria == idProfessorMateria);
            if(ProfessorMateria == null) return false;
            _context.ProfessorMateria.Remove(ProfessorMateria);
            await _context.SaveChangesAsync();
            return true;
        }
    }
}
