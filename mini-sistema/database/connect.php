<?php 
//arquivo para ser chamado sempre que precisar conectar ao banco de dados, por exemplos quando formos fazer um CRUD pelo php 

$host = "192.168.10.26";
$dbname = "salao";
$user = "salao";
$pass = "salao";

// qwe@qwe senha: 123

try {
    $conexao = new PDO(
        "pgsql:host=$host;dbname=$dbname",
        $user,
        $pass
    );
    // echo "Conexão com o Postgres realizada!<br>";
} catch(PDOException $e) {
    echo "Erro: ". $e->getMessage();
}
?>