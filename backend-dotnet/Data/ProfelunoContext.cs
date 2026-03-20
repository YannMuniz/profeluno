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

    public virtual DbSet<Material> Materials { get; set; }

    public virtual DbSet<Migration> Migrations { get; set; }

    public virtual DbSet<Role> Roles { get; set; }

    public virtual DbSet<SalaAula> SalaAulas { get; set; }

    public virtual DbSet<Simulado> Simulados { get; set; }

    public virtual DbSet<User> Users { get; set; }

    public virtual DbSet<Cargo> Cargos { get; set; }

    protected override void OnModelCreating(ModelBuilder modelBuilder)
    {
        modelBuilder.Entity<AlunoSala>(entity =>
        {
            entity.HasKey(e => e.Id).HasName("aluno_sala_pkey");

            entity.ToTable("aluno_sala");

            entity.HasIndex(e => new { e.AlunoId, e.SalaAulaId }, "aluno_sala_aluno_id_sala_aula_id_unique").IsUnique();

            entity.Property(e => e.Id).HasColumnName("id");
            entity.Property(e => e.AlunoId).HasColumnName("aluno_id");
            entity.Property(e => e.CreatedAt)
                .HasColumnType("timestamp(0) without time zone")
                .HasColumnName("created_at");
            entity.Property(e => e.JoinedAt)
                .HasColumnType("timestamp(0) without time zone")
                .HasColumnName("joined_at");
            entity.Property(e => e.LeftAt)
                .HasColumnType("timestamp(0) without time zone")
                .HasColumnName("left_at");
            entity.Property(e => e.SalaAulaId).HasColumnName("sala_aula_id");
            entity.Property(e => e.UpdatedAt)
                .HasColumnType("timestamp(0) without time zone")
                .HasColumnName("updated_at");

            entity.HasOne(d => d.SalaAula).WithMany(p => p.AlunoSalas)
                .HasForeignKey(d => d.SalaAulaId)
                .HasConstraintName("aluno_sala_sala_aula_id_foreign");
        });

        modelBuilder.Entity<Material>(entity =>
        {
            entity.HasKey(e => e.Id).HasName("material_pkey");

            entity.ToTable("material");

            entity.Property(e => e.Id).HasColumnName("id");
            entity.Property(e => e.CreatedAt)
                .HasColumnType("timestamp(0) without time zone")
                .HasColumnName("created_at");
            entity.Property(e => e.Descricao).HasColumnName("descricao");
            entity.Property(e => e.FilePath)
                .HasMaxLength(255)
                .HasColumnName("file_path");
            entity.Property(e => e.FileUrl)
                .HasMaxLength(255)
                .HasColumnName("file_url");
            entity.Property(e => e.SalaAulaId).HasColumnName("sala_aula_id");
            entity.Property(e => e.Titulo)
                .HasMaxLength(255)
                .HasColumnName("titulo");
            entity.Property(e => e.Type)
                .HasMaxLength(255)
                .HasDefaultValueSql("'document'::character varying")
                .HasColumnName("type");
            entity.Property(e => e.UpdatedAt)
                .HasColumnType("timestamp(0) without time zone")
                .HasColumnName("updated_at");
        });

        modelBuilder.Entity<Migration>(entity =>
        {
            entity.HasKey(e => e.Id).HasName("migrations_pkey");

            entity.ToTable("migrations");

            entity.Property(e => e.Id).HasColumnName("id");
            entity.Property(e => e.Batch).HasColumnName("batch");
            entity.Property(e => e.Migration1)
                .HasMaxLength(255)
                .HasColumnName("migration");
        });

        modelBuilder.Entity<Role>(entity =>
        {
            entity.HasKey(e => e.Id).HasName("roles_pkey");

            entity.ToTable("roles");

            entity.HasIndex(e => e.Name, "roles_name_unique").IsUnique();

            entity.Property(e => e.Id).HasColumnName("id");
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
            entity.HasKey(e => e.Id).HasName("sala_aula_pkey");

            entity.ToTable("sala_aula");

            entity.Property(e => e.Id).HasColumnName("id");
            entity.Property(e => e.Avaliacao).HasColumnName("avaliacao");
            entity.Property(e => e.CreatedAt)
                .HasColumnType("timestamp(0) without time zone")
                .HasColumnName("created_at");
            entity.Property(e => e.DataHoraFim)
                .HasColumnType("timestamp(0) without time zone")
                .HasColumnName("data_hora_fim");
            entity.Property(e => e.DataHoraInicio)
                .HasColumnType("timestamp(0) without time zone")
                .HasColumnName("data_hora_inicio");
            entity.Property(e => e.Descricao).HasColumnName("descricao");
            entity.Property(e => e.Materia)
                .HasMaxLength(255)
                .HasColumnName("materia");
            entity.Property(e => e.MaterialId).HasColumnName("material_id");
            entity.Property(e => e.ProfessorId).HasColumnName("professor_id");
            entity.Property(e => e.QtdAlunos).HasColumnName("qtd_alunos");
            entity.Property(e => e.Status)
                .HasMaxLength(255)
                .HasDefaultValueSql("'active'::character varying")
                .HasColumnName("status");
            entity.Property(e => e.Titulo)
                .HasMaxLength(255)
                .HasColumnName("titulo");
            entity.Property(e => e.UpdatedAt)
                .HasColumnType("timestamp(0) without time zone")
                .HasColumnName("updated_at");
            entity.Property(e => e.Url)
                .HasMaxLength(255)
                .HasColumnName("url");

            entity.HasOne(d => d.Material).WithMany(p => p.SalaAulas)
                .HasForeignKey(d => d.MaterialId)
                .HasConstraintName("sala_aula_material_id_foreign");
        });

        modelBuilder.Entity<Simulado>(entity =>
        {
            entity.HasKey(e => e.Id).HasName("simulado_pkey");

            entity.ToTable("simulado");

            entity.Property(e => e.Id).HasColumnName("id");
            entity.Property(e => e.CreatedAt)
                .HasColumnType("timestamp(0) without time zone")
                .HasColumnName("created_at");
            entity.Property(e => e.QuestaoA).HasColumnName("questao_a");
            entity.Property(e => e.QuestaoB).HasColumnName("questao_b");
            entity.Property(e => e.QuestaoC).HasColumnName("questao_c");
            entity.Property(e => e.QuestaoCorreta).HasColumnName("questao_correta");
            entity.Property(e => e.QuestaoD).HasColumnName("questao_d");
            entity.Property(e => e.QuestaoE).HasColumnName("questao_e");
            entity.Property(e => e.SalaAulaId).HasColumnName("sala_aula_id");
            entity.Property(e => e.UpdatedAt)
                .HasColumnType("timestamp(0) without time zone")
                .HasColumnName("updated_at");

            entity.HasOne(d => d.SalaAula).WithMany(p => p.Simulados)
                .HasForeignKey(d => d.SalaAulaId)
                .HasConstraintName("simulado_sala_aula_id_foreign");
        });

        modelBuilder.Entity<User>(entity =>
        {
            entity.HasKey(e => e.Id).HasName("users_pkey");

            entity.ToTable("users");

            entity.HasIndex(e => e.Email, "users_email_unique").IsUnique();

            entity.Property(e => e.Id).ValueGeneratedOnAdd().HasColumnName("id");
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
            entity.HasKey(e => e.Id).HasName("cargos_pkey");
            entity.ToTable("cargos");
            entity.HasIndex(e => e.NomeCargo, "cargos_name_unique").IsUnique();
            entity.Property(e => e.NomeCargo)
                .HasMaxLength(255)
                .HasColumnName("nome_cargo");
            entity.Property(e => e.Id).HasColumnName("id");
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
