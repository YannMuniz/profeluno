namespace backend_dotnet.Models.Requests
{
    public class AtualizarUsuarioRequest
    {
        public long IdUser { get; set; }
        public string Email { get; set; }
        public string Password { get; set; }
        public int IdCargo { get; set; }
        public string Nome_Usuario { get; set; }
    }
}
