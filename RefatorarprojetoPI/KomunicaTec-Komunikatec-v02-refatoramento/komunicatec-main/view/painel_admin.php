<?php
require_once 'verificar_login.php';
exigir_perfil('admin');
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="reset.css">
    <link rel="stylesheet" href="cabecario.css">
    <link rel="stylesheet" href="painel.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <title>Painel Administrador | KomunikaTec</title>
</head>
<body class="perfil-admin">

    <?php include_once 'cabecario.php'; ?>

    <main class="painel-main">
        <section class="painel-container">

            <header class="painel-topo">
                <div>
                    <h1 class="painel-titulo">Painel do Administrador</h1>
                    <p class="painel-subtitulo">
                        Bem-vindo(a), <strong><?= htmlspecialchars(nome_usuario()) ?></strong>
                    </p>
                </div>
                <a class="painel-sair" href="../controller/controlar_usuario.php?acao=sair">Sair</a>
            </header>

            <div class="painel-grid">
                <article class="painel-card">
                    <h2>Gerenciar Usuários</h2>
                    <p>Cadastrar, editar e remover administradores, comunicadores e discentes.</p>
                    <a class="painel-card-link" href="#">Acessar →</a>
                </article>

                <article class="painel-card">
                    <h2>Gerenciar Comunicados</h2>
                    <p>Revisar e moderar todos os comunicados publicados no sistema.</p>
                    <a class="painel-card-link" href="#">Acessar →</a>
                </article>

                <article class="painel-card">
                    <h2>Relatórios</h2>
                    <p>Visualizar estatísticas de acesso, publicações e engajamento.</p>
                    <a class="painel-card-link" href="#">Acessar →</a>
                </article>

                <article class="painel-card">
                    <h2>Configurações</h2>
                    <p>Ajustes gerais do sistema, permissões e parâmetros.</p>
                    <a class="painel-card-link" href="#">Acessar →</a>
                </article>
            </div>

        </section>
    </main>

    <footer></footer>
</body>
</html>