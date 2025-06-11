// ===============================
// Geração de PIN numérico
// ===============================
function gerarPIN(tamanho = 6) {
  let pin = "";
  for (let i = 0; i < tamanho; i++) {
    pin += Math.floor(Math.random() * 10);
  }
  return pin;
}

// ===============================
// Espera o DOM carregar para aplicar os listeners
// ===============================
document.addEventListener("DOMContentLoaded", function () {
  // ===============================
  // Preenche PIN ao abrir modal de novo funcionário
  // ===============================
  const modalNovoFuncionario = document.getElementById("modalNovoFuncionario");
  if (modalNovoFuncionario) {
    modalNovoFuncionario.addEventListener("show.bs.modal", function () {
      const campoPin = document.getElementById("pinFuncionario");
      if (campoPin) campoPin.value = gerarPIN(6);
    });
  }

  // ===============================
  // Botão de copiar PIN no modal de adicionar
  // ===============================
  const btnCopiar = document.getElementById("copiarPin");
  const mensagem = document.getElementById("mensagemCopiado");
  if (btnCopiar && mensagem) {
    btnCopiar.addEventListener("click", function () {
      const campoPin = document.getElementById("pinFuncionario");
      navigator.clipboard.writeText(campoPin.value).then(() => {
        mensagem.classList.remove("d-none");
        setTimeout(() => {
          mensagem.classList.add("d-none");
        }, 1500);
      });
    });
  }

  // ===============================
  // Edição de funcionário – copiar PIN
  // ===============================
  const btnCopiarEditar = document.getElementById("btnCopiarPinEditar");
  const campoEditarPin = document.getElementById("editarPinFuncionario");
  const feedbackEditar = document.getElementById("feedbackPin");
  if (btnCopiarEditar && campoEditarPin && feedbackEditar) {
    btnCopiarEditar.addEventListener("click", function () {
      navigator.clipboard.writeText(campoEditarPin.value).then(() => {
        feedbackEditar.classList.remove("d-none");
        setTimeout(() => {
          feedbackEditar.classList.add("d-none");
        }, 1500);
      });
    });
  }

  // ===============================
  // Edição de funcionário – mostrar/ocultar PIN
  // ===============================
  const btnMostrarPin = document.getElementById("btnMostrarPin");
  if (btnMostrarPin && campoEditarPin) {
    btnMostrarPin.addEventListener("click", function () {
      const icon = btnMostrarPin.querySelector("i");
      if (campoEditarPin.type === "password") {
        campoEditarPin.type = "text";
        icon.classList.replace("bi-eye", "bi-eye-slash");
      } else {
        campoEditarPin.type = "password";
        icon.classList.replace("bi-eye-slash", "bi-eye");
      }
    });
  }

  // ===============================
  // Preenche modal de edição de funcionário
  // ===============================
  document.querySelectorAll(".btn-editar-funcionario").forEach((btn) => {
    btn.addEventListener("click", () => {
      const id = btn.dataset.id;
      const nome = btn.dataset.nome;
      const funcao = btn.dataset.funcao;
      const pin = btn.dataset.pin;

      const inputId = document.getElementById("editarIdFuncionario");
      const inputNome = document.getElementById("editarNomeFuncionario");
      const inputFuncao = document.getElementById("editarFuncaoFuncionario");
      const inputPin = document.getElementById("editarPinFuncionario");

      if (inputId) inputId.value = id;
      if (inputNome) inputNome.value = nome;
      if (inputFuncao) inputFuncao.value = funcao;
      if (inputPin) inputPin.value = pin;
    });
  });

  // ===============================
  // Preenche modal de exclusão de funcionário
  // ===============================
  document.querySelectorAll(".btn-excluir-funcionario").forEach((btn) => {
    btn.addEventListener("click", () => {
      const inputExcluir = document.getElementById("excluirIdFuncionario");
      if (inputExcluir) inputExcluir.value = btn.dataset.id;
    });
  });

  // ===============================
  // Fecha alertas automaticamente após 4 segundos
  // ===============================
  setTimeout(() => {
    const alert = document.getElementById("alert-overlay");
    if (alert) {
      const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
      bsAlert.close();
    }
  }, 4000);
});
