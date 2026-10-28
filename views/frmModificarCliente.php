<?php
include("../controllers/clienteController.php");

$row = null;
$error = null;
if (isset($_GET['idc'])) {
    try {
        $datos = consultarCli($_GET['idc']);
        if (mysqli_num_rows($datos) > 0) {
            $row = mysqli_fetch_array($datos);
        }
    } catch (RuntimeException $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Modificar cliente</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<main class="page">
    <header class="page-header">
        <div>
            <p class="eyebrow">Clientes</p>
            <h1>Modificar cliente</h1>
            <p class="subtitle">Actualiza los datos del cliente seleccionado</p>
        </div>
        <a class="btn btn-ghost" href="frmListarCliente.php">Volver al listado</a>
    </header>

    <section class="card">
    <?php if ($error !== null) { ?>
        <div class="empty">
            <h2>No se pudo consultar la base de datos</h2>
            <p><?= htmlspecialchars($error, ENT_QUOTES, "UTF-8") ?></p>
            <a class="btn btn-ghost" href="frmListarCliente.php">Volver al listado</a>
        </div>
    <?php } elseif ($row) { ?>
        <div class="card-body">
            <form method="post" action="../controllers/clienteController.php">
                <div class="form-grid">
                    <div class="field">
                        <label for="id">ID</label>
                        <input type="text" id="id" name="id" value="<?= htmlspecialchars((string) $row['id'], ENT_QUOTES, "UTF-8") ?>" readonly>
                    </div>
                    <div class="field">
                        <label for="dni">DNI</label>
                        <input type="text" id="dni" name="dni" maxlength="8" inputmode="numeric" value="<?= htmlspecialchars((string) $row['dni'], ENT_QUOTES, "UTF-8") ?>">
                    </div>
                    <div class="field">
                        <label for="apellidos">Apellidos</label>
                        <input type="text" id="apellidos" name="apellidos" maxlength="50" value="<?= htmlspecialchars((string) $row['apellidos'], ENT_QUOTES, "UTF-8") ?>">
                    </div>
                    <div class="field">
                        <label for="nombres">Nombres</label>
                        <input type="text" id="nombres" name="nombres" maxlength="50" value="<?= htmlspecialchars((string) $row['nombres'], ENT_QUOTES, "UTF-8") ?>">
                    </div>
                    <div class="field full">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" maxlength="50" value="<?= htmlspecialchars((string) $row['email'], ENT_QUOTES, "UTF-8") ?>">
                    </div>
                    <div class="field">
                        <label for="celular">Celular</label>
                        <input type="tel" id="celular" name="celular" maxlength="9" inputmode="numeric" value="<?= htmlspecialchars((string) $row['celular'], ENT_QUOTES, "UTF-8") ?>">
                    </div>
                    <div class="field">
                        <label for="direccion">Dirección</label>
                        <input type="text" id="direccion" name="direccion" maxlength="50" value="<?= htmlspecialchars((string) $row['direccion'], ENT_QUOTES, "UTF-8") ?>">
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary" name="btnActualizar" value="Actualizar Cliente">Guardar cambios</button>
                    <a class="btn btn-ghost" href="frmListarCliente.php">Cancelar</a>
                </div>
            </form>
        </div>
    <?php } else { ?>
        <div class="empty">
            <h2>Cliente no encontrado</h2>
            <p>El cliente solicitado no existe o fue eliminado.</p>
            <a class="btn btn-primary" href="frmListarCliente.php">Volver al listado</a>
        </div>
    <?php } ?>
    </section>
</main>
</body>
</html>
