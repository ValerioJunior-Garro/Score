<?php
session_start();
if (!isset($_SESSION['rm'])) {
    header("Location: ../index.html");
    exit;
}
$nomeUsuario = $_SESSION['nome'] ?? 'Usuário';
$emailUsuario = $_SESSION['email'] ?? 'usuario@email.com';
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Home - SCORE</title>
    <style>
        :root {
            --header-h: 70px;
            --sidebar-w: 220px;
            --sidebar-collapsed-w: 90px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f7f9fc;
            overflow-x: hidden;
        }

        /* HEADER */
        header {
            height: var(--header-h);
            background: #51b6fa;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .menu-toggle {
            font-size: 1.5rem;
            cursor: pointer;
            user-select: none;
            padding: 6px 8px;
            border-radius: 6px;
            background: rgba(255, 255, 255, 0.15);
        }

        /* LAYOUT */
        .layout {
            display: flex;
            height: calc(100vh - var(--header-h));
            overflow: hidden;
        }

        /* SIDEBAR */
        .sidebar {
            width: var(--sidebar-w);
            background: #f7f9fc;
            padding-top: 14px;
            border-right: 1px solid #e6e6e6;
            transition: all 0.3s ease;
            overflow: hidden;
            flex-shrink: 0;
        }

        .sidebar.collapsed {
            width: var(--sidebar-collapsed-w);
            transition: all 0.3s ease;
        }

        /* LINKS */
        .sidebar a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            color: #111;
            text-decoration: none;
            font-weight: 500;
            transition: background 0.15s, padding 0.3s;
            white-space: nowrap;
        }

        .sidebar a:hover {
            background: #eaeaea;
        }

        .sidebar a.active {
            background: #51b6fa;
            color: #fff;
            border-radius: 10px;
            margin: 6px;
        }

        .sidebar .icon {
            width: 24px;
            text-align: center;
            font-size: 1.1rem;
        }

        /* Oculta texto no modo colapsado */
        .sidebar.collapsed .label {
            display: none;
        }

        /* Centraliza ícones no modo colapsado */
        .sidebar.collapsed a {
            justify-content: center;
            padding: 12px 0;
        }

        /* CONTEÚDO PRINCIPAL */
        .main-content {
            flex: 1;
            overflow: hidden;
        }

        .main-content iframe {
            width: 100%;
            height: 100%;
            border: none;
        }

        /* USUÁRIO */
        .user-container {
            display: flex;
            align-items: center;
            gap: 10px;
            color: white;
        }

        .user-container img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        /* BOTÕES FLUTUANTES */
        .btn-fab {
            position: fixed;
            left: calc(var(--sidebar-w) / 4 - 40px);
            width: 60px;
            height: 60px;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            z-index: 2000;
            background: #51b6fa;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
            transition: transform 0.2s ease, left 0.3s ease;
        }

        .btn-fab img {
            width: 70%;
            height: 70%;
            object-fit: contain;
        }

        .btn-fab:hover {
            transform: scale(1.1);
        }

        .btn-config {
            bottom: 90px;
        }

        .btn-logout {
            bottom: 20px;
        }

        /* Ajusta posição dos botões quando sidebar colapsa */
        .sidebar.collapsed~.btn-fab {
            left: calc(var(--sidebar-collapsed-w) / 2 - 10px);
        }

        /* PAINEL DE CONFIGURAÇÕES */
        .config-panel {
            position: fixed;
            top: 0;
            right: -400px;
            width: 400px;
            height: 100%;
            background: #fff;
            box-shadow: -4px 0 12px rgba(0, 0, 0, 0.2);
            transition: right .3s ease;
            padding: 20px;
            z-index: 3000;
        }

        .config-panel.active {
            right: 0;
        }

        .config-panel h3 {
            margin-top: 0;
            text-align: center;
            color: #333;
        }

        .config-panel label {
            display: block;
            margin: 12px 0 5px;
            font-weight: bold;
        }

        .config-panel input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
        }

        .config-panel button.save {
            margin-top: 20px;
            width: 100%;
            padding: 12px;
            background: #51b6fa;
            border: none;
            color: #fff;
            font-size: 1rem;
            border-radius: 8px;
            cursor: pointer;
        }

        .config-panel button.save:hover {
            background: #3a9ed6;
        }

        .config-panel .close-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            cursor: pointer;
            font-size: 1.2rem;
            font-weight: bold;
            background: none;
            border: none;
            color: #666;
        }

        .btn-fab.btn-config img {
            width: 34px;
            height: 34px;
            object-fit: contain;
        }

        .btn-fab.btn-config:hover img {
            transform: scale(1.1);
            transition: transform 0.2s ease;
        }

        /* Botões flutuantes no rodapé da sidebar */
        .sidebar-buttons {
            display: flex;
            flex-direction: column;
            align-items: center;
            /* centraliza horizontalmente */
            gap: 12px;
            margin-top: auto;
            padding-bottom: 20px;
        }

        /* Quando a sidebar estiver colapsada */
        .sidebar.collapsed .sidebar-buttons {
            align-items: center;
            justify-content: center;
            width: 100%;
            /* garante centralização dentro da sidebar estreita */
            padding: 0;
        }

        /* Botões dentro da sidebar (config e logout) */
        .sidebar-buttons .btn-flutuante {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #51b6fa;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            cursor: pointer;
            transition: transform 0.2s ease, background 0.2s ease;
            border: none;
            padding: 8px;
        }

        .sidebar-buttons .btn-flutuante img {
            width: 70%;
            height: 70%;
            object-fit: contain;
        }

        .sidebar-buttons .btn-flutuante:hover {
            transform: scale(1.1);
            background: #3a95d9;
        }
    </style>
</head>

<body>
    <header>
        <div class="logo-container">
            <span id="menu-toggle" class="menu-toggle">☰</span>
            <h2>S.C.O.R.E</h2>
        </div>
        <div class="user-container">
            <span id="nomeUsuario"><?= htmlspecialchars($nomeUsuario) ?></span>
            <img src="../pictures/perfil sem fundo.jpg" alt="perfil">
        </div>
    </header>

    <div class="layout">
        <aside class="sidebar" id="sidebar">
            <a href="#" data-page="home.html" class="active"><span class="icon">🏠</span><span
                    class="label">Home</span></a>
            <a href="#" data-page="chat_secre.php"><span class="icon">💬</span><span class="label">Chat</span></a>
            <a href="#" data-page="ver.php"><span class="icon">👥</span><span class="label">Usuários</span></a>
            <a href="#" data-page="cadastrar.html"><span class="icon">➕</span><span class="label">Cadastrar
                    Usuário</span></a>
            <a href="#" data-page="cadastrar_pc.html"><span class="icon">🖥️</span><span class="label">Cadastrar
                    PC</span></a>
            <a href="#" data-page="ver_pc.php"><span class="icon">💻</span><span class="label">Ver PCs</span></a>
        </aside>

        <main class="main-content">
            <iframe id="frameConteudo" src="home.html"></iframe>
        </main>
    </div>

    <!-- Botões fixos -->
    <button class="btn-fab btn-config" id="btnConfig">
        <img src="../pictures/iconconfig2.png" alt="Configurações">
    </button>

    <form action="logout.php">
        <button class="btn-fab btn-logout" id="btnLogout">
            <img src="../pictures/logout.png" alt="Sair">
        </button>
    </form>

    <!-- Painel de Configurações -->
    <div class="config-panel" id="configPanel">
        <button class="close-btn" id="closeConfig">✖</button>
        <h3>Configurações da Conta</h3>
        <form method="post" action="atualizar_usuario.php">
            <label for="nome">Nome</label>
            <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($nomeUsuario) ?>">

            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($emailUsuario) ?>">

            <label for="senha">Nova Senha</label>
            <input type="password" id="senha" name="senha">

            <button type="submit" class="save">Salvar Alterações</button>
        </form>
    </div>

    <script>
        // Faz a sidebar colapsar automaticamente ao clicar em "Chat"
        document.querySelector('a[data-page="chat_secre.php"]').addEventListener('click', () => {
            document.querySelector('.sidebar').classList.add('collapsed');
        });



        // abrir e fechar painel de config
        const btnConfig = document.getElementById("btnConfig");
        const configPanel = document.getElementById("configPanel");
        const closeConfig = document.getElementById("closeConfig");

        btnConfig.addEventListener("click", () => {
            configPanel.classList.add("active");
        });
        closeConfig.addEventListener("click", () => {
            configPanel.classList.remove("active");
        });

        // mudar iframe com links
        const links = document.querySelectorAll(".sidebar a");
        const iframe = document.getElementById("frameConteudo");
        links.forEach(link => {
            link.addEventListener("click", e => {
                e.preventDefault();
                iframe.src = link.getAttribute("data-page");
                links.forEach(l => l.classList.remove("active"));
                link.classList.add("active");
            });
        });

        // ✅ BOTÃO DE MENU (mostrar/ocultar sidebar)
        const menuToggle = document.getElementById("menu-toggle");
        const sidebar = document.getElementById("sidebar");

        menuToggle.addEventListener("click", () => {
            sidebar.classList.toggle("collapsed");
        });
    </script>

</body>

</html>