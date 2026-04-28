using backend_dotnet.Data;
using backend_dotnet.Models;
using backend_dotnet.Models.Requests;
using Microsoft.EntityFrameworkCore;

namespace backend_dotnet.Services
{
    public class AreaService
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
                SituacaoArea = request.SituacaoArea
            };

            await _context.Area.AddAsync(newArea);
            await _context.SaveChangesAsync();

            return newArea.IdArea;
        }
    }
}