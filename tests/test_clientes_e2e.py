from selenium.webdriver.common.by import By
from selenium.webdriver.remote.webdriver import WebDriver
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.support.ui import WebDriverWait


TIMEOUT_SECONDS = 8


def wait_for(driver: WebDriver) -> WebDriverWait:
    return WebDriverWait(driver, TIMEOUT_SECONDS)


def get_row_ids(driver: WebDriver) -> list[str]:
    pills = driver.find_elements(By.CSS_SELECTOR, "table tbody tr td:first-child .id-pill")
    return [pill.text.strip() for pill in pills if pill.text.strip()]


def find_row_by_dni(driver: WebDriver, dni: str):
    rows = driver.find_elements(By.CSS_SELECTOR, "table tbody tr")
    for row in rows:
        cells = row.find_elements(By.TAG_NAME, "td")
        if len(cells) >= 2 and cells[1].text.strip() == dni:
            return row
    return None


def delete_client_by_dni(driver: WebDriver, base_url: str, dni: str) -> None:
    driver.get(f"{base_url}/views/frmListarCliente.php")
    row = find_row_by_dni(driver, dni)
    if row is None:
        return
    delete_link = row.find_element(By.CSS_SELECTOR, 'a[href*="../controllers/clienteController.php?id="]')
    delete_link.click()
    wait_for(driver).until(EC.url_contains("frmListarCliente.php"))


def delete_client_by_id(driver: WebDriver, base_url: str, client_id: str) -> None:
    driver.get(f"{base_url}/controllers/clienteController.php?id={client_id}")
    wait_for(driver).until(EC.url_contains("frmListarCliente.php"))


def fill_client_form(
    driver: WebDriver,
    dni: str,
    celular: str,
    apellidos: str,
    nombres: str,
    email: str,
    direccion: str,
) -> None:
    values = {
        "dni": dni,
        "celular": celular,
        "apellidos": apellidos,
        "nombres": nombres,
        "email": email,
        "direccion": direccion,
    }
    for field_id, value in values.items():
        element = driver.find_element(By.ID, field_id)
        element.clear()
        if value:
            element.send_keys(value)


def register_client(
    driver: WebDriver,
    base_url: str,
    dni: str,
    celular: str = "987654321",
    apellidos: str = "TestApellido",
    nombres: str = "TestNombre",
    email: str = "test.selenium@correo.com",
    direccion: str = "Av. Pruebas 456",
) -> None:
    driver.get(f"{base_url}/views/frmRegistrarCliente.php")
    fill_client_form(driver, dni, celular, apellidos, nombres, email, direccion)
    driver.find_element(By.NAME, "btnRegistrar").click()
    wait_for(driver).until(EC.url_contains("frmListarCliente.php"))


def test_cp_cli_01_list_redirect(driver: WebDriver, base_url: str) -> None:
    """CP_CLI_01: Root index redirects to frmListarCliente.php and renders main UI."""
    driver.get(f"{base_url}/")
    wait_for(driver).until(EC.url_contains("/views/frmListarCliente.php"))

    assert driver.title == "Clientes"
    heading = driver.find_element(By.TAG_NAME, "h1")
    assert heading.text.strip() == "Clientes"

    register_btn = driver.find_element(By.CSS_SELECTOR, 'a[href="frmRegistrarCliente.php"]')
    assert register_btn.is_displayed()


def test_cp_cli_02_register_valid_client(driver: WebDriver, base_url: str) -> None:
    """CP_CLI_02: Register a client with all valid fields."""
    dni = "99887761"
    delete_client_by_dni(driver, base_url, dni)

    try:
        driver.get(f"{base_url}/views/frmListarCliente.php")
        driver.find_element(By.CSS_SELECTOR, 'a[href="frmRegistrarCliente.php"]').click()
        wait_for(driver).until(EC.url_contains("frmRegistrarCliente.php"))

        fill_client_form(
            driver,
            dni=dni,
            celular="987654321",
            apellidos="TestApellido",
            nombres="TestNombre",
            email="test.selenium@correo.com",
            direccion="Av. Pruebas 456",
        )
        driver.find_element(By.NAME, "btnRegistrar").click()
        wait_for(driver).until(EC.url_contains("frmListarCliente.php"))

        toast = wait_for(driver).until(EC.visibility_of_element_located((By.ID, "toast")))
        assert "Los datos fueron registrados con exito!!!" in toast.text

        row = find_row_by_dni(driver, dni)
        assert row is not None
        assert "TestApellido" in row.text
        assert "TestNombre" in row.text
    finally:
        delete_client_by_dni(driver, base_url, dni)


def test_cp_cli_03_cancel_register(driver: WebDriver, base_url: str) -> None:
    """CP_CLI_03: Cancel client registration returns to list without saving."""
    dni = "11223344"
    delete_client_by_dni(driver, base_url, dni)

    driver.get(f"{base_url}/views/frmRegistrarCliente.php")
    driver.find_element(By.ID, "dni").send_keys(dni)
    driver.find_element(By.ID, "apellidos").send_keys("NoGuardar")

    driver.find_element(By.LINK_TEXT, "Cancelar").click()
    wait_for(driver).until(EC.url_contains("frmListarCliente.php"))

    assert driver.find_elements(By.ID, "toast") == []
    assert find_row_by_dni(driver, dni) is None


def test_cp_cli_04_modify_client(driver: WebDriver, base_url: str) -> None:
    """CP_CLI_04: Modify an existing client from the list view."""
    dni = "99887764"
    delete_client_by_dni(driver, base_url, dni)
    register_client(driver, base_url, dni=dni)

    try:
        driver.get(f"{base_url}/views/frmListarCliente.php")
        row = find_row_by_dni(driver, dni)
        assert row is not None

        row.find_element(By.CSS_SELECTOR, 'a[href^="frmModificarCliente.php?idc="]').click()
        wait_for(driver).until(EC.url_contains("frmModificarCliente.php"))

        id_input = driver.find_element(By.ID, "id")
        assert id_input.get_attribute("readonly") is not None
        assert driver.find_element(By.ID, "dni").get_attribute("value") == dni

        celular_input = driver.find_element(By.ID, "celular")
        celular_input.clear()
        celular_input.send_keys("911222333")

        direccion_input = driver.find_element(By.ID, "direccion")
        direccion_input.clear()
        direccion_input.send_keys("Jr. Actualizado 789")

        driver.find_element(By.NAME, "btnActualizar").click()
        wait_for(driver).until(EC.url_contains("frmListarCliente.php"))

        toast = wait_for(driver).until(EC.visibility_of_element_located((By.ID, "toast")))
        assert "Los datos fueron modificados de manera correcta" in toast.text

        updated_row = find_row_by_dni(driver, dni)
        assert updated_row is not None
        assert "911222333" in updated_row.text
        assert "Jr. Actualizado 789" in updated_row.text
    finally:
        delete_client_by_dni(driver, base_url, dni)


def test_cp_cli_05_delete_client(driver: WebDriver, base_url: str) -> None:
    """CP_CLI_05: Delete an existing client from the list view."""
    dni = "99887765"
    delete_client_by_dni(driver, base_url, dni)
    register_client(driver, base_url, dni=dni)

    driver.get(f"{base_url}/views/frmListarCliente.php")
    row = find_row_by_dni(driver, dni)
    assert row is not None

    row.find_element(By.CSS_SELECTOR, 'a[href*="../controllers/clienteController.php?id="]').click()
    wait_for(driver).until(EC.url_contains("frmListarCliente.php"))

    toast = wait_for(driver).until(EC.visibility_of_element_located((By.ID, "toast")))
    assert "El cliente fue eliminado con Exito!!!" in toast.text
    assert find_row_by_dni(driver, dni) is None


def test_cp_cli_06_invalid_email_rejected(driver: WebDriver, base_url: str) -> None:
    """CP_CLI_06: HTML5 email validation blocks malformed email addresses."""
    dni = "55667788"
    delete_client_by_dni(driver, base_url, dni)

    try:
        driver.get(f"{base_url}/views/frmRegistrarCliente.php")
        fill_client_form(
            driver,
            dni=dni,
            celular="987654321",
            apellidos="Invalido",
            nombres="Email",
            email="correo-sin-arroba",
            direccion="Calle 1",
        )
        driver.find_element(By.NAME, "btnRegistrar").click()

        assert "frmRegistrarCliente.php" in driver.current_url
        email_validity = driver.find_element(By.ID, "email").get_property("validity")
        assert email_validity["valid"] is False
    finally:
        delete_client_by_dni(driver, base_url, dni)


def test_cp_cli_07_nonexistent_client_modify(driver: WebDriver, base_url: str) -> None:
    """CP_CLI_07: Querying a nonexistent client ID renders empty-state message."""
    driver.get(f"{base_url}/views/frmModificarCliente.php?idc=999999")

    heading = wait_for(driver).until(EC.visibility_of_element_located((By.CSS_SELECTOR, ".empty h2")))
    assert heading.text.strip() == "Cliente no encontrado"
    back_link = driver.find_element(By.LINK_TEXT, "Volver al listado")
    assert back_link.is_displayed()


def test_cp_cli_08_empty_fields_rejected(driver: WebDriver, base_url: str) -> None:
    """CP_CLI_08: Submitting empty fields must be rejected without inserting a blank row."""
    driver.get(f"{base_url}/views/frmListarCliente.php")
    ids_before = set(get_row_ids(driver))

    driver.get(f"{base_url}/views/frmRegistrarCliente.php")
    driver.find_element(By.NAME, "btnRegistrar").click()

    try:
        try:
            WebDriverWait(driver, 2).until(EC.url_contains("frmListarCliente.php"))
        except Exception:
            pass

        assert "frmRegistrarCliente.php" in driver.current_url or (
            driver.find_elements(By.ID, "toast")
            and "ERROR" in driver.find_element(By.ID, "toast").text
        ), "La aplicación permitió enviar el formulario con todos los campos vacíos."
    finally:
        if "frmListarCliente.php" in driver.current_url:
            ids_after = get_row_ids(driver)
            for created_id in [cid for cid in ids_after if cid not in ids_before]:
                delete_client_by_id(driver, base_url, created_id)


def test_cp_cli_09_whitespace_fields_rejected_server(driver: WebDriver, base_url: str) -> None:
    """CP_CLI_09: Server-side validation must reject whitespace-only inputs even if HTML5 is bypassed."""
    driver.get(f"{base_url}/views/frmListarCliente.php")
    ids_before = set(get_row_ids(driver))

    driver.get(f"{base_url}/views/frmRegistrarCliente.php")
    form = driver.find_element(By.TAG_NAME, "form")
    driver.execute_script("arguments[0].setAttribute('novalidate', 'novalidate');", form)

    fill_client_form(
        driver,
        dni="   ",
        celular="   ",
        apellidos="   ",
        nombres="   ",
        email="   ",
        direccion="   ",
    )
    driver.find_element(By.NAME, "btnRegistrar").click()
    wait_for(driver).until(EC.url_contains("frmListarCliente.php"))

    ids_after = get_row_ids(driver)
    new_ids = [cid for cid in ids_after if cid not in ids_before]

    try:
        toast = wait_for(driver).until(EC.visibility_of_element_located((By.ID, "toast")))
        assert "ERROR" in toast.text, f"Se esperaba mensaje ERROR, pero se obtuvo: {toast.text}"
        assert new_ids == [], f"Se insertaron filas con espacios en blanco: {new_ids}"
    finally:
        for created_id in new_ids:
            delete_client_by_id(driver, base_url, created_id)
