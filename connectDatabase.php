<?php
class Database {
    private $servername;
    private $username;
    private $password;
    private $dbname;
    private $port;
    private $conn;

    public function __construct() {
        $this->servername = getenv('DB_HOST') ?: "127.0.0.1"; // using 127.0.0.1 to avoid socket issues
        $this->username = getenv('DB_USER') ?: "root";
        $this->password = getenv('DB_PASSWORD') ?: "";
        $this->dbname = getenv('DB_NAME') ?: "csit314";
        $this->port = getenv('DB_PORT') ?: 3307; // explicitly set the correct port
        $this->conn = $this->connect();
    }

    private function connect() {
        $conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname, $this->port);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $conn->set_charset("utf8mb4");
        return $conn;
    }

    public function getConnection() {
        return $this->conn;
    }

    public function closeConnection() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}

// Test database connection
$database = new Database();
$conn = $database->getConnection();
?>
