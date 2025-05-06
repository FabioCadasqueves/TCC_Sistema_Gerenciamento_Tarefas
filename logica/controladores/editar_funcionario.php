<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../../conexao/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $nome = trim($_POST['nome']);
    $funcao = $_POST['funcao'];

    $stmt = $conn->prepare("UPDATE usuarios SET nome = ?, funcao = ? WHERE id = ?");
    $stmt->bind_param("ssi", $nome, $funcao, $id);

    if ($stmt->execute()) {
        header("Location: ../../telas/funcionarios.php?edicao=sucesso");
        exit;
    } else {
        echo "Erro ao editar funcionário: " . $stmt->error;
    }
}
