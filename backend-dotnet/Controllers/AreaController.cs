using backend_dotnet.Models;
using backend_dotnet.Services.Interfaces;
using Microsoft.AspNetCore.Mvc;

namespace backend_dotnet.Controllers
{
    [ApiController]
    [Route("v1/[controller]")]

    public class AreaController :ControllerBase
    {
    private readonly IAreaService _service;

        public AreaController(IAreaService area)
        {
            _service = area;
        }


        [HttpGet("RetornaTodasAreasAsync")]
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
    }
}