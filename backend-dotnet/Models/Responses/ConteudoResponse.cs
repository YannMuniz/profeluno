namespace backend_dotnet.Models.Responses
{
    public class ConteudoResponse
    {
        public long IdConteudo { get; set; }
        public string Titulo { get; set; } = null!;
        public long? IdUsuario { get; set; }
        public int IdMateria { get; set; }
        public string? Descricao { get; set; }
        public string Tipo { get; set; } = null!;
        public string? NomeArquivo { get; set; }
        public string? ExtensaoArquivo { get; set; }
        public string? Url { get; set; }
        public bool Situacao { get; set; }
        public DateTime? CreatedAt { get; set; }
        public DateTime? UpdatedAt { get; set; }
    }
}
