using backend_dotnet.Data;
using backend_dotnet.Models;
using backend_dotnet.Models.Requests;
using backend_dotnet.Services.Interfaces;
using Microsoft.EntityFrameworkCore;

namespace backend_dotnet.Services
{
    public class CargoServices : ICargoService
    {
        private readonly ProfelunoContext _context;

        public CargoServices(ProfelunoContext context)
        {
            _context = context;
        }

        public async Task<IEnumerable<Cargo>> RetornaTodosCargosAsync()
        {
            return await _context.Cargos.AsNoTracking().Where(x => x.IdCargo != 3).ToListAsync();
        }

        public async Task<Cargo> RetornaCargoPorIdAsync(int idCargo)
        {
            return await _context.Cargos.AsNoTracking().FirstOrDefaultAsync(x => x.IdCargo == idCargo);
        }

        public async Task<Cargo> RetornaCargoPorNomeAsync(string nomeCargo)
        {
            return await _context.Cargos.AsNoTracking().FirstOrDefaultAsync(x => x.NomeCargo.ToUpper().StartsWith(nomeCargo.ToUpper()));
        }

        public async Task<Cargo> AtualizaCargoAsync(AtualizarCargoRequest cargo)
        {
            var newCargo = await _context.Cargos.FirstOrDefaultAsync(x => x.IdCargo == cargo.IdCargo);

            newCargo.NomeCargo = cargo.NomeCargo;
            newCargo.UpdatedAt = DateTime.Now;

            _context.Cargos.Update(newCargo);
            await _context.SaveChangesAsync();
            return newCargo;
        }

        public async Task<bool> CadastrarCargo(CargoRequest cargo)
        {
            if(cargo == null) return false;

            var nomeExistente = await _context.Cargos.AnyAsync(x => x.NomeCargo == cargo.Nome);

            if(nomeExistente) return false;

            Cargo newCargo = new Cargo
            {
                NomeCargo = cargo.Nome,
                CreatedAt = DateTime.Now,
            };

            await _context.Cargos.AddAsync(newCargo);
            await _context.SaveChangesAsync();

            return true;
        }

        public async Task<bool> DeletarCargoAsync(int idCargo)
        {
            int linhasAfetadas = await _context.Cargos
                .Where(x => x.IdCargo == idCargo)
                .ExecuteDeleteAsync();

            return linhasAfetadas > 0;
        }
    }
}
