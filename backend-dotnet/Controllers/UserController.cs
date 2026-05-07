using backend_dotnet.Models;
using backend_dotnet.Models.Requests;
using backend_dotnet.Services.Interfaces;
using Microsoft.AspNetCore.Mvc;

namespace backend_dotnet.Controllers
{
    [ApiController]
    [Route("v1/[controller]")]
    public class UserController : ControllerBase
    {
        private readonly IUserService _userService;
        public UserController(IUserService userService)
        {
            _userService = userService;
        }

        /// <summary>
        /// Retorna Todos os Usuarios
        /// </summary>
        /// <returns></returns>
        [HttpGet("ListarUsuarios")]
        public async Task<IActionResult> RetornaUsuariosAsync()
        {
            var users = await _userService.RetornaTodosUsuariosAsync();
            return Ok(users);
        }

        /// <summary>
        /// Retorna o Usuario por Id
        /// </summary>
        /// <param name="idUsuario"></param>
        /// <returns></returns>
        [HttpGet("BuscarUsuario/{idUsuario}")]
        public async Task<IActionResult> RetornaUsuarioPorIdAsync(int idUsuario)
        {
            var user = await _userService.RetornaUsuarioPorIdAsync(idUsuario);
            if(user == null) return NotFound();
            return Ok(user);
        }

        [HttpGet("RetornaUsuarioPorNomeUsuarioIdCargo/{nomeUsuario}/{idCargo}")]
        public async Task<IActionResult> RetornaUsuarioPorNomeUsuarioIdCargo(string nomeUsuario, int idCargo)
        {
            var user = await _userService.RetornaUsuarioPorNomeUsuarioIdCargo(nomeUsuario, idCargo);
            if(user == null) return NotFound();
            return Ok(user);
        }

        /// <summary>
        /// Retorna o Usuario por Nome
        /// </summary>
        /// <param name="nomeUsuario"></param>
        /// <returns></returns>
        [HttpGet("RetornaUsuarioPorNome/{nomeUsuario}")]
        public async Task<IActionResult> RetornaUsuarioPorNomeAsync(string nomeUsuario)
        {
            var user = await _userService.RetornaUsuarioPorNomeAsync(nomeUsuario);
            if(user == null) return NotFound();
            return Ok(user);
        }

        /// <summary>
        /// Retorna o Usuario por Cargo
        /// </summary>
        /// <param name="cargoUsuario"></param>
        /// <returns></returns>
        [HttpGet("RetornaUsuarioPorCargo/{idCargo}")]
        public async Task<IActionResult> RetornaUsuarioPorCargoAsync(int idCargo)
        {
            var user = await _userService.RetornaUsuarioPorCargoAsync(idCargo);
            if(user == null) return NotFound();
            return Ok(user);
        }

        /// <summary>
        /// Atualiza o Usuario
        /// </summary>
        /// <param name="user"></param>
        /// <returns></returns>
        [HttpPut("AtualizarUsuario")]
        public async Task<IActionResult> AtualizaUsuarioAsync([FromBody] AtualizarUsuarioRequest user)
        {
            var userAtualizado = await _userService.AtualizaUsuarioAsync(user);
            return Ok(userAtualizado);
        }

        /// <summary>
        /// Metodo que verifica se o email e senha do usuario estão corretos, para realizar o login e retorna o cargo do usuario (Admin, Aluno ou Professor) e a autorização para o acesso (true ou false)
        /// </summary>
        /// <param name="email"></param>
        /// <param name="password"></param>
        /// <returns></returns>
        [HttpPost("Login")]
        public async Task<IActionResult> LoginAsync([FromBody] LoginRequest loginRequest)
        {
            var login = await _userService.LoginAsync(loginRequest);

            return Ok(login);
        }

        [HttpPost("CadastrarUsuario")]
        public async Task<IActionResult> CadastrarUsuario([FromBody] CadastroRequest cadastro)
        {
            var resultado = await _userService.CadastrarUsuario(cadastro);

            if(resultado) return Ok("Usuário cadastrado com sucesso!");

            return BadRequest("Erro ao cadastrar usuário.");
        }

        [HttpDelete("DeletarUsuario/{idUsuario}")]
        public async Task<IActionResult> DeletarUsuarioAsync(int idUsuario)
        {
            var resultado = await _userService.DeletarUsuarioAsync(idUsuario);
            if(resultado) return Ok("Usuário deletado com sucesso!");
            return BadRequest("Erro ao deletar usuário.");
        }

        [HttpGet("RetornaQuantidadeUsuarios")]
        public async Task<IActionResult> RetornaQuantidadeUsuarios()
        {
            var quantidade = await _userService.RetornaQuantidadeUsuarios();
            return Ok(quantidade);
        }
    }
}
