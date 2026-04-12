namespace backend_dotnet.Models.Responses
{
    public class QuantidadeAlunosSalaResponse
    {
        public int IdSalaAula { get; set; }
        public int QtdAlunosSala { get; set; }
        public DateTime? DataHoraInicio { get; set; }
        public DateTime? DataHoraFim { get; set; }
    }
}
