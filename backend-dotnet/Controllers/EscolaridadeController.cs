using backend_dotnet.Models.Requests;
using backend_dotnet.Services.Interfaces;
using Microsoft.AspNetCore.Http;
using Microsoft.AspNetCore.Mvc;

namespace backend_dotnet.Controllers
{
    [Route("api/[controller]")]
    [ApiController]
    public class EscolaridadeController : ControllerBase
    {
        private readonly IEscolaridadeService _escolaridadeService;

        public EscolaridadeController(IEscolaridadeService escolaridadeService)
        {
            _escolaridadeService = escolaridadeService;
        }

        [HttpGet("RetornaTodasEscolaridades")]
        public async Task<IActionResult> RetornaTodasEscolaridades()
        {
            var conteudos = await _escolaridadeService.RetornaTodasEscolaridades();
            return Ok(conteudos);
        }

        [HttpGet("RetornaEscolaridadePorId/{idEscolaridade}")]
        public async Task<IActionResult> RetornaEscolaridadePorId(int idEscolaridade)
        {
            var conteudo = await _escolaridadeService.RetornaEscolaridadePorId(idEscolaridade);
            return Ok(conteudo);
        }

        [HttpPost("CadastrarEscolaridade")]
        public async Task<IActionResult> CadastrarEscolaridade([FromBody] CadastrarEscolaridadeRequest escolaridade)
        {
            var conteudo = await _escolaridadeService.CadastraEscolaridade(escolaridade);
            return Ok(conteudo);
        }

        [HttpPut("AtualizarEscolaridade")]
        public async Task<IActionResult> AtualizarEscolaridade([FromBody] AtualizarEscolaridadeRequest escolaridade)
        {
            var conteudo = await _escolaridadeService.AtualizarEscolaridade(escolaridade);
            return Ok(conteudo);
        }

        [HttpDelete("DeletarEscolaridade/{idEscolaridade}")]
        public async Task<IActionResult> DeletarEscolaridade(int idEscolaridade)
        {
            var conteudo = await _escolaridadeService.DeletarEscolaridade(idEscolaridade);
            if(conteudo) return Ok("Escolaridade deletada com sucesso");
            return NotFound("Não foi encontrado uma Escolaridade com esse id");
        }
    }
}
