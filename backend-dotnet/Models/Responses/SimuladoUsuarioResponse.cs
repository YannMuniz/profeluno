namespace backend_dotnet.Models.Responses
{
    public class SimuladoUsuarioResponse
    {
        public int IdSimulado { get; set; }
        public string Titulo { get; set; }
        public string Descricao { get; set; }
        public bool Situacao { get; set; }
        public int IdMateria { get; set; }
        public int IdUser { get; set; }
        public Materia Materia { get; set; }
        public DateTime? CreatedAt { get; set; }
        public DateTime? UpdatedAt { get; set; }
        public int QuantidadeQuestoes { get; set; }
    }
}
