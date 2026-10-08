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
    $sql = "SELECT id, nome, email, senha FROM usuarios WHERE email = ?";

    $comando = $conexao->prepare($sql);
    $comando->execute([$email]);

    return $comando->fetch(PDO::FETCH_ASSOC);
}

function consultar_admin($conexao, $email)
{
    $sql = "SELECT id, nome, email, senha FROM administradores WHERE email = ?";
    $comando = $conexao->prepare($sql);
    $comando->execute([$email]);

    return $comando->fetch(PDO::FETCH_ASSOC);
}
?>
