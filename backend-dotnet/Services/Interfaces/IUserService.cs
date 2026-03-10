using backend_dotnet.Models;
using backend_dotnet.Models.Responses;

namespace backend_dotnet.Services.Interfaces
{
    public interface IUserService
    {
        public Task<IEnumerable<User>> RetornaTodosUsuariosAsync();
        public Task<User> RetornaUsuarioPorIdAsync(int idUsuario);
        public Task<User> CadastraUsuarioAsync(User user);
        public Task<User> AtualizaUsuarioAsync(User user);
        public Task<LoginResponse> LoginAsync(string email, string password);
    }
}
