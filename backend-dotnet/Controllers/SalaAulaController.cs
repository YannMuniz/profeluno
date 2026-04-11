using backend_dotnet.Models.Requests;
using backend_dotnet.Services.Interfaces;
using Microsoft.AspNetCore.Mvc;

namespace backend_dotnet.Controllers
{
    [ApiController]
    [Route("v1/[controller]")]
    public class SalaAulaController : ControllerBase
    {
        private readonly ISalaAulaService _salaAulaService;

        public SalaAulaController(ISalaAulaService salaAulaInterface)
        {
            _salaAulaService = salaAulaInterface;
        }

        [HttpGet("RetornaTodasSalasAula")]
        public async Task<IActionResult> RetornaSalasAula()
        {
            var response = await _salaAulaService.RetornaTodasSalasAula();
            return Ok(response);
        }

        [HttpGet("RetornaSalaAulaPorId/{idSalaAula}")]
        public async Task<IActionResult> RetornaSalaAulaPorId(int idSalaAula)
        {
            var response = await _salaAulaService.RetornaSalaAulaPorId(idSalaAula);
            if(response == null) return NotFound();
            return Ok(response);
        }

        [HttpGet("RetornaSalaAulaPorProfessor/{idProfessor}")]
        public async Task<IActionResult> RetornaSalaAulaPorProfessor(int idProfessor)
        {
            var response = await _salaAulaService.RetornaSalaAulaPorProfessor(idProfessor);
            if(response == null) return NotFound();
            return Ok(response);
        }

        [HttpPost("CadastrarSalaAula")]
        public async Task<IActionResult> CadastrarSalaAula([FromBody] CadastrarSalaAulaRequest request)
        {
            var response = await _salaAulaService.CadastraSalaAula(request);
            if(response == 0) return BadRequest();
            return Ok(true);
        }

        [HttpPut("AtualizarSalaAula")]
        public async Task<IActionResult> AtualizarSalaAula([FromBody] AtualizarSalaAulaRequest request)
        {
            var response = await _salaAulaService.AtualizaSalaAula(request);
            if(response == false) return BadRequest();
            return Ok(true);
        }

        [HttpDelete("DeletarSalaAula/{idSalaAula}")]
        public async Task<IActionResult> DeletarSalaAula(int idSalaAula)
        {
            var response = await _salaAulaService.DeletarSalaAula(idSalaAula);
            if(response == false) return BadRequest();
            return Ok(true);
        }

        [HttpPut("TrocarStatusSalaAula")]
        public async Task<IActionResult> TrocarStatusSalaAula([FromBody] TrocaStatusSalaAulaRequest request)
        {
            var response = await _salaAulaService.TrocarStatusSalaAula(request);
            if(response == null) return BadRequest();
            return Ok(response);
        }
    }
}