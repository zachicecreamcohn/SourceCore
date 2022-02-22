<?php
session_start();
?>
<!DOCTYPE html>
<html lang = 'en'>
<head>
<meta charset = 'UTF-8'>
<title>Add Article Backend</title>
<link rel = 'stylesheet' href = 'css/style.css'>
</head>
<body>
<?php
// if session variable current_user is not set, display error message
if (!isset($_SESSION['current_user'])) {
    echo "<h1>You must be logged in to add an article!</h1>";
    echo "<p>Would you like to <a href='/news_site/login.php'>login?</a></p>";
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

    
        // add the article to the database
        if (isset($_POST['addarticle'])) {

            $token = $_POST['token'];
            // check if token is valid
            if (!hash_equals($_SESSION['token'], $token)) {
                die("Request forgery detected");
                // redirect to index.php
                header("Location: index.php");
            }
            
            // get values from form
            $articleType = $_POST['articleType'];
            if ($articleType == 'regarticle') {
                if ($author_status == 1) {
                    echo "Regular Article";
                    $title = $_POST['title'];

                    // check for post data in each topic category
                    if (empty($_POST['gear'])) {
                        $gear = 0;
                    } else {
                        $gear = 1;
                    }

                    if (empty($_POST['artists_and_technicians'])) {
                        $artists_and_technicians = 0;
                    } else {
                        $artists_and_technicians = 1;
                    }

                    if (empty($_POST['shows'])) {
                        $shows = 0;
                    } else {
                        $shows = 1;
                    }

                    if (empty($_POST['picks'])) {
                        $picks = 0;
                    } else {
                        $picks = 1;
                    }


                    $imgURL = $_POST['img-url'];
                    $blurb = $_POST['blurb'];
                    $article = $_POST['article'];
                    $user_id = $_SESSION['current_user'];
                    // insert values into database
                    require 'database.php';
                    $stmt = $mysqli->prepare("INSERT INTO articles (title, imgURL, gear, artists_and_technicians, shows, staff_picks, blurb, articleContent, authorID, postType) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'regular_post')");
                    if (!$stmt) {
                        echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
                        exit;
                    }
                    
                    $stmt->bind_param('ssiiiissi', $title, $imgURL, $gear, $artists_and_technicians, $shows, $picks, $blurb, $article, $user_id);
                    $stmt->execute();
                    $stmt->close();

                    echo "<h1>Article Added!</h1>";
                    echo "<p>Would you like to <a href='/news_site/addarticle.php'>add another article?</a></p>";
                } else {
                    echo "<h1>You do not have permission to add a full article!</h1>";
                    echo "<p>For information on how to get 'Author' permission, please contact <a href='mailto:zcohn@wustl.edu'>Zach Cohn</a></p>";
                    echo "<p>Or, you can</p>";
                    echo "<form action='/news_site/requestAuthorPermissions.php' method='post'>
                    <input type='hidden' name='user_id' value='".$_SESSION['current_user']."'>
                    <input type='hidden' name='token' value='".$_SESSION['token']."'>
                    <button type='submit' name='requestauthorstatus' >Request Author Status</button></form>";

                }

            } else if ($articleType == 'linkarticle') {
                
                $title = $_POST['title'];
                
                // check for post data in each topic category
                if (empty($_POST['gear'])) {
                    $gear = 0;
                } else {
                    $gear = 1;
                }

                if (empty($_POST['artists_and_technicians'])) {
                    $artists_and_technicians = 0;
                } else {
                    $artists_and_technicians = 1;
                }

                if (empty($_POST['shows'])) {
                    $shows = 0;
                } else {
                    $shows = 1;
                }
                $imgURL = $_POST['img-url'];
                $link = $_POST['link'];
                $blurb = $_POST['blurb'];
                $user_id = $_SESSION['current_user'];

                // insert values into database
                require 'database.php';
                $stmt = $mysqli->prepare("INSERT INTO articles (title, imgURL, gear, artists_and_technicians, shows, blurb, link, authorID, postType) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'linked_post')");
                if (!$stmt) {
                    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
                    exit;
                }
                $stmt -> bind_param('ssiiissi', $title, $imgURL, $gear, $artists_and_technicians, $shows, $blurb, $link, $user_id);
                $stmt -> execute();
                $stmt -> close();

                echo "<h1>Article Added!</h1>";
                echo "<p>Would you like to <a href='/news_site/addarticle.php'>add another article?</a></p>";

            }

            // display success message
            echo "<h1>Article Published!</h1>";
            
           
            
            //redirect to homepage
            header("Location: index.php");
        }
}



?>
</body>