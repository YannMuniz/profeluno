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

        [HttpDelete("DeletarSimulado/{idSimulado}")]
        public async Task<IActionResult> DeletarSimuladoAsync(int idSimulado)
        {
            var result = await _simuladoService.DeletarSimulado(idSimulado);
            if(result) return Ok("Simulado deletado com sucesso!");
            return NotFound("Simulado não encontrado para exclusão.");
        }

        [HttpPut("AtualizarSimulado")]
        public async Task<IActionResult> AtualizarSimuladoAsync([FromBody] AtualizarSimuladoRequest request)
        {
            var result = await _simuladoService.AtualizaSimuladoAsync(request);
            if(result) return Ok("Simulado atualizado com sucesso!");
            return BadRequest("Erro ao atualizar o simulado. Verifique os dados informados.");
        }

        [HttpGet("RetornaQuantidadeSimuladosPorUsuario/{idUsuario}")]
        public async Task<IActionResult> RetornaQuantidadeSimuladosPorUsuarioAsync(int idUsuario)
        {
            var result = await _simuladoService.RetornaQuantidadeSimuladosIdUsuario(idUsuario);
            return Ok(result);
        }
    }
}
