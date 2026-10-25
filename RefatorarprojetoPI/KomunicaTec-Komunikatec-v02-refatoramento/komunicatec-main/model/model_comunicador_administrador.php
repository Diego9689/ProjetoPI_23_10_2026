<?php // Arquivo de modelagem da classe "Comunicador_Administrador"

require_once __DIR__ . "/conexao.php"; // Requer arquivo de conexão ao banco de dados

class Comunicador_Administrador // Declaração da classe
{
	// Características de classe: Atributos de "perfil_comunicador_administrador"
	private $id_cpf;
	private $cargo;
	private $nivel_de_acesso;
	private $status;
	private $id_usuario_fk;

	private $con; // Objeto de acesso para conexão com BD

	// Métodos mágicos de classe
	public function __get($atributo)
	{
		return $this->$atributo ?? null;
	}

	public function __set($atributo, $valor)
	{
		$this->$atributo = $valor;
	}

	// Construtor de conexão
	public function __construct()
	{
		$conectar = new Conexao();
		$this->con = $conectar->Conectar();
	}

	public function cadastrar()
	{
		$sql = "INSERT INTO perfil_administrador_comunicador (id_cpf, cargo, nivel_acesso, status, id_usuario_fk)
				VALUES (?, ?, ?, ?, ?)";
		$executar = $this->con->prepare($sql);
		return $executar->execute([
			$this->id_cpf,
			$this->cargo ?? 'professor',
			$this->nivel_acesso ?? 'comunicador',
			$this->status ?? 'ativo',
			$this->id_usuario_fk
		]);
	}

	public function consultar_usuario()
	{
		$consulta_SQL = "SELECT
			a.id_cpf,
			u.nome,
			u.sobrenome,
			u.email,
			u.telefone,
			u.visibilidade_telefone,
			a.cargo,
			a.nivel_acesso,
			a.status,
			a.id_usuario_fk
		FROM
			perfil_administrador_comunicador a
        LEFT JOIN
            usuario u ON u.id_usuario = a.id_usuario_fk
		WHERE
			a.id_cpf = ?
		LIMIT 1";

		$valores_consulta = array($this->id_cpf);

		$executar = $this->con->prepare($consulta_SQL);
		$executar->execute($valores_consulta);

		$valores = $executar->fetchAll();
		if ($valores)
		{
			foreach ($valores as $valor)
			{
				$consulta['id_cpf']					= $valor['id_cpf'];
				$consulta["nome"]					= $valor["nome"];
				$consulta["sobrenome"]				= $valor["sobrenome"];
				$consulta["email"]					= $valor["email"];
				$consulta["telefone"]				= $valor["telefone"];
				$consulta["visibilidade_telefone"]	= $valor["visibilidade_telefone"];
				$consulta['cargo']					= $valor['cargo'];
				$consulta['nivel_acesso']			= $valor['nivel_acesso'];
				$consulta['status']					= $valor['status'];
				$consulta['id_usuario_fk']			= $valor['id_usuario_fk'];
			}
			return $consulta;
		}
		return false;
	}
}
?>