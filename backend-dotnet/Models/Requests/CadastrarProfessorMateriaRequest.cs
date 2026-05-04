namespace backend_dotnet.Models.Requests
{
    public class CadastrarProfessorMateriaRequest
    {
        public int IdProfessor { get; set; }
        public int IdMateria { get; set; }
        public int SituacaoProfessorMateria { get; set; }
    }
}
