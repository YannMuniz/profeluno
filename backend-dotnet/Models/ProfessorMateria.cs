namespace backend_dotnet.Models
{
    public class ProfessorMateria
    {
        public int IdProfessorMateria { get; set; }
        public int IdProfessor { get; set; }
        public int IdMateria { get; set; }
        public int SituacaoProfessorMateria { get; set; }
        public DateTime? CreatedAt { get; set; }
        public DateTime? UpdatedAt { get; set; }
        
        public virtual Materia Materias { get; set; }
    }
}