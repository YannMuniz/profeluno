using backend_dotnet.Services;
using backend_dotnet.Services.Interfaces;
using System.Runtime;

namespace backend_dotnet.Configuration;

public static class DependencyInjectionConfig
{
    public static IServiceCollection AddInfrastructure(this IServiceCollection services)
    {
        services.AddScoped<IUserService, UserService>();
        services.AddScoped<ICargoService, CargoServices>();
        services.AddScoped<IMateriaService, MateriaService>();
        services.AddScoped<ISimuladoService, SimuladoService>();
        services.AddScoped<IConteudoService, ConteudoService>();
        services.AddScoped<ISalaAulaService, SalaAulaService>();
        services.AddScoped<IJitsiService, JitsiService>();
        services.AddScoped<IAlunoSalaService, AlunoSalaService>();
        services.AddScoped<IAreaService, AreaService>();
        services.AddScoped<IMatchmakingService, MatchmakingService>();
        services.AddScoped<IProfessorMateriaService, ProfessorMateriaService>();
        services.AddScoped<IEscolaridadeService, EscolaridadeService>();
        services.AddScoped<IDashboardProfessorService, DashboardProfessorService>();

        return services;
    }
}