# Laboratorio 08 — Gestión de clientes (PHP + MySQL)

Aplicación web con arquitectura por capas (vista, controlador, modelo) para el
**Ejercicio 1 de la guía: registrar, listar, modificar y eliminar clientes**.

> La guía desarrolla paso a paso el ejemplo de **cursos** (`tcurso`). Ese código se
> usó como patrón; lo que se implementa acá es el **Ejercicio 1 (clientes)**.

## Estructura

```
.
├── index.php                        # entrada: redirige al listado
├── controllers/
│   └── clienteController.php        # registrar / modificar / eliminar / listar
├── models/
│   ├── conexion.php                 # conexión mysqli
│   └── clienteDAO.php               # SQL de tcliente
├── views/
│   ├── css/style.css
│   ├── frmListarCliente.php
│   ├── frmRegistrarCliente.php
│   └── frmModificarCliente.php
├── sql/
│   └── tcliente.sql                 # crea bdalumno + tabla tcliente (auto-init)
├── tests/
│   ├── pyproject.toml               # dependencias Python (selenium, pytest, pytest-html)
│   ├── uv.lock                      # versiones bloqueadas para reproducir en otra PC
│   ├── conftest.py                  # driver Selenium + hook de capturas y reporte HTML
│   ├── test_clientes_e2e.py         # suite E2E CRUD + validaciones (CP_CLI_01..09)
│   ├── casos_prueba_clientes.xlsx   # matriz en formato Excel (Blackboard)
│   └── casos_prueba_clientes.csv    # matriz CSV compatible con Excel en español
├── .dockerignore
└── docker-compose.yaml
```

## Requisitos

**Con Docker**
- Docker Desktop con una versión de Compose que soporte
  `build.dockerfile_inline` (verificado con v5.6.0) y el plugin `buildx`.
- BuildKit habilitado (viene con Docker Desktop).

**Manual**
- PHP 8.1 o superior con la extensión `mysqli`.
- MySQL 8.x.
- Laragon/XAMPP.

## Opción A — Docker

```bash
docker compose up -d --build
```

- Aplicación: <http://localhost:8080>
- MySQL: `localhost:3306` · base `bdalumno` · usuario `root` · contraseña `root`

Ambos servicios se construyen al vuelo con `dockerfile_inline`: `web` usa
`php:8.4-apache` + `mysqli`, y `db` deriva de `mysql:8.4.13` copiando
`sql/tcliente.sql` a `/docker-entrypoint-initdb.d/`. En un **volumen nuevo**, la base
y la tabla se crean solas al levantar; no hay Dockerfile aparte ni nada que instalar
en el host.

En un volumen ya inicializado el script **no** se vuelve a ejecutar. Para aplicarlo
a mano sobre una base existente:

```bash
# bash / zsh / Git Bash / WSL
docker compose exec -T -e MYSQL_PWD=root db mysql -uroot < sql/tcliente.sql
```

```powershell
# PowerShell (el '<' no existe ahí)
Get-Content -Raw .\sql\tcliente.sql | docker compose exec -T -e MYSQL_PWD=root db mysql -uroot
```

Si cambiás `sql/tcliente.sql`, reconstruí (`docker compose build db`) y usá un
volumen nuevo para que se aplique; `docker compose down -v` reinicializa pero
**borra la base**.

Los datos persisten en el volumen `dbdata`:

```bash
docker compose down       # conserva la base
docker compose down -v    # borra la base y el volumen (irreversible)
```

## Opción B - Instalación manual (Laragon, XAMPP)

1. Copiar el proyecto dentro del directorio web, por ejemplo
   `C:\laragon\www\lab` o `htdocs/lab`. La carpeta `sql` no se usa en tiempo de
   ejecución, pero conviene conservarla para reinstalar la base.
2. Iniciar Apache y MySQL.
3. Crear la base y la tabla importando `sql/tcliente.sql`
4. Ajustar `models/conexion.php` a las credenciales del servidor local. En Laragon
   y XAMPP el usuario `root` suele no tener contraseña:

   ```php
   $cn = mysqli_connect("localhost", "root", "", "bdalumno");
   ```

   Mantener la línea `mysqli_report(MYSQLI_REPORT_OFF);` y
   `mysqli_set_charset($cn, "utf8mb4");`.
5. Abrir `http://localhost/lab/` (ajustar al nombre de la carpeta). La aplicación
   usa rutas relativas, así que funciona en subdirectorio.

## Pruebas automatizadas con Selenium (`tests/`)

La carpeta `tests/` contiene un entorno Python aislado con **`uv`**, **Selenium 4**,
**`pytest`** y **`pytest-html`**. No necesitas copiar `tests/.venv/` ni
`tests/reports/` al mover el proyecto a otra máquina: con `pyproject.toml` y
`uv.lock` se reconstruye todo de forma idéntica.

### Requisitos en la otra máquina
- Aplicación web y MySQL corriendo (Opción A con Docker o Opción B con Laragon/XAMPP).
- **`uv`** instalado (no necesitas instalar Python ni ChromeDriver a mano; `uv`
  gestiona Python/dependencias y **Selenium Manager** descarga Chrome for Testing
  automáticamente si no detecta Google Chrome).
- Conexión a internet solo en la **primera** ejecución para descargar las ruedas
  Python y el binario de navegador/driver en caché.

### Paso a paso para ejecutar las pruebas en otra máquina

1. **Instalar `uv`** (solo si aún no está instalado):

   ```bash
   # macOS / Linux
   curl -LsSf https://astral.sh/uv/install.sh | sh
   ```

   ```powershell
   # Windows (PowerShell)
   powershell -ExecutionPolicy ByPass -c "irm https://astral.sh/uv/install.ps1 | iex"
   ```

2. **Asegurar que la aplicación esté corriendo**:
   - Con Docker: `http://localhost:8080`
   - Con Laragon/XAMPP: por ejemplo `http://localhost/lab`

3. **Instalar el entorno exacto dentro de `tests/`** (crea `tests/.venv/` usando `uv.lock`):

   ```bash
   cd tests
   uv sync
   ```

4. **Ejecutar la suite de pruebas**:

   - **Con Docker (`http://localhost:8080`) generando reporte HTML y capturas**:

     ```bash
     uv run pytest -v --html=reports/reporte_final.html --self-contained-html
     ```

   - **Viendo el navegador en pantalla (`--headed`)**:

     ```bash
     uv run pytest --headed -v
     ```

   - **Con Laragon/XAMPP (cambiando `--base-url`)**:

     ```bash
     uv run pytest --base-url=http://localhost/lab -v --html=reports/reporte_final.html --self-contained-html
     ```

5. **Revisar evidencias generadas**:
   - **Reporte HTML autocontenido**: `tests/reports/reporte_final.html` (generado por `pytest-html`).
   - **Capturas de pantalla**: `tests/reports/screenshots/` (tomadas por Selenium con `save_screenshot` e incrustadas en el HTML mediante `tests/conftest.py`).
   - **Matriz de casos de prueba**: `tests/casos_prueba_clientes.xlsx` (lista para abrir en Excel o copiar en Word).

### Cómo agregar nuevos tests
1. Añadir una función `def test_... (driver: WebDriver, base_url: str) -> None:` en `tests/test_clientes_e2e.py`.
2. Usar `wait_for(driver)` para esperas explícitas y limpiar cualquier dato sintético creado dentro de un bloque `finally:` para no alterar registros reales.
3. Registrar el nuevo caso en `tests/casos_prueba_clientes.xlsx` y volver a ejecutar `uv run pytest`.
