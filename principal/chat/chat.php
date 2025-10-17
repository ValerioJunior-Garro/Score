<?php
session_start();
require "conexao.php";

if (!isset($_SESSION['rm'])) {
    header("Location: login.php");
    exit;
}

$meuRM = $_SESSION['rm'];
$meuNome = $_SESSION['nome'] ?? "";

// Buscar contatos (outros usuários)
$sql = "SELECT RM, Nome FROM usuario WHERE RM <> ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$meuRM]);
$contatos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar dados completos do usuário logado
$sqlUsuario = "SELECT RM, Nome, CPF, Tipo, Email FROM usuario WHERE RM = ?";
$stmtUsuario = $pdo->prepare($sqlUsuario);
$stmtUsuario->execute([$meuRM]);
$usuario = $stmtUsuario->fetch(PDO::FETCH_ASSOC);

// Atualiza a sessão caso ainda não tenha os dados
$_SESSION['cpf'] = $usuario['CPF'] ?? $_SESSION['cpf'] ?? '';
$_SESSION['tipo'] = $usuario['Tipo'] ?? $_SESSION['tipo'] ?? '';
$_SESSION['email'] = $usuario['Email'] ?? $_SESSION['email'] ?? '';

// Prepara o CPF mascarado
$cpfReal = $_SESSION['cpf'] ?? '';
$cpfMasked = $cpfReal ? str_repeat('•', strlen(preg_replace('/\D/', '', $cpfReal))) : '';
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Chat Oficial</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

    <header>
        <div class="logo-container">
            <span id="menu-toggle" class="menu-toggle">☰</span>
            <h2>S.C.O.R.E</h2>
        </div>
        <div class="user-container">
            <span><?= htmlspecialchars($meuNome) ?></span>
            <img src="../../pictures/perfil sem fundo.jpg" alt="perfil">
        </div>
    </header>




    <div class="layout">
        <aside class="sidebar" id="sidebar">
            <h3>Contatos</h3>
            <input type="text" id="pesquisaContato" placeholder="Pesquisar contato...">
            <?php foreach ($contatos as $c): ?>
                <button class="btn-computador" data-rm="<?= $c['RM'] ?>">
                    <?= htmlspecialchars($c['Nome']) ?>
                </button>
            <?php endforeach; ?>

            <div class="sidebar-buttons">
                <button class="btn-flutuante btn-config" id="openConfig" aria-label="Configurações">
                    <img src="../../pictures/iconconfig.png" alt="Configurações">
                </button>

                <form action="logout.php">
                    <button class="btn-flutuante btn-logout" aria-label="Sair">
                        <img src="../../pictures/iconlogout.png" alt="Sair">
                    </button>
                </form>
            </div>
        </aside>

        <main class="main-content">
            <div id="topoChat">
                <h2 id="chatTitulo">Selecione um contato</h2>
                <div id="acoesMensagem">
                    <img src="../../pictures/confirmar.png" id="btnConfirmarExclusao" title="Confirmar exclusão">
                    <img src="../../pictures/lixeira3.png" id="btnLixeira" title="Excluir mensagens">
                </div>
                <span id="msgSelecao">Selecionar mensagens</span>
            </div>



            <div id="chatBody" class="mensagens"></div>
            <form id="formEnvio">
                <textarea id="texto" placeholder="Digite sua mensagem..."></textarea>
                <button type="submit">Enviar</button>
            </form>
        </main>
    </div>

    <!-- Sidebar de Configurações -->
    <div class="sidebar-config" id="sidebarConfig">
        <span class="close-sidebar" id="closeSidebar">&times;</span>
        <h2>Configurações</h2>
        <button class="btn-editar" id="openEditar">Editar Perfil</button>

        <label for="tamanhoFonte">Tamanho da Fonte:</label>
        <select id="tamanhoFonte">
            <option value="14px">Pequena</option>
            <option value="16px" selected>Média</option>
            <option value="18px">Grande</option>
            <option value="30px">Muito Grande</option>
        </select>

        <label for="somNotificacao">Som de Notificação:</label>
        <select id="somNotificacao">
            <option value="padrao">Padrão</option>
            <option value="som1">Som 1</option>
            <option value="som2">Som 2</option>
        </select>
    </div>

    <!-- Modal Editar Usuário -->
    <div id="modalEditar" class="modal" style="display:none;">
        <div class="modal-content">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                <h2 style="margin:0; font-size:1.3rem;">Editar Usuário</h2>
                <span id="closeEditar" style="cursor:pointer; font-size:1.5rem; font-weight:bold;">&times;</span>
            </div>
            <hr style="border:none; border-top:1px solid #ccc; margin-bottom:20px;">

            <form id="formEditar" style="display:flex; flex-direction:column; gap:15px;">
                <div>
                    <label for="rmUser">RM</label>
                    <input type="text" name="rm" id="rmUser" value="<?= htmlspecialchars($usuario['RM'] ?? $meuRM) ?>"
                        readonly
                        style="width:100%; padding:8px; border-radius:5px; border:1px solid #ccc; background:#f0f0f0;">
                </div>

                <div>
                    <label for="nomeUser">Nome</label>
                    <input type="text" name="nome" id="nomeUser" value="<?= htmlspecialchars($usuario['Nome'] ?? '') ?>"
                        required style="width:100%; padding:8px; border-radius:5px; border:1px solid #ccc;">
                </div>

                <div>
                    <label for="cpfUser">CPF</label>
                    <input type="text" id="cpfMasked" value="<?= htmlspecialchars($cpfMasked) ?>" readonly
                        style="width:100%; padding:8px; border-radius:5px; border:1px solid #ccc; background:#f0f0f0;">
                    <input type="hidden" name="cpf" id="cpfUser" value="<?= htmlspecialchars($cpfReal) ?>">
                </div>

                <div>
                    <label for="tipoUser">Tipo</label>
                    <input type="text" name="tipo" id="tipoUser" value="<?= htmlspecialchars($usuario['Tipo'] ?? '') ?>"
                        readonly
                        style="width:100%; padding:8px; border-radius:5px; border:1px solid #ccc; background:#f0f0f0;">
                </div>

                <div>
                    <label for="emailUser">Email</label>
                    <input type="email" name="email" id="emailUser"
                        value="<?= htmlspecialchars($usuario['Email'] ?? '') ?>" required
                        style="width:100%; padding:8px; border-radius:5px; border:1px solid #ccc;">
                </div>

                <div>
                    <label for="confirmaEmailUser">Confirmar Email</label>
                    <input type="email" name="confirma_email" id="confirmaEmailUser" placeholder="Confirme seu email"
                        required style="width:100%; padding:8px; border-radius:5px; border:1px solid #ccc;">
                </div>

                <div>
                    <label for="senhaUser">Senha</label>
                    <input type="password" name="senha" id="senhaUser"
                        placeholder="•••••••• (deixe em branco para não alterar)"
                        style="width:100%; padding:8px; border-radius:5px; border:1px solid #ccc;">
                </div>

                <div>
                    <label for="confirmaSenhaUser">Confirmar Senha</label>
                    <input type="password" name="confirma_senha" id="confirmaSenhaUser"
                        placeholder="Confirme a nova senha"
                        style="width:100%; padding:8px; border-radius:5px; border:1px solid #ccc;">
                </div>

                <button type="submit"
                    style="padding:10px; border:none; border-radius:5px; background:#007BFF; color:#fff; font-weight:bold; cursor:pointer; transition:0.2s;">Confirmar</button>
                <p id="msgEditar" style="margin:0; margin-top:10px;"></p>
            </form>
        </div>
    </div>

    <!-- Modal de exclusão -->
    <div class="modal fade" id="modalExcluir" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content text-center">
                <div class="modal-header">
                    <h5 class="modal-title">Excluir mensagens</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <p>Deseja excluir as mensagens selecionadas?</p>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" id="btnExcluirSoParaMim" class="btn btn-secondary"
                        data-bs-dismiss="modal">
                        Excluir para mim
                    </button>
                    <button type="button" id="btnExcluirParaTodos" class="btn btn-danger"
                        data-bs-dismiss="modal">
                        Excluir para todos
                    </button>
                </div>
            </div>
        </div>
    </div>



    <script>
        window.__CHAT__ = {
            rmLogado: <?= json_encode($meuRM) ?>,
            nome: <?= json_encode($_SESSION['nome'] ?? '') ?>,
            cpf: <?= json_encode($_SESSION['cpf'] ?? '') ?>,
            tipo: <?= json_encode($_SESSION['tipo'] ?? '') ?>,
            email: <?= json_encode($_SESSION['email'] ?? '') ?>,
            contatoSelecionado: null
        };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>

    <script src="script.js" defer></script>
</body>

</html>