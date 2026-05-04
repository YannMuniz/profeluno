using backend_dotnet.Models.Requests;
using backend_dotnet.Services.Interfaces;
using Microsoft.AspNetCore.Http;
using Microsoft.AspNetCore.Mvc;

namespace backend_dotnet.Controllers
{
    [Route("v1/[controller]")]
    [ApiController]
    public class ProfessorMateriaController : ControllerBase
    {
        private readonly IProfessorMateriaService _ProfessorMateriaService;
        public ProfessorMateriaController(IProfessorMateriaService ProfessorMateriaService)
        {
            _ProfessorMateriaService = ProfessorMateriaService;
        }

        [HttpGet("RetornaTodosProfessorMaterias")]
        public async Task<IActionResult> RetornaTodosProfessorMateria()
        {
            var conteudos = await _ProfessorMateriaService.RetornaTodosProfessorMateria();
            return Ok(conteudos);
        }

        [HttpGet("RetornaProfessorMateriaPorId/{idProfessorMateria}")]
        public async Task<IActionResult> RetornaProfessorMateriaPorId(int idProfessorMateria)
        {
            var conteudo = await _ProfessorMateriaService.RetornaProfessorMateriaPorId(idProfessorMateria);
            return Ok(conteudo);
        }

        [HttpPost("CadastrarProfessorMateria")]
        public async Task<IActionResult> CadastrarProfessorMateria([FromBody] CadastrarProfessorMateriaRequest ProfessorMateria)
        {
            var conteudo = await _ProfessorMateriaService.CadastraProfessorMateria(ProfessorMateria);
            return Ok(conteudo);
        }

        [HttpPut("AtualizarProfessorMateria")]
        public async Task<IActionResult> AtualizarProfessorMateria([FromBody] AtualizarProfessorMateriaRequest ProfessorMateria)
        {
            var conteudo = await _ProfessorMateriaService.AtualizarProfessorMateria(ProfessorMateria);
            return Ok(conteudo);
        }

        [HttpDelete("DeletarProfessorMateria/{idProfessorMateria}")]
        public async Task<IActionResult> DeletarProfessorMateria(int idProfessorMateria)
        {
            var conteudo = await _ProfessorMateriaService.DeletarProfessorMateria(idProfessorMateria);
            if(conteudo) return Ok("Área-Matéria deletada com sucesso");
            return NotFound("Não foi encontrado uma Área-Matéria com esse id");
        }
    }
}
