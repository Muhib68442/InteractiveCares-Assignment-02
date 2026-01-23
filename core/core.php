<?php

// CORE FILE OF AUTHENTICATION SYSTEM
// echo "Core.php";

class Core
{
    private $db_name = "authdb";
    private $db_user = "root";
    private $db_pass = "";
    private $db_host = "localhost";
    private $conn;

    public function __construct($db_name, $db_user, $db_pass, $db_host)
    {
        $this->db_name = $db_name;
        $this->db_user = $db_user;
        $this->db_pass = $db_pass;
        $this->db_host = $db_host;

        try {
            $this->conn = new PDO("mysql:host=$this->db_host;dbname=$this->db_name", $this->db_user, $this->db_pass);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // echo "Connected Established";
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public function signup($username, $email, $hashed_pass)
    {
        $sql = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":password", $hashed_pass);
        if ($stmt->execute()) {
            session_start();
            $_SESSION['user_id'] = $this->conn->lastInsertId();

            alert("Account created successfully", "../login.php");
        } else {
            alert("Something went wrong", "../signup.php");
        }

    }

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

    public function logout()
    {
        session_start();
        session_unset();
        session_destroy();

        if (isset($_COOKIE['user_id'])) {
            unset($_COOKIE['user_id']);
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

// $ica2_auth_system = new Core("authdb", "root", "", "localhost");



// HELPERS 
function alert($message, $redirect)
{
    echo "<script>
        alert('$message');
        window.location.href = '$redirect';
    </script>";
    exit;
}
