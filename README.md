# 🎓 ProfeLuno - TCC Project

> Plataforma educacional desenvolvida em **Laravel (PHP)** + **C# (.NET)** com **Docker**

## 📋 Sobre o Projeto

ProfeLuno é um projeto de TCC que integra duas tecnologias:
- **Backend Frontend**: Laravel (PHP) para lógica de negócio e views
- **Backend APIs**: C# (.NET) para processamento complexo e APIs REST
- **Database**: PostgreSQL compartilhado
- **Frontend**: Vite + Vue/React
- **Orquestração**: Docker Compose

## 🏗️ Arquitetura

```
ProfeLuno
├── backend-laravel/       # PHP 8.3 + Laravel 11
├── backend-dotnet/        # C# 8.0 + .NET 8
├── docker-compose.yml     # Orquestração
└── docs/
    ├── GUIA_DESENVOLVIMENTO.md
    ├── INTEGRACAO_LARAVEL_DOTNET.md
    └── COMANDOS_RAPIDOS.md
```

## 🚀 Quick Start

### Pré-requisitos
- [WSL 2](https://docs.microsoft.com/pt-br/windows/wsl/install)
- [Docker Desktop](https://www.docker.com/products/docker-desktop)
- Git

### Setup (primeira vez)

```bash
# 1. Clonar repositório
git clone git@github.com:seu_usuario/profeluno.git
cd profeluno

# 2. Subir containers
docker-compose up -d

# 3. Configurar Laravel
docker-compose exec laravel bash
cp .env.example .env
php artisan key:generate
php artisan migrate
exit

# 4. Acessar no navegador
# Laravel: http://localhost:8000
# Vite: http://localhost:5173
# C#: http://localhost:6000
```

## 📚 Documentação

- **[GUIA_DESENVOLVIMENTO.md](GUIA_DESENVOLVIMENTO.md)** - Passo a passo em 3 partes:
  1. Iniciar Laravel
  2. Configurar github
  3. Integrar Laravel + C#

- **[INTEGRACAO_LARAVEL_DOTNET.md](INTEGRACAO_LARAVEL_DOTNET.md)** - Exemplos de código de integração

- **[COMANDOS_RAPIDOS.md](COMANDOS_RAPIDOS.md)** - Referência rápida de comandos

## 🎯 Portas

| Serviço | Porta | URL |
|---------|-------|-----|
| Laravel | 8000 | http://localhost:8000 |
| Vite | 5173 | http://localhost:5173 |
| C# API | 9000 | http://localhost:9000 |
| PostgreSQL | 5432 | localhost:5432 |

## 🛠️ Comandos Principais

```bash
# Ver status
docker-compose ps

# Subir
docker-compose up -d

# Parar
docker-compose down

# Logs
docker-compose logs -f laravel
docker-compose logs -f dotnet

# Entrar no Laravel
docker-compose exec laravel bash

# Entrar no C#
docker-compose exec dotnet bash

# Migração do banco
docker-compose exec laravel php artisan migrate
```

## 👥 Contribuidores

- **Você** - Laravel/PHP
- **Seu Parceiro** - C#/.NET

## 📖 Workflow

### Você (Laravel)
```bash
git checkout -b feature/laravel-nome dev
# ... código ...
git push origin feature/laravel-nome
# Create Pull Request
```

### Seu Parceiro (C#)
```bash
git checkout -b feature/dotnet-nome dev
# ... código ...
git push origin feature/dotnet-nome
# Create Pull Request
```

## 🔄 Banco de Dados

- **DBMS**: PostgreSQL 13
- **Host**: postgres (dentro do Docker)
- **User**: postgres
- **Password**: postgres
- **Database**: profeluno

Acesso local:
```bash
psql -h localhost -U postgres -d profeluno
```

## 🤝 Colaboração

1. Pull do `dev` branch
2. Criar `feature/seu-nome-feature`
3. Commit com mensagens descritivas
4. Push para seu branch
5. Create Pull Request no Bitbucket
6. Review + Merge para `dev`

## ⚠️ Importante

- **NÃO** fazer commit de `.env` (use `.env.example`)
- **NÃO** fazer commit de `vendor/` e `node_modules/`
- **NÃO** fazer commit de `bin/` e `obj/`
- Sempre comunicar antes de mexer em migrações do banco

## 🆘 Troubleshooting

```bash
# Container não inicia?
docker-compose logs <serviço>

# Reconstruir imagens?
docker-compose build --no-cache
docker-compose up -d

# Limpar tudo?
docker-compose down -v
docker-compose up -d
```

## 📞 Support

Para dúvidas sobre:
- **Integração**: Ver `INTEGRACAO_LARAVEL_DOTNET.md`
- **Desenvolvimento**: Ver `GUIA_DESENVOLVIMENTO.md`
- **Comandos**: Ver `COMANDOS_RAPIDOS.md`

---

**Criado em**: 21 de Janeiro de 2026
**Status**: 🚀 Em desenvolvimento
**License**: MIT
