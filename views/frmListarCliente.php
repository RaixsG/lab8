<?php
include("../controllers/clienteController.php");
$flash = isset($_GET['m']) ? $_GET['m'] : "";
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Clientes</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php if ($flash !== "") { ?>
<div class="toast" role="status" id="toast"><?= htmlspecialchars($flash, ENT_QUOTES, "UTF-8") ?></div>
<script>
setTimeout(function () {
    var toast = document.getElementById("toast");
    if (toast) { toast.classList.add("is-hidden"); }
}, 4000);
</script>
<?php } ?>
<main class="page">
    <header class="page-header">
        <div>
            <p class="eyebrow">Laboratorio 08</p>
            <h1>Clientes</h1>
            <p class="subtitle">Registro y administración de clientes</p>
        </div>
        <a class="btn btn-primary" href="frmRegistrarCliente.php">+ Registrar cliente</a>
    </header>

    <section class="card">
    <?php
    $sql = listaClientes();
    if (mysqli_num_rows($sql) === 0) {
    ?>
        <div class="empty">
            <h2>No hay clientes registrados</h2>
            <p>Registra el primer cliente para comenzar.</p>
            <a class="btn btn-primary" href="frmRegistrarCliente.php">Registrar cliente</a>
        </div>
    <?php
    } else {
    ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>DNI</th>
                        <th>Apellidos</th>
                        <th>Nombres</th>
                        <th>Email</th>
                        <th>Celular</th>
                        <th>Dirección</th>
                        <th style="text-align:right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = mysqli_fetch_array($sql)) { ?>
                    <tr>
                        <td><span class="id-pill"><?= htmlspecialchars((string) $row['id']) ?></span></td>
                        <td><?= htmlspecialchars((string) $row['dni']) ?></td>
                        <td><?= htmlspecialchars((string) $row['apellidos']) ?></td>
                        <td><?= htmlspecialchars((string) $row['nombres']) ?></td>
                        <td class="cell-muted"><?= htmlspecialchars((string) $row['email']) ?></td>
                        <td><?= htmlspecialchars((string) $row['celular']) ?></td>
                        <td class="cell-muted"><?= htmlspecialchars((string) $row['direccion']) ?></td>
                        <td>
                            <div class="actions">
                                <a class="btn btn-ghost btn-sm" href="frmModificarCliente.php?idc=<?= urlencode((string) $row['id']) ?>">Modificar</a>
                                <a class="btn btn-danger btn-sm" href="../controllers/clienteController.php?id=<?= urlencode((string) $row['id']) ?>">Eliminar</a>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } ?>
    </section>
</main>
</body>
</html>
