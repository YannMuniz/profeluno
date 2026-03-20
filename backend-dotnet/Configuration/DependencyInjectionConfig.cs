using backend_dotnet.Services;
using backend_dotnet.Services.Interfaces;

namespace backend_dotnet.Configuration;

public static class DependencyInjectionConfig
{
    public static IServiceCollection AddInfrastructure(this IServiceCollection services)
    {
        services.AddScoped<IUserService, UserService>();
        services.AddScoped<ICargoService, CargoServices>();

        return services;
    }
}