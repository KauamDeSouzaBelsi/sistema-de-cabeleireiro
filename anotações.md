# criara a tabela sql


CREATE DATABASE IF NOT EXISTS salao_db;
USE salao_db;

CREATE TABLE IF NOT EXISTS produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    categoria VARCHAR(50) DEFAULT 'Geral',
    quantidade INT DEFAULT 0,
    preco DECIMAL(10, 2) DEFAULT 0.00,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

require_once __DIR__ . '/../database/connect.php';

function cadastrar_produto($conexao, $nome, $marca, $categoria, $descricao, $preco, $estoque, $avaliacao, $file_imagem) {
    // Valida se a imagem foi enviada sem erros
    if (!$file_imagem || $file_imagem['error'] !== UPLOAD_ERR_OK) {
        echo "<script>alert('Erro no envio da imagem. Verifique se escolheu um arquivo válido.');</script>";
        return;
    }

    // Processamento da imagem
    $extensao = strtolower(pathinfo($file_imagem['name'], PATHINFO_EXTENSION));
    $nome_imagem = uniqid('prod_') . '.' . $extensao;
    
    $diretorio_destino = __DIR__ . '/../assets/';
    $caminho_completo = $diretorio_destino . $nome_imagem;

    if (move_uploaded_file($file_imagem['tmp_name'], $caminho_completo)) {
        $sql = "INSERT INTO produtos (nome, marca, categoria, descricao, preco, estoque, avaliacao, imagem) 
                VALUES (:nome, :marca, :categoria, :descricao, :preco, :estoque, :avaliacao, :imagem)";
                
        $stmt = $conexao->prepare($sql);
        $stmt->execute([
            ':nome' => $nome,
            ':marca' => $marca,
            ':categoria' => $categoria,
            ':descricao' => $descricao,
            ':preco' => $preco,
            ':estoque' => $estoque,
            ':avaliacao' => $avaliacao,
            ':imagem' => $nome_imagem
        ]);
        
        echo "<script>alert('Produto cadastrado com sucesso!'); window.location.href='/mini-sistema/nortrek/index.php';</script>";
    } else {
        echo "<script>alert('Erro ao salvar o arquivo de imagem na pasta assets.');</script>";
    }
}x