using backend_dotnet.Models;
using backend_dotnet.Services.Interfaces;
using Microsoft.AspNetCore.Mvc;

namespace backend_dotnet.Controllers
{
    [ApiController]
    [Route("v1/[controller]")]

    public class AreaController : ControllerBase
    {
        private readonly IAreaService _service;

        public AreaController(IAreaService area)
        {
            _service = area;
        }


        [HttpGet("RetornaTodasAreas")]
        public async Task<IActionResult> RetornaTodasAreasAsync()
        {
            var conteudos = await _service.RetornaTodasAreas();
            return Ok(conteudos);
        }

        [HttpGet("RetornaAreaPorId")]
        public async Task<IActionResult> RetornaAreaPorId(int idArea)
        {
            var conteudo = await _service.RetornaAreaId(idArea);
            return Ok(conteudo);
        }

        [HttpPost("CadastrarArea")]
        public async Task<IActionResult> CadastrarArea(CadastraAreaRequest area)
        {
            var conteudo = await _service.CadastraArea(area);
            return Ok(conteudo);
        }

        [HttpPut("AtualizarArea")]
        public async Task<IActionResult> AtualizarArea(AtualizarAreaRequest area)
        {
            var conteudo = await _service.AtualizarArea(area);
            return Ok(conteudo);
        }

        [HttpDelete("DeletarArea")]
        public async Task<IActionResult> DeletarArea(int idArea)
        {
            var conteudo = await _service.DeletarArea(idArea);
            if(conteudo) return Ok("Área deletada com sucesso");
            return NotFound("Não foi encontrado uma Área com esse id");
        }
    }
}