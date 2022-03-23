<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Portal</title>
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
        <center>
        <div class='form-container'>
    <?php
    // if current_user is not set, redirect to index.php
    if (!isset($_SESSION['current_user'])) {
        header("Location: index.php");
        exit;
    }
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
    


    

    if ($admin_status != 1) {
        echo "You are not an admin. You need admin status to access this page.";
        echo "<p>Return to <a href='index.php'>home</a></p>";
        exit;
    }

    // view all author requests
  

    // get all author requests and corresponding user info
    $stmt = $mysqli->prepare("SELECT request_datetime, users.id, users.username, users.display_name, users.email FROM author_requests JOIN users ON requesterID=users.id");
    if (!$stmt) {
        echo "Query Prep Failed: " . $mysqli->error;
        exit;
    }
    $stmt->execute();
    $stmt->bind_result($request_datetime, $user_id, $username, $display_name, $email);
    echo "<table id='author_request_table'>
    <tr>
    <th>Request Date</th>
    <th>Username</th>
    <th>Display Name</th>
    <th>Email</th>
    <th>Action</th>
    </tr>";

    while ($stmt->fetch()) {
        echo "<tr>";
        echo "<td>" .htmlspecialchars($request_datetime) . "</td>";
        echo "<td>".htmlspecialchars($username) . "</td>";
        echo "<td>".htmlspecialchars($display_name) . "</td>";
        echo "<td>".htmlspecialchars($email) ."</td>";
        $current_user_id = $_SESSION['current_user'];

        echo "<td><form action='approveauthor.php' method='post'>" .
            "<input type='hidden' name='user_id' value='" . htmlspecialchars($user_id) . "'>" .
            "<input type='hidden' name='current_user_id' value='" . htmlspecialchars($current_user_id) . "'>" .
            "<input type='hidden' name='token' value='" . htmlspecialchars($_SESSION['token']) . "'>" .
            "<input type='submit' name='approve_author_request' value='Approve'>" .
            "</form></td>";

        echo "</tr>";
    }
    echo "</table>";
    $stmt->close();
    ?>
        </div>
</center>
    </div>
</body>