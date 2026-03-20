using backend_dotnet.Models;
using backend_dotnet.Models.Requests;
using backend_dotnet.Models.Responses;

namespace backend_dotnet.Services.Interfaces
{
    public interface ICargoService
    {
        public Task<IEnumerable<Cargo>> RetornaTodosCargosAsync();
        public Task<Cargo> RetornaCargoPorIdAsync(int idCargo);
        public Task<Cargo> RetornaCargoPorNomeAsync(string nomeCargo);
        public Task<Cargo> AtualizaCargoAsync(Cargo cargo);
        public Task<bool> CadastrarCargo(CargoRequest cargo);
    }
}
