<?php // Arquivo controlador intermediário de recebimento de dados da view e envio de dados à model

// Requer arquivo das classes
require_once '../model/model_usuario.php';
require_once '../model/model_comunicador_administrador.php';
require_once '../model/model_discente_egresso.php';

// Criar objeto de classe
$usuario = new Usuario();
$comunicador_administrador = new Comunicador_Administrador();

// Verifica se há uma ação pendente
if (isset($_REQUEST["acao"]))
{
	// Verifica diferentes valores de "acao" via solicitação "GET"
	switch ($_REQUEST["acao"])
	{
		case 'entrar':
			if ($_SERVER["REQUEST_METHOD"] == "POST") // Verifica se há uma solicitação "POST"
			{
				if (session_status() === PHP_SESSION_NONE) session_start();

				$perfil = $_POST["perfil"] ?? 'comunicador';

				if ($perfil == "discente_egresso")
				{
					$ra = trim($_POST["ra"] ?? '');
					$senha = $_POST["senhaAluno"] ?? $_POST["senha"] ?? '';

					$discente_model = new DiscenteEgresso();
					$discente = $discente_model->loginDiscente($ra, $senha);

					if ($discente)
					{
						session_regenerate_id(true);
						$_SESSION['usuario_ativo'] = true;
						$_SESSION['usuario_id']    = $discente['id_usuario'];
						$_SESSION['usuario_nome']  = trim($discente['nome'] . ' ' . ($discente['sobrenome'] ?? ''));
						$_SESSION['perfil']        = 'discente_egresso';
						$_SESSION['id_ra']         = $discente['id_ra'];

						header("Location: ../view/painel_discente.php");
						exit;
					}
					else
					{
						header("Location: ../view/login.php?msg=erro");
						exit;
					}
				}
				else // "comunicador" ou "admin"
				{
					$usuario->email = trim($_POST["email"] ?? '');
					$senha_digitada = $_POST["senha"] ?? '';

					$verifica = $usuario->login_comunicador();

					if ($verifica && password_verify($senha_digitada, $verifica['senha']))
					{
						session_regenerate_id(true);
						$_SESSION['usuario_ativo'] = true;
						$_SESSION['usuario_id']    = $verifica['id_usuario'];
						$_SESSION['usuario_nome']  = trim($verifica['nome'] . ' ' . ($verifica['sobrenome'] ?? ''));
						$_SESSION['id_cpf']        = $verifica['id_cpf'];
						$_SESSION['nivel_acesso']  = $verifica['nivel_acesso'];

						if ($verifica['nivel_acesso'] == 'administrador')
						{
							$_SESSION['perfil'] = 'admin';
							header("Location: ../view/painel_admin.php");
						}
						else
						{
							$_SESSION['perfil'] = 'comunicador';
							header("Location: ../view/painel_comunicador.php");
						}
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

		case 'cadastrar':
			if ($_SERVER["REQUEST_METHOD"] == "POST")
			{
				$perfil = $_POST["perfil"] ?? 'comunicador';

				if ($perfil == "discente_egresso")
				{
					$ra = trim($_POST["ra"] ?? '');
					$nome = trim($_POST["nome"] ?? 'Discente');
					$email = trim($_POST["email"] ?? ($ra . '@fatec.sp.gov.br'));
					$senha = $_POST["senhaAluno"] ?? $_POST["senha"] ?? '';

					if (!empty($ra) && !empty($senha))
					{
						$usuario = new Usuario();
						$usuario->nome = $nome;
						$usuario->sobrenome = $_POST["sobrenome"] ?? '';
						$usuario->email = $email;
						$usuario->senha = $senha;

						$id_usuario = $usuario->cadastrar();

						$discente_model = new DiscenteEgresso();
						$discente_model->cadastrar($ra, $id_usuario);

						header("Location: ../view/login.php?msg=sucesso");
						exit;
					}
					else
					{
						header("Location: ../view/register.html?msg=erro");
						exit;
					}
				}
				else // comunicador ou admin
				{
					$email = trim($_POST["email"] ?? '');
					$senha = $_POST["senha"] ?? '';
					$cpf = trim($_POST["cpf"] ?? sprintf('%011d', rand(1, 99999999999)));
					$nome = trim($_POST["nome"] ?? 'Comunicador');

					if (!empty($email) && !empty($senha))
					{
						$usuario = new Usuario();
						$usuario->nome = $nome;
						$usuario->sobrenome = $_POST["sobrenome"] ?? '';
						$usuario->email = $email;
						$usuario->senha = $senha;

						$id_usuario = $usuario->cadastrar();

						$comunicador_model = new Comunicador_Administrador();
						$comunicador_model->id_cpf = $cpf;
						$comunicador_model->id_usuario_fk = $id_usuario;
						$comunicador_model->cargo = 'professor';
						$comunicador_model->nivel_de_acesso = ($perfil == 'admin') ? 'administrador' : 'comunicador';
						$comunicador_model->status = 'ativo';
						$comunicador_model->cadastrar();

						header("Location: ../view/login.php?msg=sucesso");
						exit;
					}
					else
					{
						header("Location: ../view/register.html?msg=erro");
						exit;
					}
				}
			}
			break;

		case 'sair':
			if (session_status() === PHP_SESSION_NONE)
			{
				session_start();
			}
			session_unset();
			session_destroy();
			header("Location: ../view/login.php");
			exit;
			break;
	}
}
elseif (isset($_REQUEST["comunicador"])) // Verifica se está buscando um comunicador específico
{
	$comunicador_administrador->id_cpf = $_REQUEST["comunicador"]; // Recebe o CPF do comunicador
	$comunicador = $comunicador_administrador->consultar_usuario(); // Recebe os valores de busca
}
?>