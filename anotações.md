Tabela usuarios
CREATE TABLE usuarios (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    tipo VARCHAR(20) NOT NULL
);

Tabela cortes
CREATE TABLE cortes (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    preco DECIMAL(10,2) NOT NULL
);

Tabela estoque
CREATE TABLE estoque (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    categoria VARCHAR(50) NOT NULL,
    fornecedor VARCHAR(100) NOT NULL,
    quantidade INTEGER NOT NULL,
    preco_unitario DECIMAL(10,2) NOT NULL
);

Tabela agendamentos
CREATE TABLE agendamentos (
    id SERIAL PRIMARY KEY,

    cliente_id INTEGER NOT NULL,

    corte_id INTEGER NOT NULL,

    data DATE NOT NULL,

    horario TIME NOT NULL,

    observacoes VARCHAR(500),

    FOREIGN KEY (cliente_id)
        REFERENCES usuarios(id),

    FOREIGN KEY (corte_id)
        REFERENCES cortes(id)
);