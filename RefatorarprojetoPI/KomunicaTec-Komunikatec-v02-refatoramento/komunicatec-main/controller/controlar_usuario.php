<?php // Arquivo controlador intermediário de recebimento de dados da view e envio de dados à model

require_once '../model/model_usuario.php';
require_once '../model/model_comunicador_administrador.php';
require_once '../model/model_discente_egresso.php';

$usuario = new Usuario();
$comunicador_administrador = new Comunicador_Administrador();
$discente = new DiscenteEgresso();

if (isset($_REQUEST["acao"]))
{
	switch ($_REQUEST["acao"])
	{
		case 'cadastrar':
			if ($_SERVER["REQUEST_METHOD"] == "POST")
			{
				$perfil = $_POST["perfil"] ?? 'comunicador';

				$usuario->nome = trim($_POST["nome"] ?? '');
				$usuario->sobrenome = trim($_POST["sobrenome"] ?? '');
				$usuario->email = trim($_POST["email"] ?? '');
				$usuario->senha = $_POST["senha"] ?? '';
				$usuario->telefone = trim($_POST["telefone"] ?? '');
				$usuario->visibilidade_telefone = isset($_POST["visibilidade_telefone"]) ? 1 : 0;

				$id_usuario = $usuario->cadastrar();
				if ($id_usuario)
				{
					$usuario->registrarAceiteObrigatorio($id_usuario);

					if ($perfil == 'comunicador')
					{
						$cpfLimpo = preg_replace('/[^0-9]/', '', $_POST["cpf"] ?? '');
						if (empty($cpfLimpo)) {
							$cpfLimpo = rand(10000000000, 99999999999);
						}
						$comunicador_administrador->id_cpf = $cpfLimpo;
						$comunicador_administrador->cargo = $_POST["cargo"] ?? 'professor';
						$comunicador_administrador->nivel_acesso = $_POST["nivel_acesso"] ?? 'comunicador';
						$comunicador_administrador->status = 'ativo';
						$comunicador_administrador->id_usuario_fk = $id_usuario;
						$comunicador_administrador->cadastrar();
					}
					else if ($perfil == 'discente_egresso')
					{
						$raLimpo = preg_replace('/[^0-9]/', '', $_POST["ra"] ?? '');
						$discente->id_ra = $raLimpo;
						$discente->curso = $_POST["curso"] ?? 'DSM';
						$discente->semestre = $_POST["semestre"] ?? '1';
						$discente->situacao = $_POST["situacao"] ?? 'discente';
						$discente->turno = $_POST["turno"] ?? 'noite';
						$discente->descricao = $_POST["descricao"] ?? '';
						$discente->id_usuario_fk = $id_usuario;
						$discente->cadastrar();
					}

					header("Location: ../view/login.php?msg=sucesso_cadastro");
					exit;
				}
				else
				{
					header("Location: ../view/register.html?msg=erro");
					exit;
				}
			}
			break;

		case 'entrar':
			if ($_SERVER["REQUEST_METHOD"] == "POST")
			{
				$perfil = $_POST["perfil"] ?? 'comunicador';

				if ($perfil == 'comunicador')
				{
					$usuario->email = trim($_POST["email"] ?? '');
					$usuario->senha = $_POST["senha"] ?? '';

					$verifica = $usuario->login_comunicador();

					if ($verifica && password_verify($usuario->senha, $verifica['senha']))
					{
						if (session_status() === PHP_SESSION_NONE) session_start();
						$_SESSION['usuario_ativo'] = TRUE;
						$_SESSION['id_usuario'] = $verifica['id_usuario'];
						$_SESSION['nome'] = $verifica['nome'];
						$_SESSION['sobrenome'] = $verifica['sobrenome'];
						$_SESSION['id_cpf'] = $verifica['id_cpf'];
						$_SESSION['nivel_acesso'] = $verifica['nivel_acesso'];
						$_SESSION['perfil'] = $verifica['nivel_acesso'];

						header("Location: ../view/publicacao.php");
						exit;
					}
					else
					{
						header("Location: ../view/login.php?msg=erro");
						exit;
					}
				}
				else if ($perfil == 'discente_egresso')
				{
					$ra = trim($_POST["ra"] ?? '');
					$senha = $_POST["senhaAluno"] ?? $_POST["senha"] ?? '';

					$verifica = $discente->loginDiscente($ra, $senha);

					if ($verifica)
					{
						if (session_status() === PHP_SESSION_NONE) session_start();
						$_SESSION['usuario_ativo'] = TRUE;
						$_SESSION['id_usuario'] = $verifica['id_usuario'];
						$_SESSION['nome'] = $verifica['nome'];
						$_SESSION['sobrenome'] = $verifica['sobrenome'];
						$_SESSION['id_ra'] = $verifica['id_ra'];
						$_SESSION['nivel_acesso'] = 'discente';
						$_SESSION['perfil'] = 'discente_egresso';

						header("Location: ../view/publicacao.php");
						exit;
					}
					else
					{
						header("Location: ../view/login.php?msg=erro");
						exit;
					}
				}
			}
			break;

		case 'sair':
			if (session_status() === PHP_SESSION_NONE) session_start();
			session_unset();
			session_destroy();
			header("Location: ../index.php");
			exit;

		default:
			header("Location: ../index.php");
			exit;
	}
}
elseif (isset($_REQUEST["comunicador"]))
{
	$comunicador_administrador->id_cpf = $_REQUEST["comunicador"];
	$comunicador = $comunicador_administrador->consultar_usuario();
}
?>