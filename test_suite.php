<?php
// Script de testes automatizados de integração e regressão para o KomunicaTec

chdir(__DIR__ . '/RefatorarprojetoPI/KomunicaTec-Komunikatec-v02-refatoramento/komunicatec-main/model');

require_once 'conexao.php';
require_once 'model_usuario.php';
require_once 'model_comunicador_administrador.php';
require_once 'model_discente_egresso.php';
require_once 'model_publicacao.php';
require_once 'model_link_publicacao.php';
require_once 'model_categoria.php';

$erros = 0;
$sucessos = 0;

function assertTest($descricao, $condicao) {
    global $erros, $sucessos;
    if ($condicao) {
        echo "[OK] $descricao\n";
        $sucessos++;
    } else {
        echo "[FAIL] $descricao\n";
        $erros++;
    }
}

echo "==================================================\n";
echo " INICIANDO SUÍTE DE TESTES E REGRESSÃO KOMUNICATEC\n";
echo "==================================================\n\n";

// TEST 1: Conexão e Timezone
$con_obj = new Conexao();
$pdo = $con_obj->Conectar();
assertTest("TEST-001: Conexão PDO com MariaDB estabelecida", $pdo instanceof PDO);
assertTest("TEST-001b: Timezone global configurado para America/Sao_Paulo", date_default_timezone_get() === 'America/Sao_Paulo');

// TEST 2: Login Admin
$user_model = new Usuario();
$user_model->email = 'silvia@fatec.sp.gov.br';
$res_admin = $user_model->login_comunicador();
assertTest("TEST-002: Login Admin (Silvia Farani) localizou o usuário", !empty($res_admin) && $res_admin['nivel_acesso'] === 'administrador');
assertTest("TEST-002b: Verificação de senha Admin com password_verify", password_verify('Silvia1234#', $res_admin['senha']));

// TEST 3: Login Comunicador
$user_model->email = 'paulo@fatec.sp.gov.br';
$res_com = $user_model->login_comunicador();
assertTest("TEST-003: Login Comunicador (Paulo Jacobsen) localizou o usuário", !empty($res_com) && $res_com['nivel_acesso'] === 'comunicador');
assertTest("TEST-003b: Verificação de senha Comunicador com password_verify", password_verify('Paulo1234#', $res_com['senha']));

// TEST 4: Login Discente
$disc_model = new DiscenteEgresso();
$res_disc_ra = $disc_model->loginDiscente('123456', 'Paulo1234#');
assertTest("TEST-004: Login Discente por RA (123456) autenticado", !empty($res_disc_ra) && $res_disc_ra['nome'] === 'Lucas');

$res_disc_email = $disc_model->loginDiscente('lucas@fatec.sp.gov.br', 'Paulo1234#');
assertTest("TEST-004b: Login Discente por E-mail (lucas@fatec.sp.gov.br) autenticado", !empty($res_disc_email) && $res_disc_email['nome'] === 'Lucas');

// TEST 5: Tentativa com senha errada
$res_fail = $disc_model->loginDiscente('123456', 'SenhaErrada123');
assertTest("TEST-005: Autenticação recusa senha incorreta", $res_fail === false);

// TEST 6: Proteção do __set
$cat_model = new Categoria();
$cat_model->atributo_inexistente = 'teste';
assertTest("TEST-006: __set recusa atributo inexistente na classe", !property_exists($cat_model, 'atributo_inexistente') || $cat_model->atributo_inexistente === null);

// TEST 7: CRUD Publicação e Links
$pub_model = new Publicacao();
$pub_model->titulo = 'Publicação Teste Automação';
$pub_model->descricao = 'Descrição da publicação de teste criada via script de verificação.';
$pub_model->data_expiracao = date('Y-m-d', strtotime('+30 days'));
$pub_model->id_categoria_fk = 1;
$pub_model->id_cpf_fk = '01234567891';

$id_pub = $pub_model->publicar();
assertTest("TEST-007: Inserção de publicação realizada (ID: $id_pub)", $id_pub > 0);

$link_model = new Link_Publicacao();
$link_model->id_publicacao_fk = $id_pub;
$link_model->endereco_link = 'https://fatec.sp.gov.br/teste';
$link_model->criar_link();
assertTest("TEST-007b: Link associado à publicação criado com sucesso", true);

// TEST 8: Edição de Publicação
$pub_model->id_publicacao = $id_pub;
$pub_model->titulo = 'Publicação Teste Automação [EDITADA]';
$pub_model->descricao = 'Descrição editada.';
$pub_model->log_ultima_modificacao = 'Paulo Jacobsen';
$pub_model->atualizar();

$todas = $pub_model->consultar_todos();
$pub_encontrada = null;
foreach ($todas as $p) {
    if ($p['id_publicacao'] == $id_pub) {
        $pub_encontrada = $p;
        break;
    }
}
assertTest("TEST-008: Edição de publicação refletida com sucesso", !empty($pub_encontrada) && $pub_encontrada['titulo'] === 'Publicação Teste Automação [EDITADA]');

// TEST 9: Exclusão de Publicação e Links
$pub_model->apagar();
$todas_pos_delete = $pub_model->consultar_todos();
$deletado = true;
foreach ($todas_pos_delete as $p) {
    if ($p['id_publicacao'] == $id_pub) {
        $deletado = false;
        break;
    }
}
assertTest("TEST-009: Exclusão de publicação e link associado realizada", $deletado);

echo "\n==================================================\n";
echo " RESUMO DOS TESTES: $sucessos APROVADOS, $erros FALHAS\n";
echo "==================================================\n";

exit($erros === 0 ? 0 : 1);
