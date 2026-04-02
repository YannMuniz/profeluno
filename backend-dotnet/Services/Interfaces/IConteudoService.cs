using backend_dotnet.Models;
using backend_dotnet.Models.Requests;
using backend_dotnet.Models.Responses;

namespace backend_dotnet.Services.Interfaces
{
    public interface IConteudoService
    {
        public Task<bool> CadastrarConteudo(UploadConteudoRequest conteudo);
        public Task<IEnumerable<Conteudo>> RetornaTodosConteudosAsync();
        public Task<ConteudoResponse> RetornaConteudoPorIdProfessor(int idUsuario);
        public Task<ConteudoResponse> RetornaConteudoPorIdConteudo(int idConteudo);
        public Task<ArquivoResponse> RetornaDadosArquivo(int idConteudo);
        public Task<Conteudo> DownloadArquivoConteudo(int idConteudo);
        public Task<bool> UpdateConteudo(AtualizarConteudoRequest conteudo);
        public Task<bool> DeleteConteudo(int id);
    }
}
