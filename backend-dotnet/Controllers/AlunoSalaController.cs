using backend_dotnet.Models;
using backend_dotnet.Models.Requests;
using backend_dotnet.Models.Responses;
using backend_dotnet.Services.Interfaces;
using Microsoft.AspNetCore.Mvc;

namespace backend_dotnet.Controllers
{
    [ApiController]
    [Route("v1/[controller]")]
    public class AlunoSalaController : ControllerBase
    {
        private IAlunoSalaService _alunoSalaService;

        public AlunoSalaController(IAlunoSalaService alunoSalaService)
        {
            _alunoSalaService = alunoSalaService;
        }

        [HttpGet("RetornaTodosAlunoSala")]
        public async Task<IActionResult> RetornaTodosAlunoSala()
        {
            var response = await _alunoSalaService.RetornaTodosAlunoSala();
            return Ok(response);
        }

        [HttpGet("RetornaAlunoSalaPorId/{idAlunoSala}")]
        public async Task<IActionResult> RetornaAlunoSalaPorId(int idAlunoSala)
        {
            var response = await _alunoSalaService.RetornaAlunoSalaPorId(idAlunoSala);
            if(response == null) return NotFound();

            return Ok(response);
        }

        [HttpGet("RetornarAlunoSalaPorIdAluno/{idAluno}")]
        public async Task<IActionResult> RetornarAlunoSalaPorIdAluno(int idAluno)
        {
            var response = await _alunoSalaService.RetornarAlunoSalaPorIdAluno(idAluno);
            if(response == null) return NotFound();

            return Ok(response);
        }

        [HttpGet("RetornaQtdAlunosSala/{idSalaAula}")]
        public async Task<IActionResult> RetornaQtdAlunosSala(int idSalaAula)
        {
            var response = await _alunoSalaService.RetornaQtdAlunosSala(idSalaAula);
            if(response == null) return NotFound();

            return Ok(response);
        }

        [HttpPost("CadastraAlunoSala")]
        public async Task<IActionResult> CadastrarAlunoSala([FromBody] CadastrarAlunoSalaRequest request)
        {
            var response = await _alunoSalaService.CadastraAlunoSala(request);
            if(response == null) return BadRequest(false);

            return Ok(true);
        }

        [HttpPut("AtualizarAlunoSala")]
        public async Task<IActionResult> AtualizarAlunoSala([FromBody] AtualizarAlunoSalaRequest request)
        {
            var response = await _alunoSalaService.AtualizarAlunoSala(request);
            if(response == null) return BadRequest();

            return Ok(true);
        }

        [HttpDelete("DeletarAlunoSala/{idAlunoSala}")]
        public async Task<IActionResult> DeletarAlunoSala(int idAlunoSala)
        {
            try
            {
                var response = await _alunoSalaService.DeletarAlunoSala(idAlunoSala);

                return Ok(true);
            }
            catch(Exception ex)
            {
                return BadRequest(ex.Message);
            }
        }
    }
}
