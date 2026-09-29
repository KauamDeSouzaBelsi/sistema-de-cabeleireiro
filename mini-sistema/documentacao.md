
#  Mini Sistema em PHP & MySQL

Um mini sistema web responsivo e modular desenvolvido em **PHP** e **MySQL** para gerenciamento de dados e controle de acesso de usuários. Projetado para servir como base sólida de aprendizado, prototipagem ou expansão para sistemas maiores.

---

##  Visão Geral

O **Mini Sistema** é uma aplicação web leve e funcional construída com o objetivo de demonstrar as melhores práticas em operações básicas do desenvolvimento web backend e frontend:

* **Autenticação segura** de usuários.
* **Módulo CRUD completo** para manipulação de registros.
* **Separação modular do código** para facilitar a manutenção e legibilidade.

---

##  Destaques do Projeto

* ⚡ **Leve e Rápido**: Sem dependências pesadas de *frameworks* de terceiros.
* 📦 **Código Modular**: Componentes reaproveitáveis (`header`, `footer`, `connect`, `functions`).
* 🔒 **Proteção de Acesso**: Restrição de rotas para usuários não autenticados.
* 📂 **Pronto para Produção/Estudo**: Estrutura clara que facilita o aprendizado de PHP puro (Vanilla PHP).

---

##  Tecnologias Utilizadas

### Backend & Banco de Dados

* **PHP**: Linguagem backend (versão 7.4+ ou 8.x).
* **MySQL / MariaDB**: Sistema de Gerenciamento de Banco de Dados Relacional (SGBD).
* **PDO / MySQLi**: Conexão e execução de queries no banco.

### Frontend

* **HTML5**: Estruturação semântica das páginas.
* **CSS3**: Estilização customizada (`style/style.css`).

### Ferramentas de Desenvolvimento

* **Apache**: Servidor Web local.
* **XAMPP / WAMP / Laragon**: Ambiente de desenvolvimento integrado.
* **VS Code / PhpStorm**: Editores de código recomendados.

---

##  Estrutura do Projeto

```text
mini_sistema/
│
├── app/                      # Módulo principal da aplicação (CRUD)
│   ├── create.php            # Formulário e ação de inclusão de dados
│   ├── delete.php            # Confirmação e remoção de registros
│   ├── dir.php               # Redirecionamento e navegação interna
│   ├── select.php            # Listagem geral dos registros
│   ├── select_w.php          # Busca parametrizada / Filtros (WHERE)
│   └── update.php            # Formulário e atualização de registros
│
├── database/                 # Conexão e scripts de banco de dados
│   └── connect.php           # Script de conexão PDO/MySQLi
│
├── includes/                 # Componentes reutilizáveis
│   ├── footer.php            # Rodapé padrão do sistema
│   ├── functions.php         # Funções auxiliares (sanitização, helpers, etc.)
│   └── header.php            # Cabeçalho com menu de navegação
│
├── login/                    # Módulo de Autenticação e Usuários
│   ├── cadastrar.php         # Registro de novos usuários
│   ├── login.php             # Formulário de autenticação
│   ├── logout.php            # Destruição da sessão do usuário
│   └── verifica_user.php     # Middleware de verificação de acesso/sessão
│
├── style/                    # Arquivos estáticos de estilo
│   └── style.css             # Folha de estilos centralizada
│
├── documentacao.md           # Documentação técnica interna do projeto
├── index.php                 # Dashboard / Página de boas-vindas
└── mini_sistema.png          # Captura de tela da interface do sistema

```

---

##  Arquitetura e Fluxo do Sistema

1. **Acesso Inicial**: O usuário acessa a página `index.php`.
2. **Verificação de Sessão**: O arquivo `login/verifica_user.php` checa se o usuário está logado.
3. **Login / Cadastro**: Caso não esteja logado, ele é redirecionado para `login/login.php` ou `login/cadastrar.php`.
4. **Navegação & Operações**: Uma vez autenticado, o usuário pode interagir com as funções do CRUD na pasta `app/` (`create`, `select`, `update`, `delete`).

---

##  Funcionalidades Principais

###  Gestão de Acesso e Usuários

* Cadastro de novos usuários com validação de campos.
* Autenticação via formulário de login.
* Controle de sessão ativa (`session_start()`).
* Encerrar sessão com segurança (`logout.php`).

###  Módulo CRUD (Gerenciamento de Registros)

* **Criar (`create.php`)**: Inserção de novos dados no banco.
* **Consultar (`select.php`)**: Visualização em tabela de todos os registros salvos.
* **Filtrar (`select_w.php`)**: Busca por termos ou condições específicas.
* **Editar (`update.php`)**: Formulário pré-preenchido para edição de registros existentes.
* **Excluir (`delete.php`)**: Exclusão segura de dados selecionados.

---

##  Medidas de Segurança

Para garantir a integridade dos dados e do acesso, o sistema implementa:

* **Controle de Sessão**: Impedimento do acesso direto às rotas internas por URL sem login prévio.
* **Sanitização de Dados**: Tratamento de entradas do usuário contra ataques de **XSS** (*Cross-Site Scripting*).
* **Prepared Statements**: Prevenção contra **SQL Injection** em consultas ao banco de dados.

---

##  Modelagem do Banco de Dados

Exemplo genérico do script SQL necessário para criar as tabelas básicas do sistema:

```sql
CREATE DATABASE IF NOT EXISTS mini_sistema;
USE mini_sistema;

-- Tabela de Usuários
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela Principal (Exemplo de registros do sistema)
CREATE TABLE IF NOT EXISTS registros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(150) NOT NULL,
    descricao TEXT,
    usuario_id INT,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

```

---

##  Pré-requisitos e Instalação

### Pré-requisitos

* **PHP** 7.4 ou superior instalado.
* Servidor local (**XAMPP**, **WAMP**, **MAMP** ou **Laragon**).
* Navegador web moderno (Chrome, Firefox, Edge).

---

### Passo a Passo de Instalação

1. **Clonar ou Baixar o Repositório**:
```bash
git clone https://github.com/seu-usuario/mini_sistema.git

```


2. **Mover para o Diretório do Servidor**:
* **XAMPP**: `C:/xampp/htdocs/mini_sistema`
* **WAMP**: `C:/wamp64/www/mini_sistema`
* **Linux (Apache)**: `/var/www/html/mini_sistema`


3. **Configurar o Banco de Dados**:
* Abra o **phpMyAdmin** (`http://localhost/phpmyadmin`).
* Crie um banco de dados chamado `mini_sistema`.
* Execute o script de criação de tabelas mostrado na seção [Modelagem do Banco de Dados](https://www.google.com/search?q=%2523-modelagem-do-banco-de-dados&utm_source=gemini).


4. **Ajustar as Credenciais de Conexão**:
Edite o arquivo `database/connect.php` com suas configurações locais:
```php
$host = 'localhost';
$db   = 'mini_sistema';
$user = 'root';
$pass = ''; // Sua senha do MySQL

```



---

##  Como Executar

1. Inicie os serviços de **Apache** e **MySQL** no painel do seu servidor local (ex: XAMPP Control Panel).
2. Abra o seu navegador e acesse:
```text
http://localhost/mini_sistema/

```


3. Crie um novo usuário em **Cadastrar** e faça o login para explorar o sistema!

---

##  Rotas e Navegação

| Página | Função | Acesso Restrito? |
| --- | --- | --- |
| `index.php` | Painel principal / Dashboard | Sim |
| `login/login.php` | Tela de Login | Não |
| `login/cadastrar.php` | Tela de Cadastro de Usuário | Não |
| `app/select.php` | Listagem geral de registros | Sim |
| `app/create.php` | Cadastro de novo registro | Sim |
| `app/update.php` | Edição de registro existente | Sim |
| `app/delete.php` | Remoção de registro | Sim |
| `login/logout.php` | Encerrar sessão atual | Sim |

---

##  Próximos Passos / Roadmap

* [ ] Implementar criptografia de senha mais robusta (`password_hash` / `password_verify`).
* [ ] Adicionar sistema de níveis de permissão (Administrador / Usuário Comum).
* [ ] Implementar paginação na listagem (`select.php`).
* [ ] Adicionar suporte a upload de arquivos/imagens.
* [ ] Melhorar a interface com **Bootstrap** ou **Tailwind CSS**.

---

##  Contribuição

Contribuições são muito bem-vindas! Se você quer melhorar este projeto:

1. Faça um **Fork** do projeto.
2. Crie uma **Branch** para sua modificação:
```bash
git checkout -b feature/minha-nova-feature

```


3. Faça o **Commit** das suas alterações:
```bash
git commit -m 'feat: adiciona nova funcionalidade'

```


4. Envie para o repositório remoto:
```bash
git push origin feature/minha-nova-feature

```


5. Abra um **Pull Request**.