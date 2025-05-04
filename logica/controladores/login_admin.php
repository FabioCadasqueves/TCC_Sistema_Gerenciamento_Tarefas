<?php
session_start();
require_once '../../conexao/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];

    // Busca o admin pelo e-mail
    $stmt = $conn->prepare("SELECT id, nome, senha FROM admins WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($admin_id, $admin_nome, $senha_hash);
        $stmt->fetch();

        // Verifica a senha
        if (password_verify($senha, $senha_hash)) {
            $_SESSION['admin_id'] = $admin_id;
            $_SESSION['admin_nome'] = $admin_nome;
            header('Location: ../../telas/dashboard.php');
            exit;
        } else {
            // Senha incorreta
            header('Location: ../../telas/login.php?erro=senha');
            exit;
        }
    } else {
        // E-mail não encontrado
        header('Location: ../../telas/login.php?erro=email');
        exit;
    }
}
