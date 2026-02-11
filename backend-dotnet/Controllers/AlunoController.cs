using backend_dotnet.Models;
using Microsoft.AspNetCore.Mvc;

namespace backend_dotnet.Controllers
{
    public class AlunoController : Controller
    {

        Aluno aluno = new Aluno
        {
            Id = 1,
            Nome = "João Silva",
            Idade = 20,
            Curso = "Engenharia de Software"
        };

        public IActionResult Index ()
        {
            return View();
        }

        [HttpGet("obter-aluno")]
        public IActionResult ObterAluno ()
        {
            return aluno != null ? Ok(aluno) : NotFound("Aluno não encontrado");
        }
    }
}
