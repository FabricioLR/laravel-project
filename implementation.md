# Plano de Implementação: Todo-App Architecture & QA (Laravel 12 + React)

Este documento descreve a arquitetura técnica, estratégia de segurança e plano de testes para a conversão da Dashboard em um sistema de Gerenciamento de Tarefas multi-usuário.

---

## 1. Arquitetura de Dados

### 1.1 Schema da Tabela `todos`
- `id`: Big Increments (Primary Key).
- `user_id`: Foreign ID (Constrained to `users.id`, onDelete cascade).
- `title`: String (Máx 255 caracteres).
- `is_completed`: Boolean (Default: `false`).
- `timestamps`: `created_at` e `updated_at`.

### 1.2 Model `Todo`
- **Relations**: `belongsTo(User::class)`.
- **Casts**: `is_completed` => `boolean`.
- **Mass Assignment**: `fillable = ['title', 'is_completed']`.

---

## 2. API & Roteamento

As rotas serão protegidas por `auth` e `verified`, substituindo a rota `/dashboard` estática por um recurso dinâmico.

```php
// routes/web.php
use App\Http\Controllers\TodoController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('dashboard', TodoController::class)
        ->names([
            'index'   => 'dashboard',
            'store'   => 'todos.store',
            'update'  => 'todos.update',
            'destroy' => 'todos.destroy',
        ]);
});
```

---

## 3. Segurança e Autorização (Policies)

Para garantir que um usuário nunca manipule tarefas de terceiros (Isolamento de Dados), utilizaremos **Laravel Policies**.

- **TodoPolicy**:
    - `viewAny`: Apenas tarefas onde `user_id === auth()->id()`.
    - `update`: Verificar propriedade antes de alternar status ou editar título.
    - `delete`: Verificar propriedade antes de remover.

---

## 4. Estratégia de Testes (TDD)

### 4.1 Backend (Pest PHP)
Utilizaremos testes de integração para validar o contrato da API e a segurança.

- **Testes de Rota**:
    - `GET /dashboard`: Deve retornar status 200 e a lista de tarefas filtrada por usuário.
    - `POST /dashboard`: Deve validar se o título é obrigatório e persistir a tarefa vinculada ao usuário logado.
    - `PATCH /dashboard/{todo}`: Deve permitir apenas que o dono altere o status. Tentar alterar tarefa de outro usuário deve retornar 403 (Forbidden).
    - `DELETE /dashboard/{todo}`: Deve garantir o isolamento na exclusão.

### 4.2 Frontend (Inertia Testing)
- Verificar se as `props.todos` estão sendo injetadas corretamente no componente.
- Validar se o estado de filtro reflete na visualização.

---

## 5. Integração Frontend (React + Tailwind)

- **Componente**: `resources/js/pages/dashboard.tsx` será refatorado para conter a lógica da `TodoApp`.
- **Layout**: Manutenção do `AppLayout` original do Starter Kit para preservar a identidade visual e navegação sidebar/topbar.
- **Estado Reativo**:
    - Uso do hook `useForm` do `@inertiajs/react` para envios atômicos e processamento de erros.
    - Implementação de **Search Bar** e **Filter Tabs** (All, Pending, Completed).
    - Feedback Visual: Uso de transições suaves do Tailwind para mudanças de status.

---

## 6. Fluxo de Execução (Step-by-Step)

1.  **Database**: Criar Migration e Model `Todo`.
2.  **Factory**: Criar `TodoFactory` para apoiar os testes automatizados.
3.  **Security**: Gerar e registrar a `TodoPolicy`.
4.  **TDD Start**: Criar arquivo de teste `tests/Feature/TodoControllerTest.php` com os casos de falha/sucesso.
5.  **Backend**: Implementar `TodoController` e `TodoRequest`.
6.  **Routing**: Configurar `web.php`.
7.  **Frontend**: Refatorar `dashboard.tsx`, integrando chamadas `router.patch` e formulário de criação.
8.  **Verify**: Executar suite de testes e validar filtros de UI.
