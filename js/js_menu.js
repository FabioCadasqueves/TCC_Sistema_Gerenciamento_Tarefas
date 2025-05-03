/* global bootstrap: false */
(() => {
  "use strict";
  const tooltipTriggerList = Array.from(
    document.querySelectorAll('[data-bs-toggle="tooltip"]')
  );
  tooltipTriggerList.forEach((tooltipTriggerEl) => {
    new bootstrap.Tooltip(tooltipTriggerEl);
  });
})();

function abrirMenu() {
  const sidebar = document.getElementById("sidebar");
  const btnMenu = document.getElementById("btn-menu");

  sidebar.classList.add("ativo");
  //btnMenu.style.display = "none";
}

function fecharMenu() {
  const sidebar = document.getElementById("sidebar");
  const btnMenu = document.getElementById("btn-menu");

  sidebar.classList.remove("ativo");
  //btnMenu.style.display = "block";
}
