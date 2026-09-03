<?php
class Auth {
    public static function user(): ?array  { return Session::get('user'); }
    public static function check(): bool   { return Session::has('user'); }
    public static function rol(): int      { return (int)(Session::get('user')['rol_id'] ?? 0); }

    public static function esAdmin(): bool    { return self::rol() === ROL_ADMIN; }
    public static function esGestor(): bool   { return self::rol() === ROL_GESTOR; }
    public static function esEmpleado(): bool { return self::rol() === ROL_EMPLEADO; }

    /** Admin y Gestor pueden operar los 3 módulos */
    public static function puedeGestionar(): bool {
        return in_array(self::rol(), [ROL_ADMIN, ROL_GESTOR], true);
    }

    public static function requireAuth(): void {
        if (!self::check()) {
            header('Location: ' . APP_URL . '/auth/login');
            exit;
        }
    }

    public static function requireRol(int ...$roles): void {
        self::requireAuth();
        if (!in_array(self::rol(), $roles, true)) {
            header('Location: ' . APP_URL . '/?error=sin_permiso');
            exit;
        }
    }

    public static function requireGestion(): void {
        self::requireRol(ROL_ADMIN, ROL_GESTOR);
    }
}
