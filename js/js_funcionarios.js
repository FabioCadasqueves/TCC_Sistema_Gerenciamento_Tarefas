// Gera um PIN numérico aleatório de 4 a 6 dígitos
function gerarPIN(tamanho = 4) {
  let pin = "";
  for (let i = 0; i < tamanho; i++) {
    pin += Math.floor(Math.random() * 10);
  }
  return pin;
}

// Ao abrir modal de novo funcionário, preenche o PIN
const modalNovoFuncionario = document.getElementById("modalNovoFuncionario");
if (modalNovoFuncionario) {
  modalNovoFuncionario.addEventListener("show.bs.modal", function () {
    const campoPin = document.getElementById("pinFuncionario");
    if (campoPin) campoPin.value = gerarPIN(4);
  });
}

// Espera o DOM estar carregado para aplicar listeners
document.addEventListener("DOMContentLoaded", function () {
  // Botão de copiar PIN no modal de adicionar
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

  // Edição de funcionário – copiar
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

  // Edição de funcionário – mostrar/ocultar
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
});

document.querySelectorAll(".btn-editar-funcionario").forEach((btn) => {
  btn.addEventListener("click", () => {
    document.getElementById("editarIdFuncionario").value = btn.dataset.id;
    document.getElementById("editarNomeFuncionario").value = btn.dataset.nome;
    document.getElementById("editarFuncaoFuncionario").value =
      btn.dataset.funcao;
    document.getElementById("editarPinFuncionario").value = btn.dataset.pin;
  });
});

document.querySelectorAll(".btn-excluir-funcionario").forEach((btn) => {
  btn.addEventListener("click", () => {
    document.getElementById("excluirIdFuncionario").value = btn.dataset.id;
  });
});

setTimeout(() => {
  const alert = document.getElementById("alert-overlay");
  if (alert) {
    const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
    bsAlert.close();
  }
}, 4000); // 4 segundos
