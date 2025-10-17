document.addEventListener("DOMContentLoaded", () => {
    // --------- Função dos botões ---------
    const botoes = document.querySelectorAll(".btn-local");
    botoes.forEach(botao => {
        botao.addEventListener("click", () => {
            const local = botao.getAttribute("data-local");
            alert(`Você selecionou: ${local}`);
        });
    });

    // --------- Função de toggle da sidebar ---------
    const toggleButton = document.getElementById('menu-toggle');
    const sidebar = document.querySelector('.sidebar');

    toggleButton.addEventListener('click', () => {
        sidebar.classList.toggle('collapsed');
    });
});

  // --------- Carregar páginas na main-content ---------
  const links = document.querySelectorAll(".sidebar a");
  const mainContent = document.querySelector(".main-content");

  function carregarPagina(page) {
    fetch(page, { credentials: 'same-origin' }) // garante envio do cookie de sessão
      .then(res => {
        if (res.status === 401) {
          // não autenticado
          mainContent.innerHTML = '<p>Você precisa estar logado. <a href=\"../index.html\">Entrar</a></p>';
          throw new Error('Não autenticado');
        }
        if (!res.ok) {
          return res.text().then(text => {
            mainContent.innerHTML = '<p>Erro ao carregar a página (status ' + res.status + ')</p>';
            throw new Error(text || 'Erro ao carregar');
          });
        }
        return res.text();
      })
      .then(html => {
        mainContent.innerHTML = html;
  
        // Atualiza o histórico do navegador (opcional)
        try { window.history.pushState({ page }, '', page); } catch(e) {}
  
        // Observação: scripts inline no HTML retornado por fetch **não** executam automaticamente.
        // Se precisar executar JS, evite usar <script> no partial; em vez disso, execute funções aqui.
      })
      .catch(err => console.error('carregarPagina:', err));
  }


document.addEventListener('DOMContentLoaded', () => {
  const links = document.querySelectorAll(".sidebar a");
  links.forEach(link => {
    link.addEventListener("click", (e) => {
      e.preventDefault();
      // seta active
      links.forEach(l => l.classList.remove("active"));
      link.classList.add("active");
      const page = link.getAttribute("data-page");
      carregarPagina(page);
    });
  });

  // carrega página padrão
  const ativo = document.querySelector(".sidebar a.active");
  if (ativo) carregarPagina(ativo.getAttribute("data-page"));
});



  
