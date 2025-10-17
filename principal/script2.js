document.addEventListener('DOMContentLoaded', () => {
    const menuToggle = document.getElementById('menu-toggle');
    const sidebar = document.querySelector('.sidebar');
    const pesquisaInput = document.getElementById('pesquisaContato');
    const contatos = document.querySelectorAll('.btn-computador');

    const chatBody = document.getElementById('chatBody');
    const chatTitulo = document.getElementById('chatTitulo');
    const formEnvio = document.getElementById('formEnvio');
    const textoInput = document.getElementById('texto');

    const rmLogado = window.__CHAT__.rmLogado;
    let contatoSelecionado = null;
    let chatInterval = null;
    let globalInterval = null;

    // cache de mensagens já notificadas
    let mensagensNotificadas = new Set();

    // ========================
    // Sidebar toggle
    // ========================
    menuToggle.addEventListener("click", () => {
        sidebar.classList.toggle("collapsed");
    });

    // ========================
    // Filtrar contatos
    // ========================
    pesquisaInput.addEventListener('input', () => {
        const filtro = pesquisaInput.value.toLowerCase();
        contatos.forEach(contato => {
            const nome = contato.textContent.toLowerCase();
            contato.style.display = nome.includes(filtro) ? 'block' : 'none';
        });
    });

    // ========================
    // Seleção de contato
    // ========================
    contatos.forEach(contato => {
        contato.addEventListener('click', () => {
            contatos.forEach(c => c.classList.remove('active'));
            contato.classList.add('active');

            contatoSelecionado = contato.dataset.rm;
            window.__CHAT__.contatoSelecionado = contatoSelecionado;

            chatTitulo.textContent = contato.textContent;
            chatBody.innerHTML = '';

            carregarMensagens(contatoSelecionado);

            if (chatInterval) clearInterval(chatInterval);
            chatInterval = setInterval(() => carregarMensagens(contatoSelecionado), 3000);
        });
    });

    // ========================
    // Carregar mensagens de UM contato
    // ========================
    async function carregarMensagens(rmDestino) {
        if (!rmDestino) return;
    
        try {
            const res = await fetch(`./chat/carregar_mensagens.php?rmDestino=${rmDestino}`);
            const mensagens = await res.json();
    
            // Renderiza no chat apenas se for o contato aberto
            if (rmDestino === contatoSelecionado) {
                chatBody.innerHTML = '';
                mensagens.forEach(m => {
                    const div = document.createElement('div');
                    div.classList.add('mensagem');
                    div.classList.add(m.RM_origem == rmLogado ? 'enviada' : 'recebida');
                    div.textContent = m.Mensagem;
    
                    const spanHora = document.createElement('span');
                    spanHora.classList.add('data');
                    const data = new Date(m.DataEnvio);
                    spanHora.textContent = `${data.getHours().toString().padStart(2, '0')}:${data.getMinutes().toString().padStart(2, '0')}`;
                    div.appendChild(spanHora);
    
                    div.dataset.id = m.id;
    
                    // 🔹 Restaurar seleção se a mensagem estiver marcada
                    if (mensagensSelecionadas.includes(String(m.id))) {
                        div.classList.add('selecionada');
                    }
    
                    chatBody.appendChild(div);
                });
    
                // Mantém scroll no final
                chatBody.scrollTop = chatBody.scrollHeight;
    
                marcarLidas(rmDestino);
            }
    
            atualizarBadges(rmDestino, mensagens);
        } catch (err) {
            console.error('Erro ao carregar mensagens:', err);
        }
    }
    

    // ========================
    // Atualizar badges e notificações
    // ========================
    function atualizarBadges(rmDestino, mensagens) {
        contatos.forEach(c => {
            const rm = c.dataset.rm;
            const naoLidas = mensagens.filter(m => m.RM_origem == rm && m.Lida == 0);

            const badgeExistente = c.querySelector('.badge');
            if (badgeExistente) badgeExistente.remove();

            if (naoLidas.length > 0 && rm !== contatoSelecionado) {
                const badge = document.createElement('span');
                badge.classList.add('badge');
                badge.textContent = naoLidas.length;
                c.appendChild(badge);

                // última mensagem não lida
                const ultima = naoLidas[naoLidas.length - 1];

                // só notifica se ainda não foi notificada
                if (ultima && ultima.id && !mensagensNotificadas.has(ultima.id)) {
                    mensagensNotificadas.add(ultima.id);
                    dispararNotificacao(ultima, c.textContent.trim());
                }
            }
        });
    }

    // ========================
    // Notificação com som
    // ========================
    let ultimaMensagemId = null;

    function dispararNotificacao(mensagem, nomeContato) {
        if (mensagem.id === ultimaMensagemId) return; // evita repetição
        ultimaMensagemId = mensagem.id;

        const audio = document.getElementById("notificacaoAudio");
        if (audio) {
            audio.currentTime = 0;
            audio.play().catch(() => { });
        }

        if (Notification.permission === "granted") {
            new Notification("Nova mensagem de " + nomeContato, {
                body: mensagem.Mensagem,
                icon: "../../pictures/perfil sem fundo.png"
            });
        } else if (Notification.permission !== "denied") {
            Notification.requestPermission().then(permission => {
                if (permission === "granted") {
                    new Notification("Nova mensagem de " + nomeContato, {
                        body: mensagem.Mensagem,
                        icon: "../../pictures/perfil sem fundo.png"
                    });
                }
            });
        }
    }

    // ========================
    // Enviar mensagem com limite de 5 e cooldown
    // ========================
    let contadorMensagens = 0;
    let bloqueado = false;

    formEnvio.addEventListener('submit', async (e) => {
        e.preventDefault();
        if (!contatoSelecionado || !textoInput.value.trim()) return;

        if (bloqueado) {
            alert("🚫 Você atingiu o limite de 5 mensagens. Aguarde 10 segundos para continuar.");
            return;
        }

        const params = new URLSearchParams();
        params.append('rmDestino', contatoSelecionado);
        params.append('mensagem', textoInput.value.trim());

        try {
            const res = await fetch('./chat/enviar_mensagem.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: params.toString()
            });
            const data = await res.json();
            if (data.status === 'ok') {
                textoInput.value = '';
                carregarMensagens(contatoSelecionado);

                contadorMensagens++;
                if (contadorMensagens >= 5) {
                    bloqueado = true;
                    alert("🚫 Limite de 5 mensagens. Aguarde 10 segundos.");
                    formEnvio.querySelector("button, input[type=submit]").disabled = true;

                    setTimeout(() => {
                        contadorMensagens = 0;
                        bloqueado = false;
                        formEnvio.querySelector("button, input[type=submit]").disabled = false;
                    }, 10000);
                }
            }
        } catch (err) {
            console.error('Erro ao enviar mensagem:', err);
        }
    });

    // ========================
    // Marcar mensagens como lidas
    // ========================
    async function marcarLidas(rmOrigem) {
        try {
            await fetch(`chat/marcar_lidas.php?rmOrigem=${rmOrigem}`);

            // limpa notificações já lidas
            mensagensNotificadas.clear();
        } catch (err) {
            console.error('Erro ao marcar mensagens como lidas:', err);
        }
    }

    // ========================
    // Intervalo GLOBAL para verificar todos os contatos
    // ========================
    function checarNovasMensagens() {
        contatos.forEach(c => {
            const rm = c.dataset.rm;
            carregarMensagens(rm);
        });
    }

    if (globalInterval) clearInterval(globalInterval);
    globalInterval = setInterval(checarNovasMensagens, 5000);

    // ======================
    // Configurações de fonte e som
    // ======================
    const tamanhoFonteSelect = document.getElementById("tamanhoFonte");
    const somSelect = document.getElementById("somNotificacao");
    const audioNotificacao = document.getElementById("notificacaoAudio");

    tamanhoFonteSelect.addEventListener("change", () => {
        chatBody.style.fontSize = tamanhoFonteSelect.value;
    });

    somSelect.addEventListener("change", () => {
        switch (somSelect.value) {
            case "padrao":
                audioNotificacao.src = "./chat/notificacao.mp3";
                break;
            case "som1":
                audioNotificacao.src = "./chat/som1.mp3";
                break;
            case "som2":
                audioNotificacao.src = "./chat/som2.mp3";
                break;
        }
    });

    // ========================
    // Exclusão de mensagens
    // ========================
    const btnLixeira = document.getElementById("btnLixeira");
    const btnConfirmarExclusao = document.getElementById("btnConfirmarExclusao");
    const msgSelecao = document.getElementById("msgSelecao");
    const modalExclusao = new bootstrap.Modal(document.getElementById("modalExclusao"));
    const btnExcluirUnico = document.getElementById("excluirUnico");
    const btnExcluirTodos = document.getElementById("excluirTodos");

    let modoSelecao = false;
    let mensagensSelecionadas = [];

    btnLixeira.addEventListener("click", () => {
        modoSelecao = !modoSelecao;
        mensagensSelecionadas = [];
        document.querySelectorAll(".mensagem").forEach(m => m.classList.remove("selecionada"));

        btnConfirmarExclusao.style.display = modoSelecao ? "inline-block" : "none";
        msgSelecao.style.display = modoSelecao ? "block" : "none";
        btnLixeira.classList.toggle("ativo", modoSelecao);
    });

    chatBody.addEventListener("click", (e) => {
        if (!modoSelecao) return;

        const mensagem = e.target.closest(".mensagem");
        if (!mensagem) return;

        const id = mensagem.dataset.id;
        if (!id) return;

        mensagem.classList.toggle("selecionada");
        if (mensagem.classList.contains("selecionada")) {
            mensagensSelecionadas.push(id);
        } else {
            mensagensSelecionadas = mensagensSelecionadas.filter(x => x !== id);
        }
    });

    btnConfirmarExclusao.addEventListener("click", () => {
        if (mensagensSelecionadas.length === 0) {
            alert("Selecione ao menos uma mensagem.");
            return;
        }

        // verifica se todas são do usuário logado
        const mensagensDoUsuario = [...document.querySelectorAll(".mensagem.selecionada")]
            .every(m => m.classList.contains("enviada"));

        if (mensagensDoUsuario) {
            modalExclusao.show();
        } else {
            excluirMensagens("unico");
        }
    });

    btnExcluirUnico.addEventListener("click", () => excluirMensagens("unico"));
    btnExcluirTodos.addEventListener("click", () => excluirMensagens("todos"));

    async function excluirMensagens(tipo) {
        const promises = mensagensSelecionadas.map(async id => {
            try {
                const res = await fetch("chat/excluir_mensagem.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: `id=${id}&tipo=${tipo}`
                });
                const data = await res.json();
                if (data.status === "ok") {
                    document.querySelector(`.mensagem[data-id="${id}"]`)?.remove();
                }
            } catch (err) {
                console.error("Erro ao excluir mensagem:", err);
            }
        });

        await Promise.all(promises);
        mensagensSelecionadas = [];
        modoSelecao = false;
        btnConfirmarExclusao.style.display = "none";
        msgSelecao.style.display = "none";
        btnLixeira.classList.remove("ativo");
        carregarMensagens(contatoSelecionado);
    }


});
