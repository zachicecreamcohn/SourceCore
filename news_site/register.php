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
<?php
// if session variable current_user is set, redirect to index.php
if (isset($_SESSION['current_user'])) {
    header("Location: index.php");
    exit;
}

// if POST data, process it
if (isset($_POST['register'])) {
    // get the data from the form
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $password = $_POST['password'];
    $password2 = $_POST['password2'];


    require 'database.php';

    // check if email is a valid email address
    // code from https://www.w3schools.com/php/filter_validate_email.asp
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email address";
        exit;
    }

    // check if the username is already in the database
    $stmt = $mysqli->prepare("SELECT COUNT(*) FROM users WHERE username=?");


    $stmt->bind_param('s', $username);
    $stmt->execute();

    
    $stmt->bind_result($countUser);
    // close the prepared statement
    
    $stmt->close();


    // check if username is already in the database
    $stmt = $mysqli->prepare("SELECT COUNT(*) FROM users WHERE username=?");
    $stmt -> bind_param('s', $username);
    $stmt -> execute();
    $stmt -> bind_result($countUser);
    $stmt -> fetch();
    $stmt -> close();

    // if the username is already in the database, display an error message
    if ($countUser > 0) {
        echo "<p>That username is already in use. Would you like to <a href='/news_site/login.php'>login?</a></p>";
    } else {
        // check if the email is already in the database
        $stmt = $mysqli->prepare("SELECT COUNT(*) FROM users WHERE email=?");
        $stmt -> bind_param('s', $email);
        $stmt -> execute();
        $stmt -> bind_result($countEmail);
        $stmt -> fetch();
        $stmt -> close();

        // if the email is already in the database, display an error message
        if ($countEmail > 0) {
            echo "<p>That email is already in use. Would you like to <a href='/news_site/login.php'>login?</a></p>";
        } else {
        
    
    
            if ($password != $password2) {
                echo "<p>Your passwords do not match. Please try again.</p>";
            } else {
                // if the passwords match, insert the user into the database
                $stmt = $mysqli->prepare("INSERT INTO users (username, first_name, last_name, display_name, author_status, admin_status, email, hashed_password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $display_name = $firstname . " " . $lastname;
                $author_status = 0;
                $admin_status = 0;
                $stmt->bind_param('ssssiiss', $username, $firstname, $lastname, $display_name, $author_status, $admin_status, $email, password_hash($password, PASSWORD_DEFAULT));
                

                if (!$stmt->execute()) {
                    echo "<p>There was a problem registering you.</p>";
                } else {
                    echo "<p>You have been registered.</p>";
                    // redirect to login page
                    header("Location: login.php");
                    exit;
                }
                
                // close the prepared statement
                $stmt->close();
            }
        }
    }
}


    



?>
<div class='center-center center-text'>
<div class='form-container'>
    <div id='register-group'>
    <h3>Register!</h3><br>
    <form action="register.php" method="post">

            <div>
                <input class='input-field' type="text" name="username" id="username" placeholder="Username" required>
            </div>
            <div>
                <input class='input-field' type="text" name="firstname" id="firstname" placeholder="First Name" required>
            </div>
            <div>
                <input class='input-field' type="text" name="lastname" id="lastname" placeholder="Last Name" required>
            </div>
            <div>
                <input class='input-field' type="text" name="email" id="email" placeholder="Email" required>
            </div>
            <div>
                <input class='input-field' type="password" name="password" autocomplete=new-password placeholder="Password" required>
            </div>
            <div>
                <input class='input-field' type="password" name="password2" placeholder="Confirm Password" required>
            </div>
            <input class='button' type="submit" name="register" value="Register">

    </form>
    </div>
</div>
</div>



  </div>
</body>
</html>

    
