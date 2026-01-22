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
- [ ] C# acessível: http://localhost:5000
- [ ] PostgreSQL conectando: `psql -h localhost -U postgres -d profeluno`
- [ ] Git configurado: `git config --list`
- [ ] Parceiro clonou e conseguiu rodar
- [ ] Primeiro commit feito
- [ ] Pull Request funciona no Bitbucket

---

**Status**: 🟢 Pronto para começar!
