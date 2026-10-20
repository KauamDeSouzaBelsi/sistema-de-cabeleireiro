<?php 
require_once __DIR__ . "/../database/connect.php";
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../style/agenda.css">
    <title>lista de cortes</title>
</head>
<body>
    <?php include __DIR__ . '/../include/header.php';?>
    <main>
        <div style="width: 50%; margin:auto; text-align:center; border:1px solid black; border-radius:5px;">
            
            <?php

$sql = "SELECT id, nome, preco FROM cortes ORDER BY nome";

$comando = $conexao->query($sql);

$cortes = $comando->fetchAll(PDO::FETCH_ASSOC);

foreach ($cortes as $corte) {
    echo "<p>";
    echo htmlspecialchars($corte["nome"]);
    echo " - R$ " . number_format($corte["preco"], 2, ",", ".");
    echo "</p>";
}

?>
       </div> 
    </main>
</body>
</html>
