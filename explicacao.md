# Guia de Onboarding: Laravel/React Starter Kit

Este documento serve como um guia técnico e de arquitetura para novos desenvolvedores, descrevendo o funcionamento interno, padrões de design e fluxos operacionais desta aplicação.

---

## 🏗️ 1. Arquitetura Geral

O projeto segue a estrutura padrão do **Laravel 12**, mas implementa padrões de design modernos para manter o código modular e testável.

### Estrutura de Pastas Destacada
- `app/Actions`: Contém classes de ação únicas (Single Action Classes). É aqui que reside a lógica de negócio específica (ex: `CreateNewUser`, `UpdateUserProfile`).
- `app/Concerns`: Utiliza Traits para compartilhar lógica entre diferentes partes do sistema, especialmente regras de validação (ex: `ProfileValidationRules`).
- `app/Http/Controllers/Settings`: Controllers organizados por domínio para gerenciar as configurações do usuário.
- `resources/js`: Estrutura React moderna com separação entre `pages`, `components`, `hooks` e `layouts`.

---

## 🔄 2. Fluxo de Requisição

O ciclo de vida de uma requisição típica (ex: Atualização de Perfil) é:

1.  **Route**: A rota é definida em `routes/settings.php` usando o middleware `auth`.
2.  **Middleware**: Verificação de autenticação e e-mail verificado.
3.  **Controller**: O `ProfileController` recebe a requisição.
4.  **Validation**: A validação é feita via `ProfileUpdateRequest`, que utiliza as regras definidas no trait `ProfileValidationRules`.
5.  **Logic**: O Controller aplica as mudanças diretamente no model `User`.
6.  **Response**: Uma resposta `Inertia` redireciona o usuário para a página React correspondente, que é renderizada no cliente.

---

## 📊 3. Camada de Dados (Models & Database)

### Entidades Principais
- **User**: Única entidade principal no momento, estendida com suporte a autenticação de dois fatores (`TwoFactorAuthenticatable`) e notificações.

### Padrões Utilizados
- **Eloquent Models**: Uso intensivo de Casts para datas e senhas.
- **Factories**: Localizadas em `database/factories` para auxílio em testes.
- **Migrations**: O banco de dados padrão é o **PostgreSQL** (configurado via Docker).

---

## 🧠 4. Lógica de Negócio

- **Controllers "Magros"**: Os controllers são responsáveis apenas por direcionar o fluxo.
- **Actions (Fortify)**: Toda a lógica de criação de usuários e autenticação está encapsulada em `app/Actions/Fortify`.
- **Validation Traits**: As regras de validação são centralizadas em `app/Concerns`, garantindo consistência entre a API de registro e a edição de perfil.

---

## 🎨 5. Front-end & Integrações

- **Stack**: React + TypeScript + Vite + Inertia.js.
- **Wayfinder**: Integração personalizada para roteamento.
- **Inertia**: Elimina a necessidade de uma API REST tradicional para o frontend interno, permitindo que o Laravel retorne componentes React com seus dados (props).
- **Themes**: Implementação nativa de Dark/Light mode via `hooks/use-appearance.ts`.

---

## 🐳 6. Docker & Ambiente

O projeto utiliza Docker para garantir paridade entre ambientes.

### Dockerfiles
- **`Dockerfile.dev`**: Otimizado para desenvolvimento. Roda o servidor PHP e o Vite (HMR) simultaneamente.
- **`Dockerfile.test`**: Ambiente isolado para execução de testes automatizados.
- **`Dockerfile.prod`**: Otimizado para produção, realiza o build dos assets estáticos (`npm run build`).

---

## 🚀 7. Pipeline CI/CD (Jenkins)

A automação está configurada via `Jenkinsfile` com as seguintes etapas:

1.  **Build & Test**:
    - Constrói a imagem de teste.
    - Executa `composer install` e `npm install`.
    - Executa `php artisan test`.
2.  **Build Production Image**:
    - Constrói a imagem final otimizada (`Dockerfile.prod`).
3.  **Deploy**:
    - Reinicia o container no servidor de produção (`server1.fabriciolr.online`).
    - Exponibiliza a aplicação na porta `8000`.

---

## 🛠️ 8. Guia de Comandos

### Desenvolvimento & Teste (Local)

**Usando Docker:**
```bash
# Iniciar ambiente de desenvolvimento
docker-compose build
docker-compose up

# Executar testes
./vendor/bin/pest
# ou via Docker
docker build -f Dockerfile.test -t laravel-test .
docker run laravel-test php artisan test
```

**Sem Docker (Manual):**
```bash
composer install
npm install
php artisan key:generate
php artisan migrate
php artisan serve
npm run dev
```

### Produção

O deploy é automatizado, mas os comandos manuais dentro do container de produção seriam:

```bash
# Dentro do ambiente de produção
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
# Os assets já vêm compilados na imagem prod
```

---

## 📦 9. Dependências Cruciais

- `inertiajs/inertia-laravel`: Ponte entre Laravel e React.
- `laravel/fortify`: Motor de autenticação (sem UI).
- `laravel/wayfinder`: Gerenciamento avançado de rotas.
- `pestphp/pest`: Framework de testes focado em desenvolvedor.
