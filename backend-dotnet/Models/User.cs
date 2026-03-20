using System;
using System.Collections.Generic;

namespace backend_dotnet.Models;

public partial class User
{
    public long Id { get; set; }
    public string Email { get; set; }
    public string Password { get; set; }
    public int IdCargo { get; set; }
    
    public virtual Cargo Cargo { get; set; }

    public string Nome_Usuario { get; set; }
    public DateTime? CreatedAt { get; set; }
    public DateTime? UpdatedAt { get; set; }
}
