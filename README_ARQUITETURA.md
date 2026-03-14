# 🏗️ Arquitetura do Projeto: Frontend + Backend

---

## 📊 Visão Geral

```
┌──────────────────┐
│   NAVEGADOR      │ (Usuário vê HTML)
└────────┬─────────┘
         │ HTTP
         ▼
┌──────────────────────────────────────┐
│ LARAVEL - Port 8000 🎨 (VOCÊ)       │
│ ├─ Controllers                       │
│ ├─ Views (Blade)                     │
│ ├─ DotNetService (chamadas ao .NET)  │
│ └─ Sessão de usuário                 │
└────────┬────────────────────────────┘
         │ HTTP JSON
         ▼
┌──────────────────────────────────────┐
│ .NET - Port 5000 🔧 (SEU AMIGO)     │
│ ├─ APIs REST                         │
│ ├─ Services (lógica)                 │
│ ├─ Banco de dados                    │
│ ├─ Autenticação JWT                  │
│ └─ Videochamada (SignalR)            │
└──────────────────────────────────────┘
```

---

## 🎯 Responsabilidades

### 🎨 Laravel (Você)
```
✅ Renderizar HTML
✅ Validar formulários
✅ Exibir dados do .NET
✅ Gerenciar sessão do usuário

❌ NÃO fazer CRUD direto
❌ NÃO implementar autenticação
❌ NÃO consultar banco
```

### 🔧 .NET (Seu Amigo)
```
✅ APIs REST completas
✅ CRUD no banco
✅ Autenticação JWT
✅ Lógica de negócio
✅ Videochamada

❌ NÃO renderizar HTML
❌ NÃO gerenciar sessão de frontend
```

---

## 💡 Exemplo Prático

### Listar Salas

**Na View:**
```blade
@foreach($classrooms as $classroom)
    <h3>{{ $classroom['name'] }}</h3>
@endforeach
```

**No Controller:**
```php
public function index()
{
    $response = $this->dotNetService->getClassrooms();
    $classrooms = $response['data'];
    return view('classrooms.index', compact('classrooms'));
}
```

**Em DotNetService:**
```php
public function getClassrooms()
{
    $response = Http::get('http://localhost:5000/api/classrooms');
    return $response->json();
}
```

**No .NET Controller:**
```csharp
[HttpGet]
public async Task<IActionResult> GetAll()
{
    var classrooms = await _service.GetAllAsync();
    return Ok(new { success = true, data = classrooms });
}
```

---

## 🔑 Arquivo Principal

**`backend-laravel/app/Services/DotNetService.php`**

Este arquivo centraliza TODAS as chamadas ao .NET. Use assim:

```php
class YourController
{
    protected $dotNetService;
    
    public function __construct(DotNetService $dotNetService)
    {
        $this->dotNetService = $dotNetService;
    }
    
    public function someMethod()
    {
        // Chamando .NET
        $response = $this->dotNetService->getClassrooms();
        
        // Tratando resposta
        if (!$response['success']) {
            return redirect()->withErrors($response['error']);
        }
        
        // Renderizando
        return view('my-view', ['data' => $response['data']]);
    }
}
```

---

## 🔐 Fluxo de Autenticação

```
1. Usuário digita email/senha → Laravel
2. Laravel POST para .NET: /api/auth/login
3. .NET valida + gera JWT token
4. Laravel armazena token em session('auth_token')
5. Próximas requisições: Authorization: Bearer {token}
```

---

## 📝 Documentação Completa

| Arquivo | Conteúdo |
|---------|----------|
| **README_ARQUITETURA.md** | Este arquivo (visão geral) |
| **GUIA_RAPIDO_INICIO.md** | Setup passo-a-passo |
| **GUIA_DOTNET_BACKEND.md** | Para seu amigo .NET |
| **MAPA_RESPONSABILIDADES.md** | Quem faz o quê (tabelas claras) |

---

## 🚀 Começar AGORA

### Você
```bash
cd backend-laravel
composer install
cp .env.example .env
php artisan serve
```

### Seu Amigo
```bash
cd backend-dotnet
dotnet restore
dotnet run
```

### Depois
Leia os arquivos de documentação nesta ordem:
1. Este arquivo (README_ARQUITETURA.md)
2. GUIA_RAPIDO_INICIO.md
3. MAPA_RESPONSABILIDADES.md
4. Repasse GUIA_DOTNET_BACKEND.md para seu amigo

---

**Data:** 11 de fevereiro de 2026
