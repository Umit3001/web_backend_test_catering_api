<?php
namespace App\Repositories;
use PDO;
use PDOException;

class DatabaseConnection {
    protected $connection;

    function __construct() {

        $config = require __DIR__ . '/../../config/config.php';

            $dbconfig = $config["db"] ?? null;
            $host = $dbconfig["host"];
            $dbname = $dbconfig["database"];
            $username = $dbconfig["username"];
            $password = $dbconfig["password"];

        try {
            $this->connection = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }
}