<?php // Arquivo de conexão com o banco de dados

date_default_timezone_set('America/Sao_Paulo');

if (!class_exists("Conexao")) // Verifica se a conexão já foi realizada
{
    class Conexao
    {
        function Conectar()
        {
            try // Executa o bloco até encontrar um erro
            {
                // "caminho", "usuario", "senha"
                // Fatec: host=localhost:3307 senha="alunos"
                $servidor = "localhost";
                $usuario  = "root";
                $senha    = "";
                $banco    = "komunicatec";

                $conexao = new PDO
                (
                    "mysql:host=$servidor;dbname=$banco;charset=utf8mb4",
                    "$usuario",
                    "$senha",
                    array(
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => true
                    )
                );
                
                return $conexao;
            }
            catch(PDOException $erro) // Caso encontre um erro, armazena a mensagem em 'erro'
            {
                die("Erro na conexão: " . $erro->getMessage()); // Exibe mensagem do erro e encerra script
            }
        }
    }
}
// Execução da conexão (Ctrl + C)
/*
include_once __DIR__ . '/conexao.php'; // "__DIR__" permite referenciar pelo diretório que o arquivo foi chamado
$classe_con = new Conexao(); // Cria novo objeto de conexão
$con = $classe_con->Conectar(); // Executa método para conectar banco de dados
*/
?>