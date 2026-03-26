using backend_dotnet.Models.Requests;

namespace backend_dotnet.Services.Interfaces
{
    public interface ISimuladoService
    {
        public Task<bool> CadastrarSimulado(IEnumerable<CriarSimuladoRequest> simulados);
    }
}
