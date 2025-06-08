//console.log("✅ js_tarefas.js carregado com sucesso");

/* =====================================================
   BLOCO 1: Visualização Grade / Lista
===================================================== */
function mudarVisualizacao(modo, botao) {
  const container = document.getElementById("containerTarefas");
  if (!container) return;

  localStorage.setItem("modoVisualizacao", modo);
  container.classList.toggle("modo-grade", modo === "grade");
  container.classList.toggle("modo-lista", modo === "lista");

  if (botao) {
    document
      .querySelectorAll(".btn-visualizacao")
      .forEach((b) => b.classList.remove("active"));
    botao.classList.add("active");
  }

  container.querySelectorAll("[data-tarefa]").forEach((card) => {
    card.classList.toggle("modo-grade-card", modo === "grade");
  });
}

document.addEventListener("DOMContentLoaded", () => {
  const modoSalvo = localStorage.getItem("modoVisualizacao") || "grade";
  const isMobile = window.innerWidth < 768;
  mudarVisualizacao(isMobile ? "lista" : modoSalvo);
});

window.addEventListener("resize", () => {
  if (window.innerWidth < 768) mudarVisualizacao("lista");
});

/* =====================================================
   BLOCO 2: Filtro por Responsável
===================================================== */
document
  .getElementById("filtroResponsavel")
  ?.addEventListener("change", function () {
    const selectedValue = this.value;
    window.location.href = `tarefas.php?usuario_id=${selectedValue}`;
  });

/* =====================================================
   BLOCO 3: Criticidade Alta - Adicionar
===================================================== */
document.addEventListener("DOMContentLoaded", () => {
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
    const logado = responsavel.dataset.userLogado;
    const tipoUsuario = responsavel.dataset.tipoUsuario;
    const condicao =
      tipoUsuario === "funcionario" && isAlta && idResp && idResp !== logado;

    alerta.classList.toggle("d-none", !condicao);
    divComentario.classList.toggle("d-none", !condicao);
    btnCriarTarefa.textContent = condicao
      ? "Enviar para Aprovação"
      : "Criar Tarefa";
    btnCriarTarefa.classList.toggle("btn-warning", condicao);
    btnCriarTarefa.classList.toggle("btn-success", !condicao);
  }

  criticidade?.addEventListener("change", verificarCriticidade);
  responsavel?.addEventListener("change", verificarCriticidade);
  document
    .getElementById("modalAdicionarTarefa")
    ?.addEventListener("shown.bs.modal", verificarCriticidade);
});

/* =====================================================
   BLOCO 4: Alterar Status (Iniciar / Concluir)
===================================================== */
function alterarStatus(id, novoStatus) {
  fetch("../logica/controladores/alterar_status.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `id_tarefa=${id}&novo_status=${encodeURIComponent(novoStatus)}`,
  })
    .then((res) => res.json())
    .then((res) =>
      res.sucesso
        ? location.reload()
        : alert(res.erro || "Erro ao alterar status.")
    )
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
   BLOCO 5: Criticidade Alta - Editar
===================================================== */
function verificarCriticidadeEdicao() {
  const crit = document.getElementById("editar_criticidade").value;
  const resp = document.getElementById("editar_responsavel_id").value;
  const logado = document.getElementById("editar_responsavel_id").dataset
    .userLogado;
  const tipoUsuario = document.getElementById("editar_responsavel_id").dataset
    .tipoUsuario;
  const alerta = document.getElementById("editar_alertaCriticidadeAlta");
  const comentario = document.getElementById("editar_divComentarioGestor");

  let tipo = "",
    id = "";
  if (resp.includes("_")) [tipo, id] = resp.split("_");

  const condicao =
    crit === "Alta" && id !== logado && tipoUsuario === "funcionario";
  alerta.classList.toggle("d-none", !condicao);
  comentario.classList.toggle("d-none", !condicao);
}

document.addEventListener("DOMContentLoaded", () => {
  document
    .getElementById("editar_criticidade")
    ?.addEventListener("change", verificarCriticidadeEdicao);
  document
    .getElementById("editar_responsavel_id")
    ?.addEventListener("change", verificarCriticidadeEdicao);
  document
    .getElementById("modalEditarTarefa")
    ?.addEventListener("shown.bs.modal", verificarCriticidadeEdicao);
});

/* =====================================================
   BLOCO 6: Preencher Modal Editar
===================================================== */
document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".editar-tarefa").forEach((btn) =>
    btn.addEventListener("click", () => {
      const id = btn.dataset.id;

      fetch(`../logica/controladores/buscar_tarefa.php?id=${id}`)
        .then((res) => res.json())
        .then((tarefa) => {
          if (tarefa.erro) return alert("Erro ao carregar dados.");

          document.getElementById("editar_tarefa_id").value = tarefa.id;
          document.getElementById("editar_titulo_tarefa").value =
            tarefa.descricao;
          document.getElementById(
            "editar_responsavel_id"
          ).value = `${tarefa.atribuido_para_tipo}_${tarefa.atribuido_para}`;
          document.getElementById("editar_criticidade").value =
            tarefa.criticidade;
          document.getElementById("editar_comentario_gestor").value =
            tarefa.justificativa_funcionario ?? "";
          verificarCriticidadeEdicao();

          new bootstrap.Modal(
            document.getElementById("modalEditarTarefa")
          ).show();
        })
        .catch((err) => {
          console.error("Erro:", err);
          alert("Erro ao carregar dados.");
        });
    })
  );
});

/* =====================================================
   BLOCO 7: Excluir Tarefa
===================================================== */
document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".excluir-tarefa").forEach((btn) =>
    btn.addEventListener("click", () => {
      const id = btn.dataset.id;
      if (!confirm("Tem certeza que deseja excluir esta tarefa?")) return;

      fetch("../logica/controladores/excluir_tarefa.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `id_tarefa=${encodeURIComponent(id)}`,
      })
        .then((res) => res.json())
        .then((res) =>
          res.sucesso
            ? location.reload()
            : alert(res.erro || "Erro ao excluir.")
        )
        .catch((err) => {
          console.error("Erro:", err);
          alert("Erro na comunicação com o servidor.");
        });
    })
  );
});

/* =====================================================
   BLOCO 8: Filtro por Status
===================================================== */
document
  .getElementById("filtroStatus")
  ?.addEventListener("change", function () {
    const status = this.value;
    const params = new URLSearchParams(window.location.search);
    params.set("status", status);
    window.location.search = params.toString();
  });
