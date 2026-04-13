namespace backend_dotnet.Models.Requests
{
    public class AtualizarAlunoSalaRequest
    {
        public int IdAlunoSala { get; set; }
        public int IdAluno { get; set; }
        public int IdSalaAula { get; set; }
        public DateTime? JoinedAt { get; set; }
        public DateTime? LeftAt { get; set; }
    }
}
