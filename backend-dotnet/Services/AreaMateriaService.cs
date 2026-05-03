using backend_dotnet.Data;
using backend_dotnet.Models;
using backend_dotnet.Models.Requests;
using backend_dotnet.Services.Interfaces;
using Microsoft.EntityFrameworkCore;

namespace backend_dotnet.Services
{
    public class AreaMateriaService : IAreaMateriaService
    {
        private readonly ProfelunoContext _context;
        public AreaMateriaService(ProfelunoContext context)
        {
            _context = context;
        }

        public async Task<List<AreaMateria>> RetornaTodasAreasMaterias()
        {
            var areasMaterias = await _context.AreaMateria.ToListAsync();
            return areasMaterias;
        }

        public async Task<AreaMateria> RetornaAreaMateriaPorId(int idAreaMateria)
        {
            var areaMateria = await _context.AreaMateria.FirstOrDefaultAsync(x => x.IdAreaMateria == idAreaMateria);
            return areaMateria;
        }

        public async Task<AreaMateria> CadastraAreaMateria(CadastraAreaMateriaRequest areaMateria)
        {
            AreaMateria newAreaMateria = new AreaMateria
            {
                IdArea = areaMateria.IdArea,
                IdMateria = areaMateria.IdMateria,
                SituacaoAreaMateria = areaMateria.SituacaoAreaMateria,
                CreatedAt = DateTime.Now
            };

            _context.AreaMateria.Add(newAreaMateria);
            await _context.SaveChangesAsync();
            return newAreaMateria;
        }

        public async Task<AreaMateria> AtualizarAreaMateria(AtualizarAreaMateriaRequest areaMateria)
        {
            var newAreaMateria = await _context.AreaMateria.FirstOrDefaultAsync(x => x.IdAreaMateria == areaMateria.IdAreaMateria);
            newAreaMateria.IdArea = areaMateria.IdArea;
            newAreaMateria.IdMateria = areaMateria.IdMateria;
            newAreaMateria.SituacaoAreaMateria = areaMateria.SituacaoAreaMateria;
            newAreaMateria.UpdatedAt = DateTime.Now;
            await _context.SaveChangesAsync();
            return newAreaMateria;
        }

        public async Task<bool> DeletarAreaMateria(int idAreaMateria)
        {
            var areaMateria = await _context.AreaMateria.FirstOrDefaultAsync(x => x.IdAreaMateria == idAreaMateria);
            if(areaMateria == null) return false;
            _context.AreaMateria.Remove(areaMateria);
            await _context.SaveChangesAsync();
            return true;
        }
    }
}
