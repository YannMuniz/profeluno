# 🚀 Guia Rápido de Início

---

## 📋 Pré-requisitos

- PHP 8.x + Composer
- .NET 7/8 SDK
- Docker + Docker Compose (opcional)

---

## 🎯 Fase 1: Laravel (Você)

### Setup
```bash
cd backend-laravel
composer install
cp .env.example .env
php artisan key:generate
php artisan serve
# http://localhost:8000
```

### .env
```env
DOTNET_API_URL=http://localhost:9000/api
DOTNET_API_TIMEOUT=30
```

---

## 🔧 Fase 2: .NET (Seu Amigo)

### Setup
```bash
cd backend-dotnet
dotnet restore
dotnet ef database update
dotnet run
# http://localhost:9000
```

---

## ✅ Fase 3: Verificar

```bash
# Sua CLI
php artisan tinker
$service = app('App\Services\DotNetService');
$response = $service->getClassrooms();
dd($response);
```

---

## 🎯 Seu Primeiro Desenvolvimento

1. Seu amigo implementa: `GET /api/classrooms`
2. Você adiciona em `DotNetService.php`:
```php
public function getClassrooms() { ... }
```

3. Você cria Controller:
```php
public function index() {
    $classrooms = $this->dotNetService->getClassrooms();
    return view('classrooms.index', compact('classrooms'));
}
```

4. Você cria View: `resources/views/classrooms/index.blade.php`
5. Você adiciona Route: `Route::get('/salas', [ClassroomController::class, 'index']);`
6. Testa em `http://localhost:8000/salas`

---

## 📚 Documentação

- **ARQUITETURA_FRONTEND_BACKEND.md** - Entender arquitetura
- **GUIA_DOTNET_BACKEND.md** - Para seu amigo .NET
- **MAPA_RESPONSABILIDADES.md** - Quem faz o quê
- **DotNetService.php** - Classe para chamar .NET

---

**Data:** 11 de fevereiro de 2026
