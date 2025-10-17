<?php 
session_start();
require_once "conexao.php";

if (!isset($_SESSION['rm'])) {
    header("Location: ../index.html");
    exit;
}

// Busca PCs (removi a parte de status)
$sql = "SELECT IP, Nome, Local FROM computador";
$stmt = $pdo->query($sql);
$pcs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Ver PCs - S.C.O.R.E</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        /* Seu CSS original aqui */
        body {
            font-family: Arial, sans-serif;
            background: #f7f9fc;
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align:center;
            color:#333;
            margin-bottom: 30px;
        }
        table {
            width:90%;
            margin:0 auto 40px auto;
            border-collapse: collapse;
            font-size:0.95rem;
            background:#fff;
            border-radius:10px;
            overflow:hidden;
            box-shadow:0 4px 12px rgba(0,0,0,0.1);
        }
        th, td {
            padding:12px;
            border-bottom:1px solid #eee;
            text-align:center;
        }
        th {
            background-color:#51b6fa;
            color:white;
            text-transform:uppercase;
            letter-spacing:0.05em;
        }
        tr:hover td {
            background:#f2faff;
        }
        .btn-acao {
            padding:6px 12px;
            border:none;
            border-radius:6px;
            color:white;
            cursor:pointer;
            margin:2px;
            text-decoration:none;
            font-size:0.85rem;
            transition:0.2s;
        }
        .btn-editar { background:#2ecc71; }
        .btn-editar:hover { background:#27ae60; transform: scale(1.05); }
        .btn-excluir { background:#e74c3c; }
        .btn-excluir:hover { background:#c0392b; transform: scale(1.05); }
        .btn-liberar { background:#f39c12; }
        .btn-liberar:hover { background:#d68910; transform: scale(1.05); }
    </style>
</head>
<body>
    <h1 style="text-align:center;">Lista de PCs</h1>

    <table>
        <thead>
            <tr>
                <th>IP</th>
                <th>Nome</th>
                <th>Local</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody id="tabela-pcs">
            <?php foreach ($pcs as $row): ?>
                <tr data-ip="<?= htmlspecialchars($row['IP']) ?>">
                    <td class="ip"><?= htmlspecialchars($row['IP']) ?></td>
                    <td class="nome"><?= htmlspecialchars($row['Nome']) ?></td>
                    <td class="local"><?= htmlspecialchars($row['Local']) ?></td>
                    <td>
                        <button class="btn-acao btn-editar">Editar</button>
                        <button class="btn-acao btn-excluir">Excluir</button>
                        <button class="btn-acao btn-liberar">
                            Codigo
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

<script>
// === EXCLUIR PC ===
document.querySelectorAll(".btn-excluir").forEach(btn => {
    btn.addEventListener("click", () => {
        const tr = btn.closest("tr");
        const ip = tr.dataset.ip;

        if (confirm("Deseja realmente excluir este PC?")) {
            fetch("excluir_pc.php", {
                method: "POST",
                headers: {"Content-Type": "application/x-www-form-urlencoded"},
                body: "ip=" + encodeURIComponent(ip)
            })
            .then(res => res.text())
            .then(msg => {
                alert(msg);
                if (!msg.toLowerCase().includes("erro")) {
                    tr.remove();
                }
            })
            .catch(() => alert("Erro na requisição."));
        }
    });
});

// === EDITAR PC ===
document.querySelectorAll(".btn-editar").forEach(btn => {
    btn.addEventListener("click", () => {
        const tr = btn.closest("tr");
        const ip = tr.querySelector(".ip").textContent.trim();
        const nome = tr.querySelector(".nome").textContent.trim();
        const local = tr.querySelector(".local").textContent.trim();

        mostrarModalEditar(ip, nome, local, tr);
    });
});

function mostrarModalEditar(ip, nome, local, tr) {
    const modal = document.createElement("div");
    modal.innerHTML = `
        <div style="
            position: fixed; inset: 0; display: flex; justify-content: center; align-items: center;
            background: rgba(0,0,0,0.6); z-index: 1000;
        ">
            <div style="
                background: white; border-radius: 10px; padding: 25px; width: 400px;
                max-width: 90%; box-shadow: 0 6px 20px rgba(0,0,0,0.3);
                font-family: Arial, sans-serif;
            ">
                <h2 style="text-align:center; margin-bottom:15px;">Editar PC</h2>
                <form id="form-editar">
                    <label style="font-weight:bold;">IP:</label>
                    <input type="text" id="edit-ip" value="${ip}" required readonly
                        style="width:100%; padding:8px; margin:5px 0 10px; border:1px solid #ccc; border-radius:5px;">
                    
                    <label style="font-weight:bold;">Nome:</label>
                    <input type="text" id="edit-nome" value="${nome}" required
                        style="width:100%; padding:8px; margin:5px 0 10px; border:1px solid #ccc; border-radius:5px;">
                    
                    <label style="font-weight:bold;">Local:</label>
                    <input type="text" id="edit-local" value="${local}" required
                        style="width:100%; padding:8px; margin:5px 0 15px; border:1px solid #ccc; border-radius:5px;">

                    <div style="text-align:center;">
                        <button type="submit" style="
                            background:#51b6fa; color:white; border:none; padding:8px 16px;
                            border-radius:6px; cursor:pointer; font-size:0.9rem; margin-right:8px;
                        ">Salvar</button>
                        <button type="button" id="fechar-editar" style="
                            background:#ccc; color:#333; border:none; padding:8px 16px;
                            border-radius:6px; cursor:pointer; font-size:0.9rem;
                        ">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    `;
    document.body.appendChild(modal);

    const form = modal.querySelector("#form-editar");
    const fechar = modal.querySelector("#fechar-editar");

    form.addEventListener("submit", e => {
        e.preventDefault();

        const novoIP = modal.querySelector("#edit-ip").value.trim();
        const novoNome = modal.querySelector("#edit-nome").value.trim();
        const novoLocal = modal.querySelector("#edit-local").value.trim();

        fetch("editar_pc.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams({
                ipAntigo: ip,
                ip: novoIP,
                nome: novoNome,
                local: novoLocal
            })
        })
        .then(res => res.text())
        .then(msg => {
            alert(msg);
            if (!msg.toLowerCase().includes("erro")) {
                tr.querySelector(".ip").textContent = novoIP;
                tr.querySelector(".nome").textContent = novoNome;
                tr.querySelector(".local").textContent = novoLocal;
                tr.dataset.ip = novoIP;
                modal.remove();
            }
        })
        .catch(() => alert("Erro na requisição."));
    });

    fechar.addEventListener("click", () => modal.remove());
}

// === LIBERAR / BLOQUEAR PC ===
document.querySelectorAll(".btn-liberar").forEach(btn => {
    btn.addEventListener("click", () => {
        const tr = btn.closest("tr");
        const ip = tr.dataset.ip;

        mostrarModalComCodigoMySQL(ip);
    });
});

function mostrarModalComCodigoMySQL(ip) {
    const codigo = `mysql -u root -p

GRANT ALL PRIVILEGES ON *.* TO 'root'@'${ip}' IDENTIFIED BY '' WITH GRANT OPTION;
FLUSH PRIVILEGES;`;

    const modal = document.createElement("div");
    modal.innerHTML = `
        <div style="
            position: fixed; inset: 0; display: flex; justify-content: center; align-items: center;
            background: rgba(0,0,0,0.6); z-index: 1000;
        ">
            <div style="
                background: white; border-radius: 10px; padding: 25px; width: 500px;
                max-width: 90%; box-shadow: 0 6px 20px rgba(0,0,0,0.3);
                font-family: Arial, sans-serif; text-align: center;
            ">
                <h2 style="margin-bottom: 10px; color: #333;">Liberar acesso MySQL pelo IP</h2>
                <p style="color: #555; font-size: 0.95rem; margin-bottom: 15px;">
                    Para permitir que o computador com IP <b>${ip}</b> acesse o banco de dados do XAMPP remotamente:<br><br>
                    1️⃣ Abra o painel do XAMPP e inicie o <b>MySQL</b>.<br>
                    2️⃣ Clique em <b>Shell</b> no painel do XAMPP.<br>
                    3️⃣ Cole o código abaixo e pressione <b>Enter</b>.<br><br>
                    Isso liberará o IP para acessar o MySQL remotamente.
                </p>

                <textarea id="codigo-mysql" readonly style="
                    width: 100%; height: 130px; padding: 10px;
                    border: 1px solid #ddd; border-radius: 8px;
                    background: #f8f9fa; color: #222; font-family: monospace;
                    font-size: 0.9rem; margin-bottom: 15px;
                ">${codigo}</textarea>

                <button id="copiar-codigo" style="
                    background: #51b6fa; color: white; border: none;
                    padding: 8px 16px; border-radius: 6px; cursor: pointer;
                    font-size: 0.9rem; margin-right: 8px;
                ">Copiar código</button>

                <button id="fechar-modal" style="
                    background: #ccc; color: #333; border: none;
                    padding: 8px 16px; border-radius: 6px; cursor: pointer;
                    font-size: 0.9rem;
                ">Fechar</button>
            </div>
        </div>
    `;

    document.body.appendChild(modal);

    modal.querySelector("#copiar-codigo").addEventListener("click", () => {
        const textarea = modal.querySelector("#codigo-mysql");
        if (navigator.clipboard) {
            navigator.clipboard.writeText(textarea.value).then(() => {
                alert("Código copiado para a área de transferência!");
            }).catch(() => alert("Falha ao copiar o código."));
        } else {
            textarea.select();
            document.execCommand("copy");
            alert("Código copiado para a área de transferência!");
        }
    });

    modal.querySelector("#fechar-modal").addEventListener("click", () => {
        modal.remove();
    });
}
</script>

</body>
</html>
