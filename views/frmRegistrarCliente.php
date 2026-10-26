<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Registrar cliente</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<main class="page">
    <header class="page-header">
        <div>
            <p class="eyebrow">Clientes</p>
            <h1>Registrar cliente</h1>
            <p class="subtitle">Completa los datos del nuevo cliente</p>
        </div>
        <a class="btn btn-ghost" href="frmListarCliente.php">Volver al listado</a>
    </header>

    <section class="card">
        <div class="card-body">
            <form method="post" action="../controllers/clienteController.php">
                <div class="form-grid">
                    <div class="field">
                        <label for="dni">DNI</label>
                        <input type="text" id="dni" name="dni" maxlength="8" inputmode="numeric" placeholder="12345678">
                    </div>
                    <div class="field">
                        <label for="celular">Celular</label>
                        <input type="tel" id="celular" name="celular" maxlength="9" inputmode="numeric" placeholder="987654321">
                    </div>
                    <div class="field">
                        <label for="apellidos">Apellidos</label>
                        <input type="text" id="apellidos" name="apellidos" maxlength="50" placeholder="Pérez García">
                    </div>
                    <div class="field">
                        <label for="nombres">Nombres</label>
                        <input type="text" id="nombres" name="nombres" maxlength="50" placeholder="Juan Carlos">
                    </div>
                    <div class="field full">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" maxlength="50" placeholder="juan@correo.com">
                    </div>
                    <div class="field full">
                        <label for="direccion">Dirección</label>
                        <input type="text" id="direccion" name="direccion" maxlength="50" placeholder="Av. Siempre Viva 123">
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary" name="btnRegistrar" value="Registrar Cliente">Registrar cliente</button>
                    <a class="btn btn-ghost" href="frmListarCliente.php">Cancelar</a>
                </div>
            </form>
        </div>
    </section>
</main>
</body>
</html>
