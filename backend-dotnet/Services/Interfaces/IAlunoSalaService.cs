using backend_dotnet.Models;
using backend_dotnet.Models.Requests;
using backend_dotnet.Models.Responses;

namespace backend_dotnet.Services.Interfaces
{
    public interface IAlunoSalaService
    {
        public Task<IEnumerable<AlunoSala>> RetornaTodosAlunoSala();
        public Task<AlunoSala> RetornaAlunoSalaPorId(int idAlunoSala);
        public Task<IEnumerable<AlunoSala>> RetornarAlunoSalaPorIdAluno(int idAluno);
        public Task<QuantidadeAlunosSalaResponse> RetornaQtdAlunosSala(int idSalaAula);
        public Task<int> CadastraAlunoSala(CadastraAlunoSalaRequest request);
        public Task<int> AtualizarAlunoSala(AtualizarAlunoSalaRequest request);
        public Task<bool> DeletarAlunoSala(int idAlunoSala);
    }
}
