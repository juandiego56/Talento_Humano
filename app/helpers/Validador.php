<?php
/** Validaciones compartidas por el formulario de empleados y la hoja de vida. Cada método devuelve un mensaje de error o null. */
class Validador {
    public const ESTADOS_CIVILES = ['Soltero(a)', 'Casado(a)', 'Unión libre', 'Separado(a)', 'Divorciado(a)', 'Viudo(a)'];
    public const FONDOS_PENSION  = ['Colpensiones', 'Porvenir', 'Protección', 'Colfondos', 'Skandia'];
    public const TIPOS_SANGRE    = ['O+', 'O-', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-'];

    public static function texto(?string $v): string { return trim(preg_replace('/\s+/u', ' ', (string)$v)); }

    public static function requerido(?string $v, string $campo): ?string {
        return self::texto($v) === '' ? "$campo es obligatorio." : null;
    }

    public static function nombrePersona(?string $v, string $campo): ?string {
        $v = self::texto($v);
        if ($v === '') return "$campo es obligatorio.";
        if (mb_strlen($v) < 2 || mb_strlen($v) > 100) return "$campo debe tener entre 2 y 100 caracteres.";
        if (!preg_match("/^[\p{L}][\p{L}\s.'\-]*$/u", $v)) return "$campo solo puede tener letras y espacios.";
        return null;
    }

    public static function documento(?string $v): ?string {
        $v = trim((string)$v);
        if ($v === '') return 'El número de documento es obligatorio.';
        if (!preg_match('/^[A-Za-z0-9]{5,15}$/', $v)) return 'El número de documento debe tener entre 5 y 15 caracteres, sin espacios ni símbolos.';
        return null;
    }

    public static function email(?string $v, string $campo = 'El correo electrónico'): ?string {
        $v = trim((string)$v);
        if ($v === '') return "$campo es obligatorio.";
        if (strlen($v) > 120 || !filter_var($v, FILTER_VALIDATE_EMAIL) || !preg_match('/@[^@]+\.[A-Za-z]{2,}$/', $v)) {
            return "$campo no tiene un formato válido (ejemplo: nombre@correo.com).";
        }
        return null;
    }

    /** Celular colombiano (10 dígitos que empiezan por 3) o fijo (7 dígitos). */
    public static function telefono(?string $v, string $campo = 'El teléfono'): ?string {
        $d = preg_replace('/\D/', '', (string)$v);
        if ($d === '') return "$campo es obligatorio.";
        if (strlen($d) === 10 && $d[0] === '3') return null;
        if (strlen($d) === 7) return null;
        return "$campo debe ser un celular de 10 dígitos que empiece por 3 (ej. 3101234567) o un fijo de 7 dígitos.";
    }

    public static function direccion(?string $v): ?string {
        $v = self::texto($v);
        if ($v === '') return 'La dirección es obligatoria.';
        if (mb_strlen($v) < 6 || mb_strlen($v) > 150) return 'La dirección debe tener entre 6 y 150 caracteres.';
        if (!preg_match('/\p{L}/u', $v) || !preg_match('/\d/', $v)) return 'La dirección debe incluir letras y números (ej. Calle 5 # 3-20).';
        if (!preg_match('/^[\p{L}\d\s#\-.,°ºa-zA-Z\/]+$/u', $v)) return 'La dirección tiene caracteres no permitidos.';
        return null;
    }

    public static function fechaValida(?string $v): ?\DateTime {
        $v = trim((string)$v);
        $d = \DateTime::createFromFormat('Y-m-d', $v);
        return ($d && $d->format('Y-m-d') === $v) ? $d : null;
    }

    public static function fechaNacimiento(?string $v): ?string {
        $d = self::fechaValida($v);
        if (!$d) return 'La fecha de nacimiento es obligatoria y debe ser una fecha válida.';
        $hoy = new \DateTime('today');
        $edad = $d->diff($hoy)->y;
        if ($d > $hoy) return 'La fecha de nacimiento no puede ser futura.';
        if ($edad < 18) return 'El empleado debe ser mayor de 18 años.';
        if ($edad > 80) return 'Revisa la fecha de nacimiento: la edad supera los 80 años.';
        return null;
    }

    public static function fechaIngreso(?string $v): ?string {
        $d = self::fechaValida($v);
        if (!$d) return 'La fecha de ingreso es obligatoria y debe ser una fecha válida.';
        if ($d < new \DateTime('1990-01-01')) return 'La fecha de ingreso no puede ser anterior a 1990.';
        if ($d > new \DateTime('+1 year')) return 'La fecha de ingreso no puede superar un año a partir de hoy.';
        return null;
    }

    /** Fecha pasada (o de hoy) dentro de un rango razonable. */
    public static function fechaPasada(?string $v, string $campo, bool $obligatoria = true, string $desde = '1950-01-01'): ?string {
        if (trim((string)$v) === '') return $obligatoria ? "$campo es obligatoria." : null;
        $d = self::fechaValida($v);
        if (!$d) return "$campo no es una fecha válida.";
        if ($d > new \DateTime('today')) return "$campo no puede ser futura.";
        if ($d < new \DateTime($desde)) return "$campo no puede ser anterior a " . substr($desde, 0, 4) . '.';
        return null;
    }

    public static function cuenta(?string $v): ?string {
        $v = trim((string)$v);
        if ($v === '') return 'El número de cuenta es obligatorio.';
        if (!preg_match('/^\d{6,20}$/', $v)) return 'El número de cuenta debe tener entre 6 y 20 dígitos, sin espacios ni guiones.';
        return null;
    }

    /** Convierte "1500000", "1500000.50" o "1.500.000,50" a float. Devuelve null si no es un número válido. */
    public static function dinero(?string $v): ?float {
        $v = trim((string)$v);
        if ($v === '') return null;
        if (preg_match('/^\d{1,3}(\.\d{3})+(,\d{1,2})?$/', $v)) { $v = str_replace('.', '', $v); $v = str_replace(',', '.', $v); }
        elseif (preg_match('/^\d+,\d{1,2}$/', $v)) { $v = str_replace(',', '.', $v); }
        if (!preg_match('/^\d+(\.\d{1,2})?$/', $v)) return null;
        return round((float)$v, 2);
    }

    public static function salario(?string $v): ?string {
        $n = self::dinero($v);
        if ($n === null) return 'El salario debe ser un valor numérico válido, con máximo 2 decimales (ej. 1750905.50).';
        if ($n <= 0) return 'El salario debe ser mayor que cero.';
        if ($n > 100000000) return 'El salario excede el máximo permitido.';
        return null;
    }

    public static function enLista(?string $v, array $lista, string $campo): ?string {
        $v = trim((string)$v);
        if ($v === '') return "$campo es obligatorio.";
        return in_array($v, $lista, true) ? null : "$campo no es una opción válida.";
    }

    /** Junta los errores no nulos. */
    public static function errores(?string ...$e): array { return array_values(array_filter($e)); }

    /** Resuelve el fondo de pensión: la opción de la lista o el texto de "Otro". */
    public static function fondoPension(array $post): string {
        $sel = trim($post['fondo_pension'] ?? '');
        return $sel === 'Otro' ? self::texto($post['fondo_pension_otro'] ?? '') : $sel;
    }
}
