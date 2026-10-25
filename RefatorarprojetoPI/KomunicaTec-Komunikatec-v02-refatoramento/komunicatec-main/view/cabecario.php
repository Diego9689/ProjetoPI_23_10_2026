<header>
	<nav class="nav-bar">
		<figure class="imagem-icon">
			<img src="imagens/logo1.png" alt="Logo da KomunikaTec" class="img-logo">
		</figure>
		<div class="botao-navegacao">
			<button class="botao" onclick="location.href='index.php'">Home</button>
			<?php
			if (session_status() === PHP_SESSION_NONE) session_start();

			if (!isset($_SESSION['usuario_ativo']) || $_SESSION['usuario_ativo'] == FALSE) :
			?>
				<button class="botao-entrar" onclick="location.href='login.php'">Entrar</button>
				<button class="botao-especial" onclick="location.href='register.html'">Cadastrar</button>
			<?php else: ?>
				<button class="botao-visualizar" onclick="location.href='publicacao.php'">Publicações</button>
				<?php if (isset($_SESSION['nivel_acesso']) && $_SESSION['nivel_acesso'] == 'administrador'): ?>
					<button class="botao-visualizar" onclick="location.href='administracao.html'">Administração</button>
				<?php endif; ?>
				<?php
				echo '<img src="imagens/user_icon.jpg" alt="Acessar Perfil" class="nav-profile" title="Acessar Perfil"';
				if (!empty($_SESSION['id_cpf']))
				{
					echo " onclick=\"location.href='publicacao_usuario.php?comunicador=".htmlspecialchars($_SESSION["id_cpf"])."'\">";
				}
				else
				{
					echo " onclick=\"location.href='publicacao_usuario.php'\">";
				}
				?>
			<?php endif ?>
		</div>
		<div class="alternar-menu">☰</div><!-- Menu para versão mobile -->
	</nav>
</header>