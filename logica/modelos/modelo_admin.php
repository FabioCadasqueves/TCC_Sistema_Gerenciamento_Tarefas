<?php
function cadastrarAdmin($conn, $nome, $email, $senha_hash)
{
    // Verifica se já existe um admin com este e-mail
    $stmt = $conn->prepare("SELECT id FROM admins WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        return "Este e-mail já está cadastrado.";
    }

    // Insere o novo admin
    $stmt = $conn->prepare("INSERT INTO admins (nome, email, senha) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nome, $email, $senha_hash);

    if ($stmt->execute()) {
        return true;
    } else {
        return "Erro ao cadastrar: " . $stmt->error;
    }
}
