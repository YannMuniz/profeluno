using System;
using System.Collections.Generic;

namespace backend_dotnet.Models;

public partial class Admin
{
    public long Id { get; set; }

    public long? UserId { get; set; }

    public string NomeAdmin { get; set; } = null!;

    public DateTime? CreatedAt { get; set; }

    public DateTime? UpdatedAt { get; set; }

    public virtual User? User { get; set; }
}
