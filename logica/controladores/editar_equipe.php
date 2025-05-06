<?php
session_start();
require_once '../../conexao/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id_equipe']);
    $nome = trim($_POST['nome']);
    $membros = isset($_POST['membros']) ? $_POST['membros'] : [];

    // Atualiza o nome da equipe
    $stmt = $conn->prepare("UPDATE equipes SET nome = ? WHERE id = ?");
    $stmt->bind_param("si", $nome, $id);
    $stmt->execute();

    // Marca todos os membros como inativos
    $conn->query("UPDATE membros_equipes SET ativo = 0 WHERE equipe_id = $id");

    // Reativa ou insere os membros enviados
    $sqlCheck = "SELECT id FROM membros_equipes WHERE equipe_id = ? AND usuario_id = ?";
    $sqlAtivar = "UPDATE membros_equipes SET ativo = 1 WHERE id = ?";
    $sqlInserir = "INSERT INTO membros_equipes (equipe_id, usuario_id, ativo) VALUES (?, ?, 1)";

    $stmtCheck = $conn->prepare($sqlCheck);
    $stmtAtivar = $conn->prepare($sqlAtivar);
    $stmtInserir = $conn->prepare($sqlInserir);

    foreach ($membros as $usuario_id) {
        $stmtCheck->bind_param("ii", $id, $usuario_id);
        $stmtCheck->execute();
        $res = $stmtCheck->get_result();

        if ($res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $membro_id = $row['id'];
            $stmtAtivar->bind_param("i", $membro_id);
            $stmtAtivar->execute();
        } else {
            $stmtInserir->bind_param("ii", $id, $usuario_id);
            $stmtInserir->execute();
        }
    }

    header('Location: ../../telas/equipes.php?edicao=sucesso');
    exit;
}
