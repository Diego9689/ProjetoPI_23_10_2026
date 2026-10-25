<?php // Arquivo de modelagem de classe "DiscenteEgresso"

require_once __DIR__ . "/conexao.php"; // Requer arquivo de conexão ao banco de dados

class DiscenteEgresso // Declaração da classe
{
    private $id_ra;
    private $curso;
    private $semestre;
    private $situacao;
    private $turno;
    private $descricao;
    private $id_usuario_fk;

    private $con; // Objeto de acesso para conexão com BD

    public function __get($atributo)
    {
        return $this->$atributo ?? null;
    }

    public function __set($atributo, $valor)
    {
        $this->$atributo = $valor;
    }

    public function __construct()
    {
        $conectar = new Conexao();
        $this->con = $conectar->Conectar();
    }

    public function cadastrar()
    {
        $sql = "INSERT INTO perfil_discente_egresso (id_ra, curso, semestre, situacao, turno, descricao, id_usuario_fk)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $executar = $this->con->prepare($sql);
        return $executar->execute([
            $this->id_ra,
            $this->curso ?? 'DSM',
            $this->semestre ?? '1',
            $this->situacao ?? 'discente',
            $this->turno ?? 'noite',
            $this->descricao ?? '',
            $this->id_usuario_fk
        ]);
    }

    public function loginDiscente($ra, $senha)
    {
        $consulta_SQL = "SELECT p.id_ra, u.id_usuario, u.nome, u.sobrenome, u.email, u.senha
                           FROM perfil_discente_egresso p
                           JOIN usuario u ON u.id_usuario = p.id_usuario_fk
                          WHERE p.id_ra = ?
                          LIMIT 1";
        $executar = $this->con->prepare($consulta_SQL);
        $executar->execute([$ra]);

        $discente = $executar->fetch(PDO::FETCH_ASSOC);

        if ($discente && password_verify($senha, $discente['senha']))
        {
            return $discente;
        }

        return false;
    }
}
?>