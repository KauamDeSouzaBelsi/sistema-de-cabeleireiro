<?php 
require_once __DIR__ . "/../include/functions.php";
require_once __DIR__ . '/../login/verifica.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
</head>
<body>
    <?php include __DIR__ . '/../include/header.php'?>
    <h1>pagina para apagar</h1>
    <form action="" method="post">
        <label for="id">ID: </label>
        <input type="number" name="id" id="id">
        <input type="submit" value="apagar">
    </form>
  <?php 
  if($_SERVER['REQUEST_METHOD'] == "POST"){
    deletar ($conexao, $_POST['id']);
  }
  ?>
</body>
<?php include __DIR__ . '/../include/footer.php'?>
</html>



