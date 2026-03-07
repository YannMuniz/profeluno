using Microsoft.AspNetCore.Mvc;

namespace backend_dotnet.Controllers
{
    [ApiController]
    [Route("v1/[controller]")]
    public class AlunoController : Controller
    {
        [HttpGet("RetornaTodosAlunos")]
        public async Task<IActionResult> RetornaTodosAlunos()
        {
            return Ok();
        }
    }
}
