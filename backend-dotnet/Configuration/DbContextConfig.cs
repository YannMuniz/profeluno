using backend_dotnet.Data;
using Microsoft.EntityFrameworkCore;
using Npgsql;

namespace backend_dotnet.Configuration
{
    public static class DbContextConfig
    {
        public static void AddDatabaseConfiguration(this IServiceCollection services, IConfiguration configuration)
        {
            services.AddDbContext<ProfelunoContext>(options => options.UseNpgsql(configuration.GetConnectionString("DefaultConnection")));
        }
    }
}
