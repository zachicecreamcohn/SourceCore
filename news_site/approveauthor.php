<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
    <meta charset="UTF-8">
    <title>Approve Author</title>
    <link rel="stylesheet" href="css/markdown_style.css">
    <link rel="stylesheet" href="css/style.css">
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

<?php
// if session variable not set, redirect to index.php
if (!isset($_SESSION['current_user'])) {
    header("Location: index.php");
    exit;
}
require 'database.php';
 // check that current_user is an admin

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
 


 

 if ($admin_status != 1) {
     echo "You are not an admin. You need admin status to access this page.";
     echo "<p>Return to <a href='index.php'>home</a></p>";
     exit;
 }


 // listen for POST request
 if (isset($_POST['approve_author_request'])) {
     $token = $_POST['token'];
     require 'database.php';

    
     $user_id = $_POST['user_id'];
     $current_user_id = $_POST['current_user_id'];
     // check that user_id is current_user
    if ($current_user_id != $_SESSION['current_user']) {
        echo "You are not authorized to approve this author request.";
        echo "<p>Return to <a href='index.php'>home</a></p>";
        exit;
    }
   


    // check that token is valid
    if (!hash_equals($_SESSION['token'], $token)) {
        echo "Invalid token. Please try again.";
        echo "<p>Return to <a href='index.php'>home</a></p>";
        exit;
    }


    // check that user_id is not already an author
    $stmt = $mysqli->prepare("SELECT author_status FROM users WHERE id=?");
    if (!$stmt) {
        echo "Query Prep Failed: " . $mysqli->error;
        exit;
    }
    echo "user_id: " . $user_id;
    $stmt -> bind_param('i', $user_id);
    $stmt -> execute();
    $stmt -> bind_result($author_status);
    $stmt -> fetch();
    $stmt -> close();
    
    if ($author_status == 1) {
        echo "This user is already an author.";
        echo "<p>Return to <a href='index.php'>home</a></p>";
        exit;
    }

    // update author_status to 1
    $stmt = $mysqli->prepare("UPDATE users SET author_status=1 WHERE id=?");
    if (!$stmt) {
        echo "Query Prep Failed: " . $mysqli->error;
        exit;
    }
    $stmt -> bind_param('i', $user_id);
    $stmt -> execute();
    $stmt -> close();

    // delete author request
    $stmt = $mysqli->prepare("DELETE FROM author_requests WHERE requesterID=?");
    if (!$stmt) {
        echo "Query Prep Failed: " . $mysqli->error;
        exit;
    }
    $stmt -> bind_param('i', $user_id);
    $stmt -> execute();
    $stmt -> close();


    echo " Author request approved.";;
    // after 2 seconds, redirect to adminportal.php
    header("refresh:2; url=adminportal.php");
    
 }


 ?>
</div>
</body>