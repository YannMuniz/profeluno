using backend_dotnet.Data;
using backend_dotnet.Models;
using backend_dotnet.Models.Requests;
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
            var response = await _context.Conteudos.ToListAsync();

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
                CreatedAt = c.CreatedAt
            }).ToList();

            return conteudosResponse;
        }

        public async Task<bool> CadastrarConteudo (UploadConteudoRequest conteudo)
        {
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
    }
}
