using System;
using System.Collections.Generic;

namespace backend_dotnet.Models;

public partial class User
{
    public long Id { get; set; }

    public string Email { get; set; } = null!;

    public string Password { get; set; } = null!;

    public DateTime? CreatedAt { get; set; }

    public DateTime? UpdatedAt { get; set; }

    public virtual ICollection<Admin> AdminUsers { get; set; } = new List<Admin>();

    public virtual ICollection<Aluno> AlunoUsers { get; set; } = new List<Aluno>();

    public virtual ICollection<Professor> ProfessorUsers { get; set; } = new List<Professor>();
}
