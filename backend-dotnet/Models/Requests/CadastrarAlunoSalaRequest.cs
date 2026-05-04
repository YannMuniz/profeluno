namespace backend_dotnet.Models.Requests
{
    public class CadastrarAlunoSalaRequest
    {
        public int IdAluno { get; set; }
        public int IdSalaAula { get; set; }
        public DateTime? JoinedAt { get; set; }
        public DateTime? LeftAt { get; set; }
    }
}
