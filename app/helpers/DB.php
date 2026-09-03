<?php
class DB {
    private static ?PDO $pdo = null;

    public static function connect(): PDO {
        if (self::$pdo === null) {
            $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', DB_HOST, DB_NAME, DB_CHARSET);
            self::$pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        }
        return self::$pdo;
    }

    public static function query(string $sql, array $p = []): PDOStatement {
        $stmt = self::connect()->prepare($sql);
        $stmt->execute($p);
        return $stmt;
    }

    public static function fetch(string $sql, array $p = []): ?array {
        return self::query($sql, $p)->fetch() ?: null;
    }

    public static function fetchAll(string $sql, array $p = []): array {
        return self::query($sql, $p)->fetchAll();
    }

    public static function insert(string $sql, array $p = []): string {
        self::query($sql, $p);
        return self::connect()->lastInsertId();
    }

    public static function execute(string $sql, array $p = []): int {
        return self::query($sql, $p)->rowCount();
    }
}
