using backend_dotnet.Models;
using backend_dotnet.Models.Requests;
using backend_dotnet.Services.Interfaces;
using Microsoft.AspNetCore.Mvc;

namespace backend_dotnet.Controllers
{
    [ApiController]
    [Route("v1/[controller]")]
    public class CargoController : ControllerBase
    {
        private readonly ICargoService _cargoService;
        public CargoController(ICargoService cargoService)
        {
            _cargoService = cargoService;
        }

        /// <summary>
        /// Retorna Todos os Cargos
        /// </summary>
        /// <returns></returns>
        [HttpGet("RetornaTodosCargos")]
        public async Task<IActionResult> RetornaCargosAsync()
        {
            var cargos = await _cargoService.RetornaTodosCargosAsync();
            return Ok(cargos);
        }

        /// <summary>
        /// Retorna o Cargo por Id
        /// </summary>
        /// <param name="idCargo"></param>
        /// <returns></returns>
        [HttpGet("RetornaCargoPorId/{idCargo}")]
        public async Task<IActionResult> RetornaCargoPorIdAsync(int idCargo)
        {
            var cargo = await _cargoService.RetornaCargoPorIdAsync(idCargo);
            if(cargo == null) return NotFound();
            return Ok(cargo);
        }

        /// <summary>
        /// Retorna o Cargo por Nome
        /// </summary>
        /// <param name="nomeCargo"></param>
        /// <returns></returns>
        [HttpGet("RetornaCargoPorNome/{nomeCargo}")]
        public async Task<IActionResult> RetornaCargoPorNomeAsync(string nomeCargo)
        {
            var cargo = await _cargoService.RetornaCargoPorNomeAsync(nomeCargo);
            if(cargo == null) return NotFound();
            return Ok(cargo);
        }

        /// <summary>
        /// Atualiza o Cargo
        /// </summary>
        /// <param name="cargo"></param>
        /// <returns></returns>
        [HttpPut("AtualizaCargo")]
        public async Task<IActionResult> AtualizaCargoAsync(Cargo cargo)
        {
            var cargoAtualizado = await _cargoService.AtualizaCargoAsync(cargo);
            return Ok(cargoAtualizado);
        }

        [HttpPost("CadastrarCargo")]
        public async Task<IActionResult> CadastrarCargo([FromBody] CargoRequest cargo)
        {
            var resultado = await _cargoService.CadastrarCargo(cargo);

            if(resultado) return Ok("Cargo cadastrado com sucesso!");

            return BadRequest("Erro ao cadastrar cargo.");
        }
    }
}
