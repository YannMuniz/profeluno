using backend_dotnet.Models.Requests;
using backend_dotnet.Services.Interfaces;
using Microsoft.AspNetCore.Http;
using Microsoft.AspNetCore.Mvc;

namespace backend_dotnet.Controllers
{
    [Route("api/[controller]")]
    [ApiController]
    public class AreaMateriaController : ControllerBase
    {
        private readonly IAreaMateriaService _areaMateriaService;
        public AreaMateriaController(IAreaMateriaService areaMateriaService)
        {
            _areaMateriaService = areaMateriaService;
        }

        [HttpGet("RetornaTodasAreasMaterias")]
        public async Task<IActionResult> RetornaTodasAreasMaterias()
        {
            var conteudos = await _areaMateriaService.RetornaTodasAreasMaterias();
            return Ok(conteudos);
        }

        [HttpGet("RetornaAreaMateriaPorId/{idAreaMateria}")]
        public async Task<IActionResult> RetornaAreaMateriaPorId(int idAreaMateria)
        {
            var conteudo = await _areaMateriaService.RetornaAreaMateriaPorId(idAreaMateria);
            return Ok(conteudo);
        }

        [HttpPost("CadastrarAreaMateria")]
        public async Task<IActionResult> CadastrarAreaMateria([FromBody] CadastraAreaMateriaRequest areaMateria)
        {
            var conteudo = await _areaMateriaService.CadastraAreaMateria(areaMateria);
            return Ok(conteudo);
        }

        [HttpPut("AtualizarAreaMateria")]
        public async Task<IActionResult> AtualizarAreaMateria([FromBody] AtualizarAreaMateriaRequest areaMateria)
        {
            var conteudo = await _areaMateriaService.AtualizarAreaMateria(areaMateria);
            return Ok(conteudo);
        }

        [HttpDelete("DeletarAreaMateria/{idAreaMateria}")]
        public async Task<IActionResult> DeletarAreaMateria(int idAreaMateria)
        {
            var conteudo = await _areaMateriaService.DeletarAreaMateria(idAreaMateria);
            if(conteudo) return Ok("Área-Matéria deletada com sucesso");
            return NotFound("Não foi encontrado uma Área-Matéria com esse id");
        }
    }
}
