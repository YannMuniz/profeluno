namespace backend_dotnet.Models
{
    public class AreaMateria
    {
        public int IdAreaMateria { get; set; }
        public int IdArea { get; set; }
        public int IdMateria { get; set; }
        public int SituacaoMateria { get; set; }
        public DateTime CreatedAt { get; set; }
        public DateTime UpdateAt { get; set; }
    }
}