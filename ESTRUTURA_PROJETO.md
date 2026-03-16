# 📁 ESTRUTURA RECOMENDADA DO PROJETO

## Para você (Laravel/PHP)

```
backend-laravel/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php
│   │   │   ├── UserController.php
│   │   │   ├── ProductController.php
│   │   │   └── ApiController.php (← chamadas para C#)
│   │   ├── Middleware/
│   │   └── Requests/
│   ├── Models/
│   │   ├── User.php
│   │   ├── Product.php
│   │   └── Order.php
│   ├── Services/
│   │   ├── UserService.php
│   │   └── DotNetApiService.php (← integração com C#)
│   ├── Exceptions/
│   └── Providers/
├── database/
│   ├── migrations/
│   │   ├── create_users_table.php
│   │   ├── create_products_table.php
│   │   └── create_orders_table.php
│   ├── factories/
│   │   └── UserFactory.php
│   └── seeders/
│       └── DatabaseSeeder.php
├── resources/
│   ├── js/
│   │   ├── app.js
│   │   ├── bootstrap.js
│   │   └── components/
│   ├── css/
│   │   └── app.css
│   └── views/
│       ├── layout.blade.php
│       ├── auth/
│       ├── dashboard/
│       └── products/
├── routes/
│   ├── web.php (← rotas da aplicação)
│   ├── api.php (← rotas de API)
│   └── console.php
├── tests/
│   ├── Feature/
│   │   ├── ApiIntegrationTest.php
│   │   └── UserTest.php
│   └── Unit/
└── config/
    ├── app.php
    ├── database.php
    └── filesystems.php
```

## Para seu parceiro (C#/.NET)

```
backend-dotnet/
├── Controllers/
│   ├── UsersController.cs
│   ├── ProductsController.cs
│   ├── OrdersController.cs
│   └── HealthController.cs
├── Models/
│   ├── User.cs
│   ├── Product.cs
│   └── Order.cs
├── DTOs/
│   ├── UserDto.cs
│   ├── ProductDto.cs
│   └── CreateUserRequest.cs
├── Services/
│   ├── UserService.cs
│   ├── ProductService.cs
│   └── OrderService.cs
├── Data/
│   ├── ProfeLunoContext.cs
│   └── DbInitializer.cs
├── Migrations/
│   ├── 20240121000000_InitialCreate.cs
│   └── 20240121000001_AddProducts.cs
├── Middleware/
├── Exceptions/
├── Program.cs
├── appsettings.json
├── appsettings.Development.json
└── ProfeLuno.csproj
```

## Estrutura de pastas compartilhadas

```
profeluno/
├── .git/                          # Git
├── .gitignore                     # Arquivo git ignore
├── docker-compose.yml             # Docker Compose
├── README.md                      # Principal
├── GUIA_DESENVOLVIMENTO.md        # Guia em 3 partes
├── INTEGRACAO_LARAVEL_DOTNET.md  # Exemplos de integração
├── COMANDOS_RAPIDOS.md           # Referência rápida
├── ESTRUTURA_PROJETO.md          # Este arquivo
│
├── backend-laravel/              # Seu lado
├── backend-dotnet/               # Lado do parceiro
│
└── docs/ (opcional)
    ├── api-spec.md
    ├── database-schema.md
    └── deployment.md
```

## Próximos passos

### 1. Inicializar .NET (seu parceiro)

```bash
docker-compose exec dotnet bash

# Criar novo projeto
dotnet new webapi -n ProfeLuno
cd ProfeLuno

# Instalar Entity Framework Core
dotnet add package Microsoft.EntityFrameworkCore
dotnet add package Microsoft.EntityFrameworkCore.Npgsql
dotnet add package Microsoft.AspNetCore.Cors

# Rodar
dotnet watch run
```

### 2. Configurar Laravel (você)

```bash
docker-compose exec laravel bash

# Criar controllers necessários
php artisan make:controller Api/UserController --api
php artisan make:controller Api/ProductController --api
php artisan make:controller DotNetApiController

# Criar models
php artisan make:model User --migration
php artisan make:model Product --migration
php artisan make:model Order --migration

# Rodar migrações
php artisan migrate
```

### 3. Criar primeira integração

**Seu parceiro (C#)**:
1. Criar endpoint `/api/users` GET
2. Criar endpoint `/api/users` POST

**Você (Laravel)**:
1. Criar rota `/api/dotnet/users` que chama API C#
2. Testar com: `curl http://localhost:8000/api/dotnet/users`

### 4. Comitar no Bitbucket

```bash
# De fora dos containers
git add .
git commit -m "Initial project structure"
git push origin main
```

## Convenções recomendadas

### Naming em Laravel
- Controllers: `UserController.php` (singular + Controller)
- Models: `User.php` (singular, PascalCase)
- Migrations: `2024_01_21_000000_create_users_table.php`
- Routes: `users.index`, `users.store`, `users.show`, etc.

### Naming em C#
- Controllers: `UsersController.cs` (plural)
- Models: `User.cs` (PascalCase)
- DTOs: `UserDto.cs`, `CreateUserRequest.cs`
- Services: `UserService.cs`
- Migrations: `20240121000000_InitialCreate.cs`

### Database
- Tabelas: lowercase plural (`users`, `products`, `orders`)
- Colunas: lowercase with underscores (`first_name`, `created_at`)
- IDs: `id` (primary key)

## Git branch strategy

```
main (produção/releases)
  ↓
dev (integração contínua)
  ├── feature/laravel-auth
  ├── feature/laravel-dashboard
  ├── feature/dotnet-user-api
  ├── feature/dotnet-product-api
  └── hotfix/bug-login
```

**Fluxo**:
1. Criar feature branch de `dev`
2. Commitar código
3. Push para Bitbucket
4. Pull Request para `dev`
5. Review do parceiro
6. Merge
7. Periodicamente: Merge `dev` → `main` (releases)

## Ambiente de desenvolvimento

| Variável | Você | Parceiro |
|----------|------|----------|
| Editor | VS Code | VS Code |
| Terminal | WSL Bash | WSL Bash |
| Browser | Chrome/Firefox | Chrome/Firefox |
| API Client | Postman | Postman |
| Git | GitHub Desktop ou CLI | GitHub Desktop ou CLI |

## ✅ Checklist antes de começar

- [ ] Docker rodando: `docker-compose ps` ✓
- [ ] Laravel acessível: http://localhost:8000
- [ ] Vite acessível: http://localhost:5173
- [ ] C# acessível: http://localhost:9000
- [ ] PostgreSQL conectando: `psql -h localhost -U postgres -d profeluno`
- [ ] Git configurado: `git config --list`
- [ ] Parceiro clonou e conseguiu rodar
- [ ] Primeiro commit feito
- [ ] Pull Request funciona no Bitbucket

---

**Status**: 🟢 Pronto para começar!

# 📁 Estrutura do Projeto Laravel - Sistema de Aulas Virtuais

## 🎯 Organização dos Arquivos

### 1. **Views (resources/views/)**

```
resources/views/
├── layouts/
│   └── app.blade.php                 # Layout principal
│
├── partials/
│   ├── sidebar.blade.php            # Componente sidebar
│   └── header.blade.php             # Componente header
│
├── aluno/
│   ├── dashboard.blade.php          # Dashboard do aluno
│   ├── buscar-sala.blade.php        # Buscar professores
│   ├── sala.blade.php               # Sala de aula (aluno)
│   ├── simulados.blade.php          # Lista de simulados
│   ├── conteudos.blade.php          # Conteúdos das aulas
│   └── perfil.blade.php             # Perfil do aluno
│
└── professor/
    ├── dashboard.blade.php          # Dashboard do professor
    ├── salas.blade.php              # Gerenciar salas
    ├── sala-criar.blade.php         # Criar nova sala
    ├── simulados.blade.php          # Gerenciar simulados
    ├── conteudos.blade.php          # Gerenciar conteúdos
    └── perfil.blade.php             # Perfil do professor
```

### 2. **CSS (public/css/)**

```
public/css/
├── dashboard.css                    # Estilos globais do dashboard
├── buscar-sala.css                  # Estilos da busca de sala
├── sala-aula.css                    # Estilos da sala de aula
└── components.css                   # Componentes reutilizáveis
```

### 3. **JavaScript (public/js/)**

```
public/js/
├── dashboard.js                     # Scripts principais
├── sala-aula.js                     # Funcionalidades da sala
├── webrtc.js                        # WebRTC para vídeo
└── chat.js                          # Sistema de chat
```

### 4. **Controllers (app/Http/Controllers/)**

```
app/Http/Controllers/
├── AlunoController.php              # Lógica do aluno
├── ProfessorController.php          # Lógica do professor
├── SalaController.php               # Lógica das salas
├── SimuladoController.php           # Lógica dos simulados
└── AuthController.php               # Autenticação
```

### 5. **Models (app/Models/)**

```
app/Models/
├── User.php                         # Usuário (aluno/professor)
├── Professor.php                    # Professor
├── Aluno.php                        # Aluno
├── Sala.php                         # Sala de aula
├── Simulado.php                     # Simulado
├── Questao.php                      # Questão do simulado
├── Conteudo.php                     # Conteúdo da aula
├── Materia.php                      # Matéria
├── Avaliacao.php                    # Avaliação de professor
└── Mensagem.php                     # Mensagem do chat
```

### 6. **Migrations (database/migrations/)**

```
database/migrations/
├── xxxx_create_users_table.php
├── xxxx_create_professores_table.php
├── xxxx_create_alunos_table.php
├── xxxx_create_salas_table.php
├── xxxx_create_simulados_table.php
├── xxxx_create_questoes_table.php
├── xxxx_create_conteudos_table.php
├── xxxx_create_materias_table.php
├── xxxx_create_avaliacoes_table.php
└── xxxx_create_mensagens_table.php
```

---

## 🚀 Comandos de Instalação

### 1. **Criar o projeto Laravel**
```bash
composer create-project laravel/laravel sistema-aulas-virtuais
cd sistema-aulas-virtuais
```

### 2. **Configurar o .env**
```env
APP_NAME="Sistema de Aulas Virtuais"
APP_URL=http://localhost:8000

DB_CONNECTION=pgsql
DB_HOST=localhost
DB_PORT=5432
DB_DATABASE=profeluno
DB_USERNAME=postgres
DB_PASSWORD=postgres
```

### 3. **Instalar dependências**
```bash
composer install
npm install
```

### 4. **Criar estrutura de pastas**
```bash
# Views
mkdir -p resources/views/layouts
mkdir -p resources/views/partials
mkdir -p resources/views/aluno
mkdir -p resources/views/professor

# CSS e JS
mkdir -p public/css
mkdir -p public/js
```

### 5. **Criar arquivos CSS e JS**
Copie os arquivos CSS e JS que criei para as respectivas pastas em `public/`

### 6. **Criar Controllers**
```bash
php artisan make:controller AlunoController
php artisan make:controller ProfessorController
php artisan make:controller SalaController
```

### 7. **Criar Models**
```bash
php artisan make:model Professor -m
php artisan make:model Aluno -m
php artisan make:model Sala -m
php artisan make:model Simulado -m
```

### 8. **Rodar migrations**
```bash
php artisan migrate
```

### 9. **Iniciar servidor**
```bash
php artisan serve
```

---

## 📝 Exemplo de Migration - Professores

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('professores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nome');
            $table->string('especialidade');
            $table->text('bio')->nullable();
            $table->string('foto')->nullable();
            $table->decimal('avaliacao_media', 3, 2)->default(0);
            $table->integer('total_avaliacoes')->default(0);
            $table->integer('total_alunos')->default(0);
            $table->boolean('ao_vivo')->default(false);
            $table->string('aula_atual')->nullable();
            $table->string('proxima_aula')->nullable();
            $table->boolean('certificado')->default(false);
            $table->string('avatar_color')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('professores');
    }
};
```

---

## 🎨 Personalização de Cores

Você pode personalizar as cores editando o `:root` no arquivo `dashboard.css`:

```css
:root {
    --primary-color: #7367f0;      /* Cor principal */
    --success-color: #28c76f;      /* Verde */
    --danger-color: #ea5455;       /* Vermelho */
    --warning-color: #ff9f43;      /* Laranja */
    --info-color: #00cfe8;         /* Azul claro */
    --dark-bg: #1e1e2d;           /* Fundo escuro */
    --card-bg: #2b2b40;           /* Fundo dos cards */
    --sidebar-bg: #262637;         /* Fundo da sidebar */
}
```

---

## 🔐 Middleware de Autenticação

Crie um middleware para diferenciar aluno e professor:

```bash
php artisan make:middleware CheckRole
```

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, $role)
    {
        if (!auth()->check() || auth()->user()->role !== $role) {
            abort(403, 'Acesso não autorizado');
        }

        return $next($request);
    }
}
```

Registre no `app/Http/Kernel.php`:

```php
protected $routeMiddleware = [
    // ...
    'role' => \App\Http\Middleware\CheckRole::class,
];
```

---

## 📚 Próximos Passos

1. ✅ Implementar autenticação (Laravel Breeze/Jetstream)
2. ✅ Criar seeders com dados de teste
3. ✅ Implementar WebRTC para vídeo chamadas
4. ✅ Adicionar sistema de chat em tempo real (Laravel Echo + Pusher)
5. ✅ Implementar upload de arquivos (materiais)
6. ✅ Adicionar notificações em tempo real
7. ✅ Criar dashboard com estatísticas

---

## 🛠️ Tecnologias Utilizadas

- **Laravel 10+** - Framework PHP
- **Bootstrap 5** - Framework CSS
- **Font Awesome** - Ícones
- **MySQL** - Banco de dados
- **WebRTC** - Vídeo chamadas
- **Pusher/Laravel Echo** - Real-time (opcional)

---

## 📞 Suporte

Se tiver dúvidas sobre a implementação, consulte:
- [Documentação Laravel](https://laravel.com/docs)
- [Bootstrap Docs](https://getbootstrap.com/docs)
- [WebRTC Guide](https://webrtc.org/getting-started/overview)

---

**Desenvolvido com ❤️ para educação online**