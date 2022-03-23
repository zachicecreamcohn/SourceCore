<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="favicon.ico">
    <title>Add Article</title>
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
        <div class='form-container'>
    
    <?php
    // if session variable current_user is not set, display error message
    if (!isset($_SESSION['current_user'])) {
        echo "<h1>You must be logged in to add an article!</h1>";
        echo "<p>Would you like to <a href='/news_site/login.php' class='red'>login?</a></p>";
    } else {
        // if session variable current_user is set, check if current user has permission to add an article
        
        // get value of author_status from database for current user
        require 'database.php';
        $stmt = $mysqli->prepare("SELECT author_status FROM users WHERE id=?");
        $stmt -> bind_param('s', $_SESSION['current_user']);
        $stmt -> execute();
        $stmt -> bind_result($author_status);
        $stmt -> fetch();
        $stmt -> close();


        if ($author_status == 1) {
            // if current user has permission to add an article, display form with both regular and linked article types

            // two buttons - one to publish regular article, one to publish link article
            echo "<div id='article-buttons'>";
            echo "<button class='button' id='show-regular-article'>Publish Regular Article</button>";
            echo "<button class='button' id='show-link-article'>Publish Link Article</button>";
            echo "</div>";

            // a div containing the form to publish a regular article
            echo "<div id='addregarticle'>";
            echo "<form action='addarticlebackend.php' method='post'>";
            echo "<input class='input-field' type='text' name='title' placeholder='Title'><br>";
           
            echo "
            <input type='checkbox' name='gear' value='1'>
            <label for='gear'>Gear</label><br>
            <input type='checkbox' name='artists_and_technicians' value='1'>
            <label for='artists_and_technicians'>Artists + Technicians</label><br>
            <input type='checkbox' name='shows' value='1'>
            <label for='shows'>Shows</label><br>
            <input type='checkbox' name='picks' value='1'>
            <label for='picks'>Staff Picks</label><br>";
            echo "<input class='input-field' type='url' name='img-url' placeholder='Image URL' required ><br>";

            echo "<textarea class='input-field' rows='5' cols='100'name='blurb' placeholder='blurb'></textarea><br>";
            echo "<textarea class='input-field' rows='15' cols='5'name='article' placeholder='Article Content'></textarea><br>";
            echo "<input type='hidden' name='articleType' value='regarticle'>";
            echo "<input type='hidden' name='token' value='".htmlspecialchars($_SESSION['token'])."'>";
            // TODO: Add photo upload feature
            echo "<input class='button' type='submit' name='addarticle' value='Publish Article'>";
            echo "</form>";
            echo "</div>";

            // a div containing the form to publish a link article
            echo "<div id='addlinkarticle'>";
            echo "<form action='addarticlebackend.php' method='post'>";
            echo "<input class='input-field' type='text' name='title' placeholder='Title'><br>";
            
            echo "
            <input type='checkbox' name='gear' value='1'>
            <label for='gear'>Gear</label><br>
            <input type='checkbox' name='artists_and_technicians' value='1'>
            <label for='artists_and_technicians'>Artists + Technicians</label><br>
            <input type='checkbox' name='shows' value='1'>
            <label for='shows'>Shows</label><br>
            <input type='checkbox' name='picks' value='1'>
            <label for='picks'>Staff Picks</label><br>";
            echo "<input class='input-field' type='url' name='img-url' placeholder='Image URL' required><br>";
            echo "<input class='input-field' type='url' name='link' placeholder='Link' required ><br>";
            echo "<textarea class='input-field' rows='5' cols='100'name='blurb' placeholder='blurb'></textarea><br>";
            echo "<input type='hidden' name='articleType' value='linkarticle'>";
            echo "<input type='hidden' name='token' value='".htmlspecialchars($_SESSION['token'])."'>";
            // TODO: Add photo upload feature
            echo "<input class='button' type='submit' name='addarticle' value='Publish Link'>";
            echo "</form>";
            echo "</div>";


            echo "<script src='js/addarticle.js'></script>";

          
        } else {

            // a div containing the form to publish a link article
            echo "<div id='addlinkarticle-only'>";
            echo "<form action='addarticlebackend.php' method='post'>";
            echo "<input class='input-field' type='text' name='title' placeholder='Title'><br>";
            echo "
            <input type='checkbox' name='gear' value='1'>
            <label for='gear'>Gear</label><br>
            <input type='checkbox' name='artists_and_technicians' value='1'>
            <label for='artists_and_technicians'>Artists + Technicians</label><br>
            <input type='checkbox' name='shows' value='1'>
            <label for='shows'>Shows</label><br>
            <input type='checkbox' name='picks' value='1'>
            <label for='picks'>Staff Picks</label><br>";
            echo "<input class='input-field' type='url' name='img-url' placeholder='Image URL' required ><br>";
            echo "<input class='input-field' type='url' name='link' placeholder='Link' required><br>";
            echo "<textarea class='input-field' rows='5' cols='100' name='blurb' placeholder='blurb'></textarea><br>";
            echo "<input type='hidden' name='articleType' value='linkarticle'>";
            echo "<input type='hidden' name='token' value='".htmlspecialchars($_SESSION['token'])."'>";
            // TODO: Add photo upload feature
            echo "<input class='button' type='submit' name='addarticle' value='Publish Link'>";
            echo "</form>";
            echo "</div>";
            // if current user does not have permission to add an article, display error message
            echo "<h3>You do not have permission to add a full-length (non-link) article!</h3>";
            echo "<p>For information on how to get 'Author' permission, please contact <a href='mailto:zcohn@wustl.edu' class='red'>Zach Cohn</a></p>";
            echo "<p>Or, you can</p>";
            echo "<form action='/news_site/requestAuthorPermissions.php' method='post'>
            <input type='hidden' name='user_id' value='".htmlspecialchars($_SESSION['current_user'])."'>
            <input type='hidden' name='token' value='".htmlspecialchars($_SESSION['token'])."'>
            <button class='button' type='submit' name='requestauthorstatus' >Request Author Status</button></form>";


        }

    }
    ?>
    </div>
</div>
    </div>
</body>