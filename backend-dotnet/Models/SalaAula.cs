using System;
using System.Collections.Generic;

namespace backend_dotnet.Models;

public partial class SalaAula
{
    public long Id { get; set; }
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
    public virtual Material? Material { get; set; }
    public virtual ICollection<Simulado> Simulados { get; set; } = new List<Simulado>();
}
