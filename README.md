# 🧩 S.C.O.R.E – Sistema de Comunicação Offline Rápida Escolar

O **S.C.O.R.E** (Sistema de Comunicação Offline Rápida Escolar) é um projeto desenvolvido para otimizar a **comunicação interna entre professores, coordenadores, direção e funcionários de escolas**, especialmente em ambientes com **conectividade limitada ou inexistente com a internet**.

O sistema utiliza **rede local cabeada (LAN)** para o envio e recebimento de mensagens, **dispensando o uso da internet** e proporcionando uma comunicação rápida, segura e eficiente dentro da instituição.

---

## 🚀 Objetivo do Projeto

O S.C.O.R.E visa oferecer uma **plataforma prática, acessível e funcional** para comunicação interna em escolas, mesmo em locais sem acesso estável à internet.  
O sistema busca:
- Melhorar a comunicação entre setores escolares;
- Reduzir falhas de repasse de informações;
- Operar totalmente **offline** em rede local;
- Garantir segurança e integridade das mensagens.

---

## 🧱 Arquitetura do Sistema

- **Servidor central (XAMPP + MySQL)**: responsável por armazenar e gerenciar as mensagens.  
- **Clientes (estações)**: computadores conectados na rede local que trocam mensagens por meio do servidor.  
- **Conexão**: cabeamento estruturado via rede LAN.  
- **Linguagens utilizadas**:
  - PHP (back-end)
  - HTML/CSS/JavaScript (front-end)
  - MySQL (banco de dados)

---

## ⚙️ Pré-requisitos

- Computador com **Windows**, **Linux** ou **Mac** (com XAMPP compatível)  
- **XAMPP** instalado (versão recente)  
- Arquivo do banco de dados **`SCORE.sql`**

---

## 🧩 Instalação e Configuração

### 1. Instalar o XAMPP
1. Baixe o XAMPP: https://www.apachefriends.org/pt_br/index.html  
2. Execute o instalador e siga os passos padrão.  
3. Abra o **Painel de Controle do XAMPP**.

---

### 2. Configurar o MySQL para acesso remoto

**Passo 1 – Editar o arquivo `my.ini`:**  
No painel do XAMPP → clique em **Config → my.ini**  
Localize a linha:
bind-address = 127.0.0.1
Substitua por:
bind-address = 0.0.0.0
Passo 2 – Reiniciar o MySQL:
Pare e inicie o serviço MySQL no painel do XAMPP.

Passo 3 – Configurar o firewall:
Libere a porta 3306 no firewall do sistema.

Passo 4 – Criar usuários remotos no MySQL:
No painel do XAMPP → Shell → digite:

sql
GRANT ALL PRIVILEGES ON *.* TO 'root'@'192.168.0.145' IDENTIFIED BY '' WITH GRANT OPTION;
GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' IDENTIFIED BY '' WITH GRANT OPTION;
FLUSH PRIVILEGES;
3. Importar o banco de dados
Acesse o phpMyAdmin: http://localhost/phpmyadmin

Clique em Importar → Escolher arquivo → selecione SCORE.sql

Clique em Executar

4. Configurar o sistema S.C.O.R.E
Coloque os arquivos do sistema em:

makefile
C:\xampp\htdocs\score
Configure o arquivo conexao.php:

php
<?php
$host = '127.0.0.1';
$db   = 'computador';
$user = 'root';
$pass = '';
// restante da configuração de conexão...
?>
Acesse o sistema no navegador:

arduino
http://localhost/score/index.html
5. Liberar PCs remotos via S.C.O.R.E
Clique no botão "Código" ao lado de um PC na interface do sistema.

Copie o código gerado e execute no Shell do XAMPP:

bash
mysql -u root -p
GRANT ALL PRIVILEGES ON *.* TO 'root'@'IP_DO_PC' IDENTIFIED BY '' WITH GRANT OPTION;
FLUSH PRIVILEGES;
Substitua IP_DO_PC pelo endereço IP do computador cliente que deverá ter acesso ao banco.

⚠️ Observações importantes
❌ Evite liberar 'root'@'%' em redes públicas — isso cria um risco de segurança.

🌐 Todos os computadores devem estar na mesma rede local para o S.C.O.R.E funcionar corretamente.

💾 Mantenha backup regular do arquivo SCORE.sql.

🔐 Se possível, crie usuários MySQL com permissões limitadas (não use root) para cada serviço/estação.

🔧 Teste primeiro em um ambiente controlado antes de aplicar em produção.

🔐 Dicas rápidas de segurança (recomendadas)
Use senhas fortes para contas de banco de dados e usuários do sistema.

Considere configurar SSL/TLS se houver tráfego atravessando redes não confiáveis.

Habilite logs e auditoria no servidor MySQL e na aplicação para detectar ações suspeitas.

Crie backups automáticos e plano de recuperação.

📄 Licença e Autor
Este projeto é de uso acadêmico e educativo.
Mantenha os créditos ao autor original ao reutilizar ou adaptar o projeto.

Autor: Valério Júnior, Vinicius Parra, Maria Fernanda, Venicius, Caio Fabio, Luiz Gabriel, Matheus Chambo
Repositório: https://github.com/ValerioJunior-Garro/Score.git
