<?php require_once '../include/functions.php'?>

<?php require_once 'verifica.php'?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style/style.css">
    <title>cadastro</title>
</head>
<body>
    <main>
        <h1>cadastre-se</h1>
        <form action="" method="post">
            <label for="email">E-mail: </label>
            <input type="email" name="email" id="email">
            <label for="senha">Senha: </label><br>
            <input type="password" name="senha" id="senha">
            <input type="submit" value="Cadastrar">
        </form>
        <?php 
        if($_SERVER['REQUEST_METHOD']=="POST"){
            cadastrar_usuario($conexao, $_POST['email'], $_POST['senha']);
        }else {
            echo "usuario e senha invalidos";
        }
        ?>
    </main>
    <?php  include '../include/footer.php'?>
</body>
</html>