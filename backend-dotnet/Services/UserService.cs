using backend_dotnet.Data;
using backend_dotnet.Models;
using backend_dotnet.Models.Responses;
using backend_dotnet.Services.Interfaces;
using Microsoft.EntityFrameworkCore;

namespace backend_dotnet.Services
{
    public class UserService : IUserService
    {
        private readonly ProfelunoContext _context;

        public UserService(ProfelunoContext context)
        {
            _context = context;
        }

        public async Task<IEnumerable<User>> RetornaTodosUsuariosAsync()
        {
            return await _context.Users.ToListAsync();
        }

        public async Task<User> RetornaUsuarioPorIdAsync(int idUsuario)
        {
            return await _context.Users.FirstOrDefaultAsync(x => x.Id == idUsuario);
        }

        public async Task<User> CadastraUsuarioAsync(User user)
        {
            _context.Users.Add(user);
            await _context.SaveChangesAsync();

            return user;
        }

        public async Task<User> AtualizaUsuarioAsync(User user)
        {
            _context.Users.Update(user);
            await _context.SaveChangesAsync();
            return user;
        }

        // Só, aluno professor ou admin, e autorizado ou não para o acesso
        public async Task<LoginResponse> LoginAsync(string email, string password)
        {
            LoginResponse login = new LoginResponse();

            var user = await _context.Users
                .Include(u => u.AdminUsers)
                .Include(v => v.AlunoUsers)
                .Include(w => w.ProfessorUsers)
                .AsNoTracking()
                .FirstOrDefaultAsync(x => x.Email == email && x.Password == password);

            if(user == null) 
            {
                login = new LoginResponse
                {
                    Cargo = "UNAUTHORIZED",
                    Autorizacao = false
                };
            }
            else
            {
                login = new LoginResponse
                {
                    Cargo = GetCargo(user),
                    Autorizacao = true
                };
            }

            return login;
        }

        public string GetCargo(User user)
        {
            if(user.AdminUsers.Any()) return "Admin";
            if(user.ProfessorUsers.Any()) return "Professor";
            if(user.AlunoUsers.Any()) return "Aluno";
            return "Convidado";
        }
    }
}
