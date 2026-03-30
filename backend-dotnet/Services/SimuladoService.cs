using backend_dotnet.Data;
using backend_dotnet.Models;
using backend_dotnet.Models.Requests;
using backend_dotnet.Models.Responses;
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
                        Descricao = string.IsNullOrEmpty(request.Descricao) ? null : request.Descricao,
                        Situacao = request.Situacao,
                        IdMateria = request.IdMateria,
                        IdUser = request.IdUser,
                        CreatedAt = DateTime.Now,
                    };

                    await _context.Simulados.AddAsync(newSimulado);

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

        public async Task<bool> DeletarSimulado(int idSimulado)
        {
            int linhasAfetadas = await _context.Simulados
                .Where(x => x.IdSimulado == idSimulado)
                .Include(x => x.SimuladoQuestao)
                .ExecuteDeleteAsync();

            return true;
        }

        public async Task<IEnumerable<Simulado>> RetornaTodosSimuladosAsync()
        {
            return await _context.Simulados.Include(x => x.SimuladoQuestao).Include(x => x.Materia).ToListAsync();
        }
        public async Task<Simulado> RetornaSimuladoPorIdAsync(int idSimulado, int idUsuario)
        {
            return await _context.Simulados.Include(x => x.SimuladoQuestao).Include(x => x.Materia).FirstOrDefaultAsync(x => x.IdSimulado == idSimulado && x.IdUser == idUsuario);
        }

        public async Task<IEnumerable<Simulado>> RetornaSimuladosPorMateriaAsync(int idMateria, int idUsuario)
        {
            return await _context.Simulados.Where(x => x.IdMateria == idMateria && x.IdUser == idUsuario).Include(x => x.SimuladoQuestao).Include(x => x.Materia).ToListAsync();
        }

        public async Task<IEnumerable<SimuladoQuestao>> RetornaSimuladoQuestoesPorIdSimulado(int idSimulado)
        {
            return await _context.SimuladoQuestoes.Where(x => x.IdSimulado == idSimulado).ToListAsync();
        }

        public async Task<IEnumerable<SimuladoUsuarioResponse>> RetornaSimuladosPorUsuarioAsync(int idUsuario)
        {
            var simulados = await _context.Simulados
                .Where(x => x.IdUser == idUsuario)
                .Include(x => x.SimuladoQuestao)
                .Include(x => x.Materia)
                .AsNoTracking()
                .ToListAsync();

            var response = simulados.Select(s => new SimuladoUsuarioResponse
            {
                IdSimulado = s.IdSimulado,
                Titulo = s.Titulo,
                Descricao = s.Descricao,
                Situacao = s.Situacao, 
                IdMateria = s.IdMateria,
                IdUser = s.IdUser,
                CreatedAt = s.CreatedAt,
                UpdatedAt = s.UpdatedAt,
                Materia = s.Materia,
                QuantidadeQuestoes = s.SimuladoQuestao?.Count ?? 0
            }).ToList();

            return response;
        }

        public async Task<bool> AtualizaSimuladoAsync(AtualizarSimuladoRequest request)
        {
            using var transaction = await _context.Database.BeginTransactionAsync();

            try
            {
                var simuladoDb = await _context.Simulados
                    .Include(x => x.SimuladoQuestao)
                    .FirstOrDefaultAsync(x => x.IdSimulado == request.IdSimulado);

                if(simuladoDb == null) return false;

                simuladoDb.Titulo = request.Titulo;
                simuladoDb.Descricao = string.IsNullOrEmpty(request.Descricao) ? null : request.Descricao;
                simuladoDb.Situacao = request?.Situacao ?? true;
                simuladoDb.IdMateria = request.IdMateria;
                simuladoDb.UpdatedAt = DateTime.Now;


                var idsNoRequest = request.SimuladoQuestoesRequests.Select(q => q.IdSimuladoQuestao).ToList();
                var questoesParaRemover = simuladoDb.SimuladoQuestao
                    .Where(q => !idsNoRequest.Contains(q.IdSimuladoQuestao))
                    .ToList();

                foreach(var qRemover in questoesParaRemover)
                {
                    _context.SimuladoQuestoes.Remove(qRemover);
                }

                foreach(var qReq in request.SimuladoQuestoesRequests)
                {
                    var questaoExistente = simuladoDb.SimuladoQuestao
                        .FirstOrDefault(q => q.IdSimuladoQuestao == qReq.IdSimuladoQuestao && q.IdSimuladoQuestao != 0);

                    if(questaoExistente != null)
                    {
                        questaoExistente.Enunciado = qReq.Enunciado;
                        questaoExistente.Ordem = qReq.Ordem;
                        questaoExistente.QuestaoCorreta = qReq.QuestaoCorreta;
                        questaoExistente.QuestaoA = qReq.QuestaoA;
                        questaoExistente.QuestaoB = qReq.QuestaoB;
                        questaoExistente.QuestaoC = qReq.QuestaoC;
                        questaoExistente.QuestaoD = qReq.QuestaoD;
                        questaoExistente.QuestaoE = qReq.QuestaoE;
                        questaoExistente.UpdatedAt = DateTime.Now;
                    }
                    else
                    {
                        var novaQuestao = new SimuladoQuestao
                        {
                            IdSimulado = simuladoDb.IdSimulado,
                            Enunciado = qReq.Enunciado,
                            Ordem = qReq.Ordem,
                            QuestaoCorreta = qReq.QuestaoCorreta,
                            QuestaoA = qReq.QuestaoA,
                            QuestaoB = qReq.QuestaoB,
                            QuestaoC = qReq.QuestaoC,
                            QuestaoD = qReq.QuestaoD,
                            QuestaoE = qReq.QuestaoE,
                            CreatedAt = DateTime.Now
                        };
                        _context.SimuladoQuestoes.Add(novaQuestao);
                    }
                }

                await _context.SaveChangesAsync();
                await transaction.CommitAsync();
                return true;
            }
            catch(Exception)
            {
                await transaction.RollbackAsync();
                return false;
            }
        }
    }
}
