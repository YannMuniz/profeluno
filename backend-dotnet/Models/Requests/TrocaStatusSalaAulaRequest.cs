namespace backend_dotnet.Models.Requests
{
    public class TrocaStatusSalaAulaRequest
    {
        public int IdSalaAula { get; set; }
        public DateTime DataHoraInicio { get; set; }
        public DateTime DataHoraFim { get; set; }
        public string Status { get; set; }
    }
}
