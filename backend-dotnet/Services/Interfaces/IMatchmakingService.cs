using backend_dotnet.Models;

namespace backend_dotnet.Services.Interfaces
{
    public interface IMatchmakingService
    {
        public Task<SalaAula> AcharMelhorProfessor(int idAluno, int idMateria, int idArea);
    }
}
