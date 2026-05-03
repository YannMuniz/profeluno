namespace backend_dotnet.Models.Requests
{
    public class AtualizarAreaMateriaRequest
    {
        public int IdAreaMateria { get; set; }
        public int IdArea { get; set; }
        public int IdMateria { get; set; }
        public int SituacaoAreaMateria { get; set; }
    }
}
