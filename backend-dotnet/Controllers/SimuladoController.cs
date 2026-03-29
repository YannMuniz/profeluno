using backend_dotnet.Services.Interfaces;
using Microsoft.AspNetCore.Mvc;
using backend_dotnet.Models.Requests;

namespace backend_dotnet.Controllers
{
    [ApiController]
    [Route("v1/[controller]")]
    public class SimuladoController : Controller
    {
        private ISimuladoService _simuladoService;

        public SimuladoController(ISimuladoService simuladoService)
        {
            _simuladoService = simuladoService;
        }

        [HttpPost("CadastrarSimulado")]
        public async Task<IActionResult> CadastrarSimuladoAsync(IEnumerable<CriarSimuladoRequest> simulados)
        {
            var result = await _simuladoService.CadastrarSimulado(simulados);
            if(result) return Ok("Simulados cadastrados com sucesso!");
            return BadRequest("Erro ao cadastrar os simulados.");
        }

        [HttpGet("RetornaTodosSimuladosQuestoes")]
        public async Task<IActionResult> RetornaTodosSimuladosQuestoesAsync()
        {
            var result = await _simuladoService.RetornaTodosSimuladosAsync();
            if(result != null) return Ok(result);
            return NotFound("Nenhum simulado encontrado.");
        }

        [HttpGet("RetornaSimuladoPorId/{idSimulado}/{idUsuario}")]
        public async Task<IActionResult> RetornaSimuladoPorIdAsync(int idSimulado, int idUsuario)
        {
            var result = await _simuladoService.RetornaSimuladoPorIdAsync(idSimulado, idUsuario);
            if(result != null) return Ok(result);
            return NotFound("Simulado não encontrado.");
        }

        [HttpGet("RetornaSimuladosPorMateria/{idMateria}/{idUsuario}")]
        public async Task<IActionResult> RetornaSimuladosPorMateriaAsync(int idMateria, int idUsuario)
        {
            var result = await _simuladoService.RetornaSimuladosPorMateriaAsync(idMateria, idUsuario);
            if(result != null) return Ok(result);
            return NotFound("Nenhum simulado encontrado para a matéria informada.");
        }

        [HttpGet("RetornaSimuladoQuestoesPorIdSimulado/{idSimulado}")]
        public async Task<IActionResult> RetornaSimuladoQuestoesPorIdSimuladoAsync(int idSimulado)
        {
            var result = await _simuladoService.RetornaSimuladoQuestoesPorIdSimulado(idSimulado);
            if(result != null) return Ok(result);
            return NotFound("Nenhuma questão encontrada para o simulado informado.");
        }

        [HttpGet("RetornaSimuladosPorUsuario/{idUsuario}")]
        public async Task<IActionResult> RetornaSimuladosPorUsuarioAsync(int idUsuario)
        {
            var result = await _simuladoService.RetornaSimuladosPorUsuarioAsync(idUsuario);
            if(result != null) return Ok(result);
            return NotFound("Nenhum simulado encontrado para o usuário informado.");
        }
    }
}
