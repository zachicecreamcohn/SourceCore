<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="favicon.ico">
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
    <center>
    <div class='skinnier'> 
    
    <?php
    // if not logged in, redirect to login.php
    if (!isset($_SESSION['current_user'])) {
        header("Location: login.php");
        exit;
    }

    
    require 'database.php';
    $stmt = $mysqli->prepare("SELECT id, display_name, username, first_name, last_name, email, admin_status, author_status FROM users WHERE id=?");
    if (!$stmt) {
        echo "Query Prep Failed: " . $mysqli->error;
        exit;
    }

    $stmt->bind_param('i', $_SESSION['current_user']);
    $stmt->execute();
    $stmt->bind_result($id, $display_name, $username, $first_name, $last_name, $email, $admin_status, $author_status);
    $stmt->fetch();
    $stmt->close();


  
    echo "<div class='form-container'>";
    
    echo "<div id='user-info'>
    
            <div id='user-info-left'>
                <h1 class='left space-below' id='userprofile-title'>User Profile</h1>
                <p><b>Display Name:</b> $display_name<a id='edit-display-name' class='red click-cursor italic left-padding'>edit</a><a id='cancel-edit-display-name' class='red click-cursor italic left-padding no-show'>cancel</a></p>
                <form action='edituser.php' method='post'>
                    <input type='hidden' name='id' value='$id'>
                    <input required id='edit-display-name-input' class='input-field no-show' type='text' name='new_display_name' placeholder='New Display Name'>
                    <input name='token' type='hidden' value='$_SESSION[token]'>
                    <input id='save-display-name' class='button no-show' type='submit' name='edit-display-name' value='Save Changes'>
                </form>
                

                <script>
                let cancelEditDisplayName = document.getElementById('cancel-edit-display-name');
                // find edit button and add event listener
                let editDisplayName = document.getElementById('edit-display-name');
                editDisplayName.addEventListener('click', function() {
                    // find input field and display it
                    let displayNameInput = document.getElementById('edit-display-name-input');
                    displayNameInput.style.display = 'block';

                    
                    // find save button and display it
                    let saveDisplayName = document.getElementById('save-display-name');
                    saveDisplayName.style.display = 'block';
                    
                    // find edit button and hide it
                    editDisplayName.classList.add('no-show');

                    // display cancel button
                    // remove no-show class from cancel button
                    cancelEditDisplayName.classList.remove('no-show');

                    // if cancel button is clicked, hide input field and save button and show edit button
                    cancelEditDisplayName.addEventListener('click', function() {
                        displayNameInput.style.display = 'none';
                        saveDisplayName.style.display = 'none';
                        editDisplayName.classList.remove('no-show');
                        cancelEditDisplayName.classList.add('no-show');
                    });


                });
                </script>
                
                <p><b>Username:</b> $username<a id='edit-username' class='red click-cursor italic left-padding'>edit</a><a id='cancel-edit-username' class='red click-cursor italic left-padding no-show'>cancel</a></p>
                <form action='edituser.php' method='post'>
                    <input type='hidden' name='id' value='$id'>
                    <input required id='edit-username-input' class='input-field no-show' type='text' name='new_username' placeholder='New Username'>
                    <input name='token' type='hidden' value='$_SESSION[token]'>
                    <input id='save-username' class='button no-show' type='submit' name='edit-username' value='Save Changes'>
                </form>
                

                <script>
                let cancelEditUsername = document.getElementById('cancel-edit-username');
                // find edit button and add event listener
                let editUsername = document.getElementById('edit-username');
                editUsername.addEventListener('click', function() {
                    // find input field and display it
                    let usernameInput = document.getElementById('edit-username-input');
                    usernameInput.style.display = 'block';

                    
                    // find save button and display it
                    let saveUsername = document.getElementById('save-username');
                    saveUsername.style.display = 'block';
                    
                    // find edit button and hide it
                    editUsername.classList.add('no-show');

                    // display cancel button
                    // remove no-show class from cancel button
                    cancelEditUsername.classList.remove('no-show');

                    // if cancel button is clicked, hide input field and save button and show edit button
                    cancelEditUsername.addEventListener('click', function() {
                        usernameInput.style.display = 'none';
                        saveUsername.style.display = 'none';
                        editUsername.classList.remove('no-show');
                        cancelEditUsername.classList.add('no-show');
                    });


                });
                </script>
                <p><b>First Name:</b> $first_name<a id='edit-first-name' class='red click-cursor italic left-padding'>edit</a><a id='cancel-edit-first-name' class='red click-cursor italic left-padding no-show'>cancel</a></p>
                <form action='edituser.php' method='post'>
                    <input type='hidden' name='id' value='$id'>
                    <input required id='edit-first-name-input' class='input-field no-show' type='text' name='new_first_name' placeholder='New First Name'>
                    <input name='token' type='hidden' value='$_SESSION[token]'>
                    <input id='save-first-name' class='button no-show' type='submit' name='edit-first-name' value='Save Changes'>
                </form>
                

                <script>
                let cancelEditFirstName = document.getElementById('cancel-edit-first-name');
                // find edit button and add event listener
                let editFirstName = document.getElementById('edit-first-name');
                editFirstName.addEventListener('click', function() {
                    // find input field and display it
                    let firstNameInput = document.getElementById('edit-first-name-input');
                    firstNameInput.style.display = 'block';

                    
                    // find save button and display it
                    let saveFirstName = document.getElementById('save-first-name');
                    saveFirstName.style.display = 'block';
                    
                    // find edit button and hide it
                    editFirstName.classList.add('no-show');

                    // display cancel button
                    // remove no-show class from cancel button
                    cancelEditFirstName.classList.remove('no-show');

                    // if cancel button is clicked, hide input field and save button and show edit button
                    cancelEditFirstName.addEventListener('click', function() {
                        firstNameInput.style.display = 'none';
                        saveFirstName.style.display = 'none';
                        editFirstName.classList.remove('no-show');
                        cancelEditFirstName.classList.add('no-show');
                    });


                });
                </script>
                <p><b>Last Name:</b> $last_name<a id='edit-last-name' class='red click-cursor italic left-padding'>edit</a><a id='cancel-edit-last-name' class='red click-cursor italic left-padding no-show'>cancel</a></p>
                <form action='edituser.php' method='post'>
                    <input type='hidden' name='id' value='$id'>
                    <input required id='edit-last-name-input' class='input-field no-show' type='text' name='new_last_name' placeholder='New Last Name'>
                    <input name='token' type='hidden' value='$_SESSION[token]'>
                    <input id='save-last-name' class='button no-show' type='submit' name='edit-last-name' value='Save Changes'>
                </form>
                

                <script>
                let cancelEditLastName = document.getElementById('cancel-edit-last-name');
                // find edit button and add event listener
                let editLastName = document.getElementById('edit-last-name');
                editLastName.addEventListener('click', function() {
                    // find input field and display it
                    let lastNameInput = document.getElementById('edit-last-name-input');
                    lastNameInput.style.display = 'block';

                    
                    // find save button and display it
                    let saveLastName = document.getElementById('save-last-name');
                    saveLastName.style.display = 'block';
                    
                    // find edit button and hide it
                    editLastName.classList.add('no-show');

                    // display cancel button
                    // remove no-show class from cancel button
                    cancelEditLastName.classList.remove('no-show');

                    // if cancel button is clicked, hide input field and save button and show edit button
                    cancelEditLastName.addEventListener('click', function() {
                        lastNameInput.style.display = 'none';
                        saveLastName.style.display = 'none';
                        editLastName.classList.remove('no-show');
                        cancelEditLastName.classList.add('no-show');
                    });


                });
                </script>
                <p><b>Email:</b> $email<a id='edit-email' class='red click-cursor italic left-padding'>edit</a><a id='cancel-edit-email' class='red click-cursor italic left-padding no-show'>cancel</a></p>
                <form action='edituser.php' method='post'>
                    <input type='hidden' name='id' value='$id'>
                    <input required id='edit-email-input' class='input-field no-show' type='text' name='new_email' placeholder='New Email'>
                    <input name='token' type='hidden' value='$_SESSION[token]'>
                    <input id='save-email' class='button no-show' type='submit' name='edit-email' value='Save Changes'>
                </form>
                

                <script>
                let cancelEditEmail = document.getElementById('cancel-edit-email');
                // find edit button and add event listener
                let editEmail = document.getElementById('edit-email');
                editEmail.addEventListener('click', function() {
                    // find input field and display it
                    let emailInput = document.getElementById('edit-email-input');
                    emailInput.style.display = 'block';

                    
                    // find save button and display it
                    let saveEmail = document.getElementById('save-email');
                    saveEmail.style.display = 'block';
                    
                    // find edit button and hide it
                    editEmail.classList.add('no-show');

                    // display cancel button
                    // remove no-show class from cancel button
                    cancelEditEmail.classList.remove('no-show');

                    // if cancel button is clicked, hide input field and save button and show edit button
                    cancelEditEmail.addEventListener('click', function() {
                        emailInput.style.display = 'none';
                        saveEmail.style.display = 'none';
                        editEmail.classList.remove('no-show');
                        cancelEditEmail.classList.add('no-show');
                    });


                });
                </script>
            </div>
            ";

    if ($admin_status == 1) {
        $admin_status = "Admin";
    } else {
        $admin_status = "Not Admin";
    }
    if ($author_status == 1) {
        $author_status = "Author";
    } else {
        $author_status = "Not Author";
        
    }
        echo "
            <div id='user-info-right'>
            <h1 class='no-opacity space-below' >A</h1>
                <p><b>Admin Status:</b> $admin_status</p>
                <p><b>Author Status:</b> $author_status</p>";
            if ($author_status == 'Not Author') {
                echo "<form action='/news_site/requestAuthorPermissions.php' method='post'>
            <input type='hidden' name='user_id' value='".$_SESSION['current_user']."'>
            <input type='hidden' name='token' value='".$_SESSION['token']."'>
            <button class='button' type='submit' name='requestauthorstatus' >Request Author Status</button></form>";
            }
echo "
            </div>
            </div>
            <a href='logout.php' id = 'logout' ><button class='button'>Logout</button></a>
            </div>
            
            </center>
            
            

        ";
        
    ?>
    </div>