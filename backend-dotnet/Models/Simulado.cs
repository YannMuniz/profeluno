namespace backend_dotnet.Models;

public partial class Simulado
{
    public int IdSimulado { get; set; }
    public string Titulo { get; set; }
    public string Descricao { get; set; }
    public int Situacao { get; set; }
    public long SalaAulaId { get; set; }
    public int UserId { get; set; }
    public DateTime? CreatedAt { get; set; }
    public DateTime? UpdatedAt { get; set; }
    public virtual SalaAula SalaAula { get; set; } = null!;
}
