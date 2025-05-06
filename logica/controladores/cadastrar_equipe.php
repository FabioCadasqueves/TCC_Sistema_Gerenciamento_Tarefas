<?php
session_start();
require_once '../../conexao/conexao.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../../telas/login.php');
    exit;
}

// Verifica se dados foram enviados via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomeEquipe = trim($_POST['nomeEquipe']);
    $membros = isset($_POST['membros']) ? $_POST['membros'] : [];
    $adminId = $_SESSION['admin_id'];

    // Verifica se o nome da equipe foi preenchido
    if (empty($nomeEquipe)) {
        die('O nome da equipe é obrigatório.');
    }

    // Insere a equipe
    $sqlEquipe = "INSERT INTO equipes (nome, admin_id, criado_em) VALUES (?, ?, NOW())";
    $stmtEquipe = $conn->prepare($sqlEquipe);
    $stmtEquipe->bind_param("si", $nomeEquipe, $adminId);

    if ($stmtEquipe->execute()) {
        $equipeId = $stmtEquipe->insert_id;

        // Insere os membros vinculados à equipe
        $sqlMembro = "INSERT INTO membros_equipes (equipe_id, usuario_id) VALUES (?, ?)";
        $stmtMembro = $conn->prepare($sqlMembro);

        foreach ($membros as $usuarioId) {
            $stmtMembro->bind_param("ii", $equipeId, $usuarioId);
            $stmtMembro->execute();
        }

        // Redireciona de volta para a tela de equipes
        header("Location: ../../telas/equipes.php");
        exit;
    } else {
        die('Erro ao criar a equipe: ' . $stmtEquipe->error);
    }
}
