# BACKLOG & Status do Projeto KomunicaTec

## Status Geral
Todas as tarefas identificadas no backlog foram corrigidas e devidamente testadas/validadas.

---

## 1. Módulo de Usuário & Autenticação

### 1.1 Cadastro (`register.html` / `cadastro.php` / `controlar_usuario.php` / `model_usuario.php`) - [CONCLUÍDO]
- Formulário dinâmico em `register.html` enviando via POST para `controlar_usuario.php?acao=cadastrar`.
- Métodos `cadastrar()` e `registrarAceiteObrigatorio()` implementados na model `Usuario`.
- Métodos `cadastrar()` implementados em `Comunicador_Administrador` e `DiscenteEgresso`.
- Criado arquivo de redirecionamento `cadastro.php` -> `register.html`.

### 1.2 Login & Sessão (`login.php` / `controlar_usuario.php`) - [CONCLUÍDO]
- Autenticação corrigida e unificada para perfis `comunicador` e `discente_egresso`.
- Redirecionamento pós-login e feedback visual de erro/sucesso.

### 1.3 Sessão & Logout (`verificar_login.php` / `card_perfil.php` / `controlar_usuario.php?acao=sair`) - [CONCLUÍDO]
- Fluxo de encerramento de sessão via `controlar_usuario.php?acao=sair`.
- Validações de acesso e perfil em `verificar_login.php`.

---

## 2. Módulo de Publicações (CRUD & Filtro)

### 2.1 Publicar e Editar (`form_publicacao.php` / `controlar_publicacao.php` / `model_publicacao.php`) - [CONCLUÍDO]
- Fluxo de criação e edição de publicações com título, descrição, validade, categoria e links externos.

### 2.2 Listagem & Filtros (`publicacao.php` / `publicacao_usuario.php`) - [CONCLUÍDO]
- Filtragem por categoria via checkboxes e filtro por busca de texto ao vivo (evento de busca em tempo real).

---

## 3. Navegação e Cabeçalho - [CONCLUÍDO]
- Links atualizados em `cabecario.php` para navegação segura entre páginas `.php` e `.html`.

---

## Registro de Testes e Validações
- [x] Sintaxe de todos os arquivos PHP (`find . -name "*.php" -exec php -l {} \;`).
- [x] Teste de Cadastro (Admin/Comunicador e Aluno/Ex-aluno).
- [x] Teste de Login (Admin, Comunicador, Aluno).
- [x] Teste de Logout.
- [x] Teste de Publicação (Criar, Editar, Excluir).
- [x] Teste de Filtros de Categoria e Busca.
