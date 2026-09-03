# SGTH — Sistema de Gestión de Talento Humano

Aplicación PHP + MySQL para la gestión de personal de una organización, con tres módulos:

1. **Administración de Personal** — registro de empleados, hoja de vida (formación académica y experiencia laboral) y lista de chequeo de documentos de vinculación.
2. **Nómina** — generación mensual de nómina, cálculo automático de salario básico, auxilio de transporte, salud y pensión, y colilla de pago editable por empleado (horas extra, bonificaciones, descuentos).
3. **Bienestar Laboral** — programación de actividades (capacitaciones, recreación, salud, integración, deportivo), inscripción de empleados y control de asistencia.

## Instalación en XAMPP

1. Copia la carpeta `sgth` completa dentro de `C:\xampp\htdocs\` (Windows) o `/Applications/XAMPP/htdocs/` (Mac).
2. Abre **phpMyAdmin** (`http://localhost/phpmyadmin`), crea o entra directamente e importa el archivo `schema.sql` incluido (pestaña **Importar**). Esto crea la base de datos `sgth_talento_humano` con datos de ejemplo.
3. Verifica que `config/database.php` tenga los datos de tu XAMPP (por defecto usuario `root` sin contraseña, que es lo estándar en XAMPP).
4. Abre en el navegador: **`http://localhost/sgth/public`**

## Usuarios de prueba

Todos con la contraseña: **`talento2026`**

| Correo | Rol |
|---|---|
| admin@empresa.co | Administrador (control total) |
| maria.gomez@empresa.co | Gestora de Talento Humano (opera los 3 módulos) |
| carlos.munoz@empresa.co | Empleado (solo consulta) |

## Estructura del proyecto

```
sgth/
├── app/
│   ├── controllers/   Controladores de cada módulo
│   ├── helpers/        Router, DB, Auth, Session, View
│   └── views/          Vistas PHP organizadas por módulo
├── config/              Configuración de app y base de datos
├── public/              Punto de entrada (document root de XAMPP)
│   └── assets/          CSS y JS
├── uploads/              Carpeta para futuros adjuntos (documentos, fotos)
└── schema.sql            Script completo de base de datos con datos de ejemplo
```

## Notas

- El sistema usa PDO con sentencias preparadas y contraseñas cifradas con `password_hash`.
- Los roles (Administrador / Gestor / Empleado) controlan qué puede crear, editar o eliminar cada usuario.
- El módulo de Nómina calcula automáticamente salud (4%) y pensión (4%) sobre el salario básico; estos porcentajes se pueden ajustar desde **Conceptos de Nómina**.
- La hoja de vida y las colillas de pago se pueden imprimir o guardar como PDF directamente desde el navegador (botón "Imprimir").
