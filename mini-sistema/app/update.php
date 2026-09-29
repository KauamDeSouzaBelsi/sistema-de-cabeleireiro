<?php 
require_once __DIR__ . '/../include/functions.php';
require_once __DIR__ . '/../login/verifica.php';
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>atualiza aluno</title>
</head>
<body>
    <?php include __DIR__ . '/../include/header.php';?>
    <form action="" method="post">
        
        <label for="id">ID:</label>
        <input type="number" name="id" id="id">

        <label for="nome">Nome: </label>
        <input type="text" name="nome" id="nome"><br>

        <label for="turma">turma: </label>
        <input type="text" name="turma" id="turma"><br>

        <label for="nasc">Nascimento: </label>
        <input type="date" name="nasc" id="nasc"><br>
        
        <label for="ativo">Ativo?</label>
        <input type="radio" name="ativo" id="ativo" value="true">
        <label for="sim">Está Ativo</label>

        <input type="radio" name="ativo" id="ativo" value="false">
        <label for="nao">Não Está Ativo</label><br>
        <input type="reset" value="limpar">
        <input type="submit" value="cadstrar">
    </form>
    <?php include '../include/footer.php';
    
    if($_SERVER['REQUEST_METHOD'] == "post"){
        atualizar($conexao, $_POST['id'], $_POST['nome'], $_POST['turma'], $_POST['nasc'] 
        , $_POST['ativo']);
    }
    ?>

    
</body>
</html>
