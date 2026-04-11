using backend_dotnet.Models;
using Microsoft.EntityFrameworkCore;

namespace backend_dotnet.Data;

public partial class ProfelunoContext : DbContext
{
    private IConfiguration _configuration;

    public ProfelunoContext()
    {
    }

    public ProfelunoContext(DbContextOptions<ProfelunoContext> options)
        : base(options)
    {
    }

    public virtual DbSet<AlunoSala> AlunoSalas { get; set; }
    public virtual DbSet<Conteudo> Conteudos { get; set; }
    public virtual DbSet<Migration> Migrations { get; set; }
    public virtual DbSet<Role> Roles { get; set; }
    public virtual DbSet<SalaAula> SalaAulas { get; set; }
    public virtual DbSet<Simulado> Simulados { get; set; }
    public virtual DbSet<User> Users { get; set; }
    public virtual DbSet<Cargo> Cargos { get; set; }
    public virtual DbSet<Materia> Materias { get; set; }
    public virtual DbSet<SimuladoQuestao> SimuladoQuestoes { get; set; }

    protected override void OnModelCreating(ModelBuilder modelBuilder)
    {
        modelBuilder.Entity<AlunoSala>(entity =>
        {
            entity.ToTable("aluno_sala");
            entity.HasKey(e => e.IdAlunoSala);
            entity.Property(e => e.IdAlunoSala).HasColumnName("id");
            entity.Property(e => e.IdAluno).HasColumnName("aluno_id");
            entity.Property(e => e.CreatedAt)
                .HasColumnType("timestamp(0) without time zone")
                .HasColumnName("created_at");
            entity.Property(e => e.JoinedAt)
                .HasColumnType("timestamp(0) without time zone")
                .HasColumnName("joined_at");
            entity.Property(e => e.LeftAt)
                .HasColumnType("timestamp(0) without time zone")
                .HasColumnName("left_at");
            entity.Property(e => e.IdSalaAula).HasColumnName("sala_aula_id");
            entity.Property(e => e.UpdatedAt)
                .HasColumnType("timestamp(0) without time zone")
                .HasColumnName("updated_at");

            entity.HasOne(d => d.SalaAula).WithMany(p => p.AlunoSalas)
                .HasForeignKey(d => d.IdSalaAula)
                .HasConstraintName("aluno_sala_sala_aula_id_foreign");
        });

        modelBuilder.Entity<Conteudo>(entity =>
        {
            entity.ToTable("conteudo");
            entity.HasKey(e => e.IdConteudo);
            entity.Property(e => e.IdConteudo).HasColumnName("id");
            entity.Property(e => e.Titulo)
                .HasColumnName("titulo");
            entity.Property(e => e.IdUsuario).HasColumnName("user_id");
            entity.Property(e => e.IdMateria).HasColumnName("materia_id");
            entity.Property(e => e.Descricao).HasColumnName("descricao");
            entity.Property(e => e.Tipo)
                .HasColumnName("tipo");
            entity.Property(e => e.Arquivo)
            .HasColumnName("arquivo")
            .HasColumnType("bytea");
            entity.Property(e => e.NomeArquivo)
                .HasColumnName("nome_arquivo");
            entity.Property(e => e.ExtensaoArquivo)
                .HasColumnName("extensao_arquivo");
            entity.Property(e => e.Url).HasColumnName("url");
            entity.Property(e => e.Situacao).HasColumnName("situacao");
            entity.Property(e => e.CreatedAt)
                .HasColumnType("timestamp(0) without time zone")
                .HasColumnName("created_at");
            entity.Property(e => e.UpdatedAt)
                .HasColumnType("timestamp(0) without time zone")
                .HasColumnName("updated_at");

            entity.HasOne(e => e.Materia).WithMany(e => e.Conteudos)
            .HasForeignKey(e => e.IdMateria)
            .HasConstraintName("material_materia_id_foreign");
        });

        modelBuilder.Entity<Migration>(entity =>
        {
            entity.ToTable("migrations");
            entity.HasKey(e => e.Id);
            entity.Property(e => e.Id).HasColumnName("id");
            entity.Property(e => e.Batch).HasColumnName("batch");
            entity.Property(e => e.Migration1)
                .HasMaxLength(255)
                .HasColumnName("migration");
        });

        modelBuilder.Entity<Role>(entity =>
        {
            entity.ToTable("roles");
            entity.HasKey(e => e.IdRole);
            entity.Property(e => e.IdRole).HasColumnName("id");
            entity.HasIndex(e => e.Name, "roles_name_unique").IsUnique();
            entity.Property(e => e.CreatedAt)
                .HasColumnType("timestamp(0) without time zone")
                .HasColumnName("created_at");
            entity.Property(e => e.Description).HasColumnName("description");
            entity.Property(e => e.Name)
                .HasMaxLength(255)
                .HasColumnName("name");
            entity.Property(e => e.UpdatedAt)
                .HasColumnType("timestamp(0) without time zone")
                .HasColumnName("updated_at");
        });

        modelBuilder.Entity<SalaAula>(entity =>
        {
            entity.ToTable("sala_aula");
            entity.HasKey(e => e.IdSalaAula);
            entity.Property(e => e.IdSalaAula).HasColumnName("id");
            entity.Property(e => e.Titulo)
                .HasMaxLength(255)
                .HasColumnName("titulo");
            entity.Property(e => e.Descricao).HasColumnName("descricao");
            entity.Property(e => e.IdProfessor).HasColumnName("user_id");
            entity.Property(e => e.Avaliacao).HasColumnName("avaliacao");
            entity.Property(e => e.DataHoraFim)
                .HasColumnType("timestamp(0) without time zone")
                .HasColumnName("data_hora_fim");
            entity.Property(e => e.DataHoraInicio)
                .HasColumnType("timestamp(0) without time zone")
                .HasColumnName("data_hora_inicio");
            entity.Property(e => e.IdMateria)
                .HasMaxLength(255)
                .HasColumnName("materia_id");
            entity.Property(e => e.IdConteudo).HasColumnName("conteudo_id");
            entity.Property(e => e.IdSimulado).HasColumnName("simulado_id");
            entity.Property(e => e.MaxAlunos).HasColumnName("max_alunos");
            entity.Property(e => e.Url)
                .HasColumnName("url");
            entity.Property(e => e.NomeSala).HasColumnName("room_name");
            entity.Property(e => e.Avaliacao).HasColumnName("avaliacao");
            entity.Property(e => e.Status)
                .HasMaxLength(255)
                .HasDefaultValueSql("'active'::character varying")
                .HasColumnName("status");
            entity.Property(e => e.CreatedAt)
                .HasColumnType("timestamp(0) without time zone")
                .HasColumnName("created_at");
            entity.Property(e => e.UpdatedAt)
                .HasColumnType("timestamp(0) without time zone")
                .HasColumnName("updated_at");

            entity.HasOne(x => x.Conteudo)
                .WithMany(x => x.SalaAula)
                .HasForeignKey(x => x.IdConteudo);

            entity.HasOne(x => x.Simulados)
                .WithMany(x => x.SalaAulas)
                .HasForeignKey(x => x.IdSimulado);
        });

        modelBuilder.Entity<Simulado>(entity =>
        {
            entity.ToTable("simulado");
            entity.HasKey(e => e.IdSimulado);
            entity.Property(e => e.IdSimulado).HasColumnName("id");
            entity.Property(e => e.CreatedAt)
                .HasColumnType("timestamp(0) without time zone")
                .HasColumnName("created_at");
            entity.Property(e => e.IdMateria).HasColumnName("materia_id");
            entity.Property(e => e.Descricao).HasColumnName("descricao");
            entity.Property(e => e.Situacao).HasColumnName("situacao");
            entity.Property(e => e.Titulo)
                .HasMaxLength(255)
                .HasColumnName("titulo");
            entity.Property(e => e.IdUser).HasColumnName("user_id");
            entity.Property(e => e.UpdatedAt)
                .HasColumnType("timestamp(0) without time zone")
                .HasColumnName("updated_at");

            entity.HasOne(d => d.Materia).WithMany(p => p.Simulados)
                .HasForeignKey(d => d.IdMateria)
                .HasConstraintName("simulado_materia_id_foreign");
        });

        modelBuilder.Entity<SimuladoQuestao>(entity =>
        {
            entity.ToTable("simulado_questao");
            entity.HasKey(e => e.IdSimuladoQuestao);
            entity.Property(e => e.IdSimuladoQuestao).HasColumnName("id");
            entity.Property(e => e.IdSimulado).HasColumnName("simulado_id");
            entity.Property(e => e.Ordem).HasColumnName("ordem");
            entity.Property(e => e.Enunciado).HasColumnName("enunciado");
            entity.Property(e => e.QuestaoCorreta).HasColumnName("questao_correta");
            entity.Property(e => e.QuestaoA).HasColumnName("questao_a");
            entity.Property(e => e.QuestaoB).HasColumnName("questao_b");
            entity.Property(e => e.QuestaoC).HasColumnName("questao_c");
            entity.Property(e => e.QuestaoD).HasColumnName("questao_d");
            entity.Property(e => e.QuestaoE).HasColumnName("questao_e");
            entity.Property(e => e.CreatedAt)
                .HasColumnType("timestamp(0) without time zone")
                .HasColumnName("created_at");
            entity.Property(e => e.UpdatedAt)
                .HasColumnType("timestamp(0) without time zone")
                .HasColumnName("updated_at");

            entity.HasOne(d => d.Simulado).WithMany(p => p.SimuladoQuestao)
                .HasForeignKey(d => d.IdSimulado)
                .HasConstraintName("simulado_questao_simulado_id_foreign");
        });

        modelBuilder.Entity<User>(entity =>
        {
            entity.ToTable("users");
            entity.HasKey(e => e.IdUser);
            entity.Property(e => e.IdUser).HasColumnName("id");
            entity.HasIndex(e => e.Email, "users_email_unique").IsUnique();
            entity.Property(e => e.CreatedAt)
                .HasColumnType("timestamp(0) without time zone")
                .HasColumnName("created_at");
            entity.Property(e => e.Email)
                .HasMaxLength(255)
                .HasColumnName("email");
            entity.Property(e => e.Password)
                .HasMaxLength(255)
                .HasColumnName("password");
            entity.Property(e => e.IdCargo)
                .HasColumnName("cargo_id");
            entity.Property(e => e.Nome_Usuario)
                .HasMaxLength(255)
                .HasColumnName("nome_usuario");
            entity.Property(e => e.UpdatedAt)
                .HasColumnType("timestamp(0) without time zone")
                .HasColumnName("updated_at");
        });

        modelBuilder.Entity<Cargo>(entity => 
        {
            entity.ToTable("cargos");
            entity.HasKey(e => e.IdCargo);
            entity.Property(e => e.IdCargo).HasColumnName("id");
            entity.Property(e => e.NomeCargo)
                .HasMaxLength(255)
                .HasColumnName("nome_cargo");
            entity.Property(e => e.CreatedAt)
                .HasColumnType("timestamp(0) without time zone")
                .HasColumnName("created_at");
            entity.Property(e => e.UpdatedAt)
                .HasColumnType("timestamp(0) without time zone")
                .HasColumnName("updated_at");
        });

        modelBuilder.Entity<Materia>(entity =>
        {
            entity.ToTable("materias");
            entity.HasKey(e => e.IdMateria);
            entity.Property(e => e.IdMateria).HasColumnName("id");
            entity.Property(e => e.NomeMateria)
                .HasMaxLength(255)
                .HasColumnName("nome_materia");
            entity.Property(e => e.SituacaoMateria).HasColumnName("situacao_materia");
            entity.Property(e => e.CreatedAt)
                .HasColumnType("timestamp(0) without time zone")
                .HasColumnName("created_at");
            entity.Property(e => e.UpdatedAt)
                .HasColumnType("timestamp(0) without time zone")
                .HasColumnName("updated_at");
        });

        OnModelCreatingPartial(modelBuilder);
    }

    partial void OnModelCreatingPartial(ModelBuilder modelBuilder);
}
