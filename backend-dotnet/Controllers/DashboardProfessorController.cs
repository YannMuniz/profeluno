using backend_dotnet.Services.Interfaces;
using Microsoft.AspNetCore.Http.HttpResults;
using Microsoft.AspNetCore.Mvc;

namespace backend_dotnet.Controllers
{
    [ApiController]
    [Route("v1/[controller]")]

    public class DashboardProfessorController : ControllerBase
    {
        private readonly IDashboardProfessorService _dashboardProfessor;

        public DashboardProfessorController(IDashboardProfessorService dashboardProfessor)
        {
            _dashboardProfessor = dashboardProfessor;
        }

        [HttpGet("TotalAulas/{idProfessor}")]
        public async Task<IActionResult> TotalAulas(int idProfessor)
        {
            var response = await _dashboardProfessor.TotalAulas(idProfessor);
            return Ok(response);
        }

        [HttpGet("AulasAtivas/{idProfessor}")]
        public async Task<IActionResult> AulasAtivas(int idProfessor)
        {
            var response = await _dashboardProfessor.AulasAtivas(idProfessor);
            return Ok(response);
        }

        [HttpGet("AulasPendentes/{idProfessor}")]
        public async Task<IActionResult> AulasPendentes(int idProfessor)
        {
            var response = await _dashboardProfessor.AulasPendentes(idProfessor);
            return Ok(response);
        }

        [HttpGet("ConteudosCriados/{idProfessor}")]
        public async Task<IActionResult> ConteudosCriados(int idProfessor)
        {
            var response = await _dashboardProfessor.ConteudosCriados(idProfessor);
            return Ok(response);
        }

        [HttpGet("SimuladoCriado/{idProfessor}")]
        public async Task<IActionResult> SimuladoCriado(int idProfessor)
        {
            var response = await _dashboardProfessor.SimuladoCriado(idProfessor);
            return Ok(response);
        }
    }
}