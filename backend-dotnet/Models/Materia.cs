using System.Text.Json.Serialization;

namespace backend_dotnet.Models
{
    public class Materia
    {
        public int IdMateria { get; set; }
        public string NomeMateria { get; set; }
        public int SituacaoMateria { get; set; }
        public DateTime CreatedAt { get; set; }
        public DateTime UpdatedAt { get; set; }

        [JsonIgnore]
        public virtual ICollection<Simulado> Simulados { get; set; } = new List<Simulado>();
    }
}
