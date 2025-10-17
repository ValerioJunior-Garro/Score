<?php
session_start();
require_once "conexao.php";

if (!isset($_SESSION['rm'])) {
    header("Location: ../index.html");
    exit;
}

$sql = "SELECT RM, Nome, CPF, Tipo, Email, Senha FROM usuario";
$stmt = $pdo->query($sql);
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Lista de Usuários - S.C.O.R.E</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f7f9fc;
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }
        table {
            width: 90%;
            margin: 0 auto 40px auto;
            border-collapse: collapse;
            font-size: 0.95rem;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px;
            border-bottom: 1px solid #eee;
            text-align: center;
        }
        th {
            background-color: #51b6fa;
            color: white;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        tr:hover td {
            background: #f2faff;
        }
        .btn-acao {
            padding: 6px 12px;
            border: none;
            border-radius: 6px;
            color: white;
            cursor: pointer;
            margin: 2px;
            text-decoration: none;
            font-size: 0.85rem;
            transition: 0.2s;
        }
        .btn-editar { background:#2ecc71; }
        .btn-editar:hover { background:#27ae60; transform: scale(1.05); }
        .btn-excluir { background:#e74c3c; }
        .btn-excluir:hover { background:#c0392b; transform: scale(1.05); }
    </style>
</head>
<body>
    <h1>Lista de Usuários</h1>

    <table>
        <thead>
            <tr>
                <th>RM</th>
                <th>Nome</th>
                <th>CPF</th>
                <th>Tipo</th>
                <th>Email</th>
                <th>Senha</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $row): ?>
                <tr data-rm="<?= htmlspecialchars($row['RM']) ?>">
                    <td><?= htmlspecialchars($row['RM']) ?></td>
                    <td class="nome"><?= htmlspecialchars($row['Nome']) ?></td>
                    <td class="cpf"><?= htmlspecialchars($row['CPF']) ?></td>
                    <td class="tipo"><?= htmlspecialchars($row['Tipo']) ?></td>
                    <td class="email"><?= htmlspecialchars($row['Email']) ?></td>
                    <td class="senha"><?= htmlspecialchars($row['Senha']) ?></td>
                    <td>
                        <button class="btn-acao btn-editar">Editar</button>
                        <button class="btn-acao btn-excluir">Excluir</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

<script>
// === Excluir Usuário ===
document.querySelectorAll(".btn-excluir").forEach(btn => {
    btn.addEventListener("click", () => {
        const tr = btn.closest("tr");
        const rm = tr.dataset.rm;

        if (confirm("Deseja realmente excluir este usuário?")) {
            fetch("excluir.php", {
                method: "POST",
                headers: {"Content-Type": "application/x-www-form-urlencoded"},
                body: "rm=" + encodeURIComponent(rm)
            })
            .then(res => res.text())
            .then(msg => {
                alert(msg);
                tr.remove();
            });
        }
    });
});

// === Editar Usuário (Modal) ===
document.querySelectorAll(".btn-editar").forEach(btn => {
    btn.addEventListener("click", () => {
        const tr = btn.closest("tr");

        const rm = tr.dataset.rm;
        const nome = tr.querySelector(".nome").textContent;
        const cpf = tr.querySelector(".cpf").textContent;
        const tipo = tr.querySelector(".tipo").textContent;
        const email = tr.querySelector(".email").textContent;
        const senha = tr.querySelector(".senha").textContent;

        const modal = document.createElement("div");
        modal.innerHTML = `
      <div style="
    position: fixed; inset: 0; background: rgba(0,0,0,0.6);
    display: flex; justify-content: center; align-items: center; z-index: 1000;
    animation: fadeIn 0.3s ease;
">
    <div style="
        background: white; border-radius: 12px; padding: 25px 30px;
        width: 440px; max-width: 90%;
        box-shadow: 0 8px 24px rgba(0,0,0,0.3);
        font-family: Arial, sans-serif; animation: popIn 0.25s ease;
    ">
        <h2 style="color:#51b6fa; text-align:center; margin-bottom:20px;">Editar Usuário</h2>

        <form id="formEditarUsuario" style="display:flex; flex-direction:column; gap:16px;">
            
            <!-- RM -->
            <div>
                <label style="font-weight:bold;">RM:</label>
                <input type="text" name="RM" value="${rm}" readonly
                    style="width:100%; padding:8px; border:1px solid #ccc; border-radius:6px; background:#f8f8f8;">
            </div>

            <!-- Nome e CPF -->
            <div style="display:flex; gap:16px;">
                <div style="flex:1;">
                    <label style="font-weight:bold;">Nome:</label>
                    <input type="text" name="Nome" value="${nome}" required
                        style="width:100%; padding:8px; border:1px solid #ccc; border-radius:6px;">
                </div>
                <div style="flex:1;">
                    <label style="font-weight:bold;">CPF:</label>
                    <input type="text" name="CPF" value="${cpf}" required
                        style="width:100%; padding:8px; border:1px solid #ccc; border-radius:6px;">
                </div>
            </div>

            <!-- Tipo e Email -->
            <div style="display:flex; gap:16px;">
                <div style="flex:1;">
                    <label style="font-weight:bold;">Tipo:</label>
                    <select name="Tipo" required
                        style="width:100%; padding:8px; border:1px solid #ccc; border-radius:6px;">
                        <option value="Professor" ${tipo === "Professor" ? "selected" : ""}>Professor</option>
                        <option value="Secretaria" ${tipo === "Secretaria" ? "selected" : ""}>Secretaria</option>
                    </select>
                </div>
                <div style="flex:1;">
                    <label style="font-weight:bold;">Email:</label>
                    <input type="email" name="Email" value="${email}" required
                        style="width:100%; padding:8px; border:1px solid #ccc; border-radius:6px;">
                </div>
            </div>

            <!-- Senha -->
            <div>
                <label style="font-weight:bold;">Senha:</label>
                <input type="text" name="Senha" value="${senha}" required
                    style="width:100%; padding:8px; border:1px solid #ccc; border-radius:6px;">
            </div>

            <!-- Botões -->
            <div style="margin-top:15px; text-align:center;">
                <button type="submit" style="
                    background:#51b6fa; color:white; border:none;
                    padding:10px 20px; border-radius:6px; cursor:pointer;
                    font-size:0.9rem; font-weight:bold; margin-right:10px;
                ">Salvar</button>
                <button type="button" id="fecharModal" style="
                    background:#ccc; color:#333; border:none;
                    padding:10px 20px; border-radius:6px; cursor:pointer;
                    font-size:0.9rem; font-weight:bold;
                ">Cancelar</button>
            </div>
        </form>
    </div>

    <style>
        @keyframes fadeIn { from {opacity:0;} to {opacity:1;} }
        @keyframes popIn { from {transform:scale(0.9); opacity:0;} to {transform:scale(1); opacity:1;} }
    </style>
</div>

            </div>
        `;
        document.body.appendChild(modal);

        // Fechar modal
        modal.querySelector("#fecharModal").addEventListener("click", () => modal.remove());

        // Salvar edição
        modal.querySelector("#formEditarUsuario").addEventListener("submit", e => {
            e.preventDefault();
            const dados = new URLSearchParams(new FormData(e.target));

            fetch("editar.php", {
                method: "POST",
                body: dados
            })
            .then(res => res.text())
            .then(msg => {
                alert(msg);
                tr.querySelector(".nome").textContent = e.target.Nome.value;
                tr.querySelector(".cpf").textContent = e.target.CPF.value;
                tr.querySelector(".tipo").textContent = e.target.Tipo.value;
                tr.querySelector(".email").textContent = e.target.Email.value;
                tr.querySelector(".senha").textContent = e.target.Senha.value;
                modal.remove();
            });
        });
    });
});
</script>

</body>
</html>
