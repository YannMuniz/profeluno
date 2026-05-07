using backend_dotnet.Models;
using backend_dotnet.Services.Interfaces;
using Microsoft.AspNetCore.Http;
using Microsoft.AspNetCore.Mvc;

namespace backend_dotnet.Controllers
{
    [ApiController]
    [Route("v1/[controller]")]
    public class MatchmakingController : ControllerBase
    {
        private readonly IMatchmakingService _matchmakingService;

        public MatchmakingController(IMatchmakingService matchmakingService)
        {
            _matchmakingService = matchmakingService;
        }

        [HttpGet("MatchmakingDeSala")]
        public async Task<IActionResult> AcharMelhorProfessor(int idMateria, int idArea)
        {
            try
            {
                var resultado = await _matchmakingService.AcharMelhorProfessor(idMateria, idArea);
                if(resultado == null) return NotFound("Não foi encotrado uma sala que se boa para você");

                return Ok(resultado);
            }
            catch(Exception ex)
            {
                return BadRequest(ex.Message);
            }
        }
    }
}
