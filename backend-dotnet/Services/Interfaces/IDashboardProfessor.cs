namespace backend_dotnet.Services.Interfaces
{
    public interface IDashboardProfessor
    {
        public Task<int> TotalAulas(int idProfessor);
        public Task<int> AulasAtivas(int idProfessor);
        public Task<int> AulasPendentes(int idProfessor);
        public Task<int> AulasConcluidas(int idProfessor);
        public Task<int> ConteudosCriados(int idProfessor);
        public Task<int> SimuladoCriado(int idProfessor);
    }
}