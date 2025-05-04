function mudarVisualizacao(modo, botao) {
  const container = document.getElementById("containerTarefas");
  if (!container) return;

  if (modo === "lista") {
    container.classList.remove("modo-grade");
    container.classList.add("modo-lista");
  } else {
    container.classList.remove("modo-lista");
    container.classList.add("modo-grade");
  }

  // Botões ativos (só se for clicado manualmente)
  if (botao) {
    const botoes = document.querySelectorAll(".btn-visualizacao");
    botoes.forEach((btn) => btn.classList.remove("active"));
    botao.classList.add("active");
  }
}

// 🔄 Ao redimensionar a tela, aplicar automaticamente o modo lista no mobile
window.addEventListener("resize", () => {
  const container = document.getElementById("containerTarefas");
  if (!container) return;

  if (window.innerWidth < 768) {
    container.classList.remove("modo-grade");
    container.classList.add("modo-lista");
  }
});

// 🟨 Também aplicar no carregamento inicial, se for mobile
document.addEventListener("DOMContentLoaded", () => {
  if (window.innerWidth < 768) {
    mudarVisualizacao("lista");
  }
});
