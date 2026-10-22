# Sistema de Cabeleireiro — Documentação Técnica e Restauração do Banco de Dados

**Data:** 20 de outubro de 2026  
**Autor:** Kauam De Souza Belsi

## Sumário

1. Visão geral do projeto
2. Requisitos funcionais
3. Regras de negócio e segurança
4. Arquitetura e funcionamento
5. Estrutura do banco de dados
6. Instalação e execução do sistema
7. Processo de geração do dump
8. Processo de restauração do banco
9. Verificação após a restauração
10. Testes funcionais
11. Possíveis erros e soluções
12. Segurança e recomendações
13. Registro de evidências
14. Conclusão
15. Fluxo de login do administrador e do cliente
16. Diferença entre o arquivo SQL e o dump completo
17. Conferência do caminho do projeto
18. Teste de restauração e compatibilidade
19. Observação sobre os requisitos funcionais

---

## 1. Visão geral do projeto

O Sistema de Cabeleireiro é um sistema web feito em PHP e PostgreSQL. O cliente cria uma conta, escolhe um corte e marca um horário; o administrador cadastra os cortes, controla o estoque e acompanha a agenda.

Esta documentação descreve as funcionalidades do projeto, a estrutura do banco de dados, a instalação do sistema e os procedimentos para gerar e restaurar uma cópia de segurança do banco.

O sistema tem três perfis de acesso:

| Perfil | O que faz |
|---|---|
| Visitante | Cria uma conta e entra no sistema. Sem login, as páginas internas redirecionam para a tela de login. |
| Cliente | Vê os cortes disponíveis e seus preços e marca um horário com observações. |
| Administrador | Cadastra, edita e exclui cortes; controla o estoque; consulta a agenda e marca atendimentos como feitos. |

### Principais funcionalidades

- **Login por perfil:** a mesma tela de login serve ao administrador, armazenado na tabela `administradores`, e ao cliente, armazenado na tabela `usuarios`.
- **Catálogo de cortes:** apresenta nome, descrição e preço dos cortes.
- **Agendamento:** o cliente escolhe o corte, a data e o horário e pode adicionar observações.
- **Agenda do administrador:** apresenta os agendamentos por data e horário.
- **Estoque:** permite cadastrar produtos, registrar entradas e saídas e mostrar avisos de estoque baixo, quando há cinco unidades ou menos.

### Tecnologias

| Camada | Tecnologia | Uso |
|---|---|---|
| Backend | PHP com PDO | Páginas, regras de negócio e sessões |
| Banco de dados | PostgreSQL | Contas, cortes, estoque e agendamentos |
| Frontend | HTML e CSS | Estrutura e aparência das páginas |
| Servidor | Apache ou servidor embutido do PHP | Execução da aplicação web |
| Versionamento | Git e GitHub | Armazenamento e controle de versões |

### 1.1 Objetivo

O Sistema de Cabeleireiro é uma aplicação web desenvolvida para facilitar o gerenciamento de um salão. O sistema permite que os clientes consultem cortes e realizem agendamentos, enquanto o administrador gerencia os cortes, os produtos do estoque e os atendimentos.

Além das funcionalidades do sistema, esta documentação explica como verificar o banco de dados PostgreSQL, gerar um dump e testar sua restauração em um banco separado.

### 1.2 Tecnologias utilizadas

| Tecnologia | Utilização |
|---|---|
| PHP | Processamento das páginas e regras do sistema |
| HTML | Estrutura das páginas |
| CSS | Estilização da interface |
| PostgreSQL | Armazenamento dos dados |
| PDO | Conexão entre PHP e PostgreSQL |
| Apache ou servidor PHP | Execução da aplicação web |
| MobaXterm | Acesso ao terminal do servidor remoto |
| Git e GitHub | Versionamento e armazenamento do código |

### 1.3 Perfis de acesso

**Cliente**

- Criar uma conta e entrar no sistema.
- Consultar os cortes e seus preços.
- Escolher um corte, uma data e um horário.
- Adicionar observações ao agendamento.

**Administrador**

- Cadastrar, editar e excluir cortes.
- Consultar a agenda.
- Marcar atendimentos como feitos.
- Cadastrar produtos e controlar as quantidades do estoque.
- Consultar alertas de estoque baixo.

O cadastro público cria contas de clientes. O administrador é cadastrado separadamente no banco de dados.

### 1.4 Organização do projeto

A pasta principal da aplicação é `mini-sistema`.

```text
mini-sistema/
├── index.php
├── app/
│   ├── agenda.php
│   ├── cortes.php
│   ├── estoque.php
│   ├── marcar_feito.php
│   └── select.php
├── database/
│   ├── connect.php
│   └── salao.sql
├── include/
│   ├── functions.php
│   ├── header.php
│   └── footer.php
├── login/
│   ├── login.php
│   ├── cadastrar.php
│   ├── logout.php
│   └── verifica.php
├── style/
└── imagens/
```

A estrutura acima resume os principais arquivos documentados do projeto. A lista exata deve ser conferida com a versão mais recente do repositório.

---

## 2. Requisitos funcionais

O sistema possui requisitos funcionais relacionados ao cadastro, login, cortes, agendamentos e estoque.

| Código | Requisito | Quem usa | Arquivo principal |
|---|---|---|---|
| RF01 | Criar uma conta de cliente com nome, e-mail e senha. | Visitante | `login/cadastrar.php` |
| RF02 | Entrar com e-mail e senha, verificando primeiro o administrador e depois o cliente. | Visitante | `login/login.php` |
| RF03 | Sair da conta e encerrar a sessão. | Cliente e administrador | `login/logout.php` |
| RF04 | Exibir os cortes disponíveis com nome e preço. | Cliente e administrador | `index.php` |
| RF05 | Mostrar o botão para agendar um corte ao cliente. | Cliente | `index.php` |
| RF06 | Mostrar o menu administrativo apenas para o administrador. | Administrador | `index.php` |
| RF07 | Cadastrar um corte com nome, descrição e preço. | Administrador | `app/cortes.php` |
| RF08 | Editar um corte existente. | Administrador | `app/cortes.php` |
| RF09 | Excluir um corte com confirmação. | Administrador | `app/cortes.php` |
| RF10 | Agendar um horário escolhendo corte, data, horário e observações. | Cliente | `app/agenda.php` |
| RF11 | Recusar o agendamento quando a data e o horário já estão ocupados. | Cliente | `app/agenda.php` |
| RF12 | Listar os agendamentos por data e horário. | Administrador | `app/agenda.php` |
| RF13 | Marcar um atendimento como feito. | Administrador | `app/marcar_feito.php` |
| RF14 | Cadastrar produtos no estoque. | Administrador | `app/estoque.php` |
| RF15 | Registrar entrada ou saída de produtos. | Administrador | `app/estoque.php` |
| RF16 | Avisar quando o estoque estiver baixo ou sem unidades e permitir excluir produtos. | Administrador | `app/estoque.php` |

Esses requisitos descrevem as principais funcionalidades previstas no projeto. Para a entrega, recomenda-se testar cada uma no ambiente em que o sistema será apresentado.

---

## 3. Regras de negócio e requisitos não funcionais

As regras abaixo foram levantadas a partir da documentação do código atual. Elas dependem das validações feitas pelo PHP e das restrições existentes no banco de dados.

### 3.1 Regras de negócio

| Código | Regra |
|---|---|
| RN01 | Toda conta criada pela tela de cadastro é de cliente. Os administradores são cadastrados diretamente no banco. |
| RN02 | Nome, e-mail e senha são obrigatórios no cadastro, e o e-mail não pode se repetir. |
| RN03 | Sem login, as páginas internas redirecionam para a tela de login. |
| RN04 | O login procura primeiro entre os administradores e, se não encontrar, procura entre os clientes. O perfil fica guardado na sessão. |
| RN05 | Só o cliente realiza agendamentos. O administrador consulta a agenda. |
| RN06 | O sistema verifica se a combinação de data e horário já está ocupada. Essa regra é verificada pelo PHP; o banco documentado não possui uma restrição `UNIQUE` para essa combinação. |
| RN07 | Corte, data e horário são obrigatórios no agendamento; as observações são opcionais. |
| RN08 | O preço do corte deve ser numérico e maior ou igual a zero. Nome e descrição são obrigatórios. |
| RN09 | Excluir um corte também exclui os agendamentos relacionados quando a chave estrangeira utiliza `ON DELETE CASCADE`. |
| RN10 | A quantidade inicial do produto não pode ser negativa, e a saída não deve deixar o estoque abaixo de zero. |
| RN11 | Produtos com cinco unidades ou menos recebem o aviso de estoque baixo; com zero, recebem o aviso de sem estoque. |
| RN12 | Só o administrador marca um atendimento como feito, e essa ação exclui o agendamento da lista. |
| RN13 | Os preços são apresentados em reais, no formato brasileiro, como `R$ 1.234,50`. |

### Requisitos não funcionais

| Código | Categoria | Requisito | Como é atendido |
|---|---|---|---|
| RNF01 | Segurança | Proteger as senhas dos clientes. | O cadastro usa `password_hash()` e o login usa `password_verify()`. |
| RNF02 | Segurança | Evitar que dados digitados sejam executados como SQL. | Consultas preparadas do PDO. |
| RNF03 | Segurança | Evitar que textos sejam interpretados como código HTML. | Uso de `htmlspecialchars()` na saída para HTML. |
| RNF04 | Controle de acesso | Restringir o acesso às páginas internas. | Verificação de sessão e de perfil administrativo. |
| RNF05 | Integridade | Validar os dados antes de gravar. | Validações no servidor e restrições do banco. |
| RNF06 | Usabilidade | Informar o resultado das ações. | Mensagens, confirmações e avisos visuais. |
| RNF07 | Robustez | Evitar repetição de ações ao recarregar a página. | Redirecionamento após o envio de formulários, quando implementado. |
| RNF08 | Manutenibilidade | Facilitar alterações no código. | Organização dos arquivos em pastas e reutilização de includes. |
| RNF09 | Compatibilidade | Executar no ambiente adequado. | PHP com `pdo_pgsql`, PostgreSQL e navegadores atuais. |

### 3.2 Segurança

O projeto utiliza PHP com PDO para acessar o PostgreSQL. Entre os cuidados importantes estão:

- Utilizar consultas preparadas para enviar dados ao banco.
- Validar os dados recebidos dos formulários no servidor.
- Proteger as páginas administrativas contra acessos não autorizados.
- Encerrar a sessão quando o usuário sair.
- Evitar expor senhas e credenciais no GitHub.
- Fazer cópias de segurança antes de alterações importantes no banco.

---

## 4. Arquitetura e funcionamento

O sistema tem dois caminhos de uso, um para o cliente e outro para o administrador.

### 4.1 Fluxo do cliente

1. O cliente abre o sistema. Sem uma sessão ativa, é levado para a tela de login.
2. Se não tiver uma conta, clica em **Cadastrar**, preenche os dados e volta ao login.
3. Entra no sistema e visualiza os cortes disponíveis e seus preços.
4. Clica em **Agendar este corte**.
5. Escolhe a data e o horário e adiciona observações, se desejar.
6. Envia o formulário.
7. Se o horário já estiver ocupado, o sistema informa o problema. Caso contrário, confirma o agendamento.
8. Ao terminar, o cliente utiliza a opção de sair para encerrar a sessão.

### 4.2 Fluxo do administrador

O administrador utiliza a mesma tela de login. Depois de entrar, tem acesso às áreas administrativas.

| Área | Funcionamento |
|---|---|
| Cortes (`app/cortes.php`) | Cadastra, edita e exclui cortes. |
| Estoque (`app/estoque.php`) | Cadastra produtos e registra entradas e saídas de quantidade. |
| Agenda (`app/agenda.php`) | Consulta os agendamentos e pode marcar atendimentos como feitos. |

### 4.3 Como uma página funciona por dentro

As páginas da pasta `app/` seguem, de modo geral, esta sequência:

1. Carregam `login/verifica.php`, que confere a sessão.
2. Carregam `database/connect.php`, que abre a conexão PDO.
3. Se houver envio de formulário (`POST`), validam os campos e executam a ação.
4. Buscam os dados necessários com consultas SQL, como `SELECT`.
5. Montam a página HTML e incluem elementos compartilhados, como o menu de navegação.

### 4.4 Conexão com o banco

O arquivo `database/connect.php` é responsável por configurar a conexão com o PostgreSQL.

O fluxo básico é:

1. O usuário interage com uma página.
2. O PHP valida os dados recebidos.
3. A aplicação acessa o banco utilizando PDO.
4. O PostgreSQL consulta ou modifica os registros.
5. O sistema apresenta o resultado na página.

A conexão depende do endereço do servidor, do nome do banco, do usuário e da senha configurados no ambiente.

---

## 5. Estrutura do banco de dados

O sistema utiliza um banco PostgreSQL chamado `salao`, com cinco tabelas principais.

### 5.1 Organização dos arquivos

```text
mini-sistema/
├── index.php
├── app/
│   ├── agenda.php
│   ├── cortes.php
│   ├── estoque.php
│   ├── marcar_feito.php
│   └── select.php
├── database/
│   ├── connect.php
│   └── salao.sql
├── include/
│   ├── functions.php
│   ├── header.php
│   └── footer.php
├── login/
│   ├── login.php
│   ├── cadastrar.php
│   ├── logout.php
│   └── verifica.php
├── style/
└── imagens/
```

### 5.2 Funções em `include/functions.php`

| Função | O que faz |
|---|---|
| `consultar_user($conexao, $email)` | Busca um cliente pelo e-mail na tabela `usuarios`. |
| `consultar_admin($conexao, $email)` | Busca um administrador pelo e-mail na tabela `administradores`. |
| `cadastrar_usuario($conexao, $email, $senha)` | Insere um usuário. O cadastro real também deve ser conferido em `login/cadastrar.php`. |

### 5.3 Tabelas principais

| Tabela | Finalidade | Colunas usadas pelo código |
|---|---|---|
| `usuarios` | Armazenar as contas dos clientes. | `id`, `nome`, `email`, `senha` |
| `administradores` | Armazenar as contas administrativas. | `id`, `nome`, `email`, `senha` |
| `cortes` | Armazenar os cortes e seus preços. | `id`, `nome`, `descricao`, `preco` |
| `estoque` | Armazenar produtos, fornecedores e quantidades. | `id`, `nome`, `categoria`, `fornecedor`, `quantidade`, `preco_unitario` |
| `agendamentos` | Armazenar os horários marcados. | `id`, `cliente_id`, `corte_id`, `data`, `horario`, `observacoes`, `status` |

### 5.4 Relacionamentos

| De | Para | Regra |
|---|---|---|
| `agendamentos.cliente_id` | `usuarios.id` | Cada agendamento pertence a um cliente. |
| `agendamentos.corte_id` | `cortes.id` | Cada agendamento está associado a um corte. Com `ON DELETE CASCADE`, apagar o corte também apaga os agendamentos relacionados. |

As tabelas `administradores` e `estoque` não possuem relacionamentos com as demais tabelas, conforme a estrutura documentada.

### 5.5 Estrutura do banco (`salao.sql`)

O arquivo `mini-sistema/database/salao.sql` contém a estrutura exportada do banco de dados. O resumo abaixo apresenta as tabelas documentadas:

```sql
CREATE TABLE usuarios (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL
);

CREATE TABLE administradores (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL
);

CREATE TABLE cortes (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    preco NUMERIC(10,2) NOT NULL,
    descricao TEXT
);

CREATE TABLE estoque (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    categoria VARCHAR(50) NOT NULL,
    fornecedor VARCHAR(100) NOT NULL,
    quantidade INTEGER NOT NULL,
    preco_unitario NUMERIC(10,2) NOT NULL
);

CREATE TABLE agendamentos (
    id SERIAL PRIMARY KEY,
    cliente_id INTEGER NOT NULL REFERENCES usuarios(id),
    corte_id INTEGER NOT NULL REFERENCES cortes(id) ON DELETE CASCADE,
    data DATE NOT NULL,
    horario TIME NOT NULL,
    observacoes VARCHAR(500),
    status VARCHAR(20) NOT NULL DEFAULT 'Pendente'
);
```

O arquivo SQL documentado contém a estrutura das tabelas, mas não os registros existentes no banco original. Também é importante observar que as regras de preço não negativo, quantidade não negativa e horário único dependem das validações da aplicação, caso não estejam definidas como restrições no banco.

### 5.6 Arquivo SQL e dados iniciais

Como o `salao.sql` contém apenas a estrutura, restaurá-lo não recupera automaticamente os cortes, produtos, clientes e agendamentos que existiam anteriormente.

Para exportar os dados separadamente, pode-se utilizar um comando como:

```bash
pg_dump -h SEU_HOST -U SEU_USUARIO -d salao --data-only --inserts --no-owner -f dados.sql
```

Substitua `SEU_HOST` e `SEU_USUARIO` pelos valores corretos do ambiente. O comando solicita acesso ao banco e cria o arquivo `dados.sql`.

Para restaurar a estrutura e depois os dados em outro ambiente, primeiro crie o banco e o usuário necessários. Em seguida, execute os arquivos na ordem correta:

```bash
psql -U salao -d salao -f mini-sistema/database/salao.sql
psql -U salao -d salao -f dados.sql
```

Guarde os arquivos de backup em local protegido. Não publique dumps que contenham dados pessoais reais.

---

## 6. Instalação e execução do sistema

Para executar o projeto, é necessário baixar o código, configurar o banco de dados, configurar a conexão, criar o primeiro administrador e iniciar o servidor web.

**Importante:** o código pode utilizar caminhos fixos como `/sistema-de-cabeleireiro/mini-sistema/`. Por isso, a pasta de instalação precisa corresponder aos caminhos utilizados nos links e redirecionamentos.

O nome do repositório pode ser diferente do nome da pasta criada no computador. Confira ambos antes de executar os comandos.

### 6.1 Pré-requisitos

Antes de executar o projeto, verifique se o ambiente possui:

- PHP compatível com o código.
- Extensão `pdo_pgsql` habilitada.
- PostgreSQL instalado e em execução.
- Git, caso o projeto seja baixado do GitHub.
- Apache ou servidor PHP configurado.

No terminal, verifique as versões:

```bash
php -v
psql --version
git --version
```

Para conferir se a extensão aparece no PHP:

```bash
php -m | grep pdo_pgsql
```

No PowerShell, utilize:

```powershell
php -m | Select-String pdo_pgsql
```

Se a extensão não aparecer, verifique a instalação e a configuração do PHP.

### 6.2 Baixar o projeto

No ambiente em que o projeto será executado, clone o repositório:

```bash
git clone https://github.com/KauamDeSouzaBelsi/sistema-de-cabeleleiro.git sistema-de-cabeleireiro
```

Entre na pasta:

```bash
cd sistema-de-cabeleireiro
```

Com Apache, coloque o projeto na pasta pública do servidor, como `C:\xampp\htdocs` no XAMPP ou `/var/www/html` no Linux.

### 6.3 Criar o banco de dados

O código espera um banco chamado `salao` e um usuário chamado `salao`.

Se eles ainda não existirem, um administrador do PostgreSQL poderá criá-los. Exemplo:

```bash
psql -U postgres -c "CREATE USER salao WITH PASSWORD 'SUA_SENHA';"
psql -U postgres -c "CREATE DATABASE salao OWNER salao;"
```

Substitua `SUA_SENHA` por uma senha segura. Não publique a senha real no GitHub.

Depois, carregue a estrutura do banco:

```bash
psql -U salao -d salao -f mini-sistema/database/salao.sql
```

A execução depende da configuração de autenticação do PostgreSQL e das permissões do usuário.

### 6.4 Configurar a conexão

Abra o arquivo:

```text
mini-sistema/database/connect.php
```

Confira as variáveis de conexão. Um exemplo de configuração é:

```php
$host = "localhost";
$dbname = "salao";
$user = "salao";
$pass = "SUA_SENHA";
```

- `$host`: endereço do servidor PostgreSQL.
- `$dbname`: nome do banco de dados.
- `$user`: usuário do banco.
- `$pass`: senha desse usuário.

Os valores devem corresponder ao ambiente em que o banco está instalado. Não publique a senha real no GitHub.

### 6.5 Criar o primeiro administrador

A tela pública de cadastro é destinada aos clientes. O administrador é cadastrado diretamente na tabela `administradores`.

O exemplo abaixo utiliza as credenciais de estudo documentadas neste projeto:

```bash
psql -U salao -d salao -c "INSERT INTO administradores (nome, email, senha) VALUES ('Administrador', 'qwe@admin.com', 'qwe123');"
```

No login do administrador, use:

- **E-mail:** `qwe@admin.com`
- **Senha:** `qwe123`

**Observação de segurança:** o código documentado compara a senha do administrador em texto puro. Isso não é recomendado para um sistema publicado. Antes de utilizar dados reais, o login administrativo deve ser atualizado para armazenar senhas com `password_hash()` e verificá-las com `password_verify()`. Essas credenciais são apenas exemplos para um ambiente de estudo.

### 6.6 Iniciar o site

Para utilizar o servidor embutido do PHP, execute o comando a partir da pasta apropriada do projeto:

```bash
php -S localhost:8000
```

Abra no navegador o endereço correspondente à localização real do projeto. Por exemplo, se o projeto estiver na pasta `sistema-de-cabeleireiro/mini-sistema/`, o endereço pode ser:

```text
http://localhost:8000/sistema-de-cabeleireiro/mini-sistema/login/login.php
```

Deixe o terminal aberto enquanto utiliza o site. Para interromper o servidor, pressione `Ctrl+C`.

Com Apache (XAMPP ou Linux), inicie o Apache e abra a URL correspondente à pasta em que o projeto foi instalado.

O teste inicial deve confirmar que a tela de login aparece, que o administrador consegue entrar e que uma conta criada como cliente consegue visualizar os cortes e realizar agendamentos.

### Atualizar o projeto

Se o repositório já estiver clonado e você quiser buscar as atualizações, entre na pasta do projeto e execute:

```bash
git pull
```

### Possíveis erros durante a instalação

| Problema | Causa provável | O que verificar |
|---|---|---|
| `could not find driver` | A extensão `pdo_pgsql` não está ativa. | Ative a extensão na configuração do PHP e reinicie o servidor. |
| `SQLSTATE[08006]` ou `connection refused` | O PHP não conseguiu conectar ao PostgreSQL. | Confira host, banco, usuário, senha e se o PostgreSQL está ligado. |
| `relation "cortes" does not exist` | As tabelas não foram criadas. | Confira se o arquivo `salao.sql` foi carregado no banco correto. |
| `role "salao" does not exist` | O usuário do banco não existe. | Crie o usuário antes de carregar o arquivo SQL. |
| Erro relacionado a `\restrict` ou `\unrestrict` | Pode haver incompatibilidade entre versões do PostgreSQL. | Confira as versões utilizadas para gerar e restaurar o dump. Preserve uma cópia original antes de qualquer alteração. |
| Página 404 ou links quebrados | O caminho da instalação não corresponde aos caminhos usados no código. | Confira o nome da pasta e os links. |
| Página sem estilo | O navegador pode estar usando CSS antigo. | Atualize a página com `Ctrl+F5`. |

---

## 7. Processo de geração do dump

O dump é uma exportação do banco de dados para um arquivo que pode ser utilizado para recuperar sua estrutura e seus dados.

### 7.1 Verificar se o banco existe

No MobaXterm, execute:

```bash
sudo -u postgres psql -l
```

A listagem deve apresentar o banco `salao`, caso ele exista nesse servidor.

### 7.2 Verificar as tabelas

Execute:

```bash
sudo -u postgres psql -d salao
```

Dentro do PostgreSQL, execute:

```text
\dt
```

Confira se as tabelas esperadas aparecem. Para sair do terminal do PostgreSQL, execute:

```text
\q
```

### 7.3 Gerar o dump

No terminal do servidor, execute:

```bash
sudo -u postgres pg_dump salao > /tmp/dump_salao.sql
```

Esse comando exporta a estrutura e os dados acessíveis do banco `salao`. O arquivo será gravado em:

```text
/tmp/dump_salao.sql
```

### 7.4 Verificar o arquivo

Confira a existência e o tamanho do arquivo:

```bash
ls -lh /tmp/dump_salao.sql
```

Confira as primeiras linhas:

```bash
head -n 30 /tmp/dump_salao.sql
```

Verifique alguns comandos SQL presentes no arquivo:

```bash
grep -E 'CREATE TABLE|COPY |INSERT INTO' /tmp/dump_salao.sql
```

A ausência de um desses comandos, isoladamente, não comprova que o dump esteja incorreto. O conteúdo depende das opções utilizadas na exportação e dos dados existentes.

### 7.5 Resultado esperado

- O banco `salao` existe e pode ser acessado.
- O comando de exportação termina sem erros.
- O arquivo é criado e contém conteúdo SQL.
- O arquivo corresponde ao tipo de backup desejado: estrutura, dados ou ambos.

---

## 8. Processo de restauração do banco

O teste de restauração deve ser realizado em um banco separado para evitar alterar o banco original.

### 8.1 Criar o banco de teste

Execute:

```bash
sudo -u postgres createdb salao_teste
```

Se o banco já existir, não tente criá-lo novamente sem verificar se ele pode ser reutilizado ou se deve ser recriado com autorização do responsável pelo servidor.

### 8.2 Restaurar o arquivo

Execute:

```bash
sudo -u postgres psql -d salao_teste -f /tmp/dump_salao.sql
```

O comando executa os comandos SQL contidos no arquivo no banco de teste.

O resultado esperado é que a restauração termine sem erros que impeçam a recuperação dos objetos e dos dados.

### 8.3 Conferir as tabelas restauradas

Abra o banco de teste:

```bash
sudo -u postgres psql -d salao_teste
```

Dentro do PostgreSQL, execute:

```text
\dt
```

Para conferir a quantidade de registros de uma tabela, por exemplo:

```sql
SELECT COUNT(*) FROM cortes;
```

O resultado permite verificar quantos registros foram restaurados na tabela consultada.

Para sair:

```text
\q
```

### 8.4 Limpeza do ambiente de teste

Após concluir e registrar os resultados, o banco `salao_teste` pode ser mantido para novas verificações ou removido, caso não seja mais necessário e exista autorização para isso.

**Não remova nem sobrescreva o banco original durante o teste.**

---

## 9. Verificação após a restauração

Utilize esta lista para registrar o resultado real de cada verificação:

- [ ] O PostgreSQL está em execução.
- [ ] O banco `salao` está disponível.
- [ ] A versão do PostgreSQL foi registrada.
- [ ] O arquivo de dump foi criado.
- [ ] O arquivo possui conteúdo.
- [ ] A restauração no banco `salao_teste` terminou sem erros.
- [ ] As tabelas esperadas aparecem no banco de teste.
- [ ] A quantidade de registros foi conferida nas tabelas relevantes.
- [ ] A conexão PDO foi conferida no ambiente PHP.
- [ ] A aplicação foi aberta no navegador.
- [ ] As funcionalidades principais foram testadas.

Marque uma etapa somente quando ela tiver sido realmente executada e conferida.

---

## 10. Testes funcionais

| Teste | Resultado esperado |
|---|---|
| Abrir a página de login | A tela de login é exibida. |
| Cadastrar um cliente | Os dados são armazenados no banco. |
| Entrar como cliente | Os cortes disponíveis são apresentados. |
| Agendar um corte | O agendamento é gravado. |
| Tentar usar um horário ocupado | O sistema informa que o horário não está disponível. |
| Entrar como administrador | As opções administrativas aparecem. |
| Cadastrar e editar cortes | Os dados são salvos corretamente. |
| Cadastrar produtos no estoque | Os produtos aparecem na listagem. |
| Registrar entrada ou saída | A quantidade é atualizada. |
| Marcar atendimento como feito | O agendamento deixa de aparecer na lista, conforme a regra implementada. |
| Sair da conta | A sessão é encerrada. |

---

## 11. Possíveis erros e soluções

| Problema | O que verificar |
|---|---|
| Banco `salao` inexistente | Confirmar o nome e o servidor em que o banco foi criado. |
| Erro ao executar `pg_dump` | Conferir permissões, acesso ao banco e mensagem de erro. |
| Arquivo vazio | Conferir se a exportação terminou corretamente. |
| Erro ao restaurar | Analisar a mensagem do PostgreSQL e a compatibilidade das versões. |
| Tabelas ausentes | Conferir o conteúdo do dump e se a restauração foi executada no banco correto. |
| `could not find driver` | Verificar a extensão `pdo_pgsql`. |
| Erro de conexão PHP | Conferir endereço, usuário, senha, nome do banco e serviço PostgreSQL. |
| Página 404 | Conferir o caminho do projeto e os caminhos dos links. |
| Tela em branco | Consultar os registros de erro do PHP e do servidor web. |

---

## 12. Segurança e recomendações

- Não publicar senhas reais no repositório.
- Não armazenar dumps com dados pessoais reais em repositórios públicos.
- Guardar cópias de segurança em local protegido e com permissões adequadas.
- Fazer um backup antes de qualquer restauração sobre um banco existente.
- Testar restaurações em bancos separados.
- Não considerar a existência do arquivo como prova suficiente de que o backup pode ser restaurado.
- Registrar as versões do PostgreSQL e os resultados dos testes.
- Usar uma conta do banco com permissões adequadas à aplicação.
- Evitar expor a porta do PostgreSQL desnecessariamente à rede.
- Utilizar mecanismos seguros para armazenar e verificar senhas.

---

## 13. Registro de evidências

Para documentar a execução no MobaXterm, registre as seguintes evidências:

| Etapa | Evidência |
|---|---|
| Verificação da versão | Saída de `psql --version`. |
| Verificação do banco | Saída de `sudo -u postgres psql -l`. |
| Verificação das tabelas | Saída do comando `\dt`. |
| Geração do dump | Comando utilizado e resultado. |
| Existência do arquivo | Saída de `ls -lh /tmp/dump_salao.sql`. |
| Restauração | Resultado do comando de restauração no banco de teste. |
| Conferência pós-restauração | Tabelas e contagens de registros. |
| Teste da aplicação | Capturas de tela do login, cortes, agenda e estoque. |

As evidências devem corresponder aos testes realizados no ambiente real. Se uma etapa ainda não foi executada, registre-a como pendente.

---

## 14. Conclusão

A documentação reúne as principais funcionalidades do Sistema de Cabeleireiro e descreve como instalar, executar, verificar e recuperar seu banco de dados PostgreSQL.

A geração do dump permite criar uma cópia do banco, enquanto o teste de restauração em um banco separado verifica se o arquivo pode ser utilizado para recuperar a estrutura e os dados exportados.

A conclusão do procedimento depende dos resultados reais obtidos no servidor. Por isso, as verificações e os testes funcionais devem ser executados e registrados antes de considerar o processo concluído.

**Projeto:** Sistema de Cabeleireiro  
**Tecnologias:** PHP, HTML, CSS e PostgreSQL  
**Repositório:** [sistema-de-cabeleleiro no GitHub](https://github.com/KauamDeSouzaBelsi/sistema-de-cabeleleiro)

---

## 15. Fluxo de login do administrador e do cliente

O sistema utiliza a mesma tela de login para os dois perfis, mas consulta tabelas diferentes no banco de dados:

1. O usuário informa e-mail e senha na tela `login/login.php`.
2. O sistema consulta os dados do administrador na tabela `administradores`.
3. Se não encontrar um administrador válido, verifica o usuário na tabela `usuarios`.
4. Quando o login é válido, os dados necessários são guardados na sessão PHP.
5. O sistema utiliza a sessão para manter o usuário conectado e direcioná-lo às páginas permitidas para seu perfil.

O cadastro público é destinado aos clientes. O primeiro administrador é cadastrado diretamente no banco de dados, conforme o procedimento descrito na seção de instalação.

No exemplo documentado, o acesso administrativo é:

- **E-mail:** `qwe@admin.com`
- **Senha:** `qwe123`

**Observação de segurança:** a documentação do código atual informa que a senha do administrador é comparada em texto puro. Isso não é recomendado para um sistema publicado. Antes de usar dados reais, o login do administrador deve ser atualizado para armazenar senhas com `password_hash()` e verificá-las com `password_verify()`. Não publique senhas reais no README, no GitHub ou em capturas de tela.

## 16. Diferença entre o arquivo SQL e o dump completo

O arquivo `mini-sistema/database/salao.sql` documentado neste projeto contém a estrutura das tabelas. Ele não deve ser considerado, sozinho, uma cópia dos registros que estavam no banco original.

- **Arquivo de estrutura:** permite criar as tabelas e seus relacionamentos.
- **Dump com dados:** também exporta os registros existentes, dependendo das opções usadas no comando.
- **Backup completo:** deve ser guardado em local protegido, principalmente quando contém e-mails, contas, agendamentos ou outros dados pessoais.

Não cole o conteúdo de um dump com dados reais dentro deste README. Guarde o arquivo de backup separadamente e não o envie a um repositório público.

## 17. Conferência do caminho do projeto

Os caminhos usados nos links e redirecionamentos do PHP precisam corresponder ao nome da pasta em que o projeto foi instalado.

A documentação utiliza a pasta `sistema-de-cabeleireiro` para executar o sistema, embora o nome do repositório no GitHub possa aparecer escrito de forma diferente.

Depois de clonar o repositório, confirme o nome da pasta criada e confira se o endereço usado no navegador corresponde à localização real do projeto. Se a pasta ou o caminho de instalação mudar, os links e redirecionamentos que usam caminhos fixos também poderão precisar ser ajustados.

## 18. Teste de restauração e compatibilidade

Depois de gerar um dump, teste a restauração em um banco separado, como `salao_teste`, sem sobrescrever o banco utilizado pela aplicação.

Após a restauração:

1. Confira se as tabelas esperadas existem.
2. Confira se os registros esperados foram restaurados, quando o dump inclui dados.
3. Teste o login, a listagem de cortes, os agendamentos e o estoque.
4. Registre os resultados reais. Se não tiver executado algum teste, indique que ele está pendente.

Se aparecer um erro relacionado a comandos do arquivo SQL, como `\restrict` ou `\unrestrict`, verifique as versões do PostgreSQL usadas para gerar e restaurar o arquivo. Não remova linhas do dump sem antes guardar uma cópia original e confirmar a causa do erro.

## 19. Observação sobre os requisitos funcionais

Ao finalizar a documentação, confira se a lista de requisitos funcionais aparece apenas uma vez e se os identificadores estão consistentes.

Se houver listas diferentes, compare-as com as funcionalidades realmente presentes no código antes de consolidá-las. Não marque como implementada uma função que ainda não foi testada.
