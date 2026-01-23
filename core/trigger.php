<?php

require "core.php";
// echo "Trigger.php";


$ica2_auth_system = new Core("authdb", "root", "", "localhost");


// TRIGGER STORE
if (isset($_POST['signup'])) {

    // PREPARE DATA 
    $username = $_POST['username'];
    $email = strtolower($_POST['email']);
    $password = $_POST['password'];
    $password_confirmation = $_POST['password_confirmation'];
    $terms = (isset($_POST['terms']) ? true : false);

    $error = false;

    // CHECK TERMS 
    if (!$terms) {
        alert("Please agree to the terms and conditions", "../signup.php");
        $error = true;
        return false;
    }

    // VALIDATE EMAIL AND CHECK UNIQUE
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        alert("Invalid email address", "../signup.php");
        $error = true;
        return false;
    }

    if ($ica2_auth_system->email_exists($email)) {
        alert("Email already exists", "../signup.php");
        $error = true;
        return false;
    }

    // VALIDATE PASSWORD LENGTH
    if (strlen($password) < 8) {
        alert("Password must be at least 8 characters", "../signup.php");
        $error = true;
        return false;
    }

    // USERNAME FIELD SANITIZE 
    if (strlen($username) <= 0) {
        alert("Username can't be empty", "../signup.php");
        $error = true;
        return false;
    } else if (!preg_match("/^[a-zA-Z .]+$/", $username)) {
        alert("Username can only contain letters", "../signup.php");
        $error = true;
        return false;
    }

    // CHECK PASSWORD CONFIRMATION (+ HASH PASS)
    if ($password === $password_confirmation) {
        $hashed_pass = password_hash($password, PASSWORD_DEFAULT);
    } else {
        alert("Password does not match", "../signup.php");
        $error = true;
        return false;
    }

    // IF ALL TESTS PASSES 
    if (!$error) {
        $ica2_auth_system->signup($username, $email, $hashed_pass);
    } else {
        alert("Something went wrong", "../signup.php");
    }


}

// TRIGGER LOGIN
if (isset($_POST['login'])) {

    $email = $_POST['email'];
    $password = $_POST['password'];
    $remember = false;

    // REMEMBER ME 
    if (isset($_POST['remember'])) {
        $remember = true;
    } else {
        $remember = false;
    }

    $ica2_auth_system->login($email, $password, $remember);
}


// TRIGGER LOGOUT
if (isset($_GET['logout'])) {
    // echo "Logout";
    $ica2_auth_system->logout();
    header("Location: ../login.php");
    exit();
}