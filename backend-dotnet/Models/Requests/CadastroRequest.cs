namespace backend_dotnet.Models.Requests
{
    public class CadastroRequest
    {
        public string Nome { get; set; }
        public string Email { get; set; }
        public string Senha { get; set; }
        public string Cargo { get; set; }
    }
}
