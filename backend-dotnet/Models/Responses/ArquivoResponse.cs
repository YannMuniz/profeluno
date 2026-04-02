namespace backend_dotnet.Models.Responses
{
    public class ArquivoResponse
    {
        public string Tipo { get; set; } = null!;
        public string? NomeArquivo { get; set; }
        public string? ExtensaoArquivo { get; set; }
        public string? Url { get; set; }
    }
}
