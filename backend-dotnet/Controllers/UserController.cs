using backend_dotnet.Models;
using backend_dotnet.Models.Responses;
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
        [HttpGet("RetornaTodosUsuarios")]
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
        [HttpGet("RetornaUsuarioPorId/{idUsuario}")]
        public async Task<IActionResult> RetornaUsuarioPorIdAsync(int idUsuario)
        {
            var user = await _userService.RetornaUsuarioPorIdAsync(idUsuario);
            if(user == null) return NotFound();
            return Ok(user);
        }

        /// <summary>
        /// Cadastra um novo Usuario
        /// </summary>
        /// <param name="user"></param>
        /// <returns></returns>
        [HttpPost("CadastraUsuario")]
        public async Task<IActionResult> CadastraUsuarioAsync(User user)
        {
            var userCadastrado = await _userService.CadastraUsuarioAsync(user);
            return Ok(userCadastrado);
        }

        /// <summary>
        /// Atualiza o Usuario
        /// </summary>
        /// <param name="user"></param>
        /// <returns></returns>
        [HttpPut("AtualizaUsuario")]
        public async Task<IActionResult> AtualizaUsuarioAsync(User user)
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
        public async Task<IActionResult> LoginAsync(string email, string password)
        {
            var login = await _userService.LoginAsync(email, password);

            return Ok(login);
        }
    }
}
