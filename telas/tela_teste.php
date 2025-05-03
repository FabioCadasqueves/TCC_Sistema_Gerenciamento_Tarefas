<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <link rel="stylesheet" href="../css/estilo_menu.css">
    <?php include '../componentes/bootstrap_refs.php'; ?>
</head>

<body>

    <!-- Cabeçalho no mobile com botão fixo no topo -->
    <header class="d-md-none bg-white shadow-sm p-2 position-sticky top-0" style="z-index: 1050;">
        <button id="btn-menu" class="btn btn-outline-dark" onclick="abrirMenu()">☰</button>
    </header>

    <!-- Conteúdo principal com menu + conteúdo lado a lado no desktop -->
    <div class="d-md-flex">
        <?php include '../componentes/menu.php'; ?>

        <main class="container py-4">
            <h1 class="mb-4">Painel de Teste</h1>

            <div class="row row-cols-1 row-cols-md-2 g-4">
                <?php for ($i = 1; $i <= 12; $i++): ?>
                    <div class="col">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">Tarefa <?= $i ?></h5>
                                <p class="card-text">
                                    Este é um exemplo de tarefa para verificar o comportamento da interface.
                                </p>
                                <a href="#" class="btn btn-primary">Acessar</a>
                            </div>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>
        </main>
    </div>

    <script src="../js/js_menu.js"></script>
</body>


</html>