namespace backend_dotnet.Models.Requests
{
    public class CadastroRequest
    {
        public string Nome { get; set; }
        public string Email { get; set; }
        public string Senha { get; set; }
        public int IdCargo { get; set; }

        //Aluno
        public string? PeriodoAluno { get; set; }
        public int? IdEscolaridadeAluno { get; set; }
        public int? IdAreaAluno { get; set; }

        //Professor
        public string? FormacaoProfessor { get; set; }
        public int? IdEscolaridadeProfessor { get; set; }
        public int? IdAreaProfessor { get; set; }
    }
}
