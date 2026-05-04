namespace backend_dotnet.Models.Requests
{
    public class AtualizarProfessorMateriaRequest
    {
        public int IdProfessorMateria { get; set; }
        public int IdArea { get; set; }
        public int IdMateria { get; set; }
        public int SituacaoProfessorMateria { get; set; }
    }
}
