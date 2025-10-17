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

            carregarMensagens();

            if (chatInterval) clearInterval(chatInterval);
            chatInterval = setInterval(carregarMensagens, 3000);
        });
    });

    // ========================
    // Carregar mensagens
    // ========================
    async function carregarMensagens() {
        if (!contatoSelecionado) return;
    
        try {
            const res = await fetch(`carregar_mensagens.php?rmDestino=${contatoSelecionado}`);
            const mensagens = await res.json();
    
            chatBody.innerHTML = '';
    
            mensagens.forEach(m => {
                const div = document.createElement('div');
                div.classList.add('mensagem');
                div.classList.add(m.RM_origem == rmLogado ? 'enviada' : 'recebida');
    
                div.textContent = m.Mensagem;
                div.dataset.id = m.id;
    
                const spanHora = document.createElement('span');
                spanHora.classList.add('data');
                const data = new Date(m.DataEnvio);
                const hora = data.getHours().toString().padStart(2, '0');
                const min = data.getMinutes().toString().padStart(2, '0');
                spanHora.textContent = `${hora}:${min}`;
                div.appendChild(spanHora);
    
                // 🔹 Restaurar seleção se a mensagem estiver marcada
                if (mensagensSelecionadas.includes(String(m.id))) {
                    div.classList.add('selecionada');
                }
    
                chatBody.appendChild(div);
            });
    
            // Mantém o scroll sempre no final
            chatBody.scrollTop = chatBody.scrollHeight;
    
            // Atualiza badges e marca como lidas
            atualizarBadges(mensagens);
            marcarLidas();
    
        } catch (err) {
            console.error('Erro ao carregar mensagens:', err);
        }
    }
    

    // ========================
    // Enviar mensagem
    // ========================
    formEnvio.addEventListener('submit', async (e) => {
        e.preventDefault();
        if (!contatoSelecionado || !textoInput.value.trim()) return;

        const params = new URLSearchParams();
        params.append('rmDestino', contatoSelecionado);
        params.append('mensagem', textoInput.value.trim());

        try {
            const res = await fetch('enviar_mensagem.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: params.toString()
            });
            const data = await res.json();
            if (data.status === 'ok') {
                textoInput.value = '';
                carregarMensagens();
            }
        } catch (err) {
            console.error('Erro ao enviar mensagem:', err);
        }
    });

    // ========================
    // Marcar mensagens como lidas
    // ========================
    async function marcarLidas() {
        if (!contatoSelecionado) return;
        try {
            await fetch(`marcar_lidas.php?rmOrigem=${contatoSelecionado}`);
        } catch (err) {
            console.error('Erro ao marcar mensagens como lidas:', err);
        }
    }

    // ========================
    // Atualizar badges
    // ========================
    function atualizarBadges(mensagens) {
        contatos.forEach(c => {
            const rm = c.dataset.rm;
            const naoLidas = mensagens.filter(m => m.RM_origem == rm && m.Lida == 0).length;

            const badgeExistente = c.querySelector('.badge');
            if (badgeExistente) badgeExistente.remove();

            if (naoLidas > 0 && rm != contatoSelecionado) {
                const badge = document.createElement('span');
                badge.classList.add('badge');
                badge.textContent = naoLidas;
                c.appendChild(badge);
            }
        });
    }


    // ======================
    // Sidebar de Configurações
    // ======================
    const openConfigBtn = document.getElementById("openConfig");
    const sidebarConfig = document.getElementById("sidebarConfig");
    const closeSidebarBtn = document.getElementById("closeSidebar");

    openConfigBtn.addEventListener("click", () => {
        sidebarConfig.classList.add("active");
    });

    closeSidebarBtn.addEventListener("click", () => {
        sidebarConfig.classList.remove("active");
    });

    // Fechar sidebar clicando fora do conteúdo
    window.addEventListener("click", (e) => {
        if (e.target === sidebarConfig) {
            sidebarConfig.classList.remove("active");
        }
    });

    // ======================
    // Modal Editar Usuário
    // ======================


    (() => {
        const modalEditar = document.getElementById("modalEditar");
        const formEditar = document.getElementById("formEditar");
        const msgEditar = document.getElementById("msgEditar");

        // Abrir modal
        const openModal = () => {
            if (!modalEditar) return console.warn("modalEditar não encontrado no DOM");
            modalEditar.style.display = "flex";
            setTimeout(() => modalEditar.classList.add("active"), 8);
            modalEditar.setAttribute("aria-hidden", "false");
            document.body.style.overflow = "hidden";
            preencherCampos(); // Preenche os campos com dados atuais do usuário
        };

        // Fechar modal
        const closeModal = () => {
            if (!modalEditar) return;
            modalEditar.classList.remove("active");
            setTimeout(() => {
                if (modalEditar) modalEditar.style.display = "none";
            }, 120);
            modalEditar.setAttribute("aria-hidden", "true");
            document.body.style.overflow = "";
            msgEditar.textContent = ""; // limpa mensagem
        };

        // Botões abrir/fechar
        document.addEventListener("click", (e) => {
            const openBtn = e.target.closest("#openEditar");
            const closeBtn = e.target.closest("#closeEditar");
            if (openBtn) {
                e.preventDefault();
                openModal();
            }
            if (closeBtn) {
                e.preventDefault();
                closeModal();
            }
        });

        // ESC fecha
        document.addEventListener("keydown", (e) => {
            if (e.key === "Escape" && modalEditar && modalEditar.style.display === "flex") {
                closeModal();
            }
        });

        // Preenche os campos do modal com os dados do usuário logado
        // Preenche os campos do modal com os dados do usuário logado
        function preencherCampos() {
            formEditar.querySelector("#rmUser").value = window.__CHAT__?.rmLogado || "";
            formEditar.querySelector("#nomeUser").value = window.__CHAT__?.nome || "";
            formEditar.querySelector("#cpfUser").value = window.__CHAT__?.cpf || ""; // hidden
            formEditar.querySelector("#cpfMasked").value = window.__CHAT__?.cpf ? '•'.repeat(window.__CHAT__.cpf.replace(/\D/g, '').length) : ''; // mascarado
            formEditar.querySelector("#tipoUser").value = window.__CHAT__?.tipo || "";
            formEditar.querySelector("#emailUser").value = window.__CHAT__?.email || "";
            formEditar.querySelector("#confirmaEmailUser").value = window.__CHAT__?.email || "";
            formEditar.querySelector("#senhaUser").value = "";
            formEditar.querySelector("#confirmaSenhaUser").value = "";
        }



        // Submissão do form
        if (formEditar) {
            formEditar.addEventListener("submit", async (ev) => {
                ev.preventDefault();
                if (!msgEditar) return;

                msgEditar.textContent = "";
                msgEditar.style.color = "";

                const email = formEditar.querySelector("#emailUser")?.value?.trim() ?? "";
                const confirmaEmail = formEditar.querySelector("#confirmaEmailUser")?.value?.trim() ?? "";
                const senha = formEditar.querySelector("#senhaUser")?.value ?? "";
                const confirmaSenha = formEditar.querySelector("#confirmaSenhaUser")?.value ?? "";

                // Validações simples
                if (email && confirmaEmail && email !== confirmaEmail) {
                    msgEditar.textContent = "❌ Os e-mails não coincidem.";
                    msgEditar.style.color = "crimson";
                    return;
                }
                if (senha && confirmaSenha && senha !== confirmaSenha) {
                    msgEditar.textContent = "❌ As senhas não coincidem.";
                    msgEditar.style.color = "crimson";
                    return;
                }

                // Envia para o backend
                try {
                    const fd = new FormData(formEditar);
                    const res = await fetch("atualizar_usuario.php", {
                        method: "POST",
                        body: fd
                    });

                    const json = await res.json().catch(() => null);
                    if (json && json.status === "ok") {
                        msgEditar.textContent = json.msg || "✅ Perfil atualizado com sucesso!";
                        msgEditar.style.color = "green";

                        // Atualiza dados globais para refletir no chat
                        window.__CHAT__.nome = formEditar.querySelector("#nomeUser")?.value ?? window.__CHAT__.nome;
                        window.__CHAT__.cpf = formEditar.querySelector("#cpfUser")?.value ?? window.__CHAT__.cpf;
                        window.__CHAT__.email = formEditar.querySelector("#emailUser")?.value ?? window.__CHAT__.email;

                        setTimeout(closeModal, 900);
                        // Atualiza o nome exibido no header
                        const userSpan = document.querySelector(".user-container span");
                        if (userSpan) userSpan.textContent = window.__CHAT__.nome;
                    } else {
                        msgEditar.textContent = (json && json.msg) ? json.msg : "❌ Erro ao atualizar perfil.";
                        msgEditar.style.color = "crimson";
                    }
                } catch (err) {
                    console.error("Erro na requisição atualizar_usuario:", err);
                    msgEditar.textContent = "❌ Erro de rede. Tente novamente.";
                    msgEditar.style.color = "crimson";
                }
            });
        }
    })();


    // ======================
    // Exclusão de mensagens
    // ======================
    let modoSelecao = false;
    let mensagensSelecionadas = [];

    const btnLixeira = document.getElementById("btnLixeira");
    const btnConfirmar = document.getElementById("btnConfirmarExclusao");
    const avisoSelecao = document.getElementById("msgSelecao");
    const btnExcluirSoParaMim = document.getElementById("btnExcluirSoParaMim");
    const btnExcluirParaTodos = document.getElementById("btnExcluirParaTodos");
    


    if (btnExcluirSoParaMim) {
        btnExcluirSoParaMim.addEventListener("click", () => confirmarExclusao("unico"));
    }
    if (btnExcluirParaTodos) {
        btnExcluirParaTodos.addEventListener("click", () => confirmarExclusao("todos"));
    }

    // insere o aviso logo abaixo do título
    chatTitulo.insertAdjacentElement("afterend", avisoSelecao);

    // Clique no botão da lixeira
    btnLixeira.addEventListener("click", () => {
        modoSelecao = !modoSelecao;
        mensagensSelecionadas = [];
        document.querySelectorAll(".mensagem").forEach(msg => msg.classList.remove("selecionada"));

        btnConfirmar.style.display = modoSelecao ? "inline-block" : "none";
        avisoSelecao.style.display = modoSelecao ? "block" : "none";
    });

    // Clique em mensagens
    document.addEventListener("click", (e) => {
        if (!modoSelecao) return;

        const msgDiv = e.target.closest(".mensagem");
        if (!msgDiv) return;

        const id = msgDiv.dataset.id;
        if (!id) return;

        // alterna seleção
        if (msgDiv.classList.contains("selecionada")) {
            msgDiv.classList.remove("selecionada");
            mensagensSelecionadas = mensagensSelecionadas.filter(m => m !== id);
        } else {
            msgDiv.classList.add("selecionada");
            mensagensSelecionadas.push(id);
        }
    });

    // Confirmar exclusão
    btnConfirmar.addEventListener("click", () => {
        if (mensagensSelecionadas.length === 0) {
            alert("Selecione pelo menos uma mensagem.");
            return;
        }

        // Verifica se todas são mensagens enviadas pelo usuário logado
        const somenteMinhas = mensagensSelecionadas.every(id => {
            const msgDiv = document.querySelector(`.mensagem[data-id="${id}"]`);
            return msgDiv && msgDiv.classList.contains("enviada");
        });



        if (somenteMinhas) {
            // abre o modal bonitinho para o usuário escolher
            const modalEl = document.getElementById("modalExcluir");
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
            // os botões do modal já têm listeners (definidos mais abaixo)
        } else {
            // se tiver mensagens que não são suas, só exclui "para mim"
            excluirSelecionadas("unico");
        }
    });



    // Função que faz as requisições para excluir as mensagens selecionadas
    function excluirSelecionadas(tipo) {
        // Faz uma cópia dos ids pra evitar problemas enquanto removemos do DOM
        const ids = [...mensagensSelecionadas];
        const promises = ids.map(id => {
            const body = `id=${encodeURIComponent(id)}&tipo=${encodeURIComponent(tipo)}`;
            return fetch("excluir_mensagem.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body
            })
                .then(res => res.json().catch(() => ({ status: "erro", message: "Resposta inválida" })))
                .then(r => ({ id, r }));
        });

        Promise.all(promises)
            .then(results => {
                results.forEach(({ id, r }) => {
                    if (r.status === "ok" || r.success === true) {
                        // remove do DOM
                        document.querySelector(`.mensagem[data-id="${id}"]`)?.remove();
                    } else {
                        console.warn("Erro ao excluir id", id, r);
                        alert(r.message || r.error || `Falha ao excluir mensagem ${id}`);
                    }
                });

                // Limpar seleção e UI
                modoSelecao = false;
                mensagensSelecionadas = [];
                document.querySelectorAll(".mensagem").forEach(msg => msg.classList.remove("selecionada"));
                btnConfirmar.style.display = "none";
                avisoSelecao.style.display = "none";
            })
            .catch(err => {
                console.error("Erro ao excluir mensagens:", err);
                alert("Erro de rede ao excluir mensagens.");
            });
    }

    // Função chamada pelos botões do modal
    function confirmarExclusao(tipo) {
        excluirSelecionadas(tipo);
    }




// ======================
// Alterar tamanho da fonte
// ======================
const tamanhoFonteSelect = document.getElementById("tamanhoFonte");
const chatMensagens = document.getElementById("chatBody");

tamanhoFonteSelect.addEventListener("change", () => {
    chatMensagens.style.fontSize = tamanhoFonteSelect.value;
});

// ======================
// Som de notificação
// ======================
const somSelect = document.getElementById("somNotificacao");
const audioNotificacao = document.getElementById("notificacaoAudio");

somSelect.addEventListener("change", () => {
    switch (somSelect.value) {
        case "padrao":
            audioNotificacao.src = "notificacao.mp3";
            break;
        case "som1":
            audioNotificacao.src = "som1.mp3";
            break;
        case "som2":
            audioNotificacao.src = "som2.mp3";
            break;
    }
});




});
