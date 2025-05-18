console.log("✅ js_tarefas.js carregado com sucesso");

// Alternar visualização: grade <-> lista
function mudarVisualizacao(modo, botao) {
  const container = document.getElementById("containerTarefas");
  if (!container) return;

  // Salva o modo no localStorage
  localStorage.setItem("modoVisualizacao", modo);

  if (modo === "lista") {
    container.classList.remove("modo-grade");
    container.classList.add("modo-lista");
  } else {
    container.classList.remove("modo-lista");
    container.classList.add("modo-grade");
  }

  if (botao) {
    const botoes = document.querySelectorAll(".btn-visualizacao");
    botoes.forEach((btn) => btn.classList.remove("active"));
    botao.classList.add("active");
  }
}

// Aplicar modo lista automaticamente em telas pequenas
window.addEventListener("resize", () => {
  const container = document.getElementById("containerTarefas");
  if (!container) return;

  if (window.innerWidth < 768) {
    container.classList.remove("modo-grade");
    container.classList.add("modo-lista");
  }
});

// Ao carregar a página
document.addEventListener("DOMContentLoaded", () => {
  // Auto modo lista no mobile
  if (window.innerWidth < 768) {
    mudarVisualizacao("lista");
  }

  // 🌟 Lógica de criticidade alta (modal adicionar)
  const criticidade = document.getElementById("criticidade");
  const responsavel = document.getElementById("responsavel_id");
  const alerta = document.getElementById("alertaCriticidadeAlta");
  const divComentario = document.getElementById("divComentarioGestor");
  const btnCriarTarefa = document.getElementById("btnCriarTarefa");
  const usuarioLogadoId = responsavel?.dataset.userLogado;
  const tipoUsuario = responsavel?.dataset.tipoUsuario;

  function verificarCriticidade() {
    if (!criticidade || !responsavel || !usuarioLogadoId) return;

    const isAlta = criticidade.value === "Alta";
    const responsavelAtual = responsavel.value;

    if (
      tipoUsuario === "funcionario" &&
      isAlta &&
      responsavelAtual &&
      responsavelAtual.toString() !== usuarioLogadoId.toString()
    ) {
      alerta.classList.remove("d-none");
      divComentario.classList.remove("d-none");
      btnCriarTarefa.textContent = "Enviar para Aprovação";
      btnCriarTarefa.classList.replace("btn-success", "btn-warning");
    } else {
      alerta.classList.add("d-none");
      divComentario.classList.add("d-none");
      btnCriarTarefa.textContent = "Criar Tarefa";
      btnCriarTarefa.classList.replace("btn-warning", "btn-success");
    }
  }

  criticidade?.addEventListener("change", verificarCriticidade);
  responsavel?.addEventListener("change", verificarCriticidade);

  const modal = document.getElementById("modalAdicionarTarefa");
  modal?.addEventListener("shown.bs.modal", () => {
    verificarCriticidade();
  });
});

document.addEventListener("DOMContentLoaded", () => {
  const modoSalvo = localStorage.getItem("modoVisualizacao") || "grade";
  mudarVisualizacao(modoSalvo);
});

document
  .getElementById("filtroResponsavel")
  .addEventListener("change", function () {
    const selectedValue = this.value;
    window.location.href = `tarefas.php?usuario_id=${selectedValue}`;
  });

function alterarStatus(idTarefa, novoStatus) {
  fetch("../logica/controladores/alterar_status.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: `id_tarefa=${idTarefa}&novo_status=${encodeURIComponent(novoStatus)}`,
  })
    .then((res) => res.json())
    .then((res) => {
      if (res.sucesso) {
        location.reload();
      } else {
        alert(res.erro || "Erro ao alterar status.");
      }
    })
    .catch((err) => {
      console.error("Erro:", err);
      alert("Falha na comunicação com o servidor.");
    });
}

document.addEventListener("DOMContentLoaded", () => {
  document
    .querySelectorAll(".iniciar-tarefa")
    .forEach((btn) =>
      btn.addEventListener("click", () =>
        alterarStatus(btn.dataset.id, "Em andamento")
      )
    );

  document
    .querySelectorAll(".concluir-tarefa")
    .forEach((btn) =>
      btn.addEventListener("click", () =>
        alterarStatus(btn.dataset.id, "Concluída")
      )
    );
});

function verificarCriticidadeEdicao() {
  const criticidade = document.getElementById("editar_criticidade").value;
  const responsavel = document.getElementById("editar_responsavel_id").value;
  const usuarioLogado = document.getElementById("editar_responsavel_id").dataset
    .userLogado;
  const tipoUsuario = document.getElementById("editar_responsavel_id").dataset
    .tipoUsuario;

  const alerta = document.getElementById("editar_alertaCriticidadeAlta");
  const comentario = document.getElementById("editar_divComentarioGestor");

  const altaParaOutro =
    criticidade === "Alta" &&
    responsavel !== usuarioLogado &&
    tipoUsuario !== "admin";

  if (altaParaOutro) {
    alerta.classList.remove("d-none");
    comentario.classList.remove("d-none");
  } else {
    alerta.classList.add("d-none");
    comentario.classList.add("d-none");
  }
}

document.addEventListener("DOMContentLoaded", () => {
  const editCrit = document.getElementById("editar_criticidade");
  const editResp = document.getElementById("editar_responsavel_id");

  editCrit?.addEventListener("change", verificarCriticidadeEdicao);
  editResp?.addEventListener("change", verificarCriticidadeEdicao);
});

document.addEventListener("DOMContentLoaded", () => {
  const editarBotoes = document.querySelectorAll(".editar-tarefa");

  editarBotoes.forEach((botao) => {
    botao.addEventListener("click", () => {
      const tarefaId = botao.dataset.id;

      fetch(`../logica/controladores/buscar_tarefa.php?id=${tarefaId}`)
        .then((res) => res.json())
        .then((tarefa) => {
          if (tarefa.erro) {
            alert("Erro ao carregar dados da tarefa.");
            return;
          }

          document.getElementById("editar_tarefa_id").value = tarefa.id;
          document.getElementById("editar_titulo_tarefa").value =
            tarefa.descricao;
          document.getElementById("editar_responsavel_id").value =
            tarefa.atribuido_para;
          document.getElementById("editar_criticidade").value =
            tarefa.criticidade;

          verificarCriticidadeEdicao();

          const modal = new bootstrap.Modal(
            document.getElementById("modalEditarTarefa")
          );
          modal.show();
        })
        .catch((err) => {
          console.error("Erro ao buscar tarefa:", err);
          alert("Erro ao carregar dados da tarefa.");
        });
    });
  });
});

document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".excluir-tarefa").forEach((btn) => {
    btn.addEventListener("click", () => {
      const tarefaId = btn.dataset.id;

      if (!confirm("Tem certeza que deseja excluir esta tarefa?")) return;

      fetch("../logica/controladores/excluir_tarefa.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: `id_tarefa=${encodeURIComponent(tarefaId)}`,
      })
        .then((res) => res.json())
        .then((res) => {
          if (res.sucesso) {
            location.reload();
          } else {
            alert(res.erro || "Erro ao excluir tarefa.");
          }
        })
        .catch((err) => {
          console.error("Erro ao excluir:", err);
          alert("Erro na comunicação com o servidor.");
        });
    });
  });
});
