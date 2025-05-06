<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../../conexao/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);

    $stmt = $conn->prepare("UPDATE usuarios SET ativo = 0 WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: ../../telas/funcionarios.php?exclusao=sucesso");
        exit;
    } else {
        echo "Erro ao inativar funcionário: " . $stmt->error;
    }
}
