<?php
require_once 'verificar_login.php';
exigir_perfil('comunicador');
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
    <title>Painel do Comunicador | KomunikaTec</title>
</head>
<body class="perfil-comunicador">

    <?php include_once 'cabecario.php'; ?>

    <main class="painel-main">
        <section class="painel-container">

            <header class="painel-topo">
                <div>
                    <h1 class="painel-titulo">Painel do Comunicador</h1>
                    <p class="painel-subtitulo">
                        Olá, <strong><?= htmlspecialchars(nome_usuario()) ?></strong> — pronto para comunicar?
                    </p>
                </div>
                <a class="painel-sair" href="../controller/controlar_usuario.php?acao=sair">Sair</a>
            </header>

            <div class="painel-grid">
                <article class="painel-card">
                    <h2>Novo Comunicado</h2>
                    <p>Criar uma nova publicação para os discentes e egressos.</p>
                    <a class="painel-card-link" href="#">Criar →</a>
                </article>

                <article class="painel-card">
                    <h2>Meus Comunicados</h2>
                    <p>Editar, excluir ou visualizar os comunicados que você já publicou.</p>
                    <a class="painel-card-link" href="#">Abrir →</a>
                </article>

                <article class="painel-card">
                    <h2>Categorias</h2>
                    <p>Organizar categorias e tags dos comunicados.</p>
                    <a class="painel-card-link" href="#">Gerenciar →</a>
                </article>

                <article class="painel-card">
                    <h2>Meu Perfil</h2>
                    <p>Atualizar seus dados pessoais e senha de acesso.</p>
                    <a class="painel-card-link" href="#">Editar →</a>
                </article>
            </div>

        </section>
    </main>

    <footer></footer>
</body>
</html>