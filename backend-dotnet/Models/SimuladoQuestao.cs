using System.Text.Json.Serialization;

namespace backend_dotnet.Models
{
    public class SimuladoQuestao
    {
        public int IdSimuladoQuestao { get; set; }
        public int IdSimulado { get; set; }
        public int Ordem { get; set; }
        public string Enunciado { get; set; }
        public int QuestaoCorreta { get; set; }
        public string? QuestaoA { get; set; }
        public string? QuestaoB { get; set; }
        public string? QuestaoC { get; set; }
        public string? QuestaoD { get; set; }
        public string? QuestaoE { get; set; }
        public DateTime? CreatedAt { get; set; }
        public DateTime? UpdatedAt { get; set; }

        [JsonIgnore]
        public virtual Simulado Simulado { get; set; } = null!;
    }
}
