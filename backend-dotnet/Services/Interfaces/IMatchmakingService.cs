using backend_dotnet.Models;

namespace backend_dotnet.Services.Interfaces
{
    public interface IMatchmakingService
    {
        public Task<List<SalaAula>> AcharMelhorProfessor(int idMateria, int idProfessor);
    }
}
