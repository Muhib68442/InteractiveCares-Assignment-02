<?php

require "core.php";
// echo "Trigger.php";

// CREATE INSTANCE 
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
    check_email_format($email);

    if ($ica2_auth_system->email_exists($email)) {
        alert("Email already exists", "../signup.php");
        $error = true;
        return false;
    }

    // VALIDATE PASSWORD LENGTH
    check_length($password, 8, "Password");

    // USERNAME FIELD SANITIZE 
    check_length($username, 3, "Full Name");
    sanitize_username($username);


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


// TRIGGER UPDATE 
if (isset($_POST['update'])) {

    $username = $_POST['username'];
    $email = $_POST['email'];
    $prev_email = $_POST['prev_email'];

    // USERNAME VALIDATION
    check_length($username, 3, "Username");
    sanitize_username($username);

    // UPDATE EMAIL ONLY IF NEEDED 
    if ($email != $prev_email) {    // EMAIL CGANGED

        check_length($email, 3, "Email");
        check_email_format($email);

        // EMAIL UNIQUE
        if ($ica2_auth_system->email_exists($email)) {
            alert("Email already exists! Please use another email.", "../edit-profile.php");
            return false;
        }
    }


    if ($ica2_auth_system->update($username, ($email == $prev_email ? null : $email))) {
        alert("Profile updated successfully", "../edit-profile.php");
    } else {
        alert("Something went wrong", "../edit-profile.php");
    }

}


// TRIGGER PASSWORD 
if (isset($_POST['update_password'])) {

    $old_pass = $_POST['old_password'];
    $new_pass = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];

    session_start();
    $userID = $_SESSION['user_id'];

    // FETCH SAVED PASSWORD 
    $user = $ica2_auth_system->fetch($userID);
    $saved_pass = $user['password']; // HASHED 
    // echo $saved_pass;

    // VERIFY 
    if (password_verify($old_pass, $saved_pass)) {

        check_length($new_pass, 8, "Password");

        if ($new_pass === $confirm_pass) {
            if ($ica2_auth_system->update_password($new_pass)) {
                alert("Password updated successfully", "../change-password.php");
            } else {
                alert("Something went wrong", "../change-password.php");
            }
        } else {
            alert("Password does not match", "../change-password.php");
        }

    } else {
        alert("Invalid password", "../change-password.php");
    }

}

