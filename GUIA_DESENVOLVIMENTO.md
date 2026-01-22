# 📚 GUIA COMPLETO - PROJETO TCCPROFELUNO

## PARTE 1: INICIALIZAR O PROJETO LARAVEL

### 1.1 Pré-requisitos instalados
✅ WSL 2 (Windows Subsystem for Linux)
✅ Docker Desktop
✅ Docker Compose
✅ Git

### 1.2 Estrutura do seu projeto (PRONTA)
```
profeluno/
├── backend-laravel/       # PHP + Laravel
├── backend-dotnet/        # C# APIs
├── docker-compose.yml     # Orquestração
└── .env (será criado)
```

### 1.3 PASSO A PASSO: Iniciar Laravel localmente

#### PASSO 1: Acessar a pasta do projeto
```bash
# No WSL terminal
cd /var/www/html/profeluno
```

#### PASSO 2: Subir os containers (VOCÊ JÁ FEZ)
```bash
docker-compose up -d
```

**Resultado esperado:**
```
✔ Container profeluno_postgres  Running
✔ Container profeluno_laravel   Started
✔ Container profeluno_vite      Running
✔ Container profeluno_dotnet    Running
```

#### PASSO 3: Entrar no container Laravel
```bash
docker-compose exec laravel bash
```

Agora você está DENTRO do container. Próximos comandos rodam lá dentro.

#### PASSO 4: Criar arquivo .env (se não existir)
```bash
# Dentro do container
cp .env.example .env
php artisan key:generate
```

#### PASSO 5: Executar migrações do banco
```bash
# Dentro do container
php artisan migrate
```

#### PASSO 6: Sair do container
```bash
exit
```

#### PASSO 7: Verificar se está funcionando
- **Laravel**: http://localhost:8000
- **Vite (JS)**: http://localhost:5173
- **C# API**: http://localhost:5000
- **PostgreSQL**: localhost:5432

#### PASSO 8: Ver logs em tempo real (IMPORTANTE)
```bash
# Terminal 1: Logs do Laravel
docker-compose logs -f laravel

# Terminal 2: Logs do C#
docker-compose logs -f dotnet

# Terminal 3: Logs do Vite
docker-compose logs -f vite
```

### 1.4 Comandos úteis do Laravel

Dentro do container (`docker-compose exec laravel bash`):

```bash
# Ver status
docker-compose ps

# Parar tudo
docker-compose down

# Reiniciar tudo
docker-compose restart

# Criar controller
php artisan make:controller NomeController

# Criar model
php artisan make:model NomeModel

# Ver rotas registradas
php artisan route:list

# Limpar cache
php artisan cache:clear
php artisan config:clear
```

---

## PARTE 2: CONFIGURAR GITHUB/BITBUCKET COM DOCKER

### 2.1 Você já usa Bitbucket? (RECOMENDADO)

#### OPÇÃO 1: Usar Bitbucket (seu caso)
✅ Já tem experiência
✅ Integra com Docker
✅ Ambientes de desenvolvimento idênticos
✅ Seu parceiro pode clonar tudo e rodar com `docker-compose up`

### 2.2 PASSO A PASSO: Configurar Bitbucket

#### PASSO 1: Criar repositório vazio no Bitbucket
1. Acesse: https://bitbucket.org/
2. Crie novo repositório: **profeluno**
3. Escolha: Git, Privado, sem README

#### PASSO 2: Inicializar Git localmente
```bash
# No seu WSL, na pasta profeluno
cd /var/www/html/profeluno
git init
git add .
git commit -m "Initial commit: Laravel + C# Docker setup"
```

#### PASSO 3: Conectar ao repositório remoto
```bash
# Copie a URL do Bitbucket (SSH ou HTTPS)
# Exemplo: git@bitbucket.org:seu_usuario/profeluno.git

git remote add origin git@bitbucket.org:seu_usuario/profeluno.git
git branch -M main
git push -u origin main
```

#### PASSO 4: Configurar SSH (RECOMENDADO)
```bash
# Gerar chave SSH (se não tiver)
ssh-keygen -t ed25519 -C "seu_email@example.com"

# Copiar chave pública
cat ~/.ssh/id_ed25519.pub

# Adicionar em Bitbucket:
# Settings > SSH Keys > Add Key
# Cole a chave pública
```

#### PASSO 5: Seu parceiro clonar o projeto
```bash
# Seu parceiro faz:
git clone git@bitbucket.org:seu_usuario/profeluno.git
cd profeluno
docker-compose up -d

# Pronto! Ele tem ambiente idêntico ao seu
```

### 2.3 Estrutura de branches (para trabalhar em dupla)

```
main (produção)
  ├── dev (desenvolvimento)
  ├── feature/laravel-auth
  ├── feature/dotnet-apis
  └── hotfix/bug-login
```

#### Workflow padrão:
```bash
# Você cria uma feature
git checkout -b feature/laravel-auth dev
# ... escreve código ...
git push origin feature/laravel-auth

# Seu parceiro cria outra
git checkout -b feature/dotnet-apis dev
# ... escreve código ...
git push origin feature/dotnet-apis

# Fazer merge (via Pull Request no Bitbucket)
```

### 2.4 .gitignore (crie esse arquivo na raiz)

```
# Laravel
backend-laravel/.env
backend-laravel/.env.local
backend-laravel/vendor/
backend-laravel/node_modules/
backend-laravel/storage/logs/*
backend-laravel/bootstrap/cache/*

# .NET
backend-dotnet/bin/
backend-dotnet/obj/
backend-dotnet/.vs/
backend-dotnet/appsettings.local.json

# Docker volumes
postgres_data/

# IDEs
.vscode/
.idea/
*.sublime-project
*.sublime-workspace

# OS
.DS_Store
Thumbs.db
```

---

## PARTE 3: INTEGRAR LARAVEL + C# NO MESMO PROJETO

### 3.1 Arquitetura do seu projeto

```
ProfeLuno = Frontend + Backend
│
├── LARAVEL (PHP)
│   ├── Controllers → Lógica de negócio
│   ├── Views → HTML/Blade templates
│   ├── Routes → Rotas HTTP
│   └── Models → Dados do banco
│
└── .NET (C#)
    ├── Controllers → APIs REST
    ├── Services → Lógica complexa
    ├── Models → DTOs
    └── Database → Context do EF Core
```

### 3.2 Como se comunicam?

#### CENÁRIO: Laravel precisa chamar API do C#

**No Laravel (backend-laravel/app/Http/Controllers):**
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class UserController extends Controller
{
    public function store()
    {
        // Laravel faz requisição para C#
        $response = Http::post('http://dotnet:5000/api/users', [
            'name' => request('name'),
            'email' => request('email'),
        ]);
        
        if ($response->successful()) {
            return response()->json($response->json());
        }
    }
}
```

**No C# (backend-dotnet/Controllers/UsersController.cs):**
```csharp
[ApiController]
[Route("api/[controller]")]
public class UsersController : ControllerBase
{
    [HttpPost]
    public async Task<ActionResult> Create([FromBody] UserDto dto)
    {
        // C# processa e retorna
        var user = new User { Name = dto.Name, Email = dto.Email };
        await _context.Users.AddAsync(user);
        await _context.SaveChangesAsync();
        
        return Ok(user);
    }
}
```

### 3.3 Compartilhando banco de dados PostgreSQL

Ambos conseguem acessar o mesmo banco via `docker-compose.yml`:

```yaml
services:
  laravel:
    environment:
      DB_HOST: postgres        # Acesso ao DB
      DB_DATABASE: profeluno
      DB_USERNAME: postgres
      DB_PASSWORD: postgres

  dotnet:
    environment:
      # Connection String do .NET
      ConnectionStrings__DefaultConnection: "Host=postgres;Database=profeluno;Username=postgres;Password=postgres;"

  postgres:
    image: postgres:16-alpine
```

### 3.4 Fluxo de dados típico

```
Usuario acessa → Laravel (8000)
                    ↓
              Controller Laravel
                    ↓
              Precisa processar foto?
                    ↓
         Chama API .NET (5000)
                    ↓
            C# processa imagem
                    ↓
            Salva no DB PostgreSQL
                    ↓
            Retorna para Laravel
                    ↓
            Laravel envia HTML/View
                    ↓
            Browser recebe (Vite 5173)
```

### 3.5 Seu parceiro (programando em C#) - Setup

```bash
# Ele clona o projeto
git clone git@bitbucket.org:seu_usuario/profeluno.git
cd profeluno

# Ele sobe Docker (Laravel já está rodando)
docker-compose up -d

# Ele trabalha em backend-dotnet/
# Qualquer mudança ele faz push para feature/dotnet-* 

git checkout -b feature/dotnet-apis dev
# ... código C# ...
git add backend-dotnet/
git commit -m "feat: create user API endpoint"
git push origin feature/dotnet-apis
```

### 3.6 Criar projeto .NET dentro do Docker

**Seu parceiro faz isso UMA VEZ:**

```bash
# Entrar no container .NET
docker-compose exec dotnet bash

# Criar projeto web API
dotnet new webapi -n ProfeLuno
cd ProfeLuno

# Restaurar dependências
dotnet restore

# Rodar
dotnet run
```

### 3.7 Portas e URLs de comunicação

| Serviço | Porta | Acesso | URL |
|---------|-------|--------|-----|
| Laravel | 8000 | Externo | http://localhost:8000 |
| Vite (Frontend) | 5173 | Externo | http://localhost:5173 |
| C# API | 5000 | Externo | http://localhost:5000 |
| PostgreSQL | 5432 | Externo | localhost:5432 |
| **Interno (entre containers)** |  |  |  |
| Laravel → C# | N/A | Interno | http://dotnet:5000 |
| C# → DB | N/A | Interno | postgresql://postgres:5432 |
| Laravel → DB | N/A | Interno | postgresql://postgres:5432 |

### 3.8 Variáveis de ambiente (IMPORTANTE)

**backend-laravel/.env:**
```
APP_ENV=local
APP_DEBUG=true
DB_CONNECTION=pgsql
DB_HOST=postgres
DB_DATABASE=profeluno
DB_USERNAME=postgres
DB_PASSWORD=postgres
DOTNET_API_URL=http://dotnet:5000
```

**backend-dotnet/appsettings.Development.json:**
```json
{
  "ConnectionStrings": {
    "DefaultConnection": "Host=postgres;Database=profeluno;Username=postgres;Password=postgres;"
  },
  "Logging": {
    "LogLevel": {
      "Default": "Debug"
    }
  }
}
```

---

## 🎯 RESUMO FINAL

### Workflow diário (em dupla):

**VOCÊ (Laravel):**
```bash
cd /var/www/html/profeluno
git pull origin dev
docker-compose up -d
docker-compose exec laravel bash
php artisan serve
# Trabalha em feature/laravel-*
```

**SEU PARCEIRO (C#):**
```bash
cd /var/www/html/profeluno
git pull origin dev
docker-compose up -d
docker-compose exec dotnet bash
dotnet watch run
# Trabalha em feature/dotnet-*
```

**Ao final do dia:**
```bash
# Ambos fazem
git push origin feature/...

# No Bitbucket: Create Pull Request → dev
# Revisar código, aprovar, merge
```

**Banco de dados (automático):**
- PostgreSQL roda em background
- Ambos acessam mesmo DB
- Migrações: Laravel (Eloquent) + C# (EF Core)

---

## ✅ CHECKLIST

- [ ] Docker subindo (você conseguiu!)
- [ ] Laravel acessível em http://localhost:8000
- [ ] Vite acessível em http://localhost:5173
- [ ] C# acessível em http://localhost:5000
- [ ] Repositório Bitbucket criado
- [ ] Git inicializado localmente
- [ ] Primeira versão enviada para Bitbucket
- [ ] Parceiro clonou e conseguiu rodar
- [ ] Teste requisição Laravel → C#
- [ ] Teste salvar dados em PostgreSQL

Perguntas? Estou aqui! 🚀
