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
        [JsonIgnore]
        public virtual ICollection<Conteudo> Conteudos { get; set; } = new List<Conteudo>();
        [JsonIgnore]
        public virtual ICollection<SalaAula> SalaAulas { get; set; } = new List<SalaAula>();
        [JsonIgnore]
        public virtual ICollection<AreaMateria> AreaMateria { get; set; }
    }
}
