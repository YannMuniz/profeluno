using System.Text.Json.Serialization;

namespace backend_dotnet.Models;

public partial class SalaAula
{
    public int IdSalaAula { get; set; }
    public string Titulo { get; set; } = null!;
    public string? Descricao { get; set; }
    public int? IdProfessor { get; set; }
    public DateTime? DataHoraInicio { get; set; }
    public DateTime? DataHoraFim { get; set; }
    public int IdMateria { get; set; }
    public int? IdConteudo { get; set; }
    public int? IdSimulado { get; set; }
    public int MaxAlunos { get; set; }
    public string? Url { get; set; } = null!;
    public string? NomeSala { get; set; }
    public double Avaliacao { get; set; }
    public string? Status { get; set; }
    public DateTime? CreatedAt { get; set; }
    public DateTime? UpdatedAt { get; set; }

    [JsonIgnore]
    public virtual ICollection<AlunoSala> AlunoSalas { get; set; } = new List<AlunoSala>();
    public virtual Conteudo? Conteudo { get; set; }
    public virtual Simulado? Simulados { get; set; }
    public virtual Materia Materia { get; set; } = null!;
}
