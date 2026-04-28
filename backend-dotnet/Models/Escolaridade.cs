namespace backend_dotnet.Models
{
    public class Escolaridade
    {
        public int IdEscolaridade { get; set; }
        public string NomeEscolaridade { get; set; }
        public int SituacaoEscolaridade { get; set; }
        public DateTime CreatedAt { get; set; }
        public DateTime UpdateAt { get; set; }
    }
}