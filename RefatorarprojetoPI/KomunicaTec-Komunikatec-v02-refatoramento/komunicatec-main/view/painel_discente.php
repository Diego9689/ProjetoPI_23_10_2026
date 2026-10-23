<?php
require_once 'verificar_login.php';
exigir_perfil('discente_egresso');
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
    <title>Painel do Discente | KomunikaTec</title>
</head>
<body class="perfil-discente">

    <?php include_once 'cabecario.php'; ?>

    <main class="painel-main">
        <section class="painel-container">

            <header class="painel-topo">
                <div>
                    <h1 class="painel-titulo">Painel do Discente / Egresso</h1>
                    <p class="painel-subtitulo">
                        Olá, <strong><?= htmlspecialchars(nome_usuario()) ?></strong> — confira as novidades!
                    </p>
                </div>
                <a class="painel-sair" href="../controller/controlar_usuario.php?acao=sair">Sair</a>
            </header>

            <div class="painel-grid">
                <article class="painel-card">
                    <h2>Comunicados</h2>
                    <p>Veja as últimas publicações da coordenação e dos comunicadores.</p>
                    <a class="painel-card-link" href="#">Ver todos →</a>
                </article>

                <article class="painel-card">
                    <h2>Notificações</h2>
                    <p>Acompanhe avisos importantes e prazos acadêmicos.</p>
                    <a class="painel-card-link" href="#">Abrir →</a>
                </article>

                <article class="painel-card">
                    <h2>Meu Perfil</h2>
                    <p>Atualizar dados pessoais, e-mail e senha.</p>
                    <a class="painel-card-link" href="#">Editar →</a>
                </article>

                <article class="painel-card">
                    <h2>Suporte</h2>
                    <p>Fale com a equipe responsável em caso de dúvidas.</p>
                    <a class="painel-card-link" href="#">Contatar →</a>
                </article>
            </div>

        </section>
    </main>

    <footer></footer>
</body>
</html>