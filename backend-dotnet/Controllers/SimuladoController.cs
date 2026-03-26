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
    }
}
