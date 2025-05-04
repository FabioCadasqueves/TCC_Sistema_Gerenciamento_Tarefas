<?php
session_start();
require_once '../../conexao/conexao.php';
require_once '../modelos/modelo_admin.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];
    $confirmar = $_POST['confirmar_senha'];

    if ($senha !== $confirmar) {
        die("As senhas não coincidem. <a href='javascript:history.back()'>Voltar</a>");
    }

    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    $resultado = cadastrarAdmin($conn, $nome, $email, $senha_hash);

    if ($resultado === true) {
        header('Location: ../../telas/login.php?cadastro=sucesso');
        exit;
    } else {
        echo $resultado;
    }
}
