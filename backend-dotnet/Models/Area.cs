namespace backend_dotnet.Models
{
    public class Area
    {
        public int IdArea { get; set; }
        public string NomeArea { get; set;}
        public int SituacaoArea { get; set; }
        public DateTime? CreatedAt { get; set; }
        public DateTime? UpdateAt { get; set; }

        public virtual ICollection<ProfessorMateria> ProfessorMateria { get; set; }
        public virtual ICollection<AlunoPerfil> AlunosPerfis { get; set; }
        public virtual ICollection<ProfessorPerfil> ProfessorPerfis { get; set; }
    }
}