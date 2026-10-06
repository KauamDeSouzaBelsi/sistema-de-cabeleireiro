<?php

session_start();

if (!isset($_SESSION["id"])) {
    header("Location: /mini-sistema/login/login.php");
    exit;
}

?>