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
            return await _context.Users.AsNoTracking().ToListAsync();
        }

        public async Task<User> RetornaUsuarioPorIdAsync(int idUsuario)
        {
            return await _context.Users.AsNoTracking().FirstOrDefaultAsync(x => x.IdUser == idUsuario);
        }

        public async Task<User> RetornaUsuarioPorNomeAsync(string nomeUsuario)
        {
            return await _context.Users.AsNoTracking().FirstOrDefaultAsync(x => x.Nome_Usuario.ToUpper().StartsWith(nomeUsuario.ToUpper()));
        }

        public async Task<User> RetornaUsuarioPorCargoAsync(int idCargo)
        {
            return await _context.Users.AsNoTracking().FirstOrDefaultAsync(x => x.IdCargo == idCargo);
        }

        public async Task<User> RetornaUsuarioPorNomeUsuarioIdCargo(string nomeUsuario, int idCargo)
        {
            return await _context.Users.AsNoTracking().FirstOrDefaultAsync((x => x.Nome_Usuario == nomeUsuario && x.IdCargo == idCargo));
        }

        public async Task<AtualizaUsuarioRequest> AtualizaUsuarioAsync(AtualizaUsuarioRequest user)
        {
            var newUser = await _context.Users.FirstOrDefaultAsync(x => x.IdUser == user.IdUser);

            newUser.Nome_Usuario = user.Nome_Usuario;
            newUser.Email = user.Email;
            newUser.IdCargo = user.IdCargo;
            newUser.Password = user.Password;
            newUser.UpdatedAt = DateTime.Now;

            await _context.SaveChangesAsync();
            return user;
        }

        public async Task<LoginResponse> LoginAsync(LoginRequest loginRequest)
        {
            LoginResponse login = new LoginResponse();

            var user = await _context.Users.AsNoTracking().FirstOrDefaultAsync(x => x.Email == loginRequest.Email && x.Password == loginRequest.Password);

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

            var emailExistente = await _context.Users.AnyAsync(x => x.Email == cadastro.Email);

            if(emailExistente) return false;

            User user = new User
            {
                Nome_Usuario = cadastro.Nome,
                Email = cadastro.Email,
                IdCargo = cadastro.IdCargo,
                Password = cadastro.Senha,
                CreatedAt = DateTime.Now,
            };

            await _context.Users.AddAsync(user);
            await _context.SaveChangesAsync();

            return true;
        }

        public async Task<bool> DeletarUsuarioAsync(int idUsuario)
        {
            var user = await _context.Users.FirstOrDefaultAsync(x => x.IdUser == idUsuario);
            if(user == null) return false;
            _context.Users.Remove(user);
            await _context.SaveChangesAsync();
            return true;
        }
    }
}
