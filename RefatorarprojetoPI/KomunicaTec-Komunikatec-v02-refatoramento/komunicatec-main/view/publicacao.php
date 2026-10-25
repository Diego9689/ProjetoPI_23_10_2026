<?php // Página de exibição de todas as publicações
require_once '../controller/controlar_publicacao.php'; // Requer arquivo controlador para exibição
$publicados = $publicacao->consultar_todos(); // Executa função de consulta, recebe dados de publicações
$categorias = $categoria->consultar();
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Publicações - KomunicaTec</title>
	<link rel="stylesheet" href="cabecario.css">
	<link rel="stylesheet" href="footer.css">
	<link rel="stylesheet" href="publi.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
	<style>
		.oculto { display: none !important; }
	</style>
</head>
<body>

	<!-- Cabeçalho e navegação principal -->
	<?php
	include_once 'cabecario.php'; // Inclui o código arquivo uma vez, evitando repetição ao recarregar a página
	?>

	<main>
		<!-- Barra de pesquisa -->
		<div class="barra-pesquisa">
			<input type="text" placeholder="pesquisar">
			<button type="button">
				<i class="fa-solid fa-magnifying-glass"></i>
			</button>
		</div>

		<!-- Filtro de categorias -->
		<div class="filter">
			<?php
			foreach ($categorias as $opcao)
			{
				echo "<label><input type='checkbox' class='filtro-checkbox' value='".htmlspecialchars($opcao['nome_categoria'])."'> ".htmlspecialchars($opcao['nome_categoria'])."</label>";
			}
			?>
		</div>

		<!-- Lista de publicações -->
		<section class="publicacoes">

			<?php if (empty($publicados)): // Verifica se não foi retornado nehuma publicação ?>

				<h1>Nenhuma informação encontrada</h1>

			<?php else: ?>

				<?php foreach($publicados as $publicado): // Para cada publicação ?>
				<div id="pub-<?= $publicado['id_publicacao'] ?>" class="card" categoria="<?= htmlspecialchars($publicado["nome_categoria"] ?? "Outro") ?>">
					<div class="cabecalho-cartao">
						<img src="imagens/user_icon.jpg" alt="Foto perfil" class="foto-cartao">
						<div>
							<h4><?= htmlspecialchars(($publicado["nome"] ?? "Usuário").' '.($publicado["sobrenome"] ?? "")) ?></h4>
							<p class="role"><?= htmlspecialchars(ucfirst($publicado["cargo"] ?? "Membro")) ?></p>
						</div>
						<button class="botao-ver" onclick="location.href='publicacao_usuario.php?comunicador=<?= htmlspecialchars($publicado['id_cpf_fk']) ?>'">
							Visualizar Perfil
						</button>
					</div>

					<div class="conteudo-cartao">
						<u><?= htmlspecialchars($publicado["nome_categoria"] ?? "Geral") ?></u>
						<h3><?= htmlspecialchars($publicado["titulo"]) ?></h3>
						<span class="date">Publicado em <?= date("d/m/Y H:i", strtotime($publicado["data_publicacao"])) ?></span>
						<p style="white-space: pre-line;"><?= htmlspecialchars($publicado["descricao"]) ?></p>
						<?php
						if (!empty($publicado["endereco_link"]))
						{
							echo '<a href="'.htmlspecialchars($publicado["endereco_link"]).'" class="link" target="_blank">'.htmlspecialchars($publicado["endereco_link"]).'</a>';
						}
						?>
						<span class="date">Válido até <?= date("d/m/Y", strtotime($publicado["data_expiracao"])) ?></span>
					</div>
				</div>
				<?php endforeach; ?>

            <?php endif; ?>

		</section>
	</main>

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

        const publicacoes = document.querySelectorAll('.card');
        publicacoes.forEach(pub => {
            idsPublicacoes.push(pub.id);
        });

        const inputPesquisa = document.querySelector('.barra-pesquisa input');
        if (inputPesquisa) {
            inputPesquisa.addEventListener('input', aplicarFiltro);
        }

        aplicarFiltro();
    }

    function aplicarFiltro() {
        const categoriasSelecionadas = [];
        const checkboxes = document.querySelectorAll('.filtro-checkbox:checked');
        checkboxes.forEach(checkbox => {
            categoriasSelecionadas.push(checkbox.value.trim());
        });

        const termoPesquisa = (document.querySelector('.barra-pesquisa input')?.value || '').toLowerCase().trim();
        const exibirTodosCat = categoriasSelecionadas.length === 0;

        idsPublicacoes.forEach(id => {
            const elemento = document.getElementById(id);
            if (!elemento) return;

            let categoriaCard = (elemento.getAttribute('categoria') || '').trim();
            if (categoriaCard === '') {
                categoriaCard = 'Outro';
            }

            const textoCard = elemento.innerText.toLowerCase();

            const atendeCategoria = exibirTodosCat || categoriasSelecionadas.includes(categoriaCard);
            const atendePesquisa = termoPesquisa === '' || textoCard.includes(termoPesquisa);

            if (atendeCategoria && atendePesquisa) {
                elemento.classList.remove('oculto');
            } else {
                elemento.classList.add('oculto');
            }
        });
    }

    document.addEventListener('DOMContentLoaded', inicializarFiltro);
	</script>
</body>

</html>