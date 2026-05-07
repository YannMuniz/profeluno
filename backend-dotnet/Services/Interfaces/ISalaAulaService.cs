using backend_dotnet.Models;
using backend_dotnet.Models.Requests;
using backend_dotnet.Models.Responses;

namespace backend_dotnet.Services.Interfaces
{
    public interface ISalaAulaService
    {
        public Task<IEnumerable<SalaAula>> RetornaTodasSalasAula();
        public Task<SalaAula> RetornaSalaAulaPorId(int idSalaAula);
        public Task<IEnumerable<SalaAula>> RetornaSalaAulaPorProfessor(int idProfessor);
        public Task<int> CadastraSalaAula(CadastrarSalaAulaRequest request);
        public Task<bool> AtualizaSalaAula(AtualizarSalaAulaRequest request);
        public Task<bool> DeletarSalaAula(int idSalaAula);
        public Task<TrocaStatusSalaAulaResponse> TrocarStatusSalaAula(TrocaStatusSalaAulaRequest request);
        public Task<int> RetornaQuantidadeSalaAulaIdProfessor(int idProfessor);
    }
}
