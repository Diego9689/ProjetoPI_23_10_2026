<?php // Arquivo de modelagem da classe "Usuario"

require_once __DIR__ . "/conexao.php"; // Requer arquivo de conexão ao banco de dados

class Usuario // Declaração da classe
{
    // Características de classe: Atributos de "usuario"
    private $id_usuario;
    private $nome;
    private $sobrenome;
    private $email;
    private $senha;
    private $telefone;
    private $visibilidade_telefone;
    private $foto_de_perfil;

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

    // Cadastra um novo usuário na tabela "usuario"
    public function cadastrar()
    {
        $sql = "INSERT INTO usuario (nome, sobrenome, email, senha, telefone, visibilidade_telefone, foto_perfil)
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        $senhaHash = password_hash($this->senha, PASSWORD_DEFAULT);
        $visibilidade = $this->visibilidade_telefone ?? 0;
        $foto = $this->foto_de_perfil ?? null;

        $stmt = $this->con->prepare($sql);
        $stmt->execute([
            $this->nome,
            $this->sobrenome,
            $this->email,
            $senhaHash,
            $this->telefone,
            $visibilidade,
            $foto
        ]);

        $this->id_usuario = $this->con->lastInsertId();
        return $this->id_usuario;
    }

    // Regista aceite obrigatório dos termos
    public function registrarAceiteObrigatorio($id_usuario)
    {
        $sql = "INSERT INTO aceite_obrigatorio (aceite_termo_de_uso, data_aceite, aceite_politica_de_privacidade, id_usuario_fk)
                VALUES (1, NOW(), 1, ?)";
        $stmt = $this->con->prepare($sql);
        return $stmt->execute([$id_usuario]);
    }

    // Verificação de login da tabela "usuario" (para comunicadores / administradores)
    public function login_comunicador()
    {
        $verifica_SQL = "SELECT
            u.id_usuario, u.nome, u.sobrenome, u.email, u.senha, c.id_cpf, c.nivel_acesso, c.cargo
        FROM usuario u
        RIGHT JOIN
            perfil_administrador_comunicador c ON u.id_usuario = c.id_usuario_fk
        WHERE u.email = ? OR c.id_cpf = ?
        LIMIT 1";

        $valores_verificacao = array($this->email, $this->email);
        $executar = $this->con->prepare($verifica_SQL);
        $executar->execute($valores_verificacao);
        $valor = $executar->fetch(PDO::FETCH_ASSOC);

        if ($valor)
        {
            return $valor;
        }
        return false;
    }
}
?>