using backend_dotnet.Models;
using backend_dotnet.Models.Requests;

namespace backend_dotnet.Services.Interfaces
{
    public interface IProfessorMateriaService
    {
        public Task<List<ProfessorMateria>> RetornaTodosProfessorMateria();
        public Task<ProfessorMateria> RetornaProfessorMateriaPorId(int idProfessorMateria);
        public Task<ProfessorMateria> CadastraProfessorMateria(CadastrarProfessorMateriaRequest ProfessorMateria);
        public Task<ProfessorMateria> AtualizarProfessorMateria(AtualizarProfessorMateriaRequest ProfessorMateria);
        public Task<bool> DeletarProfessorMateria(int idProfessorMateria);
    }
}
