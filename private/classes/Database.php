<?php

class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $conn;

    // Singleton instance
    private static $instance = null;

    // Make constructor private to enforce singleton
    private function __construct() {
        // Check if the constants from config/database.php are defined
        if (!defined('DB_SERVER') || !defined('DB_NAME') || !defined('DB_USERNAME') || !defined('DB_PASSWORD')) {
            // Include config file if constants are not defined yet
            // Adjust the path based on where Database.php is located relative to config.php
            $configPath = __DIR__ . '/../config/database.php'; // Ensure this path is correct
            if (file_exists($configPath)) {
                require_once $configPath;
            } else {
                // Handle error: Config file not found
                throw new \RuntimeException("Database configuration file not found or constants not defined.");
            }
        }

        // Check again after attempting to include the config file
        if (!defined('DB_SERVER') || !defined('DB_NAME') || !defined('DB_USERNAME') || !defined('DB_PASSWORD')) {
             throw new \RuntimeException("Database configuration constants are not defined even after including config.");
        }

        $this->host = DB_SERVER; // Use DB_SERVER
        $this->db_name = DB_NAME;
        $this->username = DB_USERNAME; // Use DB_USERNAME
        $this->password = DB_PASSWORD; // Use DB_PASSWORD

        // Establish connection
        $this->connect();

        // ensure the same instance is closed when PHP shuts down
        register_shutdown_function([$this, 'close']);
    }

    // Return or create the single instance
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Establishes a database connection using PDO.
     *
     * @return PDO|null Returns the PDO connection object on success, or null on failure.
     */
    public function connect() {
        $this->conn = null;

        try {
            // Data Source Name (DSN)
            $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->db_name . ';charset=utf8mb4';

            // PDO Options
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw exceptions on error
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Fetch results as associative arrays by default
                PDO::ATTR_EMULATE_PREPARES   => false,                  // Use native prepared statements
                PDO::ATTR_PERSISTENT         => true,                   // Enable persistent connection
            ];

            $this->conn = new PDO($dsn, $this->username, $this->password, $options);

            // Ping the database immediately after connection
            $this->conn->query('SELECT 1');

        } catch(PDOException $e) {
            error_log(
                "[Database Connect] Connection Error: " . $e->getMessage() .
                "\nHost: {$this->host}, DB: {$this->db_name}" .
                "\nTrace:\n" . $e->getTraceAsString()
            );
            throw new \RuntimeException("Unable to connect to database '{$this->db_name}'", 0, $e);
        }

        return $this->conn;
    }

    /**
     * Closes the database connection and resets singleton.
     */
    public function close() {
        if ($this->conn !== null) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();        // Ensure no open transaction remains
            }
            $this->conn = null;
        }
        // Reset the singleton so resources can be freed
        self::$instance = null;
    }

    /**
     * Destructor to ensure the connection is closed when object is destroyed.
     */
    public function __destruct() {
        $this->close();
    }

    /**
     * Returns the current PDO connection object.
     *
     * @return PDO|null The active PDO connection or null if not connected.
     */
    public function getConnection() {
        if ($this->conn === null) {
            $this->connect();
            if ($this->conn === null) {
                throw new \RuntimeException("Failed to establish database connection.");
            }
        }

        if ($this->conn !== null) {
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        return $this->conn;
    }
}
