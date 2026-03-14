# Exemplos de Integração Laravel ↔ C#

## 1️⃣ Laravel chamando API C#

### No Laravel: backend-laravel/app/Http/Controllers/ApiController.php

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class ApiController extends Controller
{
    /**
     * Exemplo: Chamar API de usuários do .NET
     */
    public function getUsersFromDotNet()
    {
        try {
            $response = Http::get('http://dotnet:5000/api/users');
            
            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'data' => $response->json()
                ]);
            }
            
            return response()->json([
                'success' => false,
                'error' => 'API C# não respondeu'
            ], 500);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exemplo: Criar usuário via .NET
     */
    public function createUserInDotNet()
    {
        $response = Http::post('http://dotnet:5000/api/users', [
            'name' => request('name'),
            'email' => request('email'),
            'phone' => request('phone')
        ]);

        if ($response->successful()) {
            return response()->json($response->json());
        }

        return response()->json($response->json(), $response->status());
    }

    /**
     * Exemplo: Atualizar usuário no .NET
     */
    public function updateUserInDotNet($id)
    {
        $response = Http::put("http://dotnet:5000/api/users/{$id}", [
            'name' => request('name'),
            'email' => request('email')
        ]);

        return response()->json($response->json());
    }

    /**
     * Exemplo: Deletar usuário no .NET
     */
    public function deleteUserInDotNet($id)
    {
        $response = Http::delete("http://dotnet:5000/api/users/{$id}");

        if ($response->successful()) {
            return response()->json(['success' => true]);
        }

        return response()->json(['error' => 'Falha ao deletar'], 500);
    }
}
```

### No Laravel: backend-laravel/routes/api.php

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

Route::get('/dotnet/users', [ApiController::class, 'getUsersFromDotNet']);
Route::post('/dotnet/users', [ApiController::class, 'createUserInDotNet']);
Route::put('/dotnet/users/{id}', [ApiController::class, 'updateUserInDotNet']);
Route::delete('/dotnet/users/{id}', [ApiController::class, 'deleteUserInDotNet']);
```

---

## 2️⃣ C# chamando APIs do Laravel (se necessário)

### No C#: backend-dotnet/Controllers/LaravelController.cs

```csharp
using Microsoft.AspNetCore.Mvc;
using System.Net.Http;
using System.Threading.Tasks;

namespace ProfeLuno.Controllers
{
    [ApiController]
    [Route("api/[controller]")]
    public class LaravelController : ControllerBase
    {
        private readonly HttpClient _httpClient;

        public LaravelController(HttpClient httpClient)
        {
            _httpClient = httpClient;
        }

        [HttpGet("products")]
        public async Task<IActionResult> GetProducts()
        {
            try
            {
                var response = await _httpClient.GetAsync("http://laravel:8000/api/products");
                
                if (response.IsSuccessStatusCode)
                {
                    var content = await response.Content.ReadAsStringAsync();
                    return Ok(content);
                }

                return StatusCode((int)response.StatusCode, "Laravel API error");
            }
            catch (Exception ex)
            {
                return StatusCode(500, ex.Message);
            }
        }
    }
}
```

### No C#: Program.cs (registrar HttpClient)

```csharp
var builder = WebApplication.CreateBuilder(args);

builder.Services.AddControllers();
builder.Services.AddEndpointsApiExplorer();
builder.Services.AddSwaggerGen();

// Registrar HttpClient para requisições
builder.Services.AddHttpClient();

var app = builder.Build();

if (app.Environment.IsDevelopment())
{
    app.UseSwagger();
    app.UseSwaggerUI();
}

app.UseHttpsRedirection();
app.UseAuthorization();
app.MapControllers();

app.Run();
```

---

## 3️⃣ Compartilhando Banco de Dados

### Migrações Laravel → PostgreSQL

**backend-laravel/database/migrations/2024_01_21_create_users_table.php**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
```

Rodar:
```bash
docker-compose exec laravel php artisan migrate
```

### Model Laravel

**backend-laravel/app/Models/User.php**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $fillable = ['name', 'email', 'phone'];
}
```

---

## 4️⃣ Entity Framework Core (.NET)

### Criar contexto do banco

**backend-dotnet/Data/ProfeLunoContext.cs**

```csharp
using Microsoft.EntityFrameworkCore;
using ProfeLuno.Models;

namespace ProfeLuno.Data
{
    public class ProfeLunoContext : DbContext
    {
        public DbSet<User> Users { get; set; }

        public ProfeLunoContext(DbContextOptions<ProfeLunoContext> options) 
            : base(options)
        {
        }

        protected override void OnModelCreating(ModelBuilder modelBuilder)
        {
            base.OnModelCreating(modelBuilder);

            modelBuilder.Entity<User>(entity =>
            {
                entity.HasKey(e => e.Id);
                entity.Property(e => e.Name).IsRequired();
                entity.Property(e => e.Email).IsRequired();
            });
        }
    }
}
```

### Model .NET

**backend-dotnet/Models/User.cs**

```csharp
namespace ProfeLuno.Models
{
    public class User
    {
        public int Id { get; set; }
        public string Name { get; set; }
        public string Email { get; set; }
        public string Phone { get; set; }
        public DateTime CreatedAt { get; set; } = DateTime.UtcNow;
    }
}
```

### Registrar DbContext

**backend-dotnet/Program.cs**

```csharp
var builder = WebApplication.CreateBuilder(args);

builder.Services.AddDbContext<ProfeLunoContext>(options =>
    options.UseNpgsql("Host=postgres;Database=profeluno;Username=postgres;Password=postgres;")
);

builder.Services.AddControllers();
// ... resto do código
```

---

## 5️⃣ Testando a integração

### Testar Laravel → C#

```bash
# Terminal 1
docker-compose logs -f laravel

# Terminal 2
docker-compose logs -f dotnet

# Terminal 3
curl -X GET http://localhost:8000/api/dotnet/users
```

### Testar C# → PostgreSQL

```bash
# Dentro do container dotnet
docker-compose exec dotnet bash

# Ver logs do EF Core
dotnet run

# Criar usuário via API
curl -X POST http://localhost:5000/api/users \
  -H "Content-Type: application/json" \
  -d '{"name":"João","email":"joao@example.com"}'
```

### Conectar ao PostgreSQL local

```bash
# De qualquer lugar (Windows PowerShell ou WSL)
psql -h localhost -U postgres -d profeluno

# Senha: postgres

# Ver tabelas
\dt

# Consultar usuários
SELECT * FROM users;

# Sair
\q
```

---

## 6️⃣ Troubleshooting

### "http://dotnet:5000 connection refused"

```bash
# Verificar se container está rodando
docker-compose ps

# Verificar logs do dotnet
docker-compose logs dotnet

# Reiniciar
docker-compose restart dotnet
```

### "PostgreSQL connection timeout"

```bash
# Verificar PostgreSQL
docker-compose logs postgres

# Verificar variáveis de ambiente
docker-compose exec laravel env | grep DB_
```

### Laravel não consegue conectar ao .NET

```bash
# Dentro do Laravel container
docker-compose exec laravel bash
curl http://dotnet:5000/health

# Se não conectar, os containers não estão na mesma rede
docker network ls
docker network inspect profeluno_profeluno_network
```

---

## 🎯 Fluxo recomendado

1. **Você (Laravel)** → Cria rota que chama API C#
2. **Seu parceiro (C#)** → Cria endpoint que responde
3. **Teste** → Verifique integração com curl/Postman
4. **Frontend (Vite)** → Chama rota Laravel
5. **Banco** → Ambos leem/escrevem mesmas tabelas

Happy coding! 🚀
