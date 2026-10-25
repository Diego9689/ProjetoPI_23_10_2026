# Backlog do Projeto

## 1. Objetivo

Documentar problemas identificados, correções implementadas, testes realizados e pendências técnicas, preservando o histórico de evolução do projeto KomunicaTec.

## 2. Resumo da execução

- Data da última auditoria: 2025-05-18
- Versão ou commit analisado: HEAD
- Total de problemas identificados: 8
- Total de problemas corrigidos: 8
- Total de problemas validados: 8
- Total de problemas pendentes: 0

## 3. Problemas e correções

### BUG-001 — Redirecionamento de erro de login sem chamada da função header()

- Data de identificação: 2025-05-18
- Status: VALIDADO
- Gravidade: Crítica
- Categoria: Lógica / Funcionamento
- Arquivo afetado: `controller/controlar_usuario.php`
- Descrição: Ao tentar redirecionar em caso de erro de login, a instrução estava formatada como uma string literal `("Location: ../view/login.php?msg=erro");` sem a função `header()`.
- Comportamento anterior: O script não redirecionava o usuário para a tela de erro e prosseguia silenciosamente.
- Comportamento esperado: O usuário deve ser redirecionado via `header("Location: ../view/login.php?msg=erro");`.
- Evidências: Linha em `controlar_usuario.php`: `("Location: ../view/login.php?msg=erro");`
- Causa raiz: Erro de sintaxe/omissão do nome da função `header`.
- Correção planejada: Adicionar a chamada de função `header()`.
- Arquivos alterados: `controller/controlar_usuario.php`
- Correção aplicada: Adicionada a função `header("Location: ../view/login.php?msg=erro"); exit;`.
- Testes executados: TEST-005 (Envio de credenciais incorretas e validação da resposta HTTP 302 com redirecionamento para `login.php?msg=erro`).
- Resultado dos testes: APROVADO
- Riscos remanescentes: Nenhum.
- Data de conclusão: 2025-05-18

### BUG-002 — Incompatibilidade no contrato de sessão entre controller e painéis/verificar_login.php

- Data de identificação: 2025-05-18
- Status: VALIDADO
- Gravidade: Crítica
- Categoria: Integração / Autenticação
- Arquivo afetado: `controller/controlar_usuario.php`, `view/verificar_login.php`
- Descrição: O arquivo `verificar_login.php` exige que a sessão contenha `$_SESSION['usuario_id']`, `$_SESSION['usuario_nome']` e `$_SESSION['perfil']`. O controller gravava apenas `usuario_ativo`, `id_cpf` e `nivel_acesso`, fazendo com que usuários autenticados fossem ejetados dos painéis.
- Comportamento anterior: Usuários autenticados que acessavam os painéis eram redirecionados de volta para `login.php?msg=erro` por falha no `exigir_perfil()`.
- Comportamento esperado: A sessão deve registrar corretamente `usuario_id`, `usuario_nome`, `perfil` ('admin', 'comunicador', 'discente_egresso'), `id_cpf`, `nivel_acesso` e `usuario_ativo`.
- Evidências: `verificar_login.php` lê `$_SESSION['usuario_id']` e `$_SESSION['perfil']`, enquanto `controlar_usuario.php` só definia `$_SESSION['usuario_ativo']`, `$_SESSION['id_cpf']` e `$_SESSION['nivel_acesso']`.
- Causa raiz: Desalinhamento na especificação das chaves de sessão entre a camada de controle e a camada de visualização.
- Correção planejada: Padronizar o preenchimento de todas as chaves exigidas na sessão durante o login e mapear `nivel_acesso` ('administrador' -> 'admin').
- Arquivos alterados: `controller/controlar_usuario.php`
- Correção aplicada: Preenchimento das variáveis de sessão `usuario_id`, `usuario_nome`, `perfil`, `id_cpf`, `nivel_acesso` e `usuario_ativo` de forma alinhada.
- Testes executados: TEST-002, TEST-003, TEST-004 e testes HTTP com cookies em `painel_admin.php`, `painel_comunicador.php` e `painel_discente.php`.
- Resultado dos testes: APROVADO
- Riscos remanescentes: Nenhum.
- Data de conclusão: 2025-05-18

### BUG-003 — Roteamento e autenticação incompleta de perfis no controlar_usuario.php

- Data de identificação: 2025-05-18
- Status: VALIDADO
- Gravidade: Crítica
- Categoria: Lógica / Integração
- Arquivo afetado: `controller/controlar_usuario.php`
- Descrição: A ação `entrar` do `controlar_usuario.php` apenas processava requisições onde `$_POST["perfil"] == "comunicador"`, ignorando solicitações para o perfil `discente_egresso` e o redirecionamento adequado por perfil.
- Comportamento anterior: Tentativas de login para discentes/egressos ou redirecionamentos após login falhavam.
- Comportamento esperado: O controller deve autenticar o perfil `comunicador`/`administrador` ou `discente_egresso` e redirecionar para o painel correto (`painel_admin.php`, `painel_comunicador.php`, `painel_discente.php`).
- Evidências: Bloco `if ($_POST["perfil"] == "comunicador")` sem bloco `else/elseif` para `discente_egresso`.
- Causa raiz: Implementação do controlador deixada incompleta com trechos de código em teste comentados.
- Correção planejada: Tratar a autenticação de discente/egresso via `DiscenteEgresso::loginDiscente()` e unificar o roteamento por perfil.
- Arquivos alterados: `controller/controlar_usuario.php`
- Correção aplicada: Implementada a verificação para `discente_egresso` via `loginDiscente` e roteamento por perfil ('admin' -> `painel_admin.php`, 'comunicador' -> `painel_comunicador.php`, 'discente_egresso' -> `painel_discente.php`).
- Testes executados: TEST-002, TEST-003, TEST-004, TEST-004b.
- Resultado dos testes: APROVADO
- Riscos remanescentes: Nenhum.
- Data de conclusão: 2025-05-18

### BUG-004 — Incompatibilidade da Model DiscenteEgresso com a estrutura do banco de dados

- Data de identificação: 2025-05-18
- Status: VALIDADO
- Gravidade: Crítica
- Categoria: Banco de dados / Integração
- Arquivo afetado: `model/model_discente_egresso.php`
- Descrição: `DiscenteEgresso::loginDiscente()` executava uma consulta na tabela `discente_egresso` buscando colunas `id, nome, ra, senha`. Porém, o schema do banco (`komunicatec.sql`) utiliza as tabelas relacioandas `usuario` e `perfil_discente_egresso`.
- Comportamento anterior: Execução de login de discente causava erro de tabela inexistente no MySQL (`Table 'komunicatec.discente_egresso' doesn't exist`).
- Comportamento esperado: A consulta do discente deve associar `usuario` e `perfil_discente_egresso` via INNER JOIN e verificar a senha com `password_verify`.
- Evidências: Consulta SQL `FROM discente_egresso` em `model_discente_egresso.php`.
- Causa raiz: Desalinhamento entre o código da model e o schema `komunicatec.sql`.
- Correção planejada: Ajustar a consulta de `loginDiscente` na model para consultar a tabela `perfil_discente_egresso` com `usuario` via JOIN por RA/e-mail e validar a senha com `password_verify`.
- Arquivos alterados: `model/model_discente_egresso.php`
- Correção aplicada: Atualizada a instrução SQL para integrar `usuario` e `perfil_discente_egresso`.
- Testes executados: TEST-004 e TEST-004b.
- Resultado dos testes: APROVADO
- Riscos remanescentes: Nenhum.
- Data de conclusão: 2025-05-18

### BUG-005 — Execução de múltiplas instruções SQL em Publicacao::apagar()

- Data de identificação: 2025-05-18
- Status: VALIDADO
- Gravidade: Crítica
- Categoria: Banco de dados / Integração
- Arquivo afetado: `model/model_publicacao.php`
- Descrição: O método `apagar()` combinava duas instruções `DELETE` separadas por ponto e vírgula em uma única chamada `prepare()`.
- Comportamento anterior: Em instâncias PDO sem `ATTR_EMULATE_PREPARES = true`, o driver PDO lança exceção ao preparar múltiplas queries SQL em uma única chamada.
- Comportamento esperado: A exclusão deve executar duas consultas preparadas separadas.
- Evidências: `exclusao_SQL = "DELETE FROM link_publicacao WHERE id_publicacao_fk = ?; DELETE FROM publicacao WHERE id_publicacao = ?"`
- Causa raiz: Tentativa de agrupamento de consultas em um único `prepare()`.
- Correção planejada: Separar a exclusão dos links e da publicação em duas queries sequenciais distintas.
- Arquivos alterados: `model/model_publicacao.php`
- Correção aplicada: Separação em dois chamados `prepare()` e `execute()` independentes.
- Testes executados: TEST-009 (Criação, verificação e exclusão de publicação com link associado).
- Resultado dos testes: APROVADO
- Riscos remanescentes: Nenhum.
- Data de conclusão: 2025-05-18

### BUG-006 — Inconsistência de fuso horário global da aplicação

- Data de identificação: 2025-05-18
- Status: VALIDADO
- Gravidade: Média
- Categoria: Lógica
- Arquivo afetado: `model/conexao.php`, `model/model_publicacao.php`
- Descrição: O fuso horário `date_default_timezone_set` estava definido de forma isolada dentro de `model_publicacao.php` com alias antigo (`Brazil/East`).
- Comportamento anterior: Operações com data e hora usavam fuso incorreto ou fuso padrão do servidor caso `model_publicacao.php` não fosse carregado primeiro.
- Comportamento esperado: Fuso horário padronizado (`America/Sao_Paulo`) na inicialização da aplicação/conexão.
- Evidências: Chamada `date_default_timezone_set('Brazil/East')` isolada.
- Causa raiz: Configuração de timezone não centralizada e alias depreciado.
- Correção planejada: Centralizar a definição do fuso horário em `conexao.php` e atualizar para `America/Sao_Paulo`.
- Arquivos alterados: `model/conexao.php`, `model/model_publicacao.php`
- Correção aplicada: Centralizado `date_default_timezone_set('America/Sao_Paulo')` em `conexao.php`.
- Testes executados: TEST-001b.
- Resultado dos testes: APROVADO
- Riscos remanescentes: Nenhum.
- Data de conclusão: 2025-05-18

### BUG-007 — Aceitação de atributos inexistentes no método mágico __set das Models

- Data de identificação: 2025-05-18
- Status: VALIDADO
- Gravidade: Média
- Categoria: Lógica / Qualidade
- Arquivo afetado: `model/model_usuario.php`, `model/model_publicacao.php`, `model/model_categoria.php`, `model/model_comunicador_administrador.php`, `model/model_link_publicacao.php`, `model/model_discente_egresso.php`
- Descrição: O método mágico `__set($atributo, $valor)` em variadas classes atribuía dinamicamente qualquer propriedade recebida sem verificar se o atributo existia na classe.
- Comportamento anterior: Atribuições incorretas criavam propriedades dinâmicas silenciosamente.
- Comportamento esperado: O `__set` deve atribuir o valor apenas se a propriedade existir na classe (`property_exists`).
- Evidências: `$this->$atributo = $valor;` sem checagem previa.
- Causa raiz: Ausência de validação em métodos mágicos.
- Correção planejada: Adicionar verificação `if (property_exists($this, $atributo))` no `__set`.
- Arquivos alterados: Todas as classes de model.
- Correção aplicada: Adicionada a checagem `if (property_exists($this, $atributo))` em todos os métodos `__set`.
- Testes executados: TEST-006.
- Resultado dos testes: APROVADO
- Riscos remanescentes: Nenhum.
- Data de conclusão: 2025-05-18

### BUG-008 — Formulário de cadastro desconectado e sem envio de parâmetros POST

- Data de identificação: 2025-05-18
- Status: VALIDADO
- Gravidade: Alta
- Categoria: Funcionamento / Integração
- Arquivo afetado: `view/register.html`, `controller/controlar_usuario.php`, `model/model_usuario.php`
- Descrição: A página `register.html` continha um formulário sem `method="POST"`, sem `action` para o controlador e sem atributos `name` nos campos de entrada. O controlador também não possuía a ação `cadastrar`.
- Comportamento anterior: O botão "Cadastrar" recarregava a página sem submeter dados ao backend.
- Comportamento esperado: O formulário envia dados via POST para `controlar_usuario.php?acao=cadastrar` e cadastra o usuário com senha criptografada nas tabelas `usuario` e perfil correspondente.
- Evidências: `<form action="">` sem `method` e sem `name` nos inputs em `register.html`.
- Causa raiz: Formulário de cadastro estático e sem integração backend.
- Correção planejada: Adicionar atributos `name`, configurar `method="POST"` e `action`, implementar método `cadastrar()` nas models e tratar no controlador.
- Arquivos alterados: `view/register.html`, `controller/controlar_usuario.php`, `model/model_usuario.php`, `model/model_comunicador_administrador.php`, `model/model_discente_egresso.php`.
- Correção aplicada: Formulário e fluxo de cadastro backend implementados e conectados.
- Testes executados: TEST-005 (Cadastro e login de novo discente/comunicador) e submissão HTTP POST.
- Resultado dos testes: APROVADO
- Riscos remanescentes: Nenhum.
- Data de conclusão: 2025-05-18

## 4. Testes de regressão

### TEST-001 — Validação de Conexão PDO e Timezone Global
- Data: 2025-05-18
- Funcionalidade testada: Instanciação da classe Conexao e fuso horário
- Procedimento: Executar `Conexao::Conectar()` e verificar `date_default_timezone_get()`
- Resultado esperado: Instância de PDO ativa e fuso 'America/Sao_Paulo'
- Resultado obtido: Instância de PDO ativa e fuso 'America/Sao_Paulo'
- Status: Aprovado
- Observações: Nenhuma.

### TEST-002 — Autenticação de Administrador e Roteamento para Painel Admin
- Data: 2025-05-18
- Funcionalidade testada: Login de administrador (`silvia@fatec.sp.gov.br`)
- Procedimento: Requisição POST para `controlar_usuario.php?acao=entrar`
- Resultado esperado: Redirecionamento para `painel_admin.php` com sessão completa (`usuario_id`, `usuario_nome`, `perfil = admin`, `id_cpf`)
- Resultado obtido: Redirecionamento para `painel_admin.php` com renderização completa do painel do administrador
- Status: Aprovado
- Observações: Nenhuma.

### TEST-003 — Autenticação de Comunicador e Roteamento para Painel Comunicador
- Data: 2025-05-18
- Funcionalidade testada: Login de comunicador (`paulo@fatec.sp.gov.br`)
- Procedimento: Requisição POST para `controlar_usuario.php?acao=entrar`
- Resultado esperado: Redirecionamento para `painel_comunicador.php` com perfil `comunicador`
- Resultado obtido: Redirecionamento para `painel_comunicador.php` com renderização completa do painel do comunicador
- Status: Aprovado
- Observações: Nenhuma.

### TEST-004 — Autenticação de Discente/Egresso e Roteamento para Painel Discente
- Data: 2025-05-18
- Funcionalidade testada: Login de discente por RA (`123456`) e e-mail (`lucas@fatec.sp.gov.br`)
- Procedimento: Requisição POST com `perfil=discente_egresso`
- Resultado esperado: Redirecionamento para `painel_discente.php` com perfil `discente_egresso`
- Resultado obtido: Redirecionamento para `painel_discente.php` com renderização do painel
- Status: Aprovado
- Observações: Nenhuma.

### TEST-005 — Cadastro e Login de Novo Usuário
- Data: 2025-05-18
- Funcionalidade testada: Inserção no banco via `controlar_usuario.php?acao=cadastrar` e posterior autenticação
- Procedimento: Submeter novo cadastro via POST e efetuar login com o novo usuário criado
- Resultado esperado: Usuário persistido no banco com senha criptografada e login efetuado com sucesso
- Resultado obtido: Cadastro e login realizados e validados via HTTP
- Status: Aprovado
- Observações: Nenhuma.

### TEST-006 — Proteção contra Acesso Não Autorizado aos Painéis
- Data: 2025-05-18
- Funcionalidade testada: `verificar_login.php` e `exigir_perfil()`
- Procedimento: Acessar `painel_admin.php` diretamente via HTTP sem cookie de sessão
- Resultado esperado: Resposta HTTP `302` redirecionando para `login.php?msg=erro`
- Resultado obtido: Resposta HTTP `302` com `Location: login.php?msg=erro`
- Status: Aprovado
- Observações: Nenhuma.

### TEST-007 — Ciclo de Vida Completo de Publicações (Criar, Editar, Excluir)
- Data: 2025-05-18
- Funcionalidade testada: `Publicacao::publicar()`, `Link_Publicacao::criar_link()`, `Publicacao::atualizar()`, `Publicacao::apagar()`
- Procedimento: Criar publicação com link, atualizar dados e excluir
- Resultado esperado: Operações executadas sem erro de SQL e estado do banco de dados consistente
- Resultado obtido: Publicação criada, editada e apagada corretamente
- Status: Aprovado
- Observações: Nenhuma.

## 5. Pendências

| ID | Descrição | Prioridade | Motivo da pendência | Próxima ação |
|---|---|---|---|---|
| N/A | Nenhuma pendência no momento | - | - | - |

## 6. Histórico de alterações do backlog

### 2025-05-18

- Problemas identificados: BUG-001 a BUG-008
- Correções realizadas: BUG-001 a BUG-008 corrigidos e validados por suíte de testes de regressão automatizada e testes HTTP reais
- Testes executados: TEST-001 a TEST-007 executados e aprovados
- Registros atualizados: Status de BUG-001 a BUG-008 alterado para VALIDADO
- Pendências: Nenhuma
