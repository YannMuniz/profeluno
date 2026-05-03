using backend_dotnet.Models;
using backend_dotnet.Models.Requests;

namespace backend_dotnet.Services.Interfaces
{
    public interface IAreaMateriaService
    {
        public Task<List<AreaMateria>> RetornaTodasAreasMaterias();
        public Task<AreaMateria> RetornaAreaMateriaPorId(int idAreaMateria);
        public Task<AreaMateria> CadastraAreaMateria(CadastraAreaMateriaRequest areaMateria);
        public Task<AreaMateria> AtualizarAreaMateria(AtualizarAreaMateriaRequest areaMateria);
        public Task<bool> DeletarAreaMateria(int idAreaMateria);
    }
}
