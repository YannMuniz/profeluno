using backend_dotnet.Data;
using backend_dotnet.Models;

namespace backend_dotnet.Services
{
    public class AlunoService
    {
        private readonly ProfelunoContext _context;

        public AlunoService(ProfelunoContext context)
        {
            _context = context;
        }

        public List<Aluno> GetAllAlunos()
        {
            return _context.Alunos.ToList();
        }
    }
}
