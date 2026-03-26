namespace backend_dotnet.Models.Requests
{
    public class CriarSimuladoRequest
    {
        public int SalaAulaId { get; set; }
        public string Titulo { get; set; }
        public string Descricao { get; set; }
        public int Situacao { get; set; }

        public List<CriarSimuladoQuestoesRequest> SimuladoQuestoesRequests { get; set; }
    }

    public class CriarSimuladoQuestoesRequest
    {
        public string Enunciado { get; set; }
        public int Ordem { get; set; }
        public int QuestaoCorreta { get; set; }
        public string? QuestaoA { get; set; }
        public string? QuestaoB { get; set; }
        public string? QuestaoC { get; set; }
        public string? QuestaoD { get; set; }
        public string? QuestaoE { get; set; }
    }
}
