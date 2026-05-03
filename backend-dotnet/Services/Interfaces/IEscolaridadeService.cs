using backend_dotnet.Models;
using backend_dotnet.Models.Requests;

namespace backend_dotnet.Services.Interfaces
{
    public interface IEscolaridadeService
    {
        public Task<List<Escolaridade>> RetornaTodasEscolaridades();
        public Task<Escolaridade> RetornaEscolaridadePorId(int idEscolaridade);
        public Task<Escolaridade> CadastraEscolaridade(CadastrarEscolaridadeRequest escolaridade);
        public Task<Escolaridade> AtualizarEscolaridade(AtualizarEscolaridadeRequest escolaridade);
        public Task<bool> DeletarEscolaridade(int idEscolaridade);
    }
}
