document.addEventListener("DOMContentLoaded", function () {
  const modal = document.getElementById("modalEditarEquipe");

  if (modal) {
    modal.addEventListener("show.bs.modal", function (event) {
      const botao = event.relatedTarget;
      const equipeId = botao.getAttribute("data-id");

      // Preenche o campo hidden com o ID da equipe
      document.getElementById("editarIdEquipe").value = equipeId;

      // Limpa e desmarca todos os checkboxes
      document
        .querySelectorAll('#modalEditarEquipe input[name="membros[]"]')
        .forEach((input) => {
          input.checked = false;
        });

      // Buscar dados da equipe via fetch
      fetch(`../logica/controladores/buscar_equipe.php?id=${equipeId}`)
        .then((res) => res.json())
        .then((data) => {
          document.getElementById("editarNomeEquipe").value = data.nome;
          document
            .querySelectorAll('#modalEditarEquipe input[name="membros[]"]')
            .forEach((checkbox) => {
              const id = parseInt(checkbox.value, 10);
              if (data.membros.includes(id)) {
                checkbox.checked = true;
              }
            });
        })
        .catch((error) =>
          console.error("Erro ao buscar dados da equipe:", error)
        );
    });
  }
});

document.addEventListener("DOMContentLoaded", function () {
  const modalExcluir = document.getElementById("modalExcluirEquipe");

  if (modalExcluir) {
    modalExcluir.addEventListener("show.bs.modal", function (event) {
      const botao = event.relatedTarget;
      const equipeId = botao.getAttribute("data-id");
      const equipeNome = botao.getAttribute("data-nome");

      document.getElementById("excluirIdEquipe").value = equipeId;
      document.getElementById("excluirNomeEquipe").textContent = equipeNome;
    });
  }
});
