<?php
ob_start();
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

$_SESSION['cpf'] = $usuario['CPF'] ?? $_SESSION['cpf'] ?? '';
$_SESSION['tipo'] = $usuario['Tipo'] ?? $_SESSION['tipo'] ?? '';
$_SESSION['email'] = $usuario['Email'] ?? $_SESSION['email'] ?? '';

$cpfReal = $_SESSION['cpf'] ?? '';
$cpfMasked = $cpfReal ? str_repeat('•', strlen(preg_replace('/\D/', '', $cpfReal))) : '';
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Chat Secreto</title>
    <link rel="stylesheet" href="stylechat.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>

<body>

    <header>
        <div class="logo-container">
            <span id="menu-toggle" class="menu-toggle">☰</span>

        </div>
    </header>

    <audio id="notificacaoAudio" src="./chat/notificacao.mp3" preload="auto"></audio>

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

            </div>
        </aside>

        <main class="main-content">
            <div id="topoChat">
                <h2 id="chatTitulo">Selecione um contato</h2>
                <div id="acoesMensagem">
                    <img src="../pictures/confirmar.png" id="btnConfirmarExclusao" title="Confirmar exclusão">
                    <img src="../pictures/lixeira3.png" id="btnLixeira" title="Excluir mensagens">
                </div>
                <span id="msgSelecao" style="display:none;">Selecionar mensagens</span>
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

    <!-- Modal para exclusão -->
    <div class="modal fade" id="modalExclusao" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Excluir mensagens</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Deseja excluir apenas para você ou para todos?
                </div>
                <div class="modal-footer">
                    <button id="excluirUnico" class="btn btn-secondary" data-bs-dismiss="modal">Só para mim</button>
                    <button id="excluirTodos" class="btn btn-danger" data-bs-dismiss="modal">Para todos</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.__CHAT__ = {
            rmLogado: <?= json_encode($meuRM) ?>,
            nome: <?= json_encode($_SESSION['nome'] ?? '') ?>,
            contatoSelecionado: null
        };
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script2.js" defer></script>

</body>

</html>