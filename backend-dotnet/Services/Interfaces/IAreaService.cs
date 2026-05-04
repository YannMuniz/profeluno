using backend_dotnet.Models;

namespace backend_dotnet.Services.Interfaces
{
    public interface IAreaService
    {
        public Task<List<Area>> RetornaTodasAreas();
        public Task<Area> RetornaAreaId(int idArea);
        public Task<int> CadastraArea(CadastrarAreaRequest request);
        public Task<Area> AtualizarArea(AtualizarAreaRequest request);
        public Task<bool> DeletarArea(int idArea);
    }
}