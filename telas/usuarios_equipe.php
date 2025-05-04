<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipe Alfa - SIGTO</title>

    <link rel="stylesheet" href="../css/estilo_menu.css">
    <?php include '../componentes/bootstrap_refs.php'; ?>
</head>

<body>

    <?php include '../componentes/botao_menu.php'; ?>

    <div class="d-md-flex">
        <?php include '../componentes/menu.php'; ?>

        <main class="container py-4">

            <!-- Voltar para Equipes (visível só no mobile) -->
            <a href="tela_equipes.php" class="btn btn-outline-secondary mb-3 d-md-none">
                ← Voltar para Equipes
            </a>
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h4">Equipe: Alfa</h1>
                <a href="#" class="btn btn-primary">+ Adicionar Membro</a>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Nome</th>
                            <th>Função</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <tr>
                                <td><?= $i ?></td>
                                <td>Usuário <?= $i ?></td>
                                <td><?= $i % 2 === 0 ? 'Mecânico' : 'Operador' ?></td>
                                <td>
                                    <span class="badge bg-success">Ativo</span>
                                </td>
                                <td>
                                    <!-- Desktop: texto + botão -->
                                    <a href="#" class="btn btn-sm btn-outline-warning d-none d-md-inline">Editar</a>
                                    <a href="#" class="btn btn-sm btn-outline-danger d-none d-md-inline">Remover</a>

                                    <!-- Mobile: ícones lado a lado com espaçamento -->
                                    <div class="d-flex gap-2 d-md-none">
                                        <a href="#" class="btn btn-sm btn-outline-warning" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-outline-danger" title="Remover">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endfor; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script src="../js/js_menu.js"></script>
</body>

</html>