<?php // Arquivo de modelagem de classe "DiscenteEgresso"

require_once __DIR__ . "/conexao.php"; // Requer arquivo de conexão ao banco de dados

class DiscenteEgresso // Declaração da classe
{
    // Características de classe: Atributos de "discente_egresso"
    private $id_discente;
    private $nome_discente;
    private $ra_discente;
    private $senha_discente;

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



    public function loginDiscente($ra, $senha) // Verifica credenciais de discente/egresso pelo RA
    {
        $consulta_SQL = "SELECT u.id_usuario, u.nome, u.sobrenome, u.email, u.senha, p.id_ra, p.curso, p.situacao
                           FROM usuario u
                     INNER JOIN perfil_discente_egresso p ON u.id_usuario = p.id_usuario_fk
                          WHERE p.id_ra = :ra OR u.email = :ra
                          LIMIT 1"; // Comando SQL de seleção por RA ou e-mail
        $executar = $this->con->prepare($consulta_SQL); // Prepara o comando de seleção e o armazena
        $executar->bindParam(':ra', $ra); // Vincula o parâmetro ao comando preparado
        $executar->execute(); // Executa o comando com o parâmetro vinculado

        $discente = $executar->fetch(PDO::FETCH_ASSOC); // Armazena a primeira linha do resultado da consulta

        if ($discente && password_verify($senha, $discente['senha'])) // Verifica se o discente existe e se a senha confere
        {
            return $discente; // Retorna os dados do discente autenticado
        }

        return false; // Retorna falso caso as credenciais não confiram
    }

    public function cadastrar($id_ra, $id_usuario_fk, $curso = 'DSM', $semestre = '1', $situacao = 'discente', $turno = 'noite')
    {
        $sql = "INSERT INTO perfil_discente_egresso (id_ra, curso, semestre, situacao, turno, id_usuario_fk)
                VALUES (?, ?, ?, ?, ?, ?)";
        $valores = array(
            $id_ra,
            $curso,
            $semestre,
            $situacao,
            $turno,
            $id_usuario_fk
        );
        $executar = $this->con->prepare($sql);
        $executar->execute($valores);
    }
}

?>