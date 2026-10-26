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
│   └── tcliente.sql                 # crea bdalumno + tabla tcliente
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

El `web` se construye al vuelo con `php:8.4-apache` + `mysqli`; no hay Dockerfile
aparte ni nada que instalar en el host. La base se crea con `MYSQL_DATABASE`, pero
**la tabla se crea ejecutando el script** (al menos una vez por volumen nuevo; el
script es idempotente):

```bash
docker compose exec -T -e MYSQL_PWD=root db mysql -uroot < sql/tcliente.sql
```

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
