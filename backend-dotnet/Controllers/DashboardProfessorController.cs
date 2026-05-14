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
            var response = _dashboardProfessor.TotalAulas(idProfessor);
            if(response == null) return BadRequest();

            return Ok(response);
        }

        [HttpGet("AulasAtivas/{idProfessor}")]
        public async Task<IActionResult> AulasAtivas(int idProfessor)
        {
            var response = _dashboardProfessor.AulasAtivas(idProfessor);
            if(response == null) return BadRequest();

            return Ok(response);
        }

        [HttpGet("AulasPendentes/{idProfessor}")]
        public async Task<IActionResult> AulasPendentes(int idProfessor)
        {
            var response = _dashboardProfessor.AulasPendentes(idProfessor);
            if(response == null) return BadRequest();

            return Ok(response);
        }

        [HttpGet("AulasConcluidas/{idProfessor}")]
        public async Task<IActionResult> AulasConcluidas(int idProfessor)
        {
            var response = _dashboardProfessor.AulasConcluidas(idProfessor);
            if(response == null) return BadRequest();

            return Ok(response);
        }

        [HttpGet("ConteudosCriados/{idProfessor}")]
        public async Task<IActionResult> ConteudosCriados(int idProfessor)
        {
            var response = _dashboardProfessor.ConteudosCriados(idProfessor);
            if(response == null) return BadRequest();

            return Ok(response);
        }

        [HttpGet("SimuladoCriado/{idProfessor}")]
        public async Task<IActionResult> SimuladoCriado(int idProfessor)
        {
            var response = _dashboardProfessor.SimuladoCriado(idProfessor);
            if(response == null) return BadRequest();

            return Ok(response);
        }
    }
}