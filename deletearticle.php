<?php
session_start();
?>
<!DOCTYPE html>
<html lang = 'en'>

<head>
<meta charset = 'UTF-8'>
<title>Delete Article</title>
<link rel = 'stylesheet' href = 'css/style.css'>
<link rel = 'stylesheet' href = 'https://cdn.jsdelivr.net/npm/katex@0.10.0/dist/katex.min.css' integrity = 'sha384-9eLZqc9ds8eNjO3TmqPeYcDj8n+Qfa4nuSiGYa6DjLNcv9BtN69ZIulL9+8CqC9Y' crossorigin = 'anonymous'>
<link rel = 'stylesheet' href = 'css/markdown_style.css'>
<script src = 'https://kit.fontawesome.com/7b8bf01427.js' crossorigin = 'anonymous'></script>
</head>

<body>
<div id = 'header'>
<div id = 'upper-header'>
<div id = 'upper-header-left' class = 'side-menu'>
<p class = 'verticle-center'></p>
</div>

<div id = 'logo'>
<a href = 'index.php'>
<p id = 'logotext'>SourceCore</p>
</a>
</div>

<div id = 'upper-header-right' class = 'side-menu'>

<?php
// check if user is admin
// check that current_user is an admin
require 'database.php';
$stmt = $mysqli->prepare( 'SELECT admin_status FROM users WHERE id=?' );
if ( !$stmt ) {
    echo 'Query Prep Failed: ' . $mysqli->error;
    exit;
}

$stmt->bind_param( 'i', $_SESSION[ 'current_user' ] );
$stmt->execute();
$stmt->bind_result( $admin_status );
$stmt->fetch();
$stmt->close();

if ( $admin_status == 1 ) {
    echo "<a href='adminportal.php'><p>Author Requests</p></a>";
}
?>

<a href = 'addarticle.php'>
<p>Add Story</p>
</a>

<?php
// if session variable current_user is set, display logout link
if ( isset( $_SESSION[ 'current_user' ] ) ) {
    echo "<a href='user.php'><p><i class='fa-regular fa-circle-user user-icon'></i></p></a>";
} else {
    echo "<a href='login.php'><p>Login</p></a>
    <a href='register.php'><p>Register</p></a>";
}
?>

</div>

</div>

<div id = 'top-thin-line' class = 'thin-line'></div>
<div id = 'nav'>

<div id = 'center-nav' class = 'options'>

<a href = 'index.php'>Explore</a>
<a href = 'index.php?topic=gear'>Gear</a>
<a href = 'index.php?topic=artists_and_technicians'>Artists + Technicians</a>
<a href = 'index.php?topic=shows'>Shows</a>
<a href = 'index.php?topic=staff_picks'>Staff Picks</a>

</div>


</div>
<div class = 'thin-line'></div>
</div>
<div class = 'skinnier'>
    <div class='center-center center-text'>

        <?php
        // if user is not logged in, redirect to login page
        if (( !isset( $_SESSION[ 'current_user' ] ) ) && $admin_status == 0 ) {
            header( 'Location: login.php' );
            exit;
        }

        // listen for post request to delete article
        if (isset($_POST['delete'])) {
            $article_id = $_POST['articleID'];
            $token = $_POST['token'];
            // check that token matches session token
            if (!hash_equals($_SESSION['token'], $token)) {
                echo "Invalid token";
                exit;
            }

            // get all comments for article
            // for each comment, delete emotes associated with comment
            // $stmt = $mysqli->prepare( 'SELECT commentID FROM comments WHERE articleID=?' );
            // if ( !$stmt ) {
            //     echo 'Query Prep Failed: (get comments) ' . $mysqli->error;
            //     exit;
            // }
            // $stmt->bind_param( 'i', $article_id );
            // $stmt->execute();
            // $stmt->bind_result( $comment_id );
            

            // while ( $stmt->fetch() ) {
            //     $stmt2 = $mysqli->prepare( 'DELETE FROM emotes WHERE commentID=?' );
            //     if ( !$stmt2 ) {
            //         echo 'Query Prep Failed:(delete emotes) ' . $mysqli->error;
            //         exit;
            //     }
            //     $stmt2->bind_param( 'i', $comment_id );
            //     $stmt2->execute();
            //     $stmt2->close();

            // }


            // delete emotes

            $stmt = $mysqli->prepare( 'DELETE FROM emotes WHERE articleID=?' );
            if ( !$stmt ) {
                echo 'Query Prep Failed: (delete emotes) ' . $mysqli->error;
                exit;
            }
            $stmt->bind_param( 'i', $article_id );
            $stmt->execute();
            $stmt->close();

            

            // NOW, delete comments
            $stmt = $mysqli->prepare( 'DELETE FROM comments WHERE articleID=?' );
            if ( !$stmt ) {
                echo 'Query Prep Failed: (delete comments) ' . $mysqli->error;
                exit;
            }
            $stmt->bind_param( 'i', $article_id );
            $stmt->execute();
            $stmt->close();

            // Finally, we delete the article
            $stmt = $mysqli->prepare( 'DELETE FROM articles WHERE id=?' );
            if ( !$stmt ) {
                echo 'Query Prep Failed (delete from articles where id=?): ' . $mysqli->error;
                exit;
            }

            $stmt->bind_param( 'i', $article_id );
            $stmt->execute();
            $stmt->close();

            echo "Article deleted";
            // after 1 second, redirect to index.php
            header( "refresh:1; url=index.php" );
            exit;


        }

        
        $articleID = $_GET[ 'id' ];

        ?>


        <div class='form-container'>
            <form action='deletearticle.php' method='post'>
                <input type='hidden' name='articleID' value='<?php echo htmlspecialchars($articleID); ?>'>
                <h1>Are you sure you want to delete this article?</h1>
                <input type='hidden' name='token' value='<?php echo htmlspecialchars($_SESSION['token']); ?>'>
                <input type='submit' class='button' name='delete' value='Yes'>
                <a href='article.php?id=<?php echo htmlspecialchars($articleID) ?>'><input class='button' type='button' value='No'></a>
            </form>
            
        </div>
                
    </div>
</div>
</body>