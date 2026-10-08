# SGTH — Sistema de Gestión de Talento Humano
Fundación Universitaria De Popayán · Desarrollado en PHP 8 (MVC propio) + MariaDB/MySQL.

Módulos: **Personal** (empleados, hoja de vida FO-TH-018, lista de chequeo, verificación de documentos, reportes), **Nómina** (nóminas, calculadora de costos, conceptos de pago), **Bienestar** (actividades y registro de asistencia por QR) y **Sistema** (usuarios, áreas, cargos y programas).

## Requisitos
- PHP 8.1 o superior (extensiones `pdo_mysql`, `mbstring`, `gd` o `fileinfo`).
- MariaDB 10.4+ / MySQL 5.7+.
- Apache con `mod_rewrite` (XAMPP lo trae).

## Instalación en un computador (XAMPP)
1. Copia la carpeta `sgth` a `C:\xampp\htdocs\sgth`.
2. Abre el Panel de XAMPP y pulsa **Start** en **Apache** y **MySQL**.
3. Entra a `http://localhost/phpmyadmin` → pestaña **Importar** → elige `database/instalacion_sgth.sql` → **Importar**. (Crea la base `sgth_talento_humano`, las tablas y los catálogos.)
4. Revisa `config/database.php` (servidor, puerto, usuario y contraseña de MySQL).
5. Abre `http://localhost/sgth/public`.

## Instalación en un hosting
1. Crea una base de datos MySQL desde el panel del hosting y anota nombre, usuario, contraseña y servidor.
2. Importa `database/instalacion_sgth.sql` con phpMyAdmin (si el hosting no deja crear bases con `CREATE DATABASE`, borra las líneas `CREATE DATABASE` y `USE` del inicio del archivo y selecciona antes tu base).
3. Sube la carpeta `sgth` a `htdocs` (o `public_html`) de modo que quede `…/sgth/public`.
4. Edita `config/database.php` con los datos del hosting.
5. Da permiso de escritura a `uploads/` y `public/uploads/`.
6. La dirección del sistema queda `https://TU-DOMINIO/sgth/public`.

## Primer ingreso
| Usuario | Contraseña inicial |
|---|---|
| `admin@empresa.co` (Administrador) | `talento2026` |

El sistema obliga a cambiar la contraseña inicial en el primer ingreso. Cada empleado nuevo recibe un usuario con su correo y su **número de documento** como clave temporal, y también debe crear una propia al entrar.

## Roles
- **Administrador**: control total, incluye usuarios y catálogos.
- **Gestor de Talento Humano**: opera personal, nómina y bienestar.
- **Empleado**: diligencia su propia hoja de vida y consulta su ficha.
- **Director de Programa**: consulta y solicita vinculación de docentes de su programa.

## Flujo de la hoja de vida
1. El administrador/gestor crea al empleado (identificación + datos laborales). Se genera su usuario.
2. El empleado entra, crea su contraseña y diligencia su hoja de vida en 7 pasos (estilo CvLAC).
3. La envía a revisión. Talento Humano la **aprueba** o la **devuelve con observaciones**.
4. Si está aprobada, el empleado puede **actualizarla**: se crea una nueva versión (y puede cancelar los cambios antes de enviarla).
5. Talento Humano no edita lo que el empleado ya llenó; solo la visualiza y valida.

## Configuración útil
- `config/app.php` → `APP_DEBUG`: `false` (por defecto) oculta los errores técnicos; ponlo en `true` solo en tu equipo para depurar.
- `config/app.php` → valores de la calculadora de costos (`CL_PISO_IBC`, `CL_AUX_TRANSPORTE`, `CL_AUX_TRANSPORTE_TOPE`): actualízalos cada año según el decreto.
- Los errores quedan registrados en el log de PHP del servidor (en XAMPP: `C:\xampp\apache\logs\error.log`).
- Si MySQL está apagado, el sistema muestra "Algo salió mal": inicia MySQL desde XAMPP.

## Estructura
```
app/controllers   lógica de cada módulo
app/views         pantallas (HTML + PHP)
app/helpers       enrutador, base de datos, autenticación, validaciones
config            configuración y conexión
database          instalacion_sgth.sql (instalación completa)
public            punto de entrada (index.php), CSS, JS e imágenes
uploads           soportes PDF de la lista de chequeo (fuera de public)
```
Los archivos `migration_*.sql` de la raíz son históricos: ya están incluidos en `instalacion_sgth.sql`.
