using backend_dotnet.Data;
using backend_dotnet.Models;
using backend_dotnet.Models.Requests;
using backend_dotnet.Services.Interfaces;
using Microsoft.EntityFrameworkCore;

namespace backend_dotnet.Services
{
    public class SimuladoService : ISimuladoService
    {
        private ProfelunoContext _context;

        public SimuladoService(ProfelunoContext context)
        {
            _context = context;
        }

        public async Task<bool> CadastrarSimulado(IEnumerable<CriarSimuladoRequest> simulados)
        {
            if(simulados == null || !simulados.Any()) return false;

            using var transaction = await _context.Database.BeginTransactionAsync();

            try
            {
                foreach(var request in simulados)
                {
                    var newSimulado = new Simulado
                    {
                        Titulo = request.Titulo,
                        Descricao = request.Descricao,
                        Situacao = request.Situacao,
                        IdMateria = (int)request.IdMateria,
                        IdUser = request.IdUser,
                        CreatedAt = DateTime.Now,
                    };

                    _context.Simulados.Add(newSimulado);

                    await _context.SaveChangesAsync();

                    if(request.SimuladoQuestoesRequests != null && request.SimuladoQuestoesRequests.Any())
                    {
                        var novasQuestoes = request.SimuladoQuestoesRequests.Select(q => new SimuladoQuestao
                        {
                            IdSimulado = newSimulado.IdSimulado,
                            Enunciado = q.Enunciado,
                            Ordem = q.Ordem,
                            QuestaoCorreta = q.QuestaoCorreta,
                            QuestaoA = q.QuestaoA,
                            QuestaoB = q.QuestaoB,
                            QuestaoC = q.QuestaoC,
                            QuestaoD = q.QuestaoD,
                            QuestaoE = q.QuestaoE,
                            CreatedAt = DateTime.Now,
                        }).ToList();

                        await _context.SimuladoQuestoes.AddRangeAsync(novasQuestoes);
                    }
                }

                await _context.SaveChangesAsync();
                await transaction.CommitAsync();

                return true;
            }
            catch(Exception ex)
            {
                await transaction.RollbackAsync();
                return false;
            }
        }

        public Task<bool> DeletarSimulado(int idSimulado)
        {
            throw new NotImplementedException();
        }
        public async Task<IEnumerable<Simulado>> RetornaTodosSimuladosAsync()
        {
            return await _context.Simulados.Include(x => x.SimuladoQuestao).ToListAsync();
        }
        public async Task<Simulado> RetornaSimuladoPorIdAsync(int idSimulado)
        {
            return await _context.Simulados.Include(x => x.SimuladoQuestao).FirstOrDefaultAsync(x => x.IdSimulado == idSimulado);
        }

        public async Task<IEnumerable<Simulado>> RetornaSimuladosPorMateriaAsync(int idMateria)
        {
            return await _context.Simulados.Where(x => x.IdMateria == idMateria).Include(x => x.SimuladoQuestao).ToListAsync();
        }

        public async Task<IEnumerable<SimuladoQuestao>> RetornaSimuladoQuestoesPorIdSimulado(int idSimulado)
        {
            return await _context.SimuladoQuestoes.Where(x => x.IdSimulado == idSimulado).ToListAsync();
        }

        public Task<Simulado> AtualizaSimuladoAsync(AtualizarSimuladoRequest simulado)
        {
            throw new NotImplementedException();
        }
    }
}
