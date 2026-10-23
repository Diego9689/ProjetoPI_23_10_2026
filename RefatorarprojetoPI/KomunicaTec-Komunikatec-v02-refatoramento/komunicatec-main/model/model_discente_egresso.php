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
        $this->$atributo = $valor; // Atribui característica de classe com o valor recebido
    }

    // Construtor de conexão
    public function __construct() // Método construtor: Executa sempre que um objeto de classe for instanciado
    {
        $conectar = new Conexao(); // Objeto de conexão ao BD
        $this->con = $conectar->Conectar(); // Executa método de conexão ao BD
    }



    public function loginDiscente($ra, $senha) // Verifica credenciais de discente/egresso pelo RA
    {
        $consulta_SQL = "SELECT id, nome, ra, senha
                           FROM discente_egresso
                          WHERE ra = :ra
                          LIMIT 1"; // Comando SQL de seleção por RA
        $executar = $this->con->prepare($consulta_SQL); // Prepara o comando de seleção e o armazena
        $executar->bindParam(':ra', $ra); // Vincula o parâmetro RA ao comando preparado
        $executar->execute(); // Executa o comando com o parâmetro vinculado

        $discente = $executar->fetch(PDO::FETCH_ASSOC); // Armazena a primeira linha do resultado da consulta

        if ($discente && password_verify($senha, $discente['senha'])) // Verifica se o discente existe e se a senha confere
        {
            return $discente; // Retorna os dados do discente autenticado
        }

        // Para testes com senha em texto puro:
        // if ($discente && $senha == $discente['senha']) return $discente;

        return false; // Retorna falso caso as credenciais não confiram
    }
}

?>