<?php 
require_once __DIR__ . '/../include/functions.php';
require_once __DIR__ . '/../login/verifica.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>lista de cortes</title>
</head>
<body>
    <?php include __DIR__ . '/../include/header.php';?>
    <main>
        <div style="width: 50%; margin:auto; text-align:center; border:1px solid black; border-radius:5px;"
            
            <?php 
            listar($conexao);
            ?>
       </div> 
    </main>
</body>
</html>
