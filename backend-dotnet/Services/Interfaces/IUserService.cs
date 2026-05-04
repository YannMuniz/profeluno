using backend_dotnet.Models;
using backend_dotnet.Models.Requests;
using backend_dotnet.Models.Responses;

namespace backend_dotnet.Services.Interfaces
{
    public interface IUserService
    {
        public Task<IEnumerable<User>> RetornaTodosUsuariosAsync();
        public Task<User> RetornaUsuarioPorIdAsync(int idUsuario);
        public Task<User> RetornaUsuarioPorNomeUsuarioIdCargo(string nomeUsuario, int idCargo);
        public Task<User> RetornaUsuarioPorNomeAsync(string nomeUsuario);
        public Task<User> RetornaUsuarioPorCargoAsync(int idCargo);
        public Task<AtualizarUsuarioRequest> AtualizaUsuarioAsync(AtualizarUsuarioRequest user);
        public Task<LoginResponse> LoginAsync(LoginRequest loginRequest);
        public Task<bool> CadastrarUsuario(CadastroRequest cadastro);
        public Task<bool> DeletarUsuarioAsync(int idUsuario);
    }
}
