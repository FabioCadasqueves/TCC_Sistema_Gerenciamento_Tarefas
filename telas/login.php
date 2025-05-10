<!DOCTYPE html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <?php include '../componentes/bootstrap_refs.php'; ?>
    <link rel="stylesheet" href="../css/estilo_login.css">
</head>

<body>
    <div class="login-container">
        <h2 id="titulo-login">Login</h2>
        <p id="subtitulo-login">Selecione seu perfil para continuar o acesso</p>

        <!-- MENSAGEM DE SUCESSO -->
        <?php if (isset($_GET['cadastro']) && $_GET['cadastro'] === 'sucesso'): ?>
            <div class="alert alert-success alert-dismissible fade show mt-2" role="alert">
                ✅ Cadastro realizado com sucesso! Faça login para continuar.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['erro'])): ?>
            <div class="alert alert-danger alert-dismissible fade show mt-2" role="alert">
                <?php
                if ($_GET['erro'] === 'senha') {
                    echo "Senha incorreta.";
                } elseif ($_GET['erro'] === 'email') {
                    echo "E-mail não encontrado.";
                } elseif ($_GET['erro'] === 'pin') {
                    echo "PIN incorreto ou colaborador inativo.";
                }
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
        <?php endif; ?>


        <!-- Seleção de perfil -->
        <div id="selecao-perfil" class="row row-cols-1 row-cols-md-2 g-3">
            <div class="col">
                <div class="opcao-login" onclick="mostrarFormulario('admin')">
                    <i class="bi bi-person-badge-fill"></i>
                    <h5>Administrador</h5>
                </div>
            </div>
            <div class="col">
                <div class="opcao-login" onclick="mostrarFormulario('colaborador')">
                    <i class="bi bi-wrench-adjustable-circle"></i>
                    <h5>Colaborador</h5>
                </div>
            </div>
        </div>

        <!-- Formulário Admin -->
        <div id="form-admin" class="formulario-login mt-4 d-none">

            <form action="../logica/controladores/login_admin.php" method="POST">
                <div class="mb-3">
                    <input type="email" name="email" class="form-control" placeholder="E-mail" required>
                </div>
                <div class="mb-2">
                    <input type="password" name="senha" class="form-control" placeholder="Senha" required>
                </div>
                <div class="text-end mb-3">
                    <a href="#" class="small link-hover">Esqueceu sua senha?</a>
                </div>
                <div class="row g-2">
                    <div class="col">
                        <button type="button" class="btn btn-outline-secondary w-100" onclick="voltar()">Voltar</button>
                    </div>
                    <div class="col">
                        <button type="submit" class="btn btn-primary w-100">Entrar</button>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="small text-reset">
                        Não tem cadastro?
                        <a href="#" class="text-primary cadastro-hover" onclick="mostrarCadastroAdmin()">Cadastre-se
                            aqui</a>
                    </span>
                </div>
            </form>
        </div>

        <!-- Cadastro Admin -->
        <div id="form-cadastro-admin" class="formulario-login mt-4 d-none">
            <form action="../logica/controladores/cadastrar_admin.php" method="POST">
                <div class="mb-3">
                    <input type="text" name="nome" class="form-control" placeholder="Nome completo" required>
                </div>
                <div class="mb-3">
                    <input type="email" name="email" class="form-control" placeholder="E-mail" required>
                </div>
                <div class="mb-3">
                    <input type="password" name="senha" class="form-control" placeholder="Senha" required>
                </div>
                <div class="mb-3">
                    <input type="password" name="confirmar_senha" class="form-control" placeholder="Confirmar senha"
                        required>
                </div>
                <div class="row g-2">
                    <div class="col">
                        <button type="button" class="btn btn-outline-secondary w-100"
                            onclick="voltarParaLoginAdmin()">Voltar</button>
                    </div>
                    <div class="col">
                        <button type="submit" class="btn btn-success w-100">Cadastrar</button>
                    </div>
                </div>
            </form>
        </div>



        <!-- Formulário Colaborador -->
        <div id="form-colaborador" class="formulario-login mt-4 d-none">
            <form action="../logica/controladores/login_funcionario.php" method="POST">
                <div class="mb-3">
                    <input type="text" name="pin" class="form-control" placeholder="PIN de Acesso" required>
                </div>
                <div class="row g-2">
                    <div class="col">
                        <button type="button" class="btn btn-outline-secondary w-100" onclick="voltar()">Voltar</button>
                    </div>
                    <div class="col">
                        <button type="submit" class="btn btn-success w-100">Entrar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="../js/js_login.js"></script>

</body>

</html>