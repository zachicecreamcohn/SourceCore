<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Home</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/markdown_style.css">
    <script src="https://kit.fontawesome.com/7b8bf01427.js" crossorigin="anonymous"></script> <!-- this grants access to a bunch of icons -->
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

    <div class="skinny">
    <?php

    
    require 'database.php';

    // get topic get variable
    if (isset($_GET['topic'])) {
        $topic = $_GET['topic'];

        // count number of articles in database where $topic = 1
        $stmt = $mysqli->prepare("SELECT COUNT(*) FROM articles WHERE $topic=1");
        if (!$stmt) {
            echo "Query Prep Failed: " . $mysqli->error;
            exit;
        }
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {

            // get title, link, postDateTime, blurb, and author from database where topic in database = 1
            $stmt = $mysqli->prepare("select title, imgURL, link, postDateTime, blurb, authorID, id, postType from articles where $topic = 1 order by postDateTime asc");
            if (!$stmt) {
                echo "Query Prep Failed: " . $mysqli->error;
                exit;
            }

            $stmt->execute();
        
            // for each row returned, display title, link, postDateTime, blurb, and author
            $stmt->bind_result($title, $imgURL, $link, $postDateTime, $blurb, $authorID, $id, $postType);
        } else {
            echo "<p>No articles found.</p>";
        }

    } else {
        // get title, link, postDateTime, blurb, and author from database
        $stmt = $mysqli->prepare("select title, imgURL, link, postDateTime, blurb, authorID, id, postType from articles order by postDateTime asc");
        if (!$stmt) {
            echo "Query Prep Failed: " . $mysqli->error;
            exit;
        }

        $stmt->execute();
    
        // for each row returned, display title, link, postDateTime, blurb, and author
        $stmt->bind_result($title, $imgURL, $link, $postDateTime, $blurb, $authorID, $id, $postType);
    }
 
    echo "<div class='article-list'>";
    while ($stmt->fetch()) {

        if ($postType == 'regular_post') {
           echo "<div class='article regular-post'>";
           echo "<a href='/news_site/article.php?id=$id'><div class='article-img' style='background-image: url($imgURL)'>
           <div class='article-img-overlay'></div></div></a>";
           echo "<p><a href='/news_site/article.php?id=$id' class='title-link'>" . htmlspecialchars($title) . "</a></p>";
        } else if ($postType == 'linked_post') {
            echo "<div class='article linked-post'>";
        
            echo "<a href='$link' target='_blank'><div class='external-link-overlay'>";
                echo "<div class='top-right-corner'>";
                    echo "<div class='down-triangle'></div>";
                    echo "<div class='left-triangle'></div>";
                echo "</div>";

                echo "<i class='fa-solid fa-arrow-up-right-from-square external-link-icon'></i>";
            echo "</div></a>";
            echo "<a href='/news_site/article.php?id=$id'><div class='article-img' style='background-image: url($imgURL)'>
            <div class='article-img-overlay'></div></div></a>";
            echo "<p><a href='/news_site/article.php?id=$id' class='title-link'>" . htmlspecialchars($title) . "</a></p>";
        }

        
        // echo "<p>Posted on " . htmlspecialchars($postDateTime) . "</p>";
        echo "<p class='blurb'>" . htmlspecialchars($blurb) . "</p>";
        // echo "<p>By " . htmlspecialchars($authorID) . "</p>";
        echo "</div>";
    }
    // insert three empty articles to fill up the space
    echo "<div class='article hidden'></div>";
    echo "<div class='article hidden'></div>";
    echo "<div class='article hidden'></div>";
    echo "</div>";

    $stmt->close();
    
    ?>


    </div>
    

</body>

