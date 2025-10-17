// Validação visual do campo RM
const rmInput = document.getElementById("rm");
const rmGroup = document.getElementById("userGroup");

rmInput.addEventListener("input", () => {
    const valor = rmInput.value;

    if (/[^0-9]/.test(valor)) { // Contém letras ou caracteres inválidos
        rmGroup.classList.remove("success");
        rmGroup.classList.add("error");
    } else if (valor.trim() !== "") { // Apenas números e não vazio
        rmGroup.classList.remove("error");
        rmGroup.classList.add("success");
    } else { // Campo vazio
        rmGroup.classList.remove("error", "success");
    }
});

// Evento de login

document.getElementById("btnLogin").addEventListener("click", function () {
  const rm = document.getElementById("rm").value.trim();
  const senha = document.getElementById("senha").value.trim();
  const mensagem = document.getElementById("mensagem");

  if (rm === "" || senha === "") {
      mensagem.style.color = "red";
      mensagem.textContent = "Preencha o RM e a senha.";
      return;
  }

  fetch("login.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `rm=${encodeURIComponent(rm)}&senha=${encodeURIComponent(senha)}`
  })
  .then(res => res.json())
  .then(data => {
      if (data.sucesso) {
          mensagem.style.color = "green";
          mensagem.textContent = "Login realizado com sucesso!";
          setTimeout(() => {
              window.location.href = data.pagina; // << usa a página que veio do PHP
          }, 1000);
      } else {
          mensagem.style.color = "red";
          mensagem.textContent = data.erro || "RM ou senha incorretos.";
      }
  })
  .catch(err => {
      console.error("Erro:", err);
      mensagem.style.color = "red";
      mensagem.textContent = "Erro ao conectar com o servidor.";
  });
});

//Entrar com Enter
const formLogin = document.getElementById("formLogin");
const btnLogin = document.getElementById("btnLogin");

formLogin.addEventListener("keydown", function(e) {
    // Se a tecla for Enter e não estiver com Shift
    if (e.key === "Enter" && !e.shiftKey) {
        e.preventDefault(); // evita comportamento padrão
        btnLogin.click();   // dispara o botão
    }
});
