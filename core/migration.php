<?php
// migration.php - OOP style migration with seeder

class Migration
{
	private $db_name = "authdb";
	private $db_user = "root";
	private $db_pass = "";
	private $db_host = "localhost";
	private $conn;

	public function __construct($db_name = "authdb", $db_user = "root", $db_pass = "", $db_host = "localhost")
	{
		$this->db_name = $db_name;
		$this->db_user = $db_user;
		$this->db_pass = $db_pass;
		$this->db_host = $db_host;

		try {
			// Connect without DB first
			$this->conn = new PDO("mysql:host={$this->db_host}", $this->db_user, $this->db_pass);
			$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			// Create DB if not exists
			$this->conn->exec("CREATE DATABASE IF NOT EXISTS `{$this->db_name}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

			// Select DB
			$this->conn->exec("USE `{$this->db_name}`");

		} catch (PDOException $e) {
			die("DB Connection/Migration failed: " . $e->getMessage());
		}
	}

	// Create users table
	public function create_users_table()
	{
		$sql = "CREATE TABLE IF NOT EXISTS users (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(32) NOT NULL,
            email VARCHAR(32) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

		$this->conn->exec($sql);
	}

	// Seed default user
	public function seed_user($username, $email, $plain_password)
	{
		// check if user exists
		$stmt = $this->conn->prepare("SELECT * FROM users WHERE email = :email");
		$stmt->bindParam(":email", $email);
		$stmt->execute();

		if ($stmt->rowCount() == 0) {
			$hashed_pass = password_hash($plain_password, PASSWORD_DEFAULT);

			$insert = $this->conn->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
			$insert->bindParam(":username", $username);
			$insert->bindParam(":email", $email);
			$insert->bindParam(":password", $hashed_pass);

			$insert->execute();
		}
	}
}

$migration = new Migration();
// $migration->create_users_table();
// $migration->seed_user("John Doe", "user@mail.com", "12345678");
