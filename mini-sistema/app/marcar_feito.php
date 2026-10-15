
<?php
session_start();

require_once "../database/connect.php";

if (!isset($_SESSION["tipo"]) || $_SESSION["tipo"] != "admin") {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"])) {
    $id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

    if ($id !== false && $id !== null && $id > 0) {
        $sql = "DELETE FROM agendamentos WHERE id = ?";
        $comando = $conexao->prepare($sql);
        $comando->execute([$id]);
    }
}

header("Location: agenda.php");
exit();
?>