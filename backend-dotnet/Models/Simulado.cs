namespace backend_dotnet.Models;

public partial class Simulado
{
    public int IdSimulado { get; set; }
    public string Titulo { get; set; }
    public string Descricao { get; set; }
    public bool Situacao { get; set; }
    public int IdMateria { get; set; }
    public int IdUser { get; set; }
    public DateTime? CreatedAt { get; set; }
    public DateTime? UpdatedAt { get; set; }
    public virtual Materia Materia { get; set; } = null!;
    public virtual ICollection<SimuladoQuestao> SimuladoQuestao { get; set; } = null!;
}
