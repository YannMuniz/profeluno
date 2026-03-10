using System;
using System.Collections.Generic;

namespace backend_dotnet.Models;

public partial class Simulado
{
    public long Id { get; set; }
    public long SalaAulaId { get; set; }
    public int QuestaoCorreta { get; set; }
    public string? QuestaoA { get; set; }
    public string? QuestaoB { get; set; }
    public string? QuestaoC { get; set; }
    public string? QuestaoD { get; set; }
    public string? QuestaoE { get; set; }
    public DateTime? CreatedAt { get; set; }
    public DateTime? UpdatedAt { get; set; }
    public virtual SalaAula SalaAula { get; set; } = null!;
}
