<?php

require_once __DIR__ . "/../database/connect.php";

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nome = trim($_POST["nome"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $senha = $_POST["senha"] ?? "";
    $tipo = $_POST["tipo"] ?? "";


    // Verificar se todos os campos foram preenchidos

    if ($nome == "" || $email == "" || $senha == "" || $tipo == "") {

        $mensagem = "Preencha todos os campos.";

    } elseif ($tipo != "cliente" && $tipo != "admin") {

        $mensagem = "Tipo de cadastro inválido.";

    } else {

        // Verificar se o email já existe

        $sql = "SELECT id FROM usuarios WHERE email = ?";

        $comando = $conexao->prepare($sql);

        $comando->execute([$email]);

        $usuario = $comando->fetch(PDO::FETCH_ASSOC);


        if ($usuario) {

            $mensagem = "Esse email já está cadastrado.";

        } else {

            // Criptografar a senha

            $senha = password_hash(
                $senha,
                PASSWORD_DEFAULT
            );


            // Cadastrar usuário

            $sql = "INSERT INTO usuarios
                    (nome, email, senha, tipo)
                    VALUES (?, ?, ?, ?)";

            $comando = $conexao->prepare($sql);

            $comando->execute([
                $nome,
                $email,
                $senha,
                $tipo
            ]);


            $mensagem = "Cadastro realizado com sucesso!";
        }
    }
}

?>

<!DOCTYPE html>

<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Cadastrar</title>

</head>

<body>


<h1>Criar conta</h1>


<?php if ($mensagem != ""): ?>

    <p>
        <?= htmlspecialchars($mensagem) ?>
    </p>

<?php endif; ?>


<form method="POST">


    <label>Nome:</label>

    <br>

    <input
        type="text"
        name="nome"
        required
    >


    <br><br>


    <label>Email:</label>

    <br>

    <input
        type="email"
        name="email"
        required
    >


    <br><br>


    <label>Senha:</label>

    <br>

    <input
        type="password"
        name="senha"
        required
    >


    <br><br>


    <label>Tipo de cadastro:</label>

    <br>

    <select name="tipo" required>

        <option value="">
            Escolha uma opção
        </option>

        <option value="cliente">
            Cliente
        </option>

        <option value="admin">
            Administrador
        </option>

    </select>


    <br><br>


    <button type="submit">
        Cadastrar
    </button>


</form>


<br>


<a href="login.php">
    Voltar para o login
</a>


</body>

</html>