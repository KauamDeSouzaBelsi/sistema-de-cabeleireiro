<?php require_once '../include/functions.php';
session_start();
?>

<!DOCTYPE html>
<!-- <html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style/style.css">
    <title>cadastro</title>
</head>
<body>
    <main>
    <?php include '../include/header.php';?>
        <h1>faça o login</h1>
        <form action="" method="post">
            <label for="email">E-mail: </label><br>
            <input type="email" name="email" id="email" required>
            <label for="senha">Senha: </label><br>
            <input type="password" name="senha" id="senha" required>
            <input type="submit" value="Cadastrar">
        </form>
        <?php 
        // if($_SERVER['REQUEST_METHOD']=="POST"){
        //     $usuario = consultar_user($conexao, $_POST['email']);
        //     if ($_POST['email'] == $usuario['email'] && $_POST['senha'] == $usuario['senha']){
        //         $_SESSION['id'] = $usuario['id'];
        //         echo "login ok";
        //         header("Location: ../index.php");
        //         exit();
        //     } else {
        //         echo "Usuário ou senha inválidos";
        //     }
        // }
        ?>
    </main>
    
</body>
</html> -->