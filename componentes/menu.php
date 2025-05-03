<div id="sidebar" class="d-flex flex-column flex-shrink-0 p-3 text-bg-dark" style="width: 280px; height: 100vh;">

    <!-- Botão de fechar (só visível no mobile) -->
    <div class="d-flex justify-content-end d-md-none mb-2">
        <button class="btn btn-close btn-close-white" onclick="fecharMenu()" aria-label="Fechar"></button>
    </div>

    <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
        <img src="../arquivos/imagens/Logo.png" alt="Logo SIGTO" width="32" height="32">
        <span class="fs-4 ms-2">SGTO</span>
    </a>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="#" class="nav-link active text-white">
                <i class="bi bi-speedometer2 me-2"></i>
                Dashboard
            </a>
        </li>
        <li>
            <a href="#" class="nav-link text-white">
                <i class="bi bi-people-fill me-2"></i>
                Equipes
            </a>
        </li>
        <li>
            <a href="#" class="nav-link text-white">
                <i class="bi bi-list-task me-2"></i>
                Tarefas
            </a>
        </li>
        <li>
            <a href="#" class="nav-link text-white">
                <i class="bi bi-chat-left-dots-fill me-2"></i>
                Solicitações
            </a>
        </li>
    </ul>
    <hr>
    <div class="dropdown">
        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle"
            data-bs-toggle="dropdown" aria-expanded="false">
            <img src="https://github.com/mdo.png" alt="" width="32" height="32" class="rounded-circle me-2">
            <strong>Fabio Cadasqueves</strong>
        </a>
        <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
            <li><a class="dropdown-item" href="#">New project...</a></li>
            <li><a class="dropdown-item" href="#">Settings</a></li>
            <li><a class="dropdown-item" href="#">Profile</a></li>
            <li>
                <hr class="dropdown-divider">
            </li>
            <li><a class="dropdown-item" href="#">Sign out</a></li>
        </ul>
    </div>
</div>