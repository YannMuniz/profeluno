namespace backend_dotnet.Models
{
    public class AreaMateria
    {
        public int IdAreaMateria { get; set; }
        public int IdArea { get; set; }
        public int IdMateria { get; set; }
        public int SituacaoAreaMateria { get; set; }
        public DateTime? CreatedAt { get; set; }
        public DateTime? UpdatedAt { get; set; }
        
        public virtual Area Areas { get; set; }
        public virtual Materia Materias { get; set; }
    }
}