<?php
session_start();
require_once '../../conexao/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    // Valida conexão
    if (!$conn) {
        die("Erro na conexão com o banco.");
    }

    // Inativa a equipe
    $stmtEquipe = $conn->prepare("UPDATE equipes SET ativo = 0 WHERE id = ?");
    $stmtEquipe->bind_param("i", $id);

    if (!$stmtEquipe->execute()) {
        die("Erro ao inativar equipe: " . $stmtEquipe->error);
    }

    // Inativa todos os membros da equipe
    $stmtMembros = $conn->prepare("UPDATE membros_equipes SET ativo = 0 WHERE equipe_id = ?");
    $stmtMembros->bind_param("i", $id);

    if (!$stmtMembros->execute()) {
        die("Erro ao inativar membros: " . $stmtMembros->error);
    }

    header('Location: ../../telas/equipes.php?exclusao=sucesso');
    exit;
} else {
    die("ID da equipe não fornecido.");
}
