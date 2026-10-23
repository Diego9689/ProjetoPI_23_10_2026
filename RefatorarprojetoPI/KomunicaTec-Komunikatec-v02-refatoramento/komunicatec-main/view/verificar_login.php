<?php
// verificar_login.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Garante que o usuário está logado e tem o perfil exigido.
 * Redireciona para o login caso contrário.
 */
function exigir_perfil(string $perfil_esperado): void
{
    if (!isset($_SESSION['usuario_id']) || ($_SESSION['perfil'] ?? '') !== $perfil_esperado) {
        header('Location: login.php?msg=erro');
        exit;
    }
}

/** Devolve o nome do usuário logado (fallback amigável). */
function nome_usuario(): string
{
    return $_SESSION['usuario_nome'] ?? 'Usuário';
}