<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../../conexao/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $funcao = $_POST['funcao'];
    $equipe_id = $_POST['equipe'];
    $admin_id = $_SESSION['admin_id'];

    // Gerar PIN aleatório de 6 dígitos
    $pin = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

    // Inserir funcionário
    $stmt = $conn->prepare("INSERT INTO usuarios (nome, funcao, pin, admin_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $nome, $funcao, $pin, $admin_id);

    if ($stmt->execute()) {
        $usuario_id = $stmt->insert_id;

        /*// Associar à equipe se não for "Nenhuma"
        if ($equipe_id != 0) {
            $stmt2 = $conn->prepare("INSERT INTO membros_equipes (usuario_id, equipe_id) VALUES (?, ?)");
            $stmt2->bind_param("ii", $usuario_id, $equipe_id);
            $stmt2->execute();
        }*/

        header("Location: ../../telas/funcionarios.php?cadastro=sucesso");
        exit;
    } else {
        echo "Erro ao cadastrar funcionário: " . $stmt->error;
    }
}
