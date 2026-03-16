# 🚀 COMECE AQUI

Seu projeto foi reorganizado. Aqui está tudo pronto.

---

## 📖 Leia Nesta Ordem

### 1️⃣ **README_ARQUITETURA.md** (você)
   - Entender visão geral
   - Tempo: 5 minutos

### 2️⃣ **MAPA_RESPONSABILIDADES.md** (você)
   - Ver tabelas de quem faz o quê
   - Tempo: 3 minutos

### 3️⃣ **GUIA_RAPIDO_INICIO.md** (você)
   - Passo-a-passo de setup
   - Tempo: 10 minutos

### 4️⃣ **GUIA_DOTNET_BACKEND.md** (seu amigo C#)
   - Passar para seu amigo
   - Ele lê e entende o que fazer

---

## ⚡ Começar Rápido (5 Minutos)

### Você (Laravel)
```bash
cd backend-laravel
composer install
cp .env.example .env
php artisan key:generate
php artisan serve
# http://localhost:8000
```

### Seu Amigo (.NET)
```bash
cd backend-dotnet
dotnet restore
dotnet ef database update
dotnet run
# http://localhost:9000
```

---

## 📂 O Que Mudou?

### ✅ NOVO
- `DotNetService.php` - Centraliza chamadas ao .NET
- `ClassroomController.php` - Exemplo de controller
- `resources/views/classrooms/index.blade.php` - Exemplo de view

### 📚 NOVA DOCUMENTAÇÃO
1. `README_ARQUITETURA.md` ⭐ **COMECE AQUI**
2. `MAPA_RESPONSABILIDADES.md` - Quem faz o quê
3. `GUIA_RAPIDO_INICIO.md` - Setup passo-a-passo
4. `GUIA_DOTNET_BACKEND.md` - Para seu amigo

---

## 🎯 Próximas Ações

### Semana 1
- [ ] Leia README_ARQUITETURA.md
- [ ] Seu amigo lê GUIA_DOTNET_BACKEND.md
- [ ] Setup Laravel (você)
- [ ] Setup .NET (seu amigo)
- [ ] Teste se conseguem se comunicar

### Semana 2+
- [ ] Seu amigo implementa primeiro endpoint
- [ ] Você integra usando DotNetService
- [ ] Você cria view para renderizar
- [ ] Testa no navegador

---

## 🔑 Arquivo Principal

**`backend-laravel/app/Services/DotNetService.php`**

Todos os Controllers usam este serviço para chamar .NET:

```php
$response = $this->dotNetService->getClassrooms();
$response = $this->dotNetService->createClassroom($data);
$response = $this->dotNetService->login($email, $password);
```

---

## 🤝 Workflow em Equipe

```
Você (Laravel)           Seu Amigo (.NET)
      │                        │
      │ 1. Pede endpoint      │
      ├───────────────────────►│
      │                        │
      │ 2. Implementa e testa  │
      │                        │
      │◄───────────────────────┤
      │ 3. Avisa que está pronto
      │                        │
4. Integra em DotNetService     │
5. Cria Controller + View       │
6. Testa no navegador          │
      │                        │
      ├───────────────────────►│ Merge em develop
      │◄───────────────────────┤
      │ Deploy                 │
```

---

## 📊 Checklist Setup

- [ ] Clone repositório
- [ ] Leia este arquivo
- [ ] Leia README_ARQUITETURA.md
- [ ] Leia GUIA_RAPIDO_INICIO.md
- [ ] Configure Laravel (.env + composer install)
- [ ] Configure .NET (appsettings + dotnet restore)
- [ ] Rode ambos
- [ ] Teste tinker: `php artisan tinker` → teste DotNetService
- [ ] Verifique CORS no .NET

---

## 💡 Dicas Importantes

1. **DotNetService é sua melhor amiga**
   - Centraliza tudo
   - Trata erros
   - Gerencia tokens

2. **Controllers apenas chamam serviço + renderizam**
   - Nunca consulte banco direto
   - Sempre use DotNetService

3. **Views apenas exibem dados**
   - Sem lógica complexa
   - Loop com Blade é OK

4. **Seu amigo: APIs sempre retornam JSON**
   - Nunca HTML no .NET
   - Sempre com `{ success: true/false, data: ... }`

5. **Trata erros sempre**
   - Quando .NET cai, .NET que seja
   - Você renderiza página de erro amigável

---

## 🆘 Problemas?

### "Laravel não conecta ao .NET"
```bash
# Verifique se .NET está rodando
curl http://localhost:9000/api/health
```

### "DotNetService não existe"
```bash
# Arquivo deve estar em:
ls backend-laravel/app/Services/DotNetService.php
```

### "Erro de validação"
```bash
# Valide em ambos:
# Laravel: request()->validate()
# .NET: DataAnnotations + ModelState
```

---

## 🎓 Próxima Leitura

Depois de setup, leia em ordem:
1. `README_ARQUITETURA.md`
2. `MAPA_RESPONSABILIDADES.md`
3. `GUIA_RAPIDO_INICIO.md`

---

**Bora começar! 🚀**

Data: 11 de fevereiro de 2026
