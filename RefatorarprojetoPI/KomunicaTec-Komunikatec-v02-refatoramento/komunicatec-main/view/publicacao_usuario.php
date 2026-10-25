<?php // Página de exibição de publicações feitas por usuários
require_once '../controller/controlar_publicacao.php'; // Requer arquivo controlador para exibição de publicações

// Executa a função de consulta, recebe dados de publicações e categorias
$categorias = $categoria->consultar();
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Minhas Publicações - KomunicaTec</title>
	<link rel="stylesheet" href="cabecario.css">
	<link rel="stylesheet" href="footer.css">
	<link rel="stylesheet" href="my_publi.css">
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
	<style>
		.oculto { display: none !important; }
	</style>
</head>

<body>
	<!-- Cabeçalho e navegação principal -->
	<?php
	include_once 'cabecario.php'; // Inclui o código arquivo uma vez, evitando repetição ao recarregar a página
	?>

	<!-- Layout principal -->
	<main class="container">
		<!-- Card lateral -->
		<?php
		include_once 'card_perfil.php'; // Inclui o código arquivo uma vez, evitando repetição ao recarregar a página
		?>

		<!-- Publicações -->
		<section class="publicacoes">
			<h1>Minhas Publicações</h1>

			<!-- Filtro -->
			<div class="filtro">
				<?php
				foreach ($categorias as $opcao)
				{
					echo "<label><input type='checkbox' class='filtro-checkbox' value='".htmlspecialchars($opcao['nome_categoria'])."'> ".htmlspecialchars($opcao['nome_categoria'])."</label>";
				}
				?>
			</div>

			<?php
			if (isset($_SESSION["id_cpf"]) || isset($_SESSION["usuario_ativo"])):
				if (!isset($_GET["comunicador"]) || $_GET["comunicador"] == ($_SESSION["id_cpf"] ?? '')): // Verifica se o perfil pertence ao usuario atual
			?>
			<button class="editar" onclick="location.href='form_publicacao.php'">+ Adicionar</button>
			<?php endif; endif; ?>

			<?php if (empty($publicados)): // Verifica se não foi retornado nehuma publicação ?>

				<h1>Nenhuma informação encontrada</h1>

			<?php else: ?>

				<!-- Lista de publicações -->
				<?php foreach($publicados as $publicado): ?>
				<div id="pub-<?= $publicado['id_publicacao'] ?>" class="publicacao-card" categoria="<?= htmlspecialchars($publicado["nome_categoria"] ?? "Outro") ?>">
					<div class="cabecalho">
						<h2><?= htmlspecialchars($publicado["titulo"]) ?></h2>
						<span><?= htmlspecialchars($publicado["nome_categoria"] ?? "Geral") ?></span>
						<span align="right">
							Publicado em <?= date("d/m/Y H:i", strtotime($publicado["data_publicacao"])) ?>
							<br>Válido até <?= date("d/m/Y", strtotime($publicado["data_expiracao"])) ?>
						</span>
					</div>
					<p style="white-space: pre-line;"><?= htmlspecialchars($publicado["descricao"]) ?></p>
					<?php
					if (!empty($publicado["endereco_link"]))
					{
						echo '<a href="'.htmlspecialchars($publicado["endereco_link"]).'" class="link" target="_blank">'.htmlspecialchars($publicado["endereco_link"]).'</a>';
					}
					?>

					<?php
					if (session_status() === PHP_SESSION_NONE) session_start();
					if (isset($_SESSION["usuario_ativo"]) && $_SESSION["usuario_ativo"]):
						if (($comunicador["id_cpf"] ?? null) == ($_SESSION["id_cpf"] ?? null) || ($_SESSION["nivel_acesso"] ?? null) == "administrador"):
					?>
					<div class="acoes">
						<?php
						echo "<button class='editar'
							onclick='atualizar(".json_encode([
								"id_publicacao"			=> $publicado['id_publicacao'],
								"titulo"				=> $publicado['titulo'],
								"descricao"				=> $publicado['descricao'],
								"data_publicacao"		=> $publicado['data_publicacao'],
								"data_expiracao"		=> $publicado['data_expiracao'],
								"endereco_link"			=> $publicado['endereco_link'],
								"id_categoria"			=> $publicado['id_categoria'],
								"id_link_publicacao"	=> $publicado['id_link_publicacao'],
								"id_cpf_fk"				=> $publicado['id_cpf_fk']
							]).")'
						>
							Editar
						</button>";
						echo "<button class='excluir' onclick='apagar(".$publicado['id_publicacao'].")'>Excluir</button>";
						?>
					</div>
					<?php endif; endif; ?>
				</div>
				<?php endforeach; ?>

			<?php endif; ?>

		</section>
	</main>

	<!-- Rodapé -->
	<footer>
		<p>© 2025 - KomunicaTec</p>
	</footer>

	<script>
    let idsPublicacoes = [];

    function inicializarFiltro() {
        const checkboxes = document.querySelectorAll('.filtro-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', aplicarFiltro);
        });

        const publicacoes = document.querySelectorAll('.publicacao-card');
        publicacoes.forEach(pub => {
            idsPublicacoes.push(pub.id);
        });

        aplicarFiltro();
    }

    function aplicarFiltro() {
        const categoriasSelecionadas = [];
        const checkboxes = document.querySelectorAll('.filtro-checkbox:checked');
        checkboxes.forEach(checkbox => {
            categoriasSelecionadas.push(checkbox.value.trim());
        });

        const exibirTodos = categoriasSelecionadas.length === 0;

        idsPublicacoes.forEach(id => {
            const elemento = document.getElementById(id);
            if (!elemento) return;

            let categoriaCard = (elemento.getAttribute('categoria') || '').trim();
            if (categoriaCard === '') {
                categoriaCard = 'Outro';
            }

            if (exibirTodos || categoriasSelecionadas.includes(categoriaCard)) {
                elemento.classList.remove('oculto');
            } else {
                elemento.classList.add('oculto');
            }
        });
    }

    document.addEventListener('DOMContentLoaded', inicializarFiltro);

	function atualizar(dados)
	{
		const FORM_ATUALIZACAO = document.createElement('form');
		FORM_ATUALIZACAO.method = 'POST';
		FORM_ATUALIZACAO.action = 'form_publicacao.php?acao=atualizacao';
		FORM_ATUALIZACAO.style.display = 'none';

		for (const dado in dados)
		{
			if (dados.hasOwnProperty(dado))
			{
				const ENTRADA = document.createElement('input');
				ENTRADA.type = 'hidden';
				ENTRADA.name = dado;
				ENTRADA.value = dados[dado];
				FORM_ATUALIZACAO.appendChild(ENTRADA);
			}
		}

		document.body.appendChild(FORM_ATUALIZACAO);
		FORM_ATUALIZACAO.submit();
	}

	function apagar(id_publicacao)
	{
		if (confirm("Deseja realmente excluir esta publicação?"))
		{
			const FORM_EXCLUSAO = document.createElement('form');
			FORM_EXCLUSAO.method = 'POST';
			FORM_EXCLUSAO.action = '../controller/controlar_publicacao.php?acao=excluir';
			FORM_EXCLUSAO.style.display = 'none';

			const ID_EXCLUSAO = document.createElement('input');
			ID_EXCLUSAO.type = 'hidden';
			ID_EXCLUSAO.name = 'id_publicacao';
			ID_EXCLUSAO.value = id_publicacao;
			FORM_EXCLUSAO.appendChild(ID_EXCLUSAO);

			document.body.appendChild(FORM_EXCLUSAO);
			FORM_EXCLUSAO.submit();
		}
	}
	</script>
</body>
</html>