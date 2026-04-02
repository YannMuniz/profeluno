using backend_dotnet.Services.Interfaces;
using Microsoft.AspNetCore.Mvc;
using backend_dotnet.Models.Requests;

namespace backend_dotnet.Controllers
{
    [ApiController]
    [Route("v1/[controller]")]
    public class ConteudoController : ControllerBase
    {
        private readonly IConteudoService _conteudoService;

        public ConteudoController(IConteudoService conteudoService)
        {
            _conteudoService = conteudoService;
        }

        [HttpPost("CadastrarConteudo")]
        public async Task<IActionResult> CadastrarConteudo([FromForm] UploadConteudoRequest conteudo)
        {
            try
            {
                var result = await _conteudoService.CadastrarConteudo(conteudo);
                if (result) return Ok("Conteúdo cadastrado com sucesso!");
                return BadRequest("Erro ao cadastrar conteúdo.");
            }
            catch (Exception ex)
            {
                return BadRequest($"Erro: {ex.Message}");
            }
        }

        [HttpGet("ListarConteudos")]
        public async Task<IActionResult> ListarConteudosAsync()
        {
            var conteudos = await _conteudoService.RetornaTodosConteudosAsync();
            return Ok(conteudos);
        }

        [HttpGet("RetornaConteudoPorIdProfessor/{idProfessor}")]
        public async Task<IActionResult> ListarConteudosPorIdProfessorAsync(int idProfessor)
        {
            var conteudos = await _conteudoService.RetornaConteudoPorIdProfessor(idProfessor);
            return Ok(conteudos);
        }

        [HttpGet("RetornaConteudoPorId/{idConteudo}")]
        public async Task<IActionResult> ListarConteudosPorIdAsync(int idConteudo)
        {
            var conteudo = await _conteudoService.RetornaConteudoPorIdConteudo(idConteudo);
            if(conteudo == null) return NotFound("Conteúdo não encontrado.");
            return Ok(conteudo);
        }

        [HttpGet("RetornaDadosDoArquivo/{idConteudo}")]
        public async Task<IActionResult> RetornaDadosDoArquivo(int idConteudo)
        {
            var arquivo = await _conteudoService.RetornaDadosArquivo(idConteudo);
            if(arquivo == null) return NotFound("Arquivo não encontrado.");
            return Ok(arquivo);
        }

        [HttpGet("DownloadArquivoConteudo/{idConteudo}")]
        public async Task<IActionResult> DownloadArquivo(int idConteudo)
        {
            var conteudo = await _conteudoService.DownloadArquivoConteudo(idConteudo);

            if(conteudo == null || conteudo.Arquivo == null)
                return NotFound("Arquivo não encontrado.");

            string contentType = "application/octet-stream";

            return File(conteudo.Arquivo, contentType, conteudo.NomeArquivo + conteudo.ExtensaoArquivo);
        }
    }
}
