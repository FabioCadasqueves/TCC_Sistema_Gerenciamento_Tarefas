/*document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".avaliar-btn").forEach((botao) => {
    botao.addEventListener("click", () => {
      const id = botao.dataset.id;
      const descricao = botao.dataset.descricao;
      const criticidade = botao.dataset.criticidade;
      const responsavel = botao.dataset.responsavelNome;

      // Preenche os campos do modal
      document.getElementById("tituloTarefa").value = descricao;
      document.getElementById("criticidade").value = criticidade;

      // Seleciona o valor do responsável, se existir no select
      const selectResponsavel = document.getElementById("responsavel");
      Array.from(selectResponsavel.options).forEach((opt) => {
        if (opt.textContent.trim() === responsavel.trim()) {
          opt.selected = true;
        }
      });

      // Armazena o ID da tarefa em campo oculto (você precisa adicionar esse input no form!)
      document.getElementById("idTarefaAvaliacao").value = id;

      // Abre o modal manualmente (caso não esteja com data-bs-toggle)
      const modal = new bootstrap.Modal(
        document.getElementById("modalAvaliarSolicitacao")
      );
      modal.show();
    });
  });
});*/

document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".avaliar-btn").forEach((botao) => {
    botao.addEventListener("click", () => {
      // Coleta os dados do botão
      const id = botao.dataset.id;
      const descricao = botao.dataset.descricao;
      const criticidade = botao.dataset.criticidade;
      const responsavelId = botao.dataset.responsavelId;
      const justificativa = botao.dataset.justificativa;
      const solicitante = botao.dataset.solicitante;

      // Preenche os campos do modal
      document.getElementById("idTarefaAvaliacao").value = id;
      document.getElementById("tituloTarefa").value = descricao;
      document.getElementById("criticidade").value = criticidade;
      document.getElementById("nomeSolicitante").value = solicitante;
      document.getElementById("responsavel_id").value = responsavelId;
      document.getElementById("justificativaFuncionario").value = justificativa;

      const select = document.getElementById("responsavel_id");

      // Abre o modal
      const modal = new bootstrap.Modal(
        document.getElementById("modalAvaliarSolicitacao")
      );
      modal.show();
    });
  });
});
