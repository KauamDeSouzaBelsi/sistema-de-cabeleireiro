<?php

require_once __DIR__ . '/../database/connect.php';

function cadastrar_usuario($conexao, $email, $senha)
{
    $sql = "INSERT INTO usuarios (email, senha) VALUES (:email, :senha)";

    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(":email", $email);
    $stmt->bindParam(":senha", $senha);
    $stmt->execute();
}

function consultar_user($conexao, $email)
{
    $sql = "SELECT id, nome, email, senha, tipo
            FROM usuarios
            WHERE email = :email";

    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(":email", $email);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
