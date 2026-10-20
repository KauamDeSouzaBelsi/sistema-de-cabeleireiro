# Documentação – Sistema de Cabeleireiro

Oct 20, 2026 · @kauam

## 1. Sobre o projeto

O Sistema de Cabeleireiro é um sistema web feito em **PHP** e **PostgreSQL**: o cliente cria uma conta, escolhe um corte e marca o horário; o administrador cadastra os cortes, controla o estoque e acompanha a agenda. Esta documentação segue a estrutura do README do repositório [Lapisari](https://github.com/LucasRetameroBortoletto/lapisari-), adaptada ao código e ao banco deste projeto.

O sistema tem três perfis de acesso:

| Perfil | O que faz |
| --- | --- |
| **Visitante** | Cria uma conta e entra. Sem login, qualquer página do sistema leva para a tela de login. |
| **Cliente** | Vê os cortes disponíveis com o preço e marca um horário, com observações sobre o corte. |
| **Administrador** | Cadastra, edita e exclui cortes; controla o estoque de produtos; vê a agenda e marca cada atendimento como feito. |

### Principais funcionalidades

- **Login por perfil:** a mesma tela de login serve ao administrador (tabela `administradores`) e ao cliente (tabela `usuarios`).
- **Catálogo de cortes:** nome, descrição e preço, exibidos na página inicial.
- **Agendamento:** o cliente escolhe corte, data e horário; o sistema recusa um horário que já esteja ocupado.
- **Agenda do administrador:** lista os agendamentos por data e horário, com botão para marcar como feito.
- **Estoque:** cadastro de produtos, entrada e saída de quantidade, e aviso de estoque baixo (5 unidades ou menos).

### Tecnologias

| Camada | Tecnologia | Uso |
| --- | --- | --- |
| Backend | PHP (PDO), sem framework | Páginas, regras de negócio e sessão |
| Banco | PostgreSQL (`pdo_pgsql`) | Contas, cortes, estoque e agendamentos |
| Front | HTML e CSS, sem framework | Telas e estilos, um arquivo CSS por página |
| Servidor | Apache (XAMPP) ou servidor embutido do PHP | Execução local e publicação |
| Versionamento | Git e GitHub | 23 commits, de 29/09/2026 a 19/10/2026 |

## 2. Requisitos funcionais

O sistema atende 16 requisitos funcionais, listados abaixo com a tela onde cada um acontece.

| Código | Requisito | Quem usa | Onde |
| --- | --- | --- | --- |
| **RF01** | Criar uma conta de cliente com nome, e-mail e senha. | Visitante | `login/cadastrar.php` |
| **RF02** | Entrar com e-mail e senha; o sistema procura primeiro entre os administradores e depois entre os clientes. | Visitante | `login/login.php` |
| **RF03** | Sair da conta, encerrando a sessão. | Cliente, admin | `login/logout.php` |
| **RF04** | Exibir na página inicial os cortes disponíveis, com nome e preço em reais. | Cliente, admin | `index.php` |
| **RF05** | Mostrar o botão "Agendar este corte" em cada corte, só para o cliente; ele leva à agenda com o corte já escolhido. | Cliente | `index.php` |
| **RF06** | Mostrar o menu de administração (cortes, estoque e agenda) na página inicial, só para o administrador. | Admin | `index.php` |
| **RF07** | Cadastrar um corte com nome, descrição e preço. | Admin | `app/cortes.php` |
| **RF08** | Editar um corte; o formulário abre preenchido com os dados atuais. | Admin | `app/cortes.php?editar=ID` |
| **RF09** | Excluir um corte, com confirmação; os agendamentos desse corte são apagados junto (chave estrangeira com ON DELETE CASCADE). | Admin | `app/cortes.php?excluir=ID` |
| **RF10** | Marcar um horário escolhendo corte, data, horário e observações. | Cliente | `app/agenda.php` |
| **RF11** | Recusar o agendamento quando a data e o horário já estão ocupados. | Cliente | `app/agenda.php` |
| **RF12** | Listar todos os agendamentos por data e horário, com e-mail do cliente, corte, observação e status. | Admin | `app/agenda.php` |
| **RF13** | Marcar um atendimento como feito, com confirmação; o agendamento é apagado da lista. | Admin | `app/marcar_feito.php` |
| **RF14** | Cadastrar um produto no estoque com nome, categoria, fornecedor, quantidade e preço unitário. | Admin | `app/estoque.php` |
| **RF15** | Dar entrada ou saída de uma quantidade num produto; a saída nunca deixa o estoque abaixo de zero. | Admin | `app/estoque.php` |
| **RF16** | Avisar "Estoque baixo" (5 unidades ou menos) ou "Sem estoque" (zero) ao lado de cada produto, e permitir excluir o produto com confirmação. | Admin | `app/estoque.php` |

## 3. Regras de negócio e requisitos não funcionais

As regras abaixo foram levantadas a partir do código atual; cada uma é cumprida por uma validação no PHP ou por uma restrição no banco.

### Regras de negócio

| Código | Regra |
| --- | --- |
| **RN01** | Toda conta criada pela tela de cadastro é de **cliente**. Os administradores ficam na tabela `administradores` e são cadastrados direto no banco. |
| **RN02** | Nome, e-mail e senha são obrigatórios no cadastro, e o e-mail não pode repetir: o sistema consulta `usuarios` antes de gravar. |
| **RN03** | Sem login, qualquer página interna redireciona para a tela de login. |
| **RN04** | O login procura primeiro entre os administradores; se não achar, procura entre os clientes. O perfil (`admin` ou `cliente`) fica na sessão. |
| **RN05** | Só o cliente agenda. O administrador, ao abrir a agenda, vê a lista de agendamentos no lugar do formulário. |
| **RN06** | Um horário só aceita um agendamento: a combinação de data e horário não pode repetir, qualquer que seja o corte. Só o PHP garante essa regra; o banco não tem restrição UNIQUE para data e horário. |
| **RN07** | Corte, data e horário são obrigatórios no agendamento; as observações são opcionais. |
| **RN08** | O preço do corte é um número maior ou igual a zero; nome e descrição são obrigatórios. |
| **RN09** | Excluir um corte apaga também os agendamentos dele, porque no banco a chave estrangeira de agendamentos para cortes tem ON DELETE CASCADE; o aviso de "corte com agendamentos" que existe no código nunca é disparado. |
| **RN10** | No estoque, o nome do produto é obrigatório e a quantidade inicial não pode ser negativa. O ajuste é de pelo menos 1 unidade, e a saída nunca deixa a quantidade abaixo de zero. |
| **RN11** | Produto com 5 unidades ou menos é marcado como "Estoque baixo"; com zero, como "Sem estoque" (constante `ESTOQUE_BAIXO`). |
| **RN12** | Só o administrador marca um atendimento como feito, e essa ação apaga o agendamento. |
| **RN13** | Todos os preços aparecem em reais, no formato brasileiro (`R$ 1.234,50`). |

### Requisitos não funcionais

| Código | Categoria | Requisito | Como é atendido |
| --- | --- | --- | --- |
| **RNF01** | Segurança | A senha do cliente não é guardada como foi digitada. | `password_hash()` no cadastro e `password_verify()` no login. |
| **RNF02** | Segurança | O que o usuário digita não é executado como SQL. | Todas as consultas usam prepared statements do PDO. |
| **RNF03** | Segurança | Textos cadastrados não rodam como código na página (XSS). | Os textos passam por `htmlspecialchars()` antes de ir para o HTML. |
| **RNF04** | Controle de acesso | As páginas internas só abrem para quem entrou. | `login/verifica.php` confere a sessão; `app/marcar_feito.php` também confere o perfil de administrador. |
| **RNF05** | Integridade | Dados inválidos não chegam ao banco. | Validação no servidor (preço numérico e não negativo, campos obrigatórios) e restrições do banco: NOT NULL, e-mail único e chaves estrangeiras em agendamentos. |
| **RNF06** | Usabilidade | O usuário entende o que aconteceu. | Mensagens de erro e sucesso, confirmação antes de excluir ou concluir e aviso visual de estoque baixo. |
| **RNF07** | Robustez | Recarregar a página não repete a ação. | Cortes e estoque redirecionam depois do envio (padrão *POST → Redirect → GET*). |
| **RNF08** | Manutenibilidade | O código é simples de alterar. | Partes repetidas ficam em `include/` (`header.php`, `functions.php`) e a conexão em `database/connect.php`. |
| **RNF09** | Compatibilidade | Ambiente suportado. | PHP 8 com a extensão `pdo_pgsql`, PostgreSQL e navegadores atuais. |

## 4. Fluxo de trabalho e organização do código

O sistema tem dois caminhos de uso, um para o cliente e outro para o administrador, e todas as páginas seguem o mesmo esqueleto de código.

### Fluxo do cliente

1. Abre o sistema; sem sessão ativa, é levado para `login/login.php`.
2. Quem não tem conta clica em **Cadastrar**, preenche nome, e-mail e senha e volta ao login.
3. Entra e cai na página inicial, que lista os cortes com o preço.
4. Clica em **Agendar este corte**; a agenda abre com esse corte já selecionado.
5. Escolhe data e horário, escreve as observações e envia.
6. Se a data e o horário já têm agendamento, aparece "Esse horário já está ocupado."; senão, "Corte marcado com sucesso!".
7. Sai pelo link **Sair**, que encerra a sessão.

### Fluxo do administrador

O administrador entra pela mesma tela de login. Na página inicial aparece o menu de administração, com três áreas:

| Área | Fluxo |
| --- | --- |
| **Cortes** (`app/cortes.php`) | Preenche nome, descrição e preço e cadastra. Na tabela abaixo, **Editar** preenche o formulário com o corte e **Excluir** pede confirmação. |
| **Estoque** (`app/estoque.php`) | Cadastra o produto; na tabela, digita a quantidade e usa **+ Adicionar** ou **− Remover**. Produtos com 5 unidades ou menos recebem o aviso. |
| **Agenda** (`app/agenda.php`) | Vê os agendamentos em ordem de data e horário e usa **Marcar como feito** quando o atendimento termina. |

### Como uma página funciona por dentro

As páginas de `app/` seguem a mesma ordem, de cima para baixo no mesmo arquivo:

1. Carrega `login/verifica.php`, que inicia a sessão e manda para a página inicial (que leva ao login) quem não entrou.
2. Carrega `database/connect.php`, que abre a conexão PDO (`$conexao`).
3. Se o pedido for um envio de formulário (`POST`), valida os campos e grava no banco; depois redireciona com `header("Location: ...")`.
4. Busca os dados com `SELECT`.
5. Monta o HTML da página, incluindo `include/header.php` (menu de navegação).

### Estrutura de pastas

```text
mini-sistema/
├── index.php              página inicial: cortes disponíveis e menu do admin
├── documentacao.md        documentação antiga (modelo genérico)
├── app/
│   ├── agenda.php         agenda: formulário do cliente e lista do admin
│   ├── cortes.php         cadastro, edição e exclusão de cortes
│   ├── estoque.php        cadastro e ajuste de produtos do estoque
│   ├── marcar_feito.php   conclui (apaga) um agendamento; só admin
│   └── select.php         lista simples de cortes, fora do menu
├── database/
│   ├── connect.php        conexão PDO com o PostgreSQL
│   └── salao.sql          dump do banco: tabelas, sem dados
├── include/
│   ├── functions.php      consultas de usuário e administrador
│   ├── header.php         menu: Início, Cadastrar novo corte, Estoque, Ver agenda
│   └── footer.php         rodapé (ainda não usado por nenhuma página)
├── login/
│   ├── login.php          entrada de administrador e cliente
│   ├── cadastrar.php      criação de conta de cliente
│   ├── logout.php         encerra a sessão
│   └── verifica.php       barra quem não está logado
├── style/                 um CSS por página (agenda, cadastrar, corte, estoque, style, tela-inicio)
└── imagens/
    ├── fundo.jpg          fundo da página inicial e da agenda
    └── fundo2.avif        fundo da tela de cortes
```

Os arquivos `base.css`, `index.css` e `cortes.css`, em `style/`, não são chamados por nenhuma página hoje.

### Funções em `include/functions.php`

| Função | O que faz |
| --- | --- |
| `consultar_user($conexao, $email)` | Busca um cliente pelo e-mail em `usuarios`. |
| `consultar_admin($conexao, $email)` | Busca um administrador pelo e-mail em `administradores`. |
| `cadastrar_usuario($conexao, $email, $senha)` | Insere um usuário só com e-mail e senha; o cadastro real usa um `INSERT` próprio em `login/cadastrar.php`. |

## 5. Banco de dados

O sistema usa um banco **PostgreSQL** chamado `salao`, com cinco tabelas. A descrição abaixo vem do dump `mini-sistema/database/salao.sql`, gerado pelo `pg_dump` 18.4; ele traz só a estrutura, sem nenhum dado.

### Tabelas

| Tabela | Guarda | Colunas usadas pelo código | Usada em |
| --- | --- | --- | --- |
| `usuarios` | Contas de clientes | `id`, `nome`, `email`, `senha` | `login/cadastrar.php`, `login/login.php`, `app/agenda.php` |
| `administradores` | Contas de administradores | `id`, `nome`, `email`, `senha` | `login/login.php` |
| `cortes` | Catálogo de cortes | `id`, `nome`, `descricao`, `preco` | `index.php`, `app/cortes.php`, `app/agenda.php` |
| `estoque` | Produtos do salão | `id`, `nome`, `categoria`, `fornecedor`, `quantidade`, `preco_unitario` | `app/estoque.php` |
| `agendamentos` | Horários marcados | `id`, `cliente_id`, `corte_id`, `data`, `horario`, `observacoes`, `status` | `app/agenda.php`, `app/marcar_feito.php` |

### Relacionamentos

| De | Para | Regra |
| --- | --- | --- |
| `agendamentos.cliente_id` | `usuarios.id` | Cada agendamento pertence a um cliente. Sem `ON DELETE`: não dá para apagar um cliente que tem agendamento. |
| `agendamentos.corte_id` | `cortes.id` | Cada agendamento é de um corte. Com `ON DELETE CASCADE`: apagar o corte apaga os agendamentos dele. |

As tabelas `administradores` e `estoque` não se ligam a nenhuma outra.

### Estrutura do banco (salao.sql)

```sql
-- Resumo do salao.sql. SERIAL = integer com sequence (id_seq) no valor padrão.
CREATE TABLE usuarios (
    id    SERIAL PRIMARY KEY,
    nome  VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL          -- hash gerado por password_hash()
);

CREATE TABLE administradores (
    id    SERIAL PRIMARY KEY,
    nome  VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL          -- hoje comparada em texto puro
);

CREATE TABLE cortes (
    id        SERIAL PRIMARY KEY,
    nome      VARCHAR(100) NOT NULL,
    preco     NUMERIC(10,2) NOT NULL,
    descricao TEXT
);

CREATE TABLE estoque (
    id             SERIAL PRIMARY KEY,
    nome           VARCHAR(100) NOT NULL,
    categoria      VARCHAR(50)  NOT NULL,
    fornecedor     VARCHAR(100) NOT NULL,
    quantidade     INTEGER NOT NULL,
    preco_unitario NUMERIC(10,2) NOT NULL
);

CREATE TABLE agendamentos (
    id          SERIAL PRIMARY KEY,
    cliente_id  INTEGER NOT NULL REFERENCES usuarios(id),
    corte_id    INTEGER NOT NULL REFERENCES cortes(id) ON DELETE CASCADE,
    data        DATE NOT NULL,
    horario     TIME NOT NULL,
    observacoes VARCHAR(500),
    status      VARCHAR(20) NOT NULL DEFAULT 'Pendente'
);
```

O banco não tem `CHECK` para preço ou quantidade negativos, nem `UNIQUE` para data e horário do agendamento; essas regras ficam só no PHP. Todas as tabelas pertencem ao usuário `salao`.

### Dump do banco e dados iniciais

Como o `salao.sql` não traz dados, o sistema sobe vazio: sem cortes, produtos, clientes nem administrador (por isso o passo 4 da seção 6 é obrigatório). Para levar os dados do banco atual para o servidor, gere um arquivo só com os dados e cole o conteúdo no espaço abaixo:

```bash
# só os dados (cortes, produtos, contas e agendamentos), em formato INSERT
pg_dump -h SEU_HOST -U SEU_USUARIO -d salao --data-only --inserts --no-owner -f dados.sql
```

```sql
-- ============================================================
-- COLE AQUI OS DADOS DO BANCO (conteúdo de dados.sql), SE FOR LEVAR CORTES E PRODUTOS
-- ============================================================
```

Para restaurar em outro servidor, crie antes o usuário salao (passo 2 da seção 6), porque o dump entrega as tabelas a ele; depois crie o banco vazio e carregue a estrutura e, se houver, os dados:

```bash
psql -U postgres -c "CREATE DATABASE salao OWNER salao;"
psql -U salao -d salao -f mini-sistema/database/salao.sql
psql -U salao -d salao -f dados.sql
```

## 6. Como rodar

Para rodar o sistema, são cinco passos: baixar o projeto, criar o banco, configurar a conexão, criar o primeiro administrador e iniciar o servidor. Os comandos podem ser copiados como estão, um bloco por vez; no **Windows** use o PowerShell, no **Linux** use o terminal.

Um detalhe vale para todos os passos: o menu e os redirecionamentos têm o caminho `/sistema-de-cabeleireiro/mini-sistema/` escrito no código (`header.php`, `index.php`, `logout.php`, `verifica.php` e `estoque.php`). Por isso o projeto precisa estar numa pasta chamada exatamente **`sistema-de-cabeleireiro`** (com *ei*), na raiz do site. O repositório no GitHub se chama `sistema-de-cabeleleiro` (com *el*), então o passo 1 define o nome da pasta.

### Passo 0: conferir os programas

É preciso ter **Git**, **PHP 8** com a extensão `pdo_pgsql` e **PostgreSQL**. Cada comando deve mostrar uma versão, e o do meio deve listar `pdo_pgsql`.

```bash
git --version
```

```bash
php -m | grep pdo_pgsql
```

```bash
psql --version
```

No PowerShell, troque `grep pdo_pgsql` por `Select-String pdo_pgsql`. Se o PHP não reconhecer a extensão, ative a linha `extension=pdo_pgsql` no `php.ini`.

### Passo 1: baixar o projeto

```bash
git clone https://github.com/KauamDeSouzaBelsi/sistema-de-cabeleleiro.git sistema-de-cabeleireiro
```

```bash
cd sistema-de-cabeleireiro
```

Com o Apache, rode esses dois comandos dentro da pasta pública do servidor (`C:\xampp\htdocs` no XAMPP, `/var/www/html` no Linux). Com o servidor embutido do PHP, qualquer pasta serve.

### Passo 2: criar o banco de dados

O código espera um banco chamado `salao` e um usuário `salao`. Os dois primeiros comandos criam os dois (troque `sua_senha` por uma senha sua); o terceiro, rodado dentro da pasta do projeto, cria as tabelas a partir do arquivo salao.sql (seção 5), sem nenhum dado.

```bash
psql -U postgres -c "CREATE USER salao WITH PASSWORD 'sua_senha';"
```

```bash
psql -U postgres -c "CREATE DATABASE salao OWNER salao;"
```

```bash
psql -U salao -d salao -f mini-sistema/database/salao.sql
```

Pelo pgAdmin, sem terminal: clique com o botão direito em *Databases*, escolha *Create* e depois *Database*, e dê o nome `salao`. Em seguida abra *Query Tool* nesse banco, carregue o arquivo do dump e execute com F5.

### Passo 3: configurar a conexão

Abra `mini-sistema/database/connect.php` e ajuste as quatro variáveis do começo do arquivo:

```php
$host   = "localhost";
$dbname = "salao";
$user   = "salao";
$pass   = "sua_senha";
```

- `$host`: onde o PostgreSQL está. `localhost` é o próprio computador; se o banco estiver em outra máquina, use o IP dela. No código enviado, o `$host` aponta para um servidor da rede local.
- `$dbname`, `$user` e `$pass`: o banco e a conta criados no passo 2.

Não envie a senha real para o GitHub; veja a seção 7.

### Passo 4: criar o primeiro administrador

A tela de cadastro só cria clientes, então o administrador entra direto pela tabela `administradores`:

```bash
psql -U salao -d salao -c "INSERT INTO administradores (nome, email, senha) VALUES ('Administrador', 'admin@exemplo.com', 'TROQUE_ESTA_SENHA');"
```

Hoje o login compara a senha do administrador em texto puro, então o valor gravado é exatamente o que será digitado na tela (ponto 1 da seção 7).

### Passo 5: iniciar o site

Para usar o servidor embutido do PHP, rode o comando abaixo **na pasta que contém** `sistema-de-cabeleireiro` (a pasta de cima, e não dentro dela):

```bash
cd ..
php -S localhost:8000
```

Abra <http://localhost:8000/sistema-de-cabeleireiro/mini-sistema/login/login.php>. Deixe o terminal aberto enquanto usa o site; para parar, aperte Ctrl+C.

Com o Apache (XAMPP ou Linux), não use o comando acima: inicie o Apache e abra <http://localhost/sistema-de-cabeleireiro/mini-sistema/login/login.php>.

**Deu certo se** a tela "Faça o login" abre, o administrador criado no passo 4 entra e vê o menu de administração, e uma conta criada em **Cadastrar** entra como cliente e vê o botão "Agendar este corte".

### Para atualizar o projeto depois

```bash
git pull
```

### Se algo der errado

| O que aparece | Causa provável | O que fazer |
| --- | --- | --- |
| `Erro: could not find driver` | A extensão `pdo_pgsql` não está ativa. | Ative `extension=pdo_pgsql` no `php.ini` e reinicie o servidor. |
| `Erro: SQLSTATE[08006]` ou `connection refused` | O PHP não conseguiu falar com o PostgreSQL. | Confira `$host`, `$dbname`, `$user` e `$pass` e se o PostgreSQL está ligado. |
| `relation "cortes" does not exist` | As tabelas não foram criadas. | Refaça o passo 2 e carregue o `salao.sql`. |
| `role "salao" does not exist` ao carregar o `salao.sql` | O dump entrega as tabelas ao usuário `salao`, que ainda não existe. | Crie o usuário (primeiro comando do passo 2) e carregue o arquivo de novo. |
| `invalid command \restrict` ao carregar o `salao.sql` | O `psql` é mais antigo que o `pg_dump` que gerou o arquivo, e não conhece essa linha. | Atualize o PostgreSQL ou apague do arquivo as linhas que começam com `\restrict` e `\unrestrict`. |
| Página "não encontrada" ou links do menu quebrados | A pasta do projeto não se chama `sistema-de-cabeleireiro` ou não está na raiz do site. | Refaça o passo 1 com o nome exato da pasta. |
| Página sem estilo | O navegador guardou o CSS antigo. | Aperte Ctrl+F5. |

### Autoria

Projeto desenvolvido por Kauam De Souza Belsi, com 23 commits entre 29/09/2026 e 19/10/2026. Repositório: [sistema-de-cabeleleiro](https://github.com/KauamDeSouzaBelsi/sistema-de-cabeleleiro).

