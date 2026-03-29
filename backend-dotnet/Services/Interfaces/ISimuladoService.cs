using backend_dotnet.Models;
using backend_dotnet.Models.Requests;

namespace backend_dotnet.Services.Interfaces
{
    public interface ISimuladoService
    {
        public Task<bool> CadastrarSimulado(IEnumerable<CriarSimuladoRequest> simulados);
        public Task<IEnumerable<Simulado>> RetornaTodosSimuladosAsync();
        public Task<Simulado> RetornaSimuladoPorIdAsync(int idSimulado, int idUsuario);
        public Task<IEnumerable<Simulado>> RetornaSimuladosPorMateriaAsync(int idMateria, int idUsuario);
        public Task<IEnumerable<SimuladoQuestao>> RetornaSimuladoQuestoesPorIdSimulado(int idSimulado);
        public Task<IEnumerable<Simulado>> RetornaSimuladosPorUsuarioAsync(int idUsuario);
    }
}
