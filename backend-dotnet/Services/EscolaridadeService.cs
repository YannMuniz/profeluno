using backend_dotnet.Data;
using backend_dotnet.Models;
using backend_dotnet.Models.Requests;
using backend_dotnet.Services.Interfaces;
using Microsoft.EntityFrameworkCore;

namespace backend_dotnet.Services
{
    public class EscolaridadeService : IEscolaridadeService
    {
        private readonly ProfelunoContext _context;

        public EscolaridadeService(ProfelunoContext context)
        {
            _context = context;
        }

        public async Task<List<Escolaridade>> RetornaTodasEscolaridades()
        {
            var escolaridades = await _context.Escolaridade.ToListAsync();
            return escolaridades;
        }

        public async Task<Escolaridade> RetornaEscolaridadePorId(int idEscolaridade)
        {
            var escolaridade = await _context.Escolaridade.FirstOrDefaultAsync(x => x.IdEscolaridade == idEscolaridade);
            return escolaridade;
        }

        public async Task<Escolaridade> CadastraEscolaridade(CadastrarEscolaridadeRequest escolaridade)
        {
            Escolaridade newEscolaridade = new Escolaridade
            {
                NomeEscolaridade = escolaridade.NomeEscolaridade,
                SituacaoEscolaridade = escolaridade.SituacaoEscolaridade,
                CreatedAt = DateTime.Now
            };
            await _context.Escolaridade.AddAsync(newEscolaridade);
            await _context.SaveChangesAsync();
            return newEscolaridade;
        }

        public async Task<Escolaridade> AtualizarEscolaridade(AtualizarEscolaridadeRequest escolaridade)
        {
            var newEscolaridade = await _context.Escolaridade.FirstOrDefaultAsync(x => x.IdEscolaridade == escolaridade.IdEscolaridade);
            newEscolaridade.NomeEscolaridade = escolaridade.NomeEscolaridade;
            newEscolaridade.SituacaoEscolaridade = escolaridade.SituacaoEscolaridade;
            newEscolaridade.UpdatedAt = DateTime.Now;
            await _context.SaveChangesAsync();
            return newEscolaridade;
        }

        public async Task<bool> DeletarEscolaridade(int idEscolaridade)
        {
            var escolaridade = await _context.Escolaridade.FirstOrDefaultAsync(x => x.IdEscolaridade == idEscolaridade);
            if(escolaridade == null) return false;
            _context.Escolaridade.Remove(escolaridade);
            await _context.SaveChangesAsync();
            return true;
        }
    }
}
