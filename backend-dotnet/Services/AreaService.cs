using backend_dotnet.Data;
using backend_dotnet.Models;
using backend_dotnet.Models.Requests;
using backend_dotnet.Services.Interfaces;
using Microsoft.EntityFrameworkCore;

namespace backend_dotnet.Services
{
    public class AreaService : IAreaService
    {
        private readonly ProfelunoContext _context;

        public AreaService(ProfelunoContext profeluno)
        {
            _context = profeluno;
        }

        public async Task<List<Area>> RetornaTodasAreas()
        {
            return await _context.Area.ToListAsync();
        }

        public async Task<Area> RetornaAreaId(int idArea)
        {
            return await _context.Area.FirstOrDefaultAsync(x => x.IdArea == idArea);
        }

        public async Task<int> CadastraArea(CadastraAreaRequest request)
        {
            Area newArea = new Area
            {
                NomeArea = request.NomeArea,
                SituacaoArea = request.SituacaoArea,
                CreatedAt = DateTime.Now
            };

            await _context.Area.AddAsync(newArea);
            await _context.SaveChangesAsync();

            return newArea.IdArea;
        }

        public async Task<Area> AtualizarArea(AtualizarAreaRequest request)
        {
            Area area = await _context.Area.FirstOrDefaultAsync(x => x.IdArea == request.IdArea);

            if(area == null) return null;

            area.NomeArea = request.NomeArea;
            area.SituacaoArea = request.SituacaoArea;
            area.UpdateAt = DateTime.Now;

            await _context.SaveChangesAsync();
            
            return area;
        }

        public async Task<bool> DeletarArea(int idArea)
        {
            int area = await _context.Area.Where(x => x.IdArea == idArea).ExecuteDeleteAsync();

            return area > 0;
        }
    }
}