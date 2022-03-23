<?php
session_start();
?>
<!DOCTYPE html>
<html lang = 'en'>

<head>
<meta charset = 'UTF-8'>
<link rel="icon" href="favicon.ico">
<title>Home</title>
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
    <div class='form-container'>

<?php
require 'database.php';
// get article id from url
$article_id = $_GET[ 'id' ];

// listen for post request for 'edit-link-post'
if (isset($_POST['edit-link-post'])) {
    $token = $_POST['token'];
    $newTitle = $_POST['title'];
    $newLink = $_POST['link'];
    $newImgURL = $_POST['img-url'];
    $newBlurb = $_POST['blurb'];
    // $newContent = $_POST['article'];

    // check that token is valid
    if (!hash_equals($_SESSION['token'], $token)) {
        die("Request forgery detected");
        header("Location: editarticle.php?id=$article_id");
        exit;
    }

    if ( isset( $_SESSION[ 'current_user' ] ) ) {
        $stmt = $mysqli->prepare( 'SELECT authorID FROM articles WHERE id=?' );
        if ( !$stmt ) {
            echo 'Query Prep Failed: ' . $mysqli->error;
            exit;
        }
    
        $stmt->bind_param( 'i', $article_id );
        $stmt->execute();
        $stmt->bind_result( $author );
        $stmt->fetch();
        $stmt->close();
    
        // if current user is not author and not admin, redirect to article page
        
        if ( $author != $_SESSION[ 'current_user' ] && ($admin_status == 0) ) {
            echo "<p>You are not the author of this article.</p>";
            // after 3 seconds, redirect to article.php
            header("Refresh: 3; URL=article.php?id=$article_id");
            exit;
        }

        // update article
        $stmt = $mysqli->prepare( 'UPDATE articles SET title=?, link=?, imgURL=?, blurb=? WHERE id=?' );
        $stmt -> bind_param( 'ssssi', $newTitle, $newLink, $newImgURL, $newBlurb, $article_id );
        $stmt -> execute();
        $stmt -> close();

        echo "<p>Article updated.</p>";
        // after 1 second, redirect to article.php
        header("Refresh: 1; URL=article.php?id=$article_id");
        exit;
    } else {
        echo "<p>You must be logged in to edit an article.</p>";
        // after 3 seconds, redirect to article.php
        header("Refresh: 3; URL=article.php?id=$article_id");
        exit;
    }
}


// listen for post request for 'edit-reg-post'
if (isset($_POST['edit-reg-post'])) {
    $token = $_POST['token'];
    $newTitle = $_POST['title'];
    $newLink = $_POST['link'];
    $newImgURL = $_POST['img-url'];
    $newBlurb = $_POST['blurb'];
    $newContent = $_POST['article'];

    

    // check that token is valid
    if (!hash_equals($_SESSION['token'], $token)) {
        die("Request forgery detected");
        header("Location: editarticle.php?id='$article_id'");
        exit;
    }



    if ( isset( $_SESSION[ 'current_user' ] ) ) {
        

        $stmt = $mysqli->prepare( 'SELECT authorID FROM articles WHERE id=?');
        if ( !$stmt ) {
            echo 'Query Prep Failed: ' . $mysqli->error;
            exit;
        }
        
        $stmt->bind_param( 'i', $article_id);
        $stmt->execute();
        $stmt->bind_result( $author );
        $stmt->fetch();
        $stmt->close();


    
    
        if (( $author != $_SESSION[ 'current_user' ]) && ($admin_status == 0) ) {

            echo "<p>You are not the author of this article.</p>";
            // after 3 seconds, redirect to article.php
            header("Refresh: 3; URL=article.php?id=$article_id");
            exit;
        }

        // update article
        // if link is empty, set it to NULL
        if ( $newLink == '' ) {
            $stmt = $mysqli->prepare( 'UPDATE articles SET title=?, link=NULL, imgURL=?, blurb=?, articleContent=? WHERE id=?' );
            $stmt -> bind_param( 'ssssi', $newTitle, $newImgURL, $newBlurb, $newContent, $article_id );
        } else {
            $stmt = $mysqli->prepare( 'UPDATE articles SET title=?, link=?, imgURL=?, blurb=?, articleContent=? WHERE id=?' );
            $stmt -> bind_param( 'sssssi', $newTitle, $newLink, $newImgURL, $newBlurb, $newContent, $article_id );
        }
        
        // error handling
        if ( !$stmt ) {
            echo 'Query Prep Failed: ' . $mysqli->error;
            exit;
        }

        $stmt -> execute();
        $stmt -> close();

        echo "<p>Article updated.</p>";
        // after 1 second, redirect to article.php
        header("Refresh: 1; URL=article.php?id=$article_id");
        exit;
    } else {
        echo "<p>You must be logged in to edit an article.</p>";
        // after 3 seconds, redirect to article.php
        header("Refresh: 1; URL=article.php?id=$article_id");
        exit;
    }
}



// if article author is current user, allow editing

if ( isset( $_SESSION[ 'current_user' ] ) ) {
    $stmt = $mysqli->prepare( 'SELECT authorID FROM articles WHERE id=?' );
    if ( !$stmt ) {
        echo 'Query Prep Failed: ' . $mysqli->error;
        exit;
    }

    $stmt->bind_param( 'i', $article_id );
    $stmt->execute();
    $stmt->bind_result( $author );
    $stmt->fetch();
    $stmt->close();



    if (( $author != $_SESSION[ 'current_user' ]) && ($admin_status == 0)) {
        
        echo "<p>You do not have permission to edit this article.</p>";
        // redirect to article page
        header( "refresh:3;url=article.php?id=$article_id" );
        exit;
    }

    // chec to see if articles exist
    $stmt = $mysqli->prepare( 'SELECT id FROM articles WHERE id=?' );
    if ( !$stmt ) {
        echo 'Query Prep Failed: ' . $mysqli->error;
        exit;
    }

    $stmt->bind_param( 'i', $article_id );
    $stmt->execute();
    $stmt->bind_result( $articleExists );
    $stmt->fetch();
    $stmt->close();

    // if article does not exist, redirect to index
    if ( $articleExists == null ) {
        echo "<p>Article does not exist.</p>";
        
        header( 'Location: index.php' );

        exit;
    }

    // get post_type database
    $stmt = $mysqli->prepare( 'SELECT postType, imgURL, articleContent, blurb, link, title FROM articles WHERE id=?' );
    if ( !$stmt ) {
        echo 'Query Prep Failed: ' . $mysqli->error;
        exit;
    }

    $stmt->bind_param( 'i', $article_id );
    $stmt->execute();
    $stmt->bind_result( $type, $imgURL, $articleContent, $blurb, $link, $title );
    $stmt->fetch();
    $stmt->close();

    if ($type == 'linked_post') {
        $thisToken = $_SESSION['token'];
        echo "
        <form action='editarticle.php?id=".htmlspecialchars($article_id)."' method='post'>
        <input type='hidden' name='post_type' value='linked_post'>
        <input type='hidden' name='article_id' value='".htmlspecialchars($article_id)."'>
        <input type='hidden'name='token' value='".htmlspecialchars($thisToken)."'>
        
        <input id='title-input' class='input-field' type='text' name='title' placeholder='Title'><br>
        <input id='link-input' class='input-field' type='url' name='link' placeholder='Link' required><br>
        <input id='img-url-input' class='input-field' type='url' name='img-url' placeholder='Image URL' required ><br>
        <textarea id='blurb-input' class='input-field' rows='5' cols='100'name='blurb' placeholder='blurb' ></textarea><br>
        <button type='submit' class='button' name='edit-link-post'>Update Post</button>
        

        <script>
        document.getElementById('title-input').value = '" . addslashes($title) . "';
        document.getElementById('link-input').value = '" . addslashes($link) . "';
        document.getElementById('img-url-input').value = '" . addslashes($imgURL) . "';
        document.getElementById('blurb-input').value = '" .  addslashes($blurb) ."';
        </script>
        
        ";


    }
    if ($type =='regular_post') {

        $thisToken = $_SESSION['token'];
        echo"
        <form action='editarticle.php?id=".htmlspecialchars($article_id)."' method='post'>
        <input type='hidden' name='post_type' value='linked_post'>
        <input type='hidden' name='article_id' value='".htmlspecialchars($article_id)."'>
        <input type='hidden'name='token' value='".htmlspecialchars($thisToken)."'>
        <input id='title-input' class='input-field' type='text' name='title' placeholder='Title'><br>";

        // if link is not empty, display link
        if ( $link != null ) {
            echo "<input id='link-input' class='input-field' type='url' name='link' placeholder='Link' required>";
        } else {
            echo "<input id='link-input' class='input-field' type='url' name='link' placeholder='Link' hidden >";
        }

        echo "

        <input id='img-url-input' class='input-field' type='url' name='img-url' placeholder='Image URL' required ><br>
        <textarea id='blurb-input' class='input-field' rows='5' cols='100'name='blurb' placeholder='blurb' ></textarea><br>
        <textarea id='article-content-input' class='input-field' rows='15' cols='5'name='article' placeholder='Article Content' ></textarea><br>
        <button type='submit' class='button' name='edit-reg-post'>Update Post</button>
        
        <script>
        document.getElementById('title-input').value = '" . addslashes($title) . "';
        document.getElementById('link-input').value = '" . addslashes($link) . "';

        document.getElementById('img-url-input').value = '" . addslashes($imgURL) . "';
        document.getElementById('blurb-input').value = '" .  addslashes($blurb) ."';

        // this line is important because some special characters important for Markdown confuse JS (e.g. >)
        // this is a workaround that puts the content into a multi-line string (denoted by ``) and then puts that into the textarea
        let articleContent = `" . addslashes($articleContent) . "`;

        document.getElementById('article-content-input').value = articleContent;
        
        </script>
        
        ";
    }

    



}
?>
    </div>
</div>
</div>