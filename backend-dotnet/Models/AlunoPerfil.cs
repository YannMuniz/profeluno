namespace backend_dotnet.Models
{
    public class AlunoPerfil
    {
        public int IdAlunoPerfil { get; set; }
        public int IdUser { get; set; }
        public string? Periodo { get; set; }
        public int IdEscolaridade { get; set; }
        public int IdArea { get; set; }
        public DateTime? CreatedAt { get; set; }
        public DateTime? UpdatedAt { get; set; }

        public virtual Area Area { get; set; }
        public virtual User Users { get; set; }
    }
}