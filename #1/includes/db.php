<?php
/**
 * Database Connection (PDO Singleton)
 * Wedding Invitation CMS
 */

require_once __DIR__ . '/config.php';

function get_db(): PDO {
    static $pdo = null;

    if ($pdo === null) {
        try {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Log error, never expose to user
            error_log('Database connection failed: ' . $e->getMessage());
            die(json_encode(['error' => true, 'message' => 'Database connection failed.']));
        }
    }

    return $pdo;
}

/**
 * Execute a query and return all rows
 */
function db_query(string $sql, array $params = []): array {
    try {
        $stmt = get_db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log('Query error: ' . $e->getMessage() . ' SQL: ' . $sql);
        return [];
    }
}

/**
 * Execute a query and return a single row
 */
function db_row(string $sql, array $params = []): ?array {
    try {
        $stmt = get_db()->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return $row ?: null;
    } catch (PDOException $e) {
        error_log('Query error: ' . $e->getMessage() . ' SQL: ' . $sql);
        return null;
    }
}

/**
 * Execute a query and return a single value
 */
function db_value(string $sql, array $params = []) {
    try {
        $stmt = get_db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    } catch (PDOException $e) {
        error_log('Query error: ' . $e->getMessage() . ' SQL: ' . $sql);
        return null;
    }
}

/**
 * Execute an INSERT/UPDATE/DELETE query, return affected rows
 */
function db_execute(string $sql, array $params = []): int {
    try {
        $stmt = get_db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    } catch (PDOException $e) {
        error_log('Execute error: ' . $e->getMessage() . ' SQL: ' . $sql);
        return 0;
    }
}

/**
 * Get last insert ID
 */
function db_last_id(): string {
    return get_db()->lastInsertId();
}
