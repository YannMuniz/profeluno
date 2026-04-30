namespace backend_dotnet.Models.Responses
{
    public class UserResponse
    {
        public int IdUser { get; set; }
        public string Email { get; set; }
        public string Password { get; set; }
        public int IdCargo { get; set; }
        public string Nome_Usuario { get; set; }
        public string TipoPerfil { get; set; }

        public virtual ProfessorPerfil ProfessorPerfil { get; set; }
        public virtual AlunoPerfil AlunoPerfil { get; set; }
    }
}
