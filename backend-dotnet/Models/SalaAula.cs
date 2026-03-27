namespace backend_dotnet.Models;

public partial class SalaAula
{
    public long IdSalaAula { get; set; }
    public string Titulo { get; set; } = null!;
    public string? Descricao { get; set; }
    public long? ProfessorId { get; set; }
    public DateTime? DataHoraInicio { get; set; }
    public DateTime? DataHoraFim { get; set; }
    public string Materia { get; set; } = null!;
    public long? MaterialId { get; set; }
    public int QtdAlunos { get; set; }
    public string Url { get; set; } = null!;
    public double Avaliacao { get; set; }
    public string Status { get; set; } = null!;
    public DateTime? CreatedAt { get; set; }
    public DateTime? UpdatedAt { get; set; }
    public virtual ICollection<AlunoSala> AlunoSalas { get; set; } = new List<AlunoSala>();
    public virtual Conteudo? Material { get; set; }
}
