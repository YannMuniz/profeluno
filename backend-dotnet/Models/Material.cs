using System;
using System.Collections.Generic;

namespace backend_dotnet.Models;

public partial class Material
{
    public long Id { get; set; }

    public long? SalaAulaId { get; set; }

    public string Titulo { get; set; } = null!;

    public string? Descricao { get; set; }

    public string Type { get; set; } = null!;

    public string? FilePath { get; set; }

    public string? FileUrl { get; set; }

    public DateTime? CreatedAt { get; set; }

    public DateTime? UpdatedAt { get; set; }

    public virtual ICollection<SalaAula> SalaAulas { get; set; } = new List<SalaAula>();
}
