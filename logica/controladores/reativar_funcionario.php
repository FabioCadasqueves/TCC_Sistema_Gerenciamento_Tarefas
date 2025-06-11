<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../../conexao/conexao.php';

// Verifica se o usuário está autenticado e é admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header('Location: ../../telas/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $idFuncionario = intval($_POST['id']);

    $sql = "UPDATE usuarios SET ativo = 1 WHERE id = ? AND admin_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $idFuncionario, $_SESSION['admin_id']);
    $stmt->execute();

    // Redireciona de volta para a tela de funcionários
    header('Location: ../../telas/funcionarios.php?reativacao=sucesso');
    exit;
} else {
    // Redirecionamento de segurança
    header('Location: ../../telas/funcionarios.php');
    exit;
}
