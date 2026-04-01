using backend_dotnet.Models;
using backend_dotnet.Models.Requests;

namespace backend_dotnet.Services.Interfaces
{
    public interface IConteudoService
    {
        public Task<bool> CadastrarConteudo(UploadConteudoRequest conteudo);
        public Task<IEnumerable<Conteudo>> RetornaTodosConteudosAsync();
        public Task<Conteudo> DownloadArquivoConteudo(int idConteudo);
    }
}
