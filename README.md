# Sistema de Cabeleireiro — Documentação Técnica e Restauração do Banco de Dados

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
19. Observações sobre os requisitos funcionais

---

## 1. Visão geral do projeto

O Sistema de Cabeleireiro é uma aplicação web desenvolvida em PHP e PostgreSQL. O cliente cria uma conta, consulta os cortes disponíveis e agenda um horário. O administrador cadastra os cortes, controla o estoque e acompanha a agenda.

### 1.1 Objetivo

O objetivo do projeto é facilitar o gerenciamento de um salão de cabeleireiro, permitindo que clientes consultem os serviços disponíveis e realizem agendamentos, enquanto o administrador gerencia os cortes, os produtos e os atendimentos.

Esta documentação também apresenta os procedimentos para verificar o banco de dados PostgreSQL, gerar uma cópia de segurança (dump) e testar sua restauração em um banco separado.

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

**Visitante**
- Criar uma conta de cliente.
- Entrar no sistema.

**Cliente**
- Consultar os cortes e seus preços.
- Escolher um corte, uma data e um horário.
- Adicionar observações ao agendamento.

**Administrador**
- Cadastrar, editar e excluir cortes.
- Consultar a agenda.
- Marcar atendimentos como feitos.
- Cadastrar produtos e controlar as quantidades do estoque.
- Consultar alertas de estoque baixo.

O cadastro público é destinado aos clientes. O administrador é cadastrado separadamente no banco de dados.

### 1.4 Organização do projeto

A pasta principal da aplicação é `sistema-de-cabeleireiro`. A pasta `mini-sistema` foi removida, portanto os arquivos estão diretamente na estrutura principal.

```text
sistema-de-cabeleireiro/
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

A estrutura acima resume os principais arquivos documentados. Confira os nomes com a versão atual do projeto para garantir que a documentação corresponda aos arquivos existentes.

---

## 2. Requisitos funcionais

O sistema possui os seguintes requisitos funcionais documentados:

| Código | Requisito | Quem usa | Arquivo principal |
|---|---|---|---|
| RF01 | Criar uma conta de cliente com nome, e-mail e senha | Visitante | `login/cadastrar.php` |
| RF02 | Entrar com e-mail e senha, verificando primeiro os administradores e depois os clientes | Visitante | `login/login.php` |
| RF03 | Sair da conta, encerrando a sessão | Cliente e administrador | `login/logout.php` |
| RF04 | Exibir os cortes disponíveis com nome e preço | Cliente e administrador | `index.php` |
| RF05 | Mostrar a opção de agendamento para o cliente | Cliente | `index.php` |
| RF06 | Mostrar o menu administrativo apenas para o administrador | Administrador | `index.php` |
| RF07 | Cadastrar um corte com nome, descrição e preço | Administrador | `app/cortes.php` |
| RF08 | Editar um corte existente | Administrador | `app/cortes.php` |
| RF09 | Excluir um corte com confirmação | Administrador | `app/cortes.php` |
| RF10 | Agendar um corte com data, horário e observações | Cliente | `app/agenda.php` |
| RF11 | Recusar um horário que já esteja ocupado | Cliente | `app/agenda.php` |
| RF12 | Consultar os agendamentos | Administrador | `app/agenda.php` |
| RF13 | Marcar um atendimento como feito | Administrador | `app/marcar_feito.php` |
| RF14 | Cadastrar produtos no estoque | Administrador | `app/estoque.php` |
| RF15 | Registrar entrada e saída de produtos | Administrador | `app/estoque.php` |
| RF16 | Exibir alertas de estoque baixo ou sem estoque | Administrador | `app/estoque.php` |

Esses requisitos descrevem as funcionalidades documentadas do sistema. Cada uma deve ser testada no ambiente em que o projeto será apresentado.

---

## 3. Regras de negócio e segurança

### 3.1 Regras de negócio

| Código | Regra |
|---|---|
| RN01 | O cadastro público cria contas de clientes. Os administradores são cadastrados separadamente no banco. |
| RN02 | Nome, e-mail e senha são obrigatórios no cadastro, e o e-mail não pode se repetir. |
| RN03 | Páginas internas exigem que o usuário esteja autenticado. |
| RN04 | O login procura primeiro o administrador e depois o cliente. O perfil fica armazenado na sessão. |
| RN05 | Somente o cliente realiza agendamentos; o administrador consulta a agenda. |
| RN06 | O sistema verifica se a data e o horário já estão ocupados. |
| RN07 | Corte, data e horário são obrigatórios no agendamento; as observações são opcionais. |
| RN08 | O preço do corte deve ser numérico e não negativo. |
| RN09 | A exclusão de um corte pode excluir seus agendamentos relacionados devido à regra `ON DELETE CASCADE` documentada no banco. |
| RN10 | A quantidade de produtos não pode ficar negativa durante a saída de estoque. |
| RN11 | Produtos com cinco unidades ou menos recebem o aviso de estoque baixo; produtos com quantidade zero recebem o aviso de falta de estoque. |
| RN12 | Somente o administrador pode marcar um atendimento como feito. Essa ação remove o agendamento da lista conforme a implementação documentada. |
| RN13 | Os preços são apresentados em reais, no formato brasileiro. |

### 3.2 Requisitos não funcionais

| Código | Categoria | Requisito | Implementação documentada |
|---|---|---|---|
| RNF01 | Segurança | Proteger as senhas dos clientes | `password_hash()` e `password_verify()` |
| RNF02 | Segurança | Evitar a execução de dados digitados como SQL | Consultas preparadas do PDO |
| RNF03 | Segurança | Evitar que textos cadastrados sejam interpretados como código HTML | `htmlspecialchars()` |
| RNF04 | Controle de acesso | Restringir páginas internas | Verificação de sessão e de perfil |
| RNF05 | Integridade | Validar os dados recebidos | Validações no PHP e restrições do banco |
| RNF06 | Usabilidade | Informar o resultado das operações | Mensagens e confirmações |
| RNF07 | Robustez | Evitar repetir operações ao recarregar a página | Redirecionamento após envio de formulários |
| RNF08 | Manutenção | Facilitar a organização do código | Arquivos compartilhados em `include/` e conexão em `database/connect.php` |
| RNF09 | Compatibilidade | Executar no ambiente necessário | PHP com `pdo_pgsql`, PostgreSQL e navegador atualizado |

---

## 4. Arquitetura e funcionamento

### 4.1 Fluxo do cliente

1. O visitante abre o sistema.
2. Se não estiver conectado, é direcionado para a tela de login.
3. Caso não tenha conta, realiza o cadastro de cliente.
4. Após entrar, visualiza os cortes disponíveis e seus preços.
5. Seleciona um corte e acessa a agenda.
6. Informa a data, o horário e, se desejar, as observações.
7. O sistema verifica se o horário está ocupado e apresenta o resultado.
8. O cliente pode encerrar a sessão pelo link de saída.

### 4.2 Fluxo do administrador

O administrador utiliza a tela de login e, após a autenticação, tem acesso às áreas administrativas.

| Área | Funcionamento |
|---|---|
| Cortes | Cadastrar, editar e excluir cortes |
| Estoque | Cadastrar produtos e registrar entradas e saídas |
| Agenda | Consultar agendamentos e marcar atendimentos como feitos |

### 4.3 Funcionamento de uma página PHP

As páginas administrativas seguem, de modo geral, este fluxo:

1. Verificar se o usuário está conectado.
2. Carregar a conexão com o banco de dados.
3. Receber e validar os dados enviados por formulários.
4. Executar consultas no PostgreSQL por meio do PDO.
5. Buscar os registros necessários.
6. Exibir os dados em HTML e incluir os elementos de navegação.

### 4.4 Conexão com o banco

O arquivo responsável pela conexão com o PostgreSQL é:

`database/connect.php`

A conexão utiliza PDO. O endereço do servidor, o nome do banco, o usuário e a senha devem corresponder ao ambiente em que a aplicação está executando.

---

## 5. Estrutura do banco de dados

O sistema utiliza um banco PostgreSQL chamado `salao`, com cinco tabelas principais.

### 5.1 Tabelas

| Tabela | Finalidade | Colunas principais |
|---|---|---|
| `usuarios` | Contas de clientes | `id`, `nome`, `email`, `senha` |
| `administradores` | Contas administrativas | `id`, `nome`, `email`, `senha` |
| `cortes` | Cortes e preços | `id`, `nome`, `descricao`, `preco` |
| `estoque` | Produtos do salão | `id`, `nome`, `categoria`, `fornecedor`, `quantidade`, `preco_unitario` |
| `agendamentos` | Horários marcados | `id`, `cliente_id`, `corte_id`, `data`, `horario`, `observacoes`, `status` |

### 5.2 Relacionamentos

| De | Para | Regra |
|---|---|---|
| `agendamentos.cliente_id` | `usuarios.id` | Cada agendamento pertence a um cliente. |
| `agendamentos.corte_id` | `cortes.id` | Cada agendamento está relacionado a um corte. |

A chave estrangeira de `agendamentos.corte_id` utiliza `ON DELETE CASCADE` na estrutura documentada. Assim, excluir um corte também pode excluir os agendamentos relacionados a ele.

As tabelas `administradores` e `estoque` não possuem relacionamentos com as demais tabelas na estrutura descrita.

### 5.3 Arquivo SQL

O arquivo `database/salao.sql` contém a estrutura do banco de dados exportada para SQL.

**Importante:** a versão documentada desse arquivo contém a estrutura das tabelas, mas não os registros de clientes, cortes, produtos e agendamentos. Para transferir também os registros existentes, é necessário gerar um dump que inclua os dados.

---

## 6. Instalação e execução do sistema

### 6.1 Pré-requisitos

Antes de executar o projeto, verifique se o ambiente possui:

- PHP compatível com o código.
- Extensão `pdo_pgsql` habilitada.
- PostgreSQL instalado e em execução.
- Git, caso o projeto seja baixado do GitHub.
- Apache ou servidor PHP configurado.

Para conferir as versões:

```bash
php -v
psql --version
git --version
```

Para verificar se a extensão está habilitada no Linux:

```bash
php -m | grep pdo_pgsql
```

No PowerShell, pode-se usar:

```powershell
php -m | Select-String pdo_pgsql
```

### 6.2 Baixar o projeto

No terminal, execute:

```bash
git clone https://github.com/KauamDeSouzaBelsi/sistema-de-cabeleleiro.git sistema-de-cabeleireiro
cd sistema-de-cabeleireiro
```

O nome do repositório no GitHub é `sistema-de-cabeleleiro`, enquanto a pasta local utilizada nesta documentação é `sistema-de-cabeleireiro`.

### 6.3 Criar o banco de dados

Se o usuário e o banco ainda não existirem, um administrador do PostgreSQL poderá criá-los:

```bash
psql -U postgres -c "CREATE USER salao WITH PASSWORD 'SUA_SENHA';"
psql -U postgres -c "CREATE DATABASE salao OWNER salao;"
```

Substitua `SUA_SENHA` por uma senha segura. Se o usuário ou o banco já existirem, verifique sua configuração em vez de executar os comandos novamente.

Para carregar a estrutura das tabelas, execute a partir da pasta principal do projeto:

```bash
psql -U salao -d salao -f database/salao.sql
```

### 6.4 Configurar a conexão

Abra o arquivo:

`database/connect.php`

Confira se as configurações correspondem ao banco utilizado:

```php
$host = "localhost";
$dbname = "salao";
$user = "salao";
$pass = "SUA_SENHA";
```

Os valores são exemplos. Utilize a senha configurada no PostgreSQL e não publique credenciais reais no GitHub.

### 6.5. Criar o administrador no banco de dados

O administrador é cadastrado separadamente dos clientes. Dessa forma, o usuário que realiza o cadastro público no site não pode escolher se deseja ser administrador.

Para criar o administrador diretamente no PostgreSQL pelo MobaXterm, siga os passos abaixo.

**Passo 1 — Acessar o banco de dados**

No terminal do servidor, execute:

```bash
sudo -u postgres psql -d salao
```

**Passo 2 — Inserir o administrador**

Se a tabela `administradores` possuir as colunas `nome`, `email` e `senha`, execute:

```sql
INSERT INTO administradores (nome, email, senha)
VALUES ('Administrador', 'qwe@admin.com', 'qwe123');
```

**Passo 3 — Conferir se o administrador foi criado**

Execute:

```sql
SELECT id, nome, email FROM administradores;
```

Se os dados aparecerem na consulta, o registro foi inserido.

**Passo 4 — Sair do PostgreSQL**

```sql
\q
```

**Passo 5 — Entrar no sistema**

Abra a página de login e informe o e-mail e a senha cadastrados:

- E-mail: `qwe@admin.com`
- Senha: `qwe123`

Essas são credenciais de exemplo para o ambiente de estudo. Se a aplicação utiliza `password_verify()` para validar senhas, a senha deve ser armazenada com `password_hash()` em vez de texto puro. O procedimento de inserção precisa corresponder à validação implementada no arquivo de login.

**Importante:** antes de executar o `INSERT`, confira se o administrador já existe para evitar duplicar o registro. Não utilize essas credenciais em um ambiente público.


### 6.6 Iniciar o sistema

Com o XAMPP, coloque a pasta `sistema-de-cabeleireiro` dentro de `htdocs`, inicie o Apache e acesse:

```text
http://localhost/sistema-de-cabeleireiro/login/login.php
```

A página inicial pode ser acessada por:

```text
http://localhost/sistema-de-cabeleireiro/index.php
```

Se utilizar o servidor embutido do PHP, o comando e a URL dependem da pasta a partir da qual o servidor for iniciado. Os caminhos devem corresponder à localização real do projeto.

### 6.7 Atualizar o projeto

Para buscar alterações do repositório:

```bash
git pull
```

---

## 7. Processo de geração do dump

O dump é uma exportação do banco de dados para um arquivo que pode ser utilizado para recuperar a estrutura e, conforme o comando usado, os registros existentes.

### 7.1 Verificar se o banco existe

No terminal do servidor acessado pelo MobaXterm, execute:

```bash
sudo -u postgres psql -l
```

Verifique se o banco `salao` aparece na listagem.

### 7.2 Verificar as tabelas

Acesse o banco:

```bash
sudo -u postgres psql -d salao
```

Dentro do PostgreSQL, execute:

```sql
\dt
```

Confira se as tabelas esperadas aparecem. Para sair do terminal interativo:

```sql
\q
```

### 7.3 Gerar o dump

Para exportar o banco `salao` para um arquivo SQL:

```bash
sudo -u postgres pg_dump salao > /tmp/dump_salao.sql
```

O arquivo será salvo em `/tmp/dump_salao.sql`. O comando exporta a estrutura e os dados acessíveis do banco, conforme o conteúdo existente e as permissões utilizadas.

### 7.4 Verificar o arquivo

Confira se o arquivo existe e seu tamanho:

```bash
ls -lh /tmp/dump_salao.sql
```

Visualize as primeiras linhas:

```bash
head -n 30 /tmp/dump_salao.sql
```

Para procurar comandos comuns de criação e inserção:

```bash
grep -E 'CREATE TABLE|COPY |INSERT INTO' /tmp/dump_salao.sql
```

A ausência de um desses comandos, isoladamente, não comprova que o dump está incorreto. O conteúdo depende das opções de exportação e dos dados existentes.

### 7.5 Resultado esperado

- O banco `salao` existe e pode ser acessado.
- O comando de exportação termina sem erros.
- O arquivo é criado e contém conteúdo SQL.
- O conteúdo corresponde ao tipo de backup desejado.

---

## 8. Processo de restauração do banco

A restauração deve ser testada em um banco separado para evitar alterações no banco original.

### 8.1 Criar o banco de teste

```bash
sudo -u postgres createdb salao_teste
```

Se o banco já existir, verifique se ele pode ser reutilizado ou se deve ser recriado com autorização do responsável pelo servidor.

### 8.2 Restaurar o arquivo

```bash
sudo -u postgres psql -d salao_teste -f /tmp/dump_salao.sql
```

O comando executa no banco de teste as instruções SQL contidas no arquivo.

### 8.3 Conferir as tabelas restauradas

Acesse o banco de teste:

```bash
sudo -u postgres psql -d salao_teste
```

Liste as tabelas:

```sql
\dt
```

Para conferir a quantidade de registros de uma tabela, por exemplo:

```sql
SELECT COUNT(*) FROM cortes;
```

Para sair:

```sql
\q
```

### 8.4 Limpeza do ambiente de teste

Após registrar os resultados, o banco `salao_teste` pode ser mantido para verificações futuras ou removido se não for mais necessário e houver autorização.

Não remova nem sobrescreva o banco original durante o teste.

---

## 9. Verificação após a restauração

Utilize a lista abaixo para registrar o resultado real de cada verificação:

- [ ] O PostgreSQL está em execução.
- [ ] O banco `salao` está disponível.
- [ ] A versão do PostgreSQL foi registrada.
- [ ] O arquivo de dump foi criado.
- [ ] O arquivo possui conteúdo.
- [ ] A restauração no banco `salao_teste` terminou sem erros impeditivos.
- [ ] As tabelas esperadas aparecem no banco de teste.
- [ ] A quantidade de registros foi conferida nas tabelas relevantes.
- [ ] A conexão PDO foi verificada no ambiente PHP.
- [ ] A aplicação foi aberta no navegador.
- [ ] As funcionalidades principais foram testadas.

Marque uma etapa somente depois de executá-la e conferir o resultado.

---

## 10. Testes funcionais

| Teste | Resultado esperado |
|---|---|
| Abrir a página de login | A tela de login é exibida |
| Cadastrar um cliente | Os dados são armazenados no banco |
| Entrar como cliente | Os cortes disponíveis são apresentados |
| Agendar um corte | O agendamento é gravado |
| Tentar utilizar um horário ocupado | O sistema informa que o horário não está disponível |
| Entrar como administrador | As opções administrativas aparecem |
| Cadastrar e editar cortes | Os dados são salvos corretamente |
| Cadastrar produtos no estoque | Os produtos aparecem na listagem |
| Registrar entrada ou saída | A quantidade é atualizada |
| Marcar atendimento como feito | O agendamento deixa de aparecer na lista, conforme a regra implementada |
| Sair da conta | A sessão é encerrada |

Registre os resultados reais dos testes no ambiente em que o sistema será apresentado.

---

## 11. Possíveis erros e soluções

| Problema | O que verificar |
|---|---|
| Banco `salao` inexistente | Confirmar o nome do banco e o servidor em que foi criado |
| Erro ao executar `pg_dump` | Conferir permissões, acesso ao banco e mensagem de erro |
| Arquivo vazio | Conferir se a exportação terminou corretamente |
| Erro ao restaurar | Analisar a mensagem do PostgreSQL e a compatibilidade das versões |
| Tabelas ausentes | Conferir o conteúdo do dump e se a restauração foi feita no banco correto |
| `could not find driver` | Verificar se a extensão `pdo_pgsql` está habilitada |
| Erro de conexão PHP | Conferir endereço, usuário, senha, nome do banco e serviço PostgreSQL |
| Página 404 | Conferir o caminho do projeto e os caminhos dos links e redirecionamentos |
| Tela em branco | Consultar os registros de erro do PHP e do servidor web |
| Links do menu quebrados | Conferir os caminhos definidos nos arquivos PHP |

---

## 12. Segurança e recomendações

- Não publicar senhas reais no repositório.
- Não armazenar dumps com dados pessoais reais em repositórios públicos.
- Guardar cópias de segurança em local protegido.
- Fazer um backup antes de alterações importantes no banco.
- Testar restaurações em bancos separados.
- Não considerar a existência do arquivo como prova suficiente de que o backup pode ser restaurado.
- Registrar as versões do PostgreSQL e os resultados dos testes.
- Utilizar uma conta do banco com as permissões adequadas.
- Evitar expor desnecessariamente a porta do PostgreSQL à rede.
- Utilizar `password_hash()` e `password_verify()` para proteger senhas.

---

## 13. Registro de evidências

Para documentar a execução pelo MobaXterm, registre:

| Etapa | Evidência |
|---|---|
| Verificação da versão | Saída de `psql --version` |
| Verificação do banco | Saída de `sudo -u postgres psql -l` |
| Verificação das tabelas | Saída do comando `\dt` |
| Geração do dump | Comando utilizado e resultado |
| Existência do arquivo | Saída de `ls -lh /tmp/dump_salao.sql` |
| Restauração | Resultado do comando de restauração |
| Conferência após a restauração | Tabelas e contagens de registros |
| Teste da aplicação | Capturas de tela do login, cortes, agenda e estoque |

As evidências devem corresponder aos testes realizados no ambiente real. Se uma etapa ainda não foi executada, registre-a como pendente.

---

## 14. Conclusão

Esta documentação reúne as principais funcionalidades do Sistema de Cabeleireiro e descreve como instalar, executar, verificar e recuperar seu banco de dados PostgreSQL.

A geração do dump permite criar uma cópia do banco, enquanto o teste de restauração em um banco separado verifica se o arquivo pode ser utilizado para recuperar a estrutura e os dados exportados.

A conclusão do procedimento depende dos resultados obtidos no servidor. Por isso, as verificações e os testes funcionais devem ser executados e registrados antes de considerar o processo concluído.

**Projeto:** Sistema de Cabeleireiro  
**Tecnologias:** PHP, HTML, CSS e PostgreSQL  
**Repositório:** [sistema-de-cabeleleiro](https://github.com/KauamDeSouzaBelsi/sistema-de-cabeleleiro)

---

## 15. Fluxo de login do administrador e do cliente

O sistema utiliza a mesma tela de login para os dois perfis, mas consulta tabelas diferentes no banco de dados:

1. O usuário informa e-mail e senha na tela `login/login.php`.
2. O sistema consulta o administrador na tabela `administradores`.
3. Se não encontrar um administrador válido, verifica o usuário na tabela `usuarios`.
4. Quando o login é válido, os dados necessários são guardados na sessão PHP.
5. A sessão é utilizada para manter o usuário conectado e direcioná-lo às páginas permitidas para seu perfil.

O cadastro público é destinado aos clientes. O administrador é cadastrado diretamente no banco de dados, conforme o procedimento descrito na seção 6.

**Observação de segurança:** a documentação anterior descreve uma comparação de senha administrativa em texto puro. Isso não é recomendado para um sistema publicado. Não utilize credenciais de exemplo em uma instalação com dados reais e não publique senhas no README ou no GitHub.

---

## 16. Diferença entre o arquivo SQL e o dump completo

O arquivo `database/salao.sql` contém a estrutura das tabelas, conforme a versão documentada. Ele não deve ser considerado, sozinho, uma cópia dos registros que estavam no banco original.

- **Arquivo de estrutura:** permite criar as tabelas e seus relacionamentos.
- **Dump com dados:** também exporta os registros existentes, dependendo das opções utilizadas.
- **Backup completo:** deve ser guardado em local protegido, principalmente quando contém e-mails, contas, agendamentos ou outros dados pessoais.

Não cole o conteúdo de um dump com dados reais dentro deste README. Guarde o arquivo de backup separadamente e não o envie a um repositório público.

---

## 17. Conferência do caminho do projeto

Os caminhos usados nos links e redirecionamentos do PHP precisam corresponder ao nome da pasta em que o projeto foi instalado.

A pasta utilizada nesta documentação é `sistema-de-cabeleireiro`, e os arquivos estão diretamente dentro dela, sem a antiga pasta `mini-sistema`.

Por isso, os caminhos de arquivos e endereços devem ser atualizados. Por exemplo:

| Caminho antigo | Caminho atualizado |
|---|---|
| `mini-sistema/database/connect.php` | `database/connect.php` |
| `mini-sistema/database/salao.sql` | `database/salao.sql` |
| `/sistema-de-cabeleireiro/mini-sistema/login/login.php` | `/sistema-de-cabeleireiro/login/login.php` |
| `/sistema-de-cabeleireiro/mini-sistema/index.php` | `/sistema-de-cabeleireiro/index.php` |

Confira especialmente os links e redirecionamentos em `include/header.php`, `index.php`, `login/logout.php`, `login/verifica.php` e `app/estoque.php`, caso esses arquivos utilizem caminhos fixos.

---

## 18. Teste de restauração e compatibilidade

Depois de gerar um dump, teste a restauração em um banco separado, como `salao_teste`, sem sobrescrever o banco utilizado pela aplicação.

Após a restauração:

1. Confira se as tabelas esperadas existem.
2. Confira se os registros foram restaurados, quando o dump inclui dados.
3. Teste o login, a listagem de cortes, os agendamentos e o estoque.
4. Registre os resultados reais. Se não tiver executado algum teste, indique que ele está pendente.

Se aparecer um erro relacionado a comandos do arquivo SQL, como `\restrict` ou `\unrestrict`, verifique as versões do PostgreSQL utilizadas para gerar e restaurar o arquivo. Não remova linhas do dump sem antes guardar uma cópia original e confirmar a causa do erro.

---

## 19. Observações sobre os requisitos funcionais

Ao finalizar a documentação, confira se os requisitos funcionais estão consistentes com o código atual.

Se houver listas diferentes de requisitos, compare-as com as funcionalidades realmente presentes no projeto antes de consolidá-las. Não marque como implementada uma função que ainda não foi testada.

**Autoria:** Kauam De Souza Belsi  
**Projeto:** Sistema de Cabeleireiro  
**Repositório:** [https://github.com/KauamDeSouzaBelsi/sistema-de-cabeleleiro](https://github.com/KauamDeSouzaBelsi/sistema-de-cabeleleiro)
