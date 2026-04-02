using backend_dotnet.Data;
using backend_dotnet.Models;
using backend_dotnet.Models.Requests;
using backend_dotnet.Models.Responses;
using backend_dotnet.Services.Interfaces;
using Microsoft.AspNetCore.Identity;
using Microsoft.EntityFrameworkCore;

namespace backend_dotnet.Services
{
    public class ConteudoService : IConteudoService
    {
        ProfelunoContext _context;
        public ConteudoService(ProfelunoContext profelunoContext) 
        { 
            _context = profelunoContext;
        }

        public async Task<IEnumerable<Conteudo>> RetornaTodosConteudosAsync()
        {
            var response = await _context.Conteudos.Include(x => x.Materia).ToListAsync();

            var conteudosResponse = response.Select(c => new Conteudo
            {
                IdConteudo = c.IdConteudo,
                Titulo = c.Titulo,
                Descricao = c.Descricao,
                IdUsuario = c.IdUsuario,
                IdMateria = c.IdMateria,
                Tipo = c.Tipo,
                Situacao = c.Situacao,
                NomeArquivo = c.NomeArquivo,
                ExtensaoArquivo = c.ExtensaoArquivo,
                Url = c.Url,
                CreatedAt = c.CreatedAt,
                Materia = c.Materia,
            }).ToList();

            return conteudosResponse;
        }

        public async Task<ConteudoResponse> RetornaConteudoPorIdProfessor(int idUsuario)
        {
            var response = _context.Conteudos.Include(x => x.Materia).FirstOrDefaultAsync(x => x.IdUsuario == idUsuario);

            ConteudoResponse newConteudo = new ConteudoResponse()
            {
                IdConteudo = response.Result.IdConteudo,
                Titulo = response.Result.Titulo,
                Descricao = response.Result.Descricao,
                IdUsuario = response.Result.IdUsuario,
                IdMateria = response.Result.IdMateria,
                Tipo = response.Result.Tipo,
                Situacao = response.Result.Situacao,
                NomeArquivo = response.Result.NomeArquivo,
                ExtensaoArquivo = response.Result.ExtensaoArquivo,
                Url = response.Result.Url,
                CreatedAt = response.Result.CreatedAt,
                UpdatedAt = response.Result.UpdatedAt,
                Materia = response.Result.Materia
            };

            return newConteudo;
        }

        public async Task<ConteudoResponse> RetornaConteudoPorIdConteudo(int idConteudo)
        {
            var response = _context.Conteudos.Include(x => x.Materia).FirstOrDefaultAsync(x => x.IdConteudo == idConteudo);

            ConteudoResponse newConteudo = new ConteudoResponse()
            {
                IdConteudo = response.Result.IdConteudo,
                Titulo = response.Result.Titulo,
                Descricao = response.Result.Descricao,
                IdUsuario = response.Result.IdUsuario,
                IdMateria = response.Result.IdMateria,
                Tipo = response.Result.Tipo,
                Situacao = response.Result.Situacao,
                NomeArquivo = response.Result.NomeArquivo,
                ExtensaoArquivo = response.Result.ExtensaoArquivo,
                Url = response.Result.Url,
                CreatedAt = response.Result.CreatedAt,
                UpdatedAt = response.Result.UpdatedAt,
                Materia = response.Result.Materia
            };
            return newConteudo;
        }

        public async Task<ArquivoResponse> RetornaDadosArquivo(int idConteudo)
        {
            var response = _context.Conteudos.FirstOrDefaultAsync(x => x.IdConteudo == idConteudo);
            ArquivoResponse arquivoResponse = new ArquivoResponse()
            {
                Tipo = response.Result.Tipo,
                NomeArquivo = response.Result.NomeArquivo,
                ExtensaoArquivo = response.Result.ExtensaoArquivo,
                Url = response.Result.Url,
            };
            return arquivoResponse;
        }

        public async Task<bool> CadastrarConteudo (UploadConteudoRequest conteudo)
        {
            // Validar se a matéria existe
            var materiaExiste = await _context.Materias.AnyAsync(m => m.IdMateria == conteudo.IdMateria);
            if (!materiaExiste)
            {
                throw new Exception("Matéria não encontrada.");
            }

            using var ms = new MemoryStream();
            await conteudo.Arquivo.CopyToAsync(ms);

            var entidade = new Conteudo
            {
                Titulo = conteudo.Titulo,
                IdUsuario = conteudo.IdUsuario,
                Descricao = conteudo.Descricao,
                IdMateria = conteudo.IdMateria,
                Tipo = conteudo.Tipo,
                Situacao = conteudo.Situacao,
                NomeArquivo = conteudo.NomeArquivo,
                ExtensaoArquivo = conteudo.ExtensaoArquivo,
                Url = conteudo.Url,
                Arquivo = ms.ToArray(),
                CreatedAt = DateTime.Now
            };

            _context.Conteudos.Add(entidade);
            await _context.SaveChangesAsync();
            return true;
        }

        public async Task<Conteudo> DownloadArquivoConteudo(int idConteudo)
        {
            return await _context.Conteudos
                .Include(c => c.Materia)
                .FirstOrDefaultAsync(c => c.IdConteudo == idConteudo);
        }

        public async Task<bool> UpdateConteudo(AtualizarConteudoRequest conteudo)
        {
            var entidade = await _context.Conteudos.FindAsync(conteudo.IdConteudo);
            if (entidade == null) return false;

            // Validar matéria
            var materiaExiste = await _context.Materias.AnyAsync(m => m.IdMateria == conteudo.IdMateria);
            if (!materiaExiste) throw new Exception("Matéria não encontrada.");

            if (conteudo.Arquivo != null)
            {
                using var ms = new MemoryStream();
                await conteudo.Arquivo.CopyToAsync(ms);
                entidade.Arquivo = ms.ToArray();
                entidade.NomeArquivo = conteudo.NomeArquivo;
                entidade.ExtensaoArquivo = conteudo.ExtensaoArquivo;
            }

            entidade.Titulo = conteudo.Titulo;
            entidade.Descricao = conteudo.Descricao;
            entidade.IdMateria = conteudo.IdMateria;
            entidade.Tipo = conteudo.Tipo;
            entidade.Situacao = conteudo.Situacao;
            entidade.Url = conteudo.Url;
            entidade.UpdatedAt = DateTime.Now;

            await _context.SaveChangesAsync();
            return true;
        }

        public async Task<bool> DeleteConteudo(int id)
        {
            var entidade = await _context.Conteudos.FindAsync(id);
            if (entidade == null) return false;

            _context.Conteudos.Remove(entidade);
            await _context.SaveChangesAsync();
            return true;
        }
    }
}
