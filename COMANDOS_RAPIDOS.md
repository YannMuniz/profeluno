# 🚀 COMANDOS RÁPIDOS - PROFELUNO

## Status & Logs

```bash
# Ver todos os containers
docker-compose ps

# Ver logs em tempo real (substituir "laravel" por: dotnet, postgres, vite)
docker-compose logs -f laravel
docker-compose logs -f dotnet

# Ver todos os logs
docker-compose logs

# Últimas 50 linhas
docker-compose logs --tail=50
```

## Iniciar / Parar

```bash
# Subir tudo em background
docker-compose up -d

# Subir e ver logs
docker-compose up

# Parar tudo
docker-compose down

# Parar e remover volumes (⚠️ CUIDADO: deleta dados!)
docker-compose down -v

# Reiniciar tudo
docker-compose restart

# Reiniciar apenas um serviço
docker-compose restart laravel
docker-compose restart dotnet
```

## Entrar nos containers

```bash
# Entrar no Laravel (bash/shell)
docker-compose exec laravel bash

# Entrar no .NET
docker-compose exec dotnet bash

# Entrar no PostgreSQL
docker-compose exec postgres psql -U postgres -d profeluno

# Sair: exit ou \q
```

## Laravel (dentro do container)

```bash
# Estar dentro: docker-compose exec laravel bash

# Criar controller
php artisan make:controller NomeController

# Criar model
php artisan make:model NomeModel

# Criar migration
php artisan make:migration create_tabela_table

# Rodar migrações
php artisan migrate

# Desfazer migrations
php artisan migrate:rollback

# Limpar cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Ver rotas
php artisan route:list

# Criar seeder
php artisan make:seeder UserSeeder

# Rodar seeders
php artisan db:seed

# Artisan Tinker (console interativo)
php artisan tinker
```

## npm / Vite

```bash
# Dentro do container Laravel

# Instalar dependências
npm install

# Rodar dev (já está em outro container, mas pode usar aqui)
npm run dev

# Build para produção
npm run build

# Verificar versões
npm list vue
npm list laravel-vite-plugin
```

## Git (na sua máquina)

```bash
# Inicializar repo (primeira vez)
cd /var/www/html/profeluno
git init
git add .
git commit -m "Initial commit"

# Adicionar remote (primeira vez)
git remote add origin git@bitbucket.org:seu_usuario/profeluno.git

# Clonar projeto (seu parceiro)
git clone git@bitbucket.org:seu_usuario/profeluno.git

# Branches
git checkout -b feature/laravel-auth dev
git checkout dev
git branch -l

# Push
git push origin feature/laravel-auth

# Pull
git pull origin dev

# Merge local
git merge feature/laravel-auth

# Status
git status

# Ver commits
git log --oneline
```

## PostgreSQL

```bash
# Conectar (fora do Docker)
psql -h localhost -U postgres -d profeluno
# Senha: postgres

# Ou dentro do container
docker-compose exec postgres psql -U postgres -d profeluno

# Comandos SQL
\dt                      # Ver tabelas
SELECT * FROM users;     # Consultar
\q                       # Sair
```

## Limpeza & Manutenção

```bash
# Remover imagens não usadas
docker image prune

# Remover containers não usados
docker container prune

# Remover volumes não usados
docker volume prune

# Ver espaço em disco
docker system df

# Limpeza completa (⚠️ CUIDADO!)
docker system prune -a
```

## Rebuild (recriar imagens)

```bash
# Forçar rebuild das imagens
docker-compose build --no-cache

# Depois subir
docker-compose up -d
```

## URLs de acesso

- **Laravel (APP)**: http://localhost:8000
- **Vite (Frontend Dev)**: http://localhost:5173
- **C# API**: http://localhost:9000
- **PostgreSQL**: localhost:5432
- **Bitbucket**: https://bitbucket.org

## Variáveis de ambiente

| Variável | Valor |
|----------|-------|
| `DB_HOST` | localhost |
| `DB_PORT` | 5432 |
| `DB_USER` | postgres |
| `DB_PASS` | postgres |
| `DB_NAME` | profeluno |

## Teste rápido

```bash
# Testar Laravel
curl http://localhost:8000

# Testar C#
curl http://localhost:9000

# Testar API via Laravel
curl http://localhost:8000/api/dotnet/users
```

## SSH/Git setup (primeira vez)

```bash
# Gerar chave (se não tiver)
ssh-keygen -t ed25519 -C "seu_email@example.com"

# Copiar chave pública
cat ~/.ssh/id_ed25519.pub

# Adicionar em Bitbucket: 
# Settings > SSH Keys > Add key
# Cole a chave pública
```

## Troubleshooting rápido

```bash
# Container não inicia?
docker-compose logs <serviço>

# Porta já está sendo usada?
lsof -i :8000
lsof -i :9000
lsof -i :5173

# Reiniciar Docker Desktop
# Ou no WSL: sudo systemctl restart docker

# Limpar tudo e começar do zero
docker-compose down -v
docker-compose up --build -d
```

---

**Dica**: Bookmark isso ou coloque em um sticky note na sua área de trabalho! 📌
