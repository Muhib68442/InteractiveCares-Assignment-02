<?php

// HELPERS 
function alert($message, $redirect)
{
    $redirect = ($redirect == "") ? "#" : $redirect;

    echo "<script>
        alert('$message');
        window.location.href = '$redirect';
    </script>";

    exit;
}

function check_length($data, $min_length, $message)
{
    if (strlen($data) < $min_length) {
        alert("$message must be at least $min_length characters", "../signup.php");
        // echo "$message must be at least $length characters";
        return false;
    } else {
        return true;
    }
}

function check_email_format($email)
{
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        alert("Invalid email address", "");
        return false;
    } else {
        return true;
    }
}

function sanitize_username($username)
{
    if (!preg_match("/^[a-zA-Z .]+$/", $username)) {
        alert("Username can only contain letters", "");
        return false;
    } else {
        return true;
    }
}

