using backend_dotnet.Services.Interfaces;
using Microsoft.AspNetCore.Mvc;
using backend_dotnet.Models;

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

        [HttpGet("RetornaTodosUsuarios")]
        public async Task<IActionResult> RetornaUsuariosAsync()
        {
            var users = await _userService.RetornaTodosUsuariosAsync();
            return Ok(users);
        }

        [HttpGet("RetornaUsuarioPorId/{idUsuario}")]
        public async Task<IActionResult> RetornaUsuarioPorIdAsync(int idUsuario)
        {
            var user = await _userService.RetornaUsuarioPorIdAsync(idUsuario);
            if (user == null) return NotFound();
            return Ok(user);
        }

        [HttpPost("CadastraUsuario")]
        public async Task<IActionResult> CadastraUsuarioAsync(User user)
        {
            var userCadastrado = await _userService.CadastraUsuarioAsync(user);
            return Ok(userCadastrado);
        }

        [HttpPut("AtualizaUsuario")]
        public async Task<IActionResult> AtualizaUsuarioAsync(User user)
        {
            var userAtualizado = await _userService.AtualizaUsuarioAsync(user);
            return Ok(userAtualizado);
        }
    }
}
