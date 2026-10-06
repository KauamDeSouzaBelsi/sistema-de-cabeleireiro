<?php

require_once __DIR__ . "/../login/verifica.php";
require_once __DIR__ . "/../database/connect.php";


// Somente cliente pode marcar

if ($_SESSION["tipo"] != "cliente") {

    echo "Somente clientes podem marcar horários.";
    exit;
}


// Buscar os cortes disponíveis

$sql = "SELECT * FROM cortes ORDER BY nome";

$comando = $conexao->query($sql);

$cortes = $comando->fetchAll(PDO::FETCH_ASSOC);

$corteSelecionado = $_GET["corte"] ?? "";


$mensagem = "";


// Quando o cliente enviar o formulário

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $corte_id = $_POST["corte_id"];
    $data = $_POST["data"];
    $horario = $_POST["horario"];
    $observacoes = trim($_POST["observacoes"]);


    if ($corte_id == "" || $data == "" || $horario == "") {

        $mensagem = "Preencha a data, o horário e o tipo de corte.";

    } else {

        // Verificar se já existe alguém nesse horário

        $sql = "SELECT id
                FROM agendamentos
                WHERE data = ?
                AND horario = ?";

        $comando = $conexao->prepare($sql);

        $comando->execute([
            $data,
            $horario
        ]);

        $horarioOcupado = $comando->fetch(PDO::FETCH_ASSOC);


        if ($horarioOcupado) {

            $mensagem = "Esse horário já está ocupado.";

        } else {

            // Salvar o agendamento

            $sql = "INSERT INTO agendamentos
                    (cliente_id, corte_id, data, horario, observacoes)
                    VALUES (?, ?, ?, ?, ?)";

            $comando = $conexao->prepare($sql);

            $comando->execute([
                $_SESSION["id"],
                $corte_id,
                $data,
                $horario,
                $observacoes
            ]);

            $mensagem = "Corte marcado com sucesso!";
        }
    }
}

?>

<!DOCTYPE html>

<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <title>Marcar horário</title>

</head>

<body>

<h1>Marcar horário</h1>

<p>
    Olá, <?= htmlspecialchars($_SESSION["nome"]) ?>
</p>


<?php if ($mensagem != ""): ?>

    <p>
        <?= htmlspecialchars($mensagem) ?>
    </p>

<?php endif; ?>


<form method="POST">

    <label>Tipo de corte:</label>

    <br>

    <select name="corte_id" required>

        <option value="">
            Escolha o corte
        </option>

        <?php foreach ($cortes as $corte): ?>

            <option
                value="<?= $corte["id"] ?>"
                <?= $corteSelecionado == $corte["id"] ? "selected" : "" ?>
            >

                <?= htmlspecialchars($corte["nome"]) ?>

                - R$

                <?= number_format(
                    $corte["preco"],
                    2,
                    ",",
                    "."
                ) ?>

            </option>

        <?php endforeach; ?>

    </select>

    <br><br>

    <label>Data:</label>

    <br>

    <input
        type="date"
        name="data"
        required
    >

    <br><br>

    <label>Horário:</label>

    <br>

    <input
        type="time"
        name="horario"
        required
    >

    <br><br>

    <label>Observações do corte:</label>

    <br>

    <textarea
        name="observacoes"
        rows="5"
        cols="40"
    ></textarea>

    <br><br>

    <button type="submit">
        Marcar horário
    </button>

</form>

</body>

</html>

