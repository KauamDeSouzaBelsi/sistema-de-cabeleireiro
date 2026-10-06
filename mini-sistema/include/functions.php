<?php
require_once '../database/connect.php';

function  cadastrar($conexao, $nome, $nasc, $turma, $ativo)
{
    $sql = "INSERT INTO alunos (nome, nasc, turma, ativo) VALUES(:nome, :nasc, :turma, :ativo)";

    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(":nome", $nome);
    $stmt->bindParam(":nasc", $nasc);
    $stmt->bindParam(":turma", $turma);
    $stmt->bindParam(":ativo", $ativo);

    $stmt->execute();
    echo "Aluno cadastrado com sucesso!";
}



function consultar($conexao, $id){

    $sql = "SELECT nome, nasc, turma, ativo
            FROM alunos
            WHERE id = :id";

    $stmt = $conexao->prepare($sql);
    $stmt-> bindParam(":id", $id);
    $stmt->execute();

    $aluno = $stmt->fetch(PDO::FETCH_ASSOC);

    echo  "Aluno: {$aluno['nome']}<br>Turma: {$aluno['turma']}<br>Nascimento: {$aluno['nasc']}<br>Ativo: {$aluno['ativo']}<br>";

}

function atualizar ($conexao,$id, $nome, $turma, $nasc, $ativo)
{
    $sql = "UPDATE alunos SET nome  = :nome , turma = :turma ,nasc = :nasc , ativo = :ativo WHERE id = :id";

    $stmt = $conexao->prepare($sql);
    $stmt -> bindParam(":nome", $nome);
    $stmt -> bindParam(":turma", $turma);
    $stmt -> bindParam(":nasc", $nasc);
    $stmt -> bindParam(":ativo", $ativo);
    $stmt -> bindParam(":id", $id);
    $stmt ->execute();
}

function  cadastrar_usuario($conexao, $email, $senha)
{
    $sql = "INSERT INTO usuarios (email, senha) VALUES(:email, :senha)";

    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(":email", $email);
    $stmt->bindParam(":senha", $senha);


    $stmt->execute();
    echo "Usuario cadastrado com sucesso!";
}   

function consultar_user($conexao, $email){

    $sql = "SELECT id, email, senha, tipo
        FROM usuarios
        WHERE email = :email";

    $stmt = $conexao->prepare($sql);
    $stmt-> bindParam(":email", $email);
    $stmt->execute();

    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    return $usuario;
}
?>