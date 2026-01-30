<?php

// CORE FILE OF AUTHENTICATION SYSTEM
// echo "Core.php";

require_once 'helper.php';

class Core
{
    private $db_name = "authdb";
    private $db_user = "root";
    private $db_pass = "";
    private $db_host = "localhost";
    private $conn;

    // INIT
    public function __construct($db_name, $db_user, $db_pass, $db_host)
    {
        $this->db_name = $db_name;
        $this->db_user = $db_user;
        $this->db_pass = $db_pass;
        $this->db_host = $db_host;

        // try {
        //     $this->conn = new PDO("mysql:host=$this->db_host;dbname=$this->db_name", $this->db_user, $this->db_pass);
        //     $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        //     // echo "Connected Established";
        // } catch (PDOException $e) {
        //     // if db not found, then create db and mrun migration.php
        //     // code here 

        //     die("Connection failed: " . $e->getMessage());
        // }

        try {
            $this->conn = new PDO("mysql:host=$this->db_host;dbname=$this->db_name", $this->db_user, $this->db_pass);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {

            // RUN MIGRATION
            require_once 'migration.php';
            $migration = new Migration($this->db_name, $this->db_user, $this->db_pass, $this->db_host);
            $migration->create_users_table();
            $migration->seed_user("Md. Muhibbur Rahman", "muhib2929@gmail.com", "12345678");

            // RECONNECT
            try {
                $this->conn = new PDO("mysql:host=$this->db_host;dbname=$this->db_name", $this->db_user, $this->db_pass);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e2) {
                die("Connection failed even after migration: " . $e2->getMessage());
            }

        }
    }

    // SIGNUP
    public function signup($username, $email, $hashed_pass)
    {
        $sql = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":password", $hashed_pass);
        try {
            $stmt->execute();
            session_start();
            $_SESSION['user_id'] = $this->conn->lastInsertId();

            alert("Account created successfully", "../login.php");
        } catch (PDOException $e) {
            alert("Something went wrong ({$e->getMessage()})", "../signup.php");
        }
    }

    // LOGIN
    public function login($email, $password, $remember)
    {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $verified = password_verify($password, $user['password']);

        // USER NOT FOUND 
        if (!$user) {
            alert("Not Registered", "../signup.php");
            return false;
        }

        // WRONG PASSWROD
        if (!$verified) {
            alert("Invalid password", "../login.php");
            return false;
        }


        if ($user && $verified) {
            session_start();
            $_SESSION['user_id'] = $user['id'];

            if ($remember) {
                setcookie("user_id", $user['id'], time() + (86400 * 30), "/");
            }

            header("Location: ../dashboard.php");
            exit();
        } else {
            alert("Invalid email or password", "../login.php");
        }
    }

    // LOGOUT
    public function logout()
    {
        session_start();
        session_unset();
        session_destroy();

        if (isset($_COOKIE['user_id'])) {
            // unset($_COOKIE['user_id']);
            setcookie("user_id", "", time() - 3600, "/");
        }
    }


    // FETCH 
    public function fetch($userID)
    {
        $sql = "SELECT * FROM  users WHERE id = :userID";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":userID", $userID);
        try {
            $stmt->execute();
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            return $data;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    // UPDATE 
    public function update($username, $email = null)
    {
        try {
            if ($email) {
                $sql = "UPDATE users SET username = :username, email = :email WHERE id = :userID";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":username", $username);
                $stmt->bindParam(":email", $email);
            } else {
                $sql = "UPDATE users SET username = :username WHERE id = :userID";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":username", $username);
            }

            session_start();
            $userID = $_SESSION['user_id'];
            $stmt->bindParam(":userID", $userID);

            $stmt->execute();
            return true;

        } catch (PDOException $e) {
            echo "Update failed: " . $e->getMessage();
            return false;
        }
    }

    // UPDATE PASSWORD 
    public function update_password($password)
    {
        $hashed_pass = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password = :password WHERE id = :userID";
        session_start();
        $userID = $_SESSION['user_id'];
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":password", $hashed_pass);
        $stmt->bindParam(":userID", $userID);
        try {
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            echo "Update failed: " . $e->getMessage();
            return false;
        }
    }


    // CHECK IF EMAIL EXISTS
    public function email_exists($email)
    {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function __destruct()
    {
        $this->conn = null;
    }
}



