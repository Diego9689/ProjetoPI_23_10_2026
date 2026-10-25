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
    public function __get($atributo) // Função get mágico automaticamente recebe nome do atributo de classe
    {
        return $this->$atributo ?? null; // Retorna o valor do atributo de classe ou nulo se não encontrar
    }

    public function __set($atributo, $valor) // Função set mágico recebe automaticamente nome do atributo e o valor a aplicar
    {
        if (property_exists($this, $atributo))
        {
            $this->$atributo = $valor; // Atribui característica de classe com o valor recebido
        }
    }

    // Construtor de conexão
    public function __construct() // Método construtor: Executa sempre que um objeto de classe for instanciado
    {
        $conectar = new Conexao(); // Objeto de conexão ao BD
        $this->con = $conectar->Conectar(); // Executa método de conexão ao BD
    }



    // Verificação de login da tabela "usuario"
    public function login_comunicador() // Verifica dados recebidos com os dados cadastrados
    {
        // Comando SQL de busca
        $verifica_SQL = "SELECT
            u.id_usuario, u.nome, u.sobrenome, u.email, u.senha, c.id_cpf, c.nivel_acesso, c.cargo
        FROM usuario u
        INNER JOIN
            perfil_administrador_comunicador c ON u.id_usuario = c.id_usuario_fk
        WHERE u.email = ?
        LIMIT 1";
        // Valores de busca
        $valores_verificacao = array($this->email);
        $executar = $this->con->prepare($verifica_SQL); // Prepara o comando de busca e o armazena
        $executar->execute($valores_verificacao); // Executa o comando com os valores especificados
        $valor = $executar->fetch(); // Armazena uma linha de valores da consulta

        if ($valor) // Se existem valores na consulta
        {
            $verificacao['id_usuario']   = $valor['id_usuario']; // Armazena resultado da busca
            $verificacao['nome']         = $valor['nome'];
            $verificacao['sobrenome']    = $valor['sobrenome'];
            $verificacao['email']        = $valor['email'];
            $verificacao['senha']        = $valor['senha'];
            $verificacao['id_cpf']       = $valor['id_cpf'];
            $verificacao['nivel_acesso'] = $valor['nivel_acesso'];
            $verificacao['cargo']        = $valor['cargo'];

            return $verificacao; // Retorna o valor da busca
        }

        return false;
    }

    // Cadastrar novo usuário na tabela "usuario"
    public function cadastrar()
    {
        $sql = "INSERT INTO usuario (nome, sobrenome, email, senha, telefone, visibilidade_telefone)
                VALUES (?, ?, ?, ?, ?, ?)";
        $senha_hash = password_hash($this->senha, PASSWORD_DEFAULT);
        $valores = array(
            $this->nome ?? 'Usuário',
            $this->sobrenome ?? '',
            $this->email,
            $senha_hash,
            $this->telefone ?? null,
            $this->visibilidade_telefone ?? 0
        );
        $executar = $this->con->prepare($sql);
        $executar->execute($valores);
        return $this->con->lastInsertId();
    }
}

?>