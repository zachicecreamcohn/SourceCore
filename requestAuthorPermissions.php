<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Request Author Status</title>
    <link rel="icon" href="favicon.ico">
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
    <div class='skinnier'> 
    <?php
    // if session variable current_user not set, redirect to index.php
    if (!isset($_SESSION['current_user'])) {
        header("Location: index.php");
        exit;
    }

    // if POST data, process it
    if (isset($_POST['requestauthorstatus'])) {
        // get post data
        $requesterID = $_POST['user_id'];
        if ($requesterID != $_SESSION['current_user']) {
            echo "You can only request author status for yourself.";
            echo "<p>Return to <a href='index.php' class='red'>home</a></p>";
            exit;
        }
        $token  = $_POST['token'];
        // check if token is valid
       
        if (!hash_equals($_SESSION['token'], $token)) {
            echo "Invalid token. Please try again.";
            echo "<p>Return to <a href='index.php'>home</a></p>";
            
        } else {
            require 'database.php';

            // token is valid, request author status

            //check if user has already requested author status
            $stmt = $mysqli->prepare("SELECT COUNT(*) FROM author_requests WHERE requesterID=?");
            $stmt->bind_param('i', $requesterID);
            $stmt->execute();
            $stmt->bind_result($count);
            $stmt->fetch();
            $stmt->close();
            


            if ($count > 0) {
                echo "You have already requested author status.";
                echo "<p>Return to <a href='index.php'class='red'>home</a></p>";
            } else {
                
            // insert row into author_requests table
            $stmt = $mysqli->prepare("INSERT INTO author_requests (request_datetime, requesterID) VALUES (?, ?)");
            if (!$stmt) {
                echo "Query Prep Failed: " . $mysqli->error;
                exit;
            }
            // datetime in MySQL format
            $request_datetime = date("Y-m-d H:i:s");
  
            $stmt->bind_param('si', $request_datetime, $requesterID);
            
            $stmt->execute();
            $stmt->close();
            echo "<p>Your request has been sent to the site administrator.</p><p>Check back to see if you've been granted Author status.</p>";
            echo "<p>Return to <a href='index.php' class='red'>home</a></p>";

        

            

        }


    }
}
    ?>
    </div>