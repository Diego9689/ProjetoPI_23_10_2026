<?php
require_once '../controller/controlar_usuario.php'; // Requer arquivo controlador para exibição do perfil

if (isset($comunicador_administrador->id_cpf) && !empty($comunicador_administrador->id_cpf)) {
    $publicacao->id_cpf_fk = $comunicador_administrador->id_cpf;
    $publicados = $publicacao->consultar_usuario();
} else {
    $publicados = array();
}

$comunicador_nome = isset($comunicador["nome"]) ? $comunicador["nome"].' '.($comunicador["sobrenome"] ?? '') : ($_SESSION["nome"] ?? "Usuário").' '.($_SESSION["sobrenome"] ?? "");
$comunicador_cargo = isset($comunicador["cargo"]) ? ucfirst($comunicador["cargo"]) : ucfirst($_SESSION["nivel_acesso"] ?? "Membro");
$comunicador_nivel = isset($comunicador["nivel_acesso"]) ? ucfirst($comunicador["nivel_acesso"]) : ucfirst($_SESSION["perfil"] ?? "");
$comunicador_email = $comunicador["email"] ?? $_SESSION["email"] ?? "";
$comunicador_telefone = $comunicador["telefone"] ?? "";
$comunicador_visivel = $comunicador["visibilidade_telefone"] ?? 0;
$comunicador_cpf = $comunicador["id_cpf"] ?? $_SESSION["id_cpf"] ?? null;
?>

<aside class="perfil-card">
	<img src="imagens/user_icon.jpg" alt="Foto de Perfil" class="foto-perfil">
	<h2><?= htmlspecialchars($comunicador_nome) ?></h2>
	<p class="cargo"><?= htmlspecialchars($comunicador_cargo) ?></p>
	<p class="subcargo"><?= htmlspecialchars($comunicador_nivel) ?></p>

	<?php if (!empty($comunicador_email)): ?>
		<p class="label">e-mail corporativo:</p>
		<p><?= htmlspecialchars($comunicador_email) ?></p>
	<?php endif; ?>

	<?php if ($comunicador_visivel == 1 && !empty($comunicador_telefone)): ?>
		<p class="label">Telefone:</p>
		<p><?= htmlspecialchars($comunicador_telefone) ?></p>
	<?php endif; ?>
	<br>
	<?php
    if (isset($_SESSION["id_cpf"]) || isset($_SESSION["usuario_ativo"])):
    if (
        (isset($_SESSION["id_cpf"]) && $comunicador_cpf == $_SESSION["id_cpf"]) ||
        (isset($_SESSION["nivel_acesso"]) && $_SESSION["nivel_acesso"] == "administrador")
    ):
    ?>
		<button class="botao-especial" onclick="location.href='edit_profile.html'">Editar Dados</button>
	<?php
    endif;
    if (isset($_SESSION["usuario_ativo"]) && $_SESSION["usuario_ativo"]):
    ?>
		<button class="botao" onclick="logout()">Encerrar Sessão</button>
	<?php endif; endif; ?>
</aside>

<script>
    function logout() // Encerrar sessão atual
	{
	if (confirm("Deseja realmente sair da conta?")) // Alerta de confirmação
		{
	    location.href='../controller/controlar_usuario.php?acao=sair'; // Envia ação de encerrar sessão
	}
	}
</script>