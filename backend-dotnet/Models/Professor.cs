using System;
using System.Collections.Generic;

namespace backend_dotnet.Models;

public partial class Professor
{
    public long Id { get; set; }

    public long? UserId { get; set; }

    public string NomeProfessor { get; set; } = null!;

    public DateTime? CreatedAt { get; set; }

    public DateTime? UpdatedAt { get; set; }

    public long? CreatedBy { get; set; }

    public long? UpdatedBy { get; set; }

    public virtual User? CreatedByNavigation { get; set; }

    public virtual ICollection<SalaAula> SalaAulas { get; set; } = new List<SalaAula>();

    public virtual User? UpdatedByNavigation { get; set; }

    public virtual User? User { get; set; }
}
