namespace backend_dotnet.Models.Requests
{
    public class UploadConteudoRequest
    {
        public string Titulo { get; set; }
        public int IdUsuario { get; set; }
        public string Descricao { get; set; }
        public int IdMateria { get; set; }
        public string Tipo { get; set; }
        public bool Situacao { get; set; }
        public string? NomeArquivo { get; set; }
        public string? ExtensaoArquivo { get; set; }
        public string? Url { get; set; }

        public IFormFile? Arquivo { get; set; }
    }
}
