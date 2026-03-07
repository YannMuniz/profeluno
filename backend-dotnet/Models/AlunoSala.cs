using System;
using System.Collections.Generic;

namespace backend_dotnet.Models;

public partial class AlunoSala
{
    public long Id { get; set; }

    public long AlunoId { get; set; }

    public long SalaAulaId { get; set; }

    public DateTime? JoinedAt { get; set; }

    public DateTime? LeftAt { get; set; }

    public DateTime? CreatedAt { get; set; }

    public DateTime? UpdatedAt { get; set; }

    public virtual Aluno Aluno { get; set; } = null!;

    public virtual SalaAula SalaAula { get; set; } = null!;
}
