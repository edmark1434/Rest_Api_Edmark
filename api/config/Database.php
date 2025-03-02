<?php
require __DIR__ . '../../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

class Database {
    private $pdo;

    public function __construct() {
        // Load environment variables
        $db_host = $_ENV['DB_HOST'];
        $db_name = $_ENV['DB_NAME'];
        $db_user = $_ENV['DB_USER'];
        $db_pass = $_ENV['DB_PASS'];

        try {
            $this->pdo = new PDO("mysql:host=$db_host;port=3307;dbname=$db_name", $db_user, $db_pass);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->pdo;
    }
}
?>
