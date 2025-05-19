console.log("✅ js_tarefas.js carregado com sucesso");

/* =====================================================
   BLOCO 1: Alteração de Visualização (Grade / Lista)
===================================================== */
function mudarVisualizacao(modo, botao) {
  const container = document.getElementById("containerTarefas");
  if (!container) return;
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

// Responsivo: aplica modo lista automaticamente em telas pequenas
window.addEventListener("resize", () => {
  const container = document.getElementById("containerTarefas");
  if (!container) return;
  if (window.innerWidth < 768) {
    container.classList.remove("modo-grade");
    container.classList.add("modo-lista");
  }
});

// Aplica visualização salva ao carregar a página
document.addEventListener("DOMContentLoaded", () => {
  const modoSalvo = localStorage.getItem("modoVisualizacao") || "grade";
  mudarVisualizacao(modoSalvo);
});

/* =====================================================
   BLOCO 2: Filtro por Responsável (Select)
===================================================== */
document
  .getElementById("filtroResponsavel")
  .addEventListener("change", function () {
    const selectedValue = this.value;
    window.location.href = `tarefas.php?usuario_id=${selectedValue}`;
  });

/* =====================================================
   BLOCO 3: Lógica de Criticidade Alta (Modal Adicionar)
===================================================== */
document.addEventListener("DOMContentLoaded", () => {
  // Auto modo lista no mobile
  if (window.innerWidth < 768) mudarVisualizacao("lista");

  // Elementos do modal adicionar tarefa
  const criticidade = document.getElementById("criticidade");
  const responsavel = document.getElementById("responsavel_id");
  const alerta = document.getElementById("alertaCriticidadeAlta");
  const divComentario = document.getElementById("divComentarioGestor");
  const btnCriarTarefa = document.getElementById("btnCriarTarefa");

  function getInfoResponsavel() {
    const [tipoResp, idResp] = responsavel.value.split("_");
    return { tipoResp, idResp };
  }

  function verificarCriticidade() {
    if (!criticidade || !responsavel) return;
    const isAlta = criticidade.value === "Alta";
    const { tipoResp, idResp } = getInfoResponsavel();
    const usuarioLogadoId = responsavel.dataset.userLogado;
    const tipoUsuario = responsavel.dataset.tipoUsuario;

    if (
      tipoUsuario === "funcionario" &&
      isAlta &&
      idResp &&
      idResp !== usuarioLogadoId
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

  // Eventos no modal adicionar tarefa
  criticidade?.addEventListener("change", verificarCriticidade);
  responsavel?.addEventListener("change", verificarCriticidade);

  const modal = document.getElementById("modalAdicionarTarefa");
  modal?.addEventListener("shown.bs.modal", () => {
    verificarCriticidade();
  });
});

/* =====================================================
   BLOCO 4: Alterar Status de Tarefa (Iniciar / Concluir)
===================================================== */
function alterarStatus(idTarefa, novoStatus) {
  fetch("../logica/controladores/alterar_status.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
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

/* =====================================================
   BLOCO 5: Lógica de Criticidade Alta (Modal Editar)
===================================================== */
function verificarCriticidadeEdicao() {
  const criticidade = document.getElementById("editar_criticidade").value;
  const responsavel = document.getElementById("editar_responsavel_id").value;
  const usuarioLogado = document.getElementById("editar_responsavel_id").dataset
    .userLogado;
  const tipoUsuario = document.getElementById("editar_responsavel_id").dataset
    .tipoUsuario;
  const alerta = document.getElementById("editar_alertaCriticidadeAlta");
  const comentario = document.getElementById("editar_divComentarioGestor");

  // Extração do tipo e id do responsável selecionado
  let tipoResp = "",
    idResp = "";
  if (responsavel && responsavel.includes("_")) {
    [tipoResp, idResp] = responsavel.split("_");
  }
  const altaParaOutro =
    criticidade === "Alta" &&
    idResp !== usuarioLogado &&
    tipoUsuario === "funcionario";

  if (altaParaOutro) {
    alerta.classList.remove("d-none");
    comentario.classList.remove("d-none");
  } else {
    alerta.classList.add("d-none");
    comentario.classList.add("d-none");
  }
}

// Adiciona listeners no modal editar
document.addEventListener("DOMContentLoaded", () => {
  const editCrit = document.getElementById("editar_criticidade");
  const editResp = document.getElementById("editar_responsavel_id");
  editCrit?.addEventListener("change", verificarCriticidadeEdicao);
  editResp?.addEventListener("change", verificarCriticidadeEdicao);

  const modalEditar = document.getElementById("modalEditarTarefa");
  modalEditar?.addEventListener("shown.bs.modal", verificarCriticidadeEdicao);
});

/* =====================================================
   BLOCO 6: Abrir Modal Editar Tarefa + Preencher Dados
===================================================== */
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

          // Preenche os campos do modal
          document.getElementById("editar_tarefa_id").value = tarefa.id;
          document.getElementById("editar_titulo_tarefa").value =
            tarefa.descricao;

          // AJUSTE AQUI: Seleciona corretamente o responsável
          const valorResponsavel =
            tarefa.atribuido_para_tipo + "_" + tarefa.atribuido_para;
          document.getElementById("editar_responsavel_id").value =
            valorResponsavel;

          document.getElementById("editar_criticidade").value =
            tarefa.criticidade;
          document.getElementById("editar_comentario_gestor").value =
            tarefa.justificativa_funcionario ?? "";

          verificarCriticidadeEdicao();

          // Exibe o modal de edição
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

/* =====================================================
   BLOCO 7: Excluir Tarefa
===================================================== */
document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".excluir-tarefa").forEach((btn) => {
    btn.addEventListener("click", () => {
      const tarefaId = btn.dataset.id;
      if (!confirm("Tem certeza que deseja excluir esta tarefa?")) return;

      fetch("../logica/controladores/excluir_tarefa.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
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

/* =====================================================
   BLOCO 8: Filtro de Status Atividade
===================================================== */

document.getElementById("filtroStatus").addEventListener("change", function () {
  const status = this.value;
  const params = new URLSearchParams(window.location.search);
  params.set("status", status);
  window.location.search = params.toString();
});
