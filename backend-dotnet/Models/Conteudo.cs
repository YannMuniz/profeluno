namespace backend_dotnet.Models;

public partial class Conteudo
{
    public long IdConteudo{ get; set; }
    public string Titulo { get; set; } = null!;
    public long? IdUsuario { get; set; }
    public string? Descricao { get; set; }
    public string Type { get; set; } = null!;
    public string? FilePath { get; set; }
    public string? FileUrl { get; set; }
    public DateTime? CreatedAt { get; set; }
    public DateTime? UpdatedAt { get; set; }
}
