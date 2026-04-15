# Tasks: Todo-App Implementation

Lista de tarefas individuais e independentes. Cada tarefa inclui sua respectiva suite de testes para garantir a qualidade contínua.

---

## ✅ Task 1: Camada de Persistência (Model & Migration)
Criar a fundação de dados para as tarefas.
- **Ações**: Gerar Migration `create_todos_table` e o Model `Todo`. Configurar `$fillable` e `$casts`.
- **Suite de Testes**: `tests/Feature/TodoPersistenceTest.php`
    - [x] Criar instância de `Todo` e persistir no banco.
    - [x] Validar se `is_completed` é castado para boolean.
    - [x] Validar se a constraint `user_id` impede registros órfãos.

## 🟢 Task 2: Fábricas e Relacionamentos
Habilitar a geração de dados fakes para testes e definir vínculos Eloquent.
- **Ações**: Criar `TodoFactory`. Definir `todos()` em `User.php` e `user()` em `Todo.php`.
- **Suite de Testes**: `tests/Unit/TodoRelationshipTest.php`
    - [ ] Assert que um `User` possui relação `HasMany` com `Todo`.
    - [ ] Assert que um `Todo` pertence a um `User`.

## 🟢 Task 3: Camada de Segurança (Policies)
Garantir o isolamento de dados entre usuários.
- **Ações**: Gerar `TodoPolicy` e registrar no `AuthServiceProvider` (se necessário no Laravel 12) ou usar resolução automática. Implementar logicamente `update` e `delete`.
- **Suite de Testes**: `tests/Feature/TodoAuthorizationTest.php`
    - [ ] Usuario A **não pode** visualizar tarefas do Usuario B.
    - [ ] Usuario A **não pode** atualizar tarefas do Usuario B.
    - [ ] Usuario A **não pode** deletar tarefas do Usuario B.

## 🟢 Task 4: Fluxo de Criação (Store)
Implementar a criação de tarefas com validação.
- **Ações**: Criar `TodoRequest` (regras: title required, min:3). Implementar `TodoController@store`.
- **Suite de Testes**: `tests/Feature/TodoStoreTest.php`
    - [ ] `POST /dashboard` com título válido cria a tarefa.
    - [ ] `POST /dashboard` sem título retorna erro de validação (422).
    - [ ] Assert que a tarefa criada pertence ao usuário autenticado.

## 🟢 Task 5: Listagem e Filtragem (Index)
Implementar a visualização principal com suporte a filtros e busca.
- **Ações**: Implementar `TodoController@index`. Adicionar filtros por status (pending/completed) e busca textual.
- **Suite de Testes**: `tests/Feature/TodoIndexTest.php`
    - [ ] `GET /dashboard` retorna apenas tarefas do usuário logado.
    - [ ] Filtro `?status=completed` retorna apenas tarefas concluídas.
    - [ ] Filtro `?search=xyz` retorna apenas tarefas correspondentes.

## 🟢 Task 6: Atualização e Remoção (Update/Destroy)
Implementar modificação de estado e exclusão.
- **Ações**: Implementar `TodoController@update` (toggle e edição) e `TodoController@destroy`.
- **Suite de Testes**: `tests/Feature/TodoUpdateDeleteTest.php`
    - [ ] `PATCH /dashboard/{id}` alterna `is_completed`.
    - [ ] `DELETE /dashboard/{id}` remove o registro e redireciona.

## 🟢 Task 7: Interface Reativa (Frontend)
Reconstruir a UI da Dashboard para a aplicação de tarefas.
- **Ações**: Refatorar `dashboard.tsx`. Integrar form creation, toggle hooks e visualização de filtros.
- **Suite de Testes**:
    - [ ] **Manual**: Verificar se o botão de toggle atualiza a lista sem reload total.
    - [ ] **Manual**: Validar exibição de erros do Laravel via `useForm`.
