<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register for an Account</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/markdown_style.css">
    <script src="https://kit.fontawesome.com/7b8bf01427.js" crossorigin="anonymous"></script>

</head>
<body>
<div id="header">
        <div id="upper-header">
            <div id='upper-header-left' class='side-menu'>
                <p class='verticle-center'></p>
            </div>

            <div id="logo">
                <a href="index.php">
                    <p id="logotext">SourceCore</p>
                </a>
            </div>

            <div id="upper-header-right" class='side-menu'>
                
            <?php
                // check if user is admin
                // check that current_user is an admin
                require 'database.php';
                $stmt = $mysqli->prepare("SELECT admin_status FROM users WHERE id=?");
                if (!$stmt) {
                    echo "Query Prep Failed: " . $mysqli->error;
                    exit;
                }

                $stmt->bind_param('i', $_SESSION['current_user']);
                $stmt->execute();
                $stmt->bind_result($admin_status);
                $stmt->fetch();
                $stmt->close();

                if ($admin_status == 1) {
                    echo "<a href='adminportal.php'><p>Author Requests</p></a>";
                }
                ?>
                

            <a href="addarticle.php"><p>Add Story</p></a>

            <?php
                // if session variable current_user is set, display logout link
                if (isset($_SESSION['current_user'])) {
                    echo "<a href='user.php'><p><i class='fa-regular fa-circle-user user-icon'></i></p></a>";
                } else {
                    echo "<a href='login.php'><p>Login</p></a>
                    <a href='register.php'><p>Register</p></a>";
                }
                ?>

            </div>
        

        </div>
      
        <div id="top-thin-line" class="thin-line"></div>
        <div id="nav">
            
            <div id='center-nav' class='options'>
            
                <a href="index.php">Explore</a>
                <a href="index.php?topic=gear">Gear</a>
                <a href="index.php?topic=artists_and_technicians">Artists + Technicians</a>
                <a href="index.php?topic=shows">Shows</a>
                <a href="index.php?topic=staff_picks">Staff Picks</a>
                
                

                
                
                
            
            </div>

            
            
        </div>
        <div class="thin-line"></div>
    </div>
    <div class="skinnier">
            <div class='center-center center-text'>
    <?php
    // if session variable current_user is setl, redirect to index.php
    if (isset($_SESSION['current_user'])) {
        header("Location: index.php");
    }


    require 'database.php';

    // if POST data, process it
    if (isset($_POST['login'])) {
        // get the data from the form
        $username = $_POST['username'];
        $password = $_POST['password'];

        // if username is a valid email address, login using it as the email
        if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
            $email = $username;
        }

        // check if row exists in the database where email = $email
        $stmt = $mysqli->prepare("SELECT COUNT(*) FROM users WHERE email=?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->bind_result($countEmail);
        $stmt->fetch();
        $stmt->close();

        if ($countEmail > 0) {
            // a user exists in the database with that email
            echo "logging in with email";
            // TODO: login using the email

            $stmt = $mysqli->prepare("SELECT COUNT(*), id, hashed_password FROM users WHERE email=?");
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $stmt->bind_result($userCount, $userID, $hashed_password);
            $stmt->fetch();
            $stmt->close();

            // check if the password is correct
            if ($userCount == 1 && password_verify($password, $hashed_password)) {
                // password is correct, log the user in
                session_start();
                $_SESSION['current_user'] = $userID;
                // generate a random string to use as a token
                $token = bin2hex(openssl_random_pseudo_bytes(32)); // help from https://classes.engineering.wustl.edu/cse330/index.php?title=Web_Application_Security,_Part_2
                $_SESSION['token'] = $token;


                header("Location: index.php");
                exit;
            } else {
                // password is incorrect
                echo "Incorrect password";
                // after 2 seconds, redirect to login.php
            }

        } else {
            // check if row exists in the database where username = $username
            $stmt = $mysqli->prepare("SELECT COUNT(*) FROM users WHERE username=?");
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $stmt->bind_result($countUser);
            $stmt->fetch();
            $stmt->close();

            if ($countUser > 0) {
                // a user exists in the database with that username
                
                // TODO: login using the username
                $stmt = $mysqli->prepare("SELECT COUNT(*), id, hashed_password FROM users WHERE username=?");
                $stmt->bind_param('s', $username);
                $stmt->execute();
                $stmt->bind_result($userCount, $userID, $hashed_password);
                $stmt->fetch();
                $stmt->close();

                // check if the password is correct
                if ($userCount == 1 && password_verify($password, $hashed_password)) {
                    // password is correct, log the user in
                    session_start();
                    $_SESSION['current_user'] = $userID;

                    // generate a random string to use as a token
                    // code from https://www.php.net/manual/en/function.openssl-random-pseudo-bytes.php

                    $token = bin2hex(openssl_random_pseudo_bytes(32));
                    $_SESSION['token'] = $token;


                    header("Location: index.php");
                    exit;
                } else {
                    // password is incorrect
                    echo "Incorrect password";
                }
        } else {
            echo "No user found with that username or email";
        }
        
        }
    }

        ?>
        <div>
        <div class='form-container'>
            <h3>Login!</h3><br>
            
            <div id='login-group'>
            <form action="login.php" method="post">
                <div>
                    <input class='input-field' type="text" name="username" placeholder="Username or Email" required>
                </div>
                <div>
                    <input class='input-field' type="password" name="password" placeholder="Password" required>
                </div>
                <div>
                    <input type="submit" name="login" class='button' value="Login">
                </div>
            </form>
            <br>
            
            </div>
        </div>
        <p><i>Don't have an account? <a class='red' href='register.php'>Register</a></i></p>
        <div>
</div>
    </div>
    </div>
</div>

</body>
