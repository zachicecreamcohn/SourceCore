<?php
session_start();
?>
<!DOCTYPE html>
<html lang = 'en'>
<head>
<meta charset = 'UTF-8'>
<title>Edit User</title>
<link rel = 'stylesheet' href = 'css/style.css'>
</head>
<body>
<?php
// listen for post request




if (isset($_POST['edit-display-name'])) {

    $newDisplayName = $_POST['new_display_name'];

    $token = $_POST['token'];

    
    if (hash_equals($_SESSION['token'], $token)) {
        // the submitted token matches the session token
        // update the display name in the database
        require 'database.php';
        
        $stmt = $mysqli->prepare("UPDATE users SET display_name=? WHERE id=?");
        if (!$stmt) {
            echo "Query Prep Failed: " . $mysqli->error;
            exit;
        }
        $stmt->bind_param('si', $newDisplayName, $_SESSION['current_user']);
        $stmt->execute();
        $stmt->close();

        // redirect to user.php
        header("Location: user.php");
        exit;
        
    } else {
        // the submitted token does not match the session token
        // redirect to user.php
        header("Location: user.php");
        exit;
    }
}

// listen for post request to edit username
if (isset($_POST['edit-username'])) {

    $newUsername = $_POST['new_username'];

    $token = $_POST['token'];

    //hceck if username is already taken
    require 'database.php';
    $stmt = $mysqli->prepare("SELECT username FROM users WHERE username=?");
    if (!$stmt) {
        echo "Query Prep Failed: " . $mysqli->error;
        exit;
    }
    $stmt->bind_param('s', $newUsername);
    $stmt->execute();
    $stmt->bind_result($username);
    $stmt->fetch();
    $stmt->close();

    // if username is taken, redirect to user.php
    if ($username != null) {
        header("Location: user.php");
        exit;
    }
    
    
    if (hash_equals($_SESSION['token'], $token)) {
        // the submitted token matches the session token
        // update the username in the database
        require 'database.php';
        
        $stmt = $mysqli->prepare("UPDATE users SET username=? WHERE id=?");
        if (!$stmt) {
            echo "Query Prep Failed: " . $mysqli->error;
            exit;
        }
        $stmt->bind_param('si', $newUsername, $_SESSION['current_user']);
        $stmt->execute();
        $stmt->close();

        // redirect to user.php
        header("Location: user.php");
        exit;
        
    } else {
        // the submitted token does not match the session token
        // redirect to user.php
        header("Location: user.php");
        exit;
    }
}


// listen for post request to change first name 
if (isset($_POST['edit-first-name'])) {

    $newFirstName = $_POST['new_first_name'];

    $token = $_POST['token'];

    
    if (hash_equals($_SESSION['token'], $token)) {
        // the submitted token matches the session token
        // update the first name in the database
        require 'database.php';
        
        $stmt = $mysqli->prepare("UPDATE users SET first_name=? WHERE id=?");
        if (!$stmt) {
            echo "Query Prep Failed: " . $mysqli->error;
            exit;
        }
        $stmt->bind_param('si', $newFirstName, $_SESSION['current_user']);
        $stmt->execute();
        $stmt->close();

        // redirect to user.php
        header("Location: user.php");
        exit;
        
    } else {
        // the submitted token does not match the session token
        // redirect to user.php
        header("Location: user.php");
        exit;
    }
}


// listen for post request to change last name
if (isset($_POST['edit-last-name'])) {

    $newLastName = $_POST['new_last_name'];

    $token = $_POST['token'];

    
    if (hash_equals($_SESSION['token'], $token)) {
        // the submitted token matches the session token
        // update the last name in the database
        require 'database.php';
        
        $stmt = $mysqli->prepare("UPDATE users SET last_name=? WHERE id=?");
        if (!$stmt) {
            echo "Query Prep Failed: " . $mysqli->error;
            exit;
        }
        $stmt->bind_param('si', $newLastName, $_SESSION['current_user']);
        $stmt->execute();
        $stmt->close();

        // redirect to user.php
        header("Location: user.php");
        exit;
        
    } else {
        // the submitted token does not match the session token
        // redirect to user.php
        header("Location: user.php");
        exit;
    }
}


// listen for post request to change email
if (isset($_POST['edit-email'])) {

    $newEmail = $_POST['new_email'];

    $token = $_POST['token'];

    // check if email is valid
    if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
        // redirect to user.php
        header("Location: user.php");
        exit;
    }

    // check if email is already in use
    require 'database.php';
    $stmt = $mysqli->prepare("SELECT email FROM users WHERE email=?");
    if (!$stmt) {
        echo "Query Prep Failed: " . $mysqli->error;
        exit;
    }
    $stmt->bind_param('s', $newEmail);
    $stmt->execute();
    $stmt->bind_result($email);
    $stmt->fetch();
    $stmt->close();

    // if email is already in use, redirect to user.php
    if ($email) {
        // redirect to user.php
        header("Location: user.php");
        exit;
    }

    
    if (hash_equals($_SESSION['token'], $token)) {
        // the submitted token matches the session token
        // update the email in the database
        require 'database.php';
        
        $stmt = $mysqli->prepare("UPDATE users SET email=? WHERE id=?");
        if (!$stmt) {
            echo "Query Prep Failed: " . $mysqli->error;
            exit;
        }
        $stmt->bind_param('si', $newEmail, $_SESSION['current_user']);
        $stmt->execute();
        $stmt->close();

        // redirect to user.php
        header("Location: user.php");
        exit;
        
    } else {
        // the submitted token does not match the session token
        // redirect to user.php
        header("Location: user.php");
        exit;
    }
}

echo "no post request with correct name";

?>
</body>