namespace backend_dotnet.Models
{
    public class ProfessorPerfil
    {
        public int IdProfessorPerfil { get; set; }
        public int IdUsuario { get; set; }
        public string? Formacao { get; set; }
        public int IdEscolaridade { get; set; }
        public int IdArea { get; set; }
        public float Avalicao { get; set; }
        public int TotalAvaliacao { get; set; }
        public int TotalAlunos { get; set; }
        public DateTime? CreatedAt { get; set; }
        public DateTime? UpdateAt { get; set; }

        public virtual Area Area { get; set; }
        public virtual User Users { get; set; }
    }
}