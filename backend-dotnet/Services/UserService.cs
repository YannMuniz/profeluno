using backend_dotnet.Data;
using backend_dotnet.Models;
using backend_dotnet.Models.Requests;
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

        public async Task<User> AtualizaUsuarioAsync(User user)
        {
            _context.Users.Update(user);
            await _context.SaveChangesAsync();
            return user;
        }

        public async Task<LoginResponse> LoginAsync(LoginRequest loginRequest)
        {
            LoginResponse login = new LoginResponse();

            var user = await _context.Users.FirstOrDefaultAsync(x => x.Email == loginRequest.Email && x.Password == loginRequest.Password);

            if(user == null) 
            {
                login = new LoginResponse
                {
                    IdCargo = 0,
                    Autorizacao = false
                };
            }
            else
            {
                login = new LoginResponse
                {
                    IdCargo = user.IdCargo,
                    Autorizacao = true
                };
            }

            return login;
        }

        public async Task<bool> CadastrarUsuario(CadastroRequest cadastro)
        {
            if(cadastro == null) return false;

            User user = new User
            {
                Id = 3,
                Nome_Usuario = cadastro.Nome,
                Email = cadastro.Email,
                IdCargo = cadastro.IdCargo,
                Password = cadastro.Senha,
                CreatedAt = DateTime.Now,
                UpdatedAt = DateTime.Now
            };

            await _context.Users.AddAsync(user);
            await _context.SaveChangesAsync();

            return true;
        }
    }
}
