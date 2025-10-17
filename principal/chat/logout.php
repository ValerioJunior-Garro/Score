<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Logout - S.C.O.R.E</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #4ab4ff;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .logout-container {
            background: #fff;
            backdrop-filter: blur(10px);
            padding: 40px 50px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 8px 30px rgba(0,0,0,0.3);
            color: #4ab4ff;
        }

        h1 {
            margin-bottom: 30px;
            font-size: 1.8rem;
        }

        .button-wrapper {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .btn {
            flex: 1; /* Faz os botões ocuparem o mesmo espaço */
            max-width: 150px; /* largura máxima */
            padding: 12px 0; /* altura uniforme */
            border-radius: 12px;
            border: none;
            font-weight: bold;
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.25s ease;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }

        .btn-yes {
            background: #1673cc;
            color: #fff;
        }

        .btn-no {
            background: #1673cc;
            color: #fff;
        }

        /* Efeitos de hover */
        .btn-yes:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(0,0,0,0.3);
        }

        .btn-no:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(0,0,0,0.3);
        }
    </style>
</head>
<body>
    <div class="logout-container">
        <h1>Deseja realmente sair?</h1>
        <div class="button-wrapper">
            <button class="btn btn-yes" onclick="logout()">Sim</button>
            <button class="btn btn-no" onclick="cancelLogout()">Cancelar</button>
        </div>
    </div>

    <script>
        function logout() {
            window.location.href = "../../index.html";
        }

        function cancelLogout() {
            window.history.back();
        }
    </script>
</body>
</html>
