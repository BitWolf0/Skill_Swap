<?php
/**
 * ISMO-SkillSwap v4 — Database Connection (PDO Singleton)
 * 
 * Usage:
 *   $db = Database::getInstance();
 *   $stmt = $db->prepare('SELECT * FROM utilisateurs WHERE id = ?');
 *   $stmt->execute([$id]);
 */

if (!defined('APP_RUNNING')) {
    http_response_code(403);
    die('Accès direct interdit');
}

class Database
{
    private static ?PDO $instance = null;

    private const DB_HOST = '127.0.0.1';
    private const DB_PORT = 3306;
    private const DB_NAME = 'ismo_skillswap_v4';
    private const DB_USER = 'root';
    private const DB_PASS = '';
    private const DB_CHARSET = 'utf8mb4';

    /**
     * Return the singleton PDO connection.
     * Creates it on first call, reuses on subsequent calls.
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            try {
                $dsn = sprintf(
                    'mysql:host=%s;port=%d;dbname=%s;charset=%s',
                    self::DB_HOST,
                    self::DB_PORT,
                    self::DB_NAME,
                    self::DB_CHARSET
                );

                self::$instance = new PDO($dsn, self::DB_USER, self::DB_PASS, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                    PDO::ATTR_STRINGIFY_FETCHES  => false,
                ]);
            } catch (PDOException $e) {
                // Log the real error server-side, show a safe message to the user
                error_log('[Database] Connection failed: ' . $e->getMessage());
                die('Erreur de connexion à la base de données. Veuillez réessayer plus tard.');
            }
        }

        return self::$instance;
    }

    /**
     * Prevent cloning and unserialization (singleton pattern).
     */
    private function __clone() {}
    public function __wakeup()
    {
        throw new \Exception('Cannot unserialize singleton');
    }
}
