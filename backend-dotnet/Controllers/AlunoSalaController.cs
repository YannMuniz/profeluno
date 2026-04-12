using backend_dotnet.Models;
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

        [HttpGet("RetornaAlunoSalaPorId/{IdAlunoSala}")]
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
    }
}
