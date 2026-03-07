using backend_dotnet.Data;
using backend_dotnet.Models;
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
    }
}
