<?php
class Session {
    public static function start(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_name(SESSION_NAME);
            session_start();
        }
    }
    public static function set(string $k, mixed $v): void    { $_SESSION[$k] = $v; }
    public static function get(string $k, mixed $d = null): mixed { return $_SESSION[$k] ?? $d; }
    public static function has(string $k): bool              { return isset($_SESSION[$k]); }
    public static function delete(string $k): void           { unset($_SESSION[$k]); }
    public static function destroy(): void                   { session_destroy(); }

    public static function flash(string $k, string $msg): void { $_SESSION['_flash'][$k] = $msg; }
    public static function getFlash(string $k): ?string {
        $msg = $_SESSION['_flash'][$k] ?? null;
        unset($_SESSION['_flash'][$k]);
        return $msg;
    }
}
