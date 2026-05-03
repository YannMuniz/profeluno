namespace backend_dotnet.Models.Requests
{
    public class AtualizarEscolaridadeRequest
    {
        public int IdEscolaridade { get; set; }
        public string NomeEscolaridade { get; set; }
        public int SituacaoEscolaridade { get; set; }
    }
}
