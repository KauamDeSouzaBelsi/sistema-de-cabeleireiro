<?php require_once __DIR__ . '/../include/functions.php';
require_once __DIR__ . '/../login/verifica.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php include __DIR__ . '/../include/header.php';?>
    <h3>consulta de Aluno</h3>
    <form action="" method="post">
        <label for="id">aluno id</label>
        <input type="number" name="id" id="id">
        <input type="submit" value="consultar">
    </form>

    <?php 
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        consultar($conexao, $_POST['id']);
    }
    include __DIR__ . '/../include/footer.php';
    ?>
</body>
</html>

