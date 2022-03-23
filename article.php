<?php
session_start();
?>
<!DOCTYPE html>
<html lang = 'en'>

<head>
<meta charset = 'UTF-8'>
<title>Home</title>
<link rel = 'stylesheet' href = 'css/style.css'>
<link rel = 'stylesheet' href = 'https://cdn.jsdelivr.net/npm/katex@0.10.0/dist/katex.min.css' integrity = 'sha384-9eLZqc9ds8eNjO3TmqPeYcDj8n+Qfa4nuSiGYa6DjLNcv9BtN69ZIulL9+8CqC9Y' crossorigin = 'anonymous'>
<link rel = 'stylesheet' href = 'css/markdown_style.css'>
<script src = 'https://kit.fontawesome.com/7b8bf01427.js' crossorigin = 'anonymous'></script>
<meta name="viewport" content="width=device-width, initial-scale=1.0">  



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

<?php


require 'parsedown-1.7.4/Parsedown.php';
require 'database.php';
$Parsedown = new Parsedown();

if ( $_GET[ 'id' ] ) {

    // check if article exists
    $stmt = $mysqli->prepare( 'SELECT COUNT(*) FROM articles WHERE id=?' );
    if ( !$stmt ) {
        echo 'Query Prep Failed: ' . $mysqli->error;
        exit;
    }

    $stmt->bind_param( 'i', $_GET[ 'id' ] );
    $stmt->execute();
    $stmt->bind_result( $article_exists );
    $stmt->fetch();
    $stmt->close();

    if ( $article_exists == 0 ) {
        echo '<p>Article does not exist</p>';
        // exit;
    }

    // get article type
    $stmt = $mysqli->prepare( 'SELECT postType FROM articles WHERE id=?' );
    if ( !$stmt ) {
        echo 'Query Prep Failed: ' . $mysqli->error;
        exit;
    }

    $stmt->bind_param( 'i', $_GET[ 'id' ] );
    $stmt->execute();
    $stmt->bind_result( $postType );
    $stmt->fetch();
    $stmt->close();

    // if post is a 'linked_post', get id, imageULR, title, blurb, author, and publish date

    if ( $postType == 'linked_post' ) {
        $stmt = $mysqli->prepare( 'SELECT articles.id, imgURL, link, title, blurb, users.display_name, postDateTime, authorID FROM articles JOIN users on users.id=authorID WHERE articles.id=?' );
        if ( !$stmt ) {
            echo 'Query Prep Failed: ' . $mysqli->error;
            exit;
        }

        $stmt->bind_param( 'i', $_GET[ 'id' ] );
        $stmt->execute();
        $stmt->bind_result( $id, $imgURL, $link, $title, $blurb, $author, $postDateTime, $authorID);
        $stmt->fetch();
        $stmt->close();

        // if author is current user, display edit button
        if ( ( $_SESSION[ 'current_user' ] == $authorID ) || ($admin_status == 1) ) {
            
            echo "<a href='editarticle.php?id=".htmlspecialchars($id)."' class='red' >Edit</a><a>|</a>
            <a href='deletearticle.php?id=".htmlspecialchars($id)."' class='red' >Delete</a>";
        } //TODO: make this work


        // display some stuff
        echo $Parsedown->text( '# ' . $title );
        echo '<p><i>Posted by ' . htmlspecialchars($author) . ' on ' . htmlspecialchars($postDateTime) . '</i></p><br>';

        echo "<a href='".htmlspecialchars($link)."' target='_blank'><div class='big-article-img' style='background-image: url(".htmlspecialchars($imgURL).")'></div></a>";
        echo '<br>';

        echo $Parsedown->text( '---' );
        echo "<p>".htmlspecialchars($blurb)."</p>";
        echo "<p>Visit the original page: <a id='reference-link' target='_blank' href='".htmlspecialchars($link)."'>".htmlspecialchars($link)."</a></p>";
    } else {

        // display the regular article in markdown

        // get articleContent from database where id = $_GET[ 'id' ]
        $stmt = $mysqli->prepare( 'SELECT articles.id, title, imgURL, users.display_name, postDateTime, articleContent, authorID from articles JOIN users ON users.id=authorID where articles.id = ?' );
        if ( !$stmt ) {
            echo 'Query Prep Failed: ' . $mysqli->error;
            exit;
        }


        $stmt->bind_param( 'i', $_GET[ 'id' ] );
        $stmt->execute();
        $stmt->bind_result( $id, $title, $imgURL, $author, $postDateTime, $articleContent, $authorID);
        $stmt->fetch();
        $stmt->close();

         // if author is current user, display edit button
         if ( ( $_SESSION[ 'current_user' ] == $authorID ) || ($admin_status == 1) ) {
            echo "<a href='editarticle.php?id=".htmlspecialchars($id)."' class='red' >Edit </a><a>|</a>
            <a href='deletearticle.php?id=".htmlspecialchars($id)."' class='red' >Delete</a>";
        }




        echo $Parsedown->text( '# ' . $title );
        echo '<p><i>Posted by ' . htmlspecialchars($author) . ' on ' . htmlspecialchars($postDateTime) . '</i></p><br>';

        // display image
        echo "<div class='big-article-img' style='background-image: url(".htmlspecialchars($imgURL).")'></div>";
        echo '<br>';
        
        echo $Parsedown->text( '---' );
        echo $Parsedown->text( $articleContent );

        

        // prints text as HTML ( with Markdown syntax )
        // NOTE: this doesn't need htmlspecialchars() because it's already been escaped by parsedown
    }

    // COMMENTS
    echo "<div class='comments'>";
    echo $Parsedown->text( '---' );
    echo '<h2>Comments</h2>';
    echo '<center>';
    echo "<div class='comment_container'>";

    // if user is logged in, display comment form
    if (isset($_SESSION['current_user'])) {
        
        // post comment
        echo "
    <div class='comment-form'>
        <form action='commentbackend.php' method='post'>
            <input type='hidden' name='origin_article' value='".htmlspecialchars($_GET['id'])."'>
            <input type='hidden' name='user_id' value='".htmlspecialchars($_SESSION['current_user'])."'>
            <input type='hidden' name='token' value='".htmlspecialchars($_SESSION['token'])."'>

            <div class='new-comment-group'>
                <textarea class='input-field comment-box' name='comment' rows='2' cols='100' placeholder='Comment'></textarea>
                <input type='submit' class='button' name='postcomment' value='Post Comment'>
            </div>

        </form>
    </div>";
    } else {
        echo "<p>Please <a href='login.php' class='red'>login</a> to comment</p>";
    }

echo "<div class='thin-line'></div>";



    //get list of comments liked  by current user
    $stmt = $mysqli->prepare( 'select likedCommentID, isLike, isDislike from emotes where likerID = ? ');
    if ( !$stmt ) {
        echo 'Query Prep Failed: ' . $mysqli->error;
        exit;
    }
    $stmt->bind_param( 'i', $_SESSION[ 'current_user' ]);
    $stmt->execute();
    $stmt->bind_result( $likedCommentID, $isLike, $isDislike );
    $stmt->store_result();
    // for each comment liked by current user, store in array
    // if comment is liked, store 1 in array
    // if comment is disliked, store -1 in array
    // each value is stored in array as [commentID] => [value]
    
    $likedComments = array();
    while ( $stmt->fetch() ) {
        $likedComments[ $likedCommentID ] = $isLike - $isDislike;
    }

    $stmt->close();






    // display comments
    // get comments from database where article_id = $_GET[ 'id' ]
    // for each comment, count all likes and dislikes




    $stmt = $mysqli->prepare('SELECT users.display_name, users.id, comments.likes, comments.dislikes, comments.datetime_posted, comments.commentID, comments.commentContent FROM comments JOIN users ON users.id = comments.posterID AND comments.articleID = ?');
    if ( !$stmt ) {
        echo 'Query Prep Failed: ' . $mysqli->error;
        exit;
    }
    $stmt->bind_param( 'i', $_GET[ 'id' ] );
    $stmt->execute();
    $stmt->bind_result( $display_name, $user_id, $likes, $dislikes, $datetime_posted, $commentID, $commentContent);

    // check if there are any comments
    $isFirst = true;
    // for each comment, display the comment
    while ( $stmt->fetch() ) {
        if ( $isFirst ) {
            // check if $commentID exists
            if ($commentID) {
                $thereAreComments = true;
            } else {
                $thereAreComments = false;
            }
            $isFirst = false;
        }


        echo "<div class='comment' id='".htmlspecialchars($commentID)."'>";
        echo "<p><b>".htmlspecialchars($display_name)."</b> on ".htmlspecialchars($datetime_posted)."</p>";
        echo "<p id='content_".htmlspecialchars($commentID)."'>".htmlspecialchars($commentContent)."</p>";
        
        // cast $likes, $dislikes to int
        $likes = (int)$likes;
        $dislikes = (int)$dislikes;
        $like_count = $likes - $dislikes;
        
        
        // if user is logged in, display like/dislike buttons
        if (isset($_SESSION['current_user'])) {
        // like

        echo "<div class='likedislike-container'>";
        echo "<form action='commentbackend.php' method='post'>
        <input type='hidden' name='origin_article_id' value='".htmlspecialchars($_GET['id'])."'>
        <input type='hidden' name='comment_id' value='".htmlspecialchars($commentID)."'>
        <input type='hidden' name='token' value='".htmlspecialchars($_SESSION['token'])."'>";
        
        if ( array_key_exists( $commentID, $likedComments ) ) {
            if ( $likedComments[ $commentID ] == 1 ) {
                echo "<button type='submit' name='likecomment' value='Like' class='highlighted-like'>
                    <i class='fa-solid fa-thumbs-up'></i>
                </button>";
            }  else {
                echo "<button type='submit' name='likecomment' value='Like'>
                <i class='fa-solid fa-thumbs-up'></i>
            </button>";
            }
        } else {
            echo "<button type='submit' name='likecomment' value='Like'>
                <i class='fa-solid fa-thumbs-up'></i>
            </button>";
        }
        
        echo "</form>";

        

        // display number of likes
        echo "<p><i>".htmlspecialchars($like_count)."</i></p>";

        // dislike
        echo "<form action='commentbackend.php' method='post'>
        <input type='hidden' name='origin_article_id' value='".htmlspecialchars($_GET['id'])."'>
        <input type='hidden' name='comment_id' value='".htmlspecialchars($commentID)."'>
        <input type='hidden' name='token' value='".htmlspecialchars($_SESSION['token'])."'>";
  
        
        
 
        if (array_key_exists( $commentID, $likedComments ) ) {
            if ( $likedComments[ $commentID ] == -1 ) {
                echo "<button type='submit' name='dislikecomment' value='Dislike' class='highlighted-dislike'>
                    <i class='fa-solid fa-thumbs-down'></i>
                </button>";
            } else {
                echo "<button type='submit' name='dislikecomment' value='Dislike'>
                <i class='fa-solid fa-thumbs-down'></i>
            </button>";
            }
        } else {
            echo "<button type='submit' name='dislikecomment' value='Dislike'>
                <i class='fa-solid fa-thumbs-down'></i>
            </button>";
        }
        

        

        echo "</form></div>";
        

        // if user is logged in, display delete button and edit button
        if ( $_SESSION[ 'current_user' ] == $user_id ) {
            //delete button

            
            echo "<form action='commentbackend.php' method='post'>
        <input type='hidden' name='origin_article_id' value='".htmlspecialchars($_GET['id'])."'>

        <input type='hidden' name='comment_id' value='".htmlspecialchars($commentID)."'>
        <input type='hidden' name='token' value='".htmlspecialchars($_SESSION['token'])."'>
        
        
            <input type='submit' name='deletecomment' class='grey comment-link'value='Delete&nbsp;'>
        </form>";

            // edit button
            echo "<button class='editComment comment-link grey' id='edit_".htmlspecialchars($commentID)."'>Edit</button>";
            // echo "</div>";
            echo "<form action='commentbackend.php' method='post'>
        <input type='hidden' name='origin_article_id' value='".htmlspecialchars($_GET['id'])."'>
        <input type='hidden' name='comment_id' value='".htmlspecialchars($commentID)."'>
        <input type='hidden' name='token' value='".htmlspecialchars($_SESSION['token'])."'>
        <div class='new-comment-group'>

            <textarea class='no-show input-field comment-box' id='editedComment_".htmlspecialchars($commentID)."' name='comment_content' rows='2' cols='100'></textarea>
            <input class='no-show type='submit' id='submit_".htmlspecialchars($commentID)."' name='editcomment' value='Edit'>
            </form>
            <button class='no-show updateComment button' id='update_".htmlspecialchars($commentID)."' >Update</button>
            </div>";
        
        

        echo "<script>
        let editBtn_$commentID = document.getElementById('edit_$commentID');
        editBtn_$commentID.onclick = function () {
            // hide edit button
            this.style.display = 'none';
            
            let textarea_$commentID= document.getElementById('editedComment_$commentID');
            // paste the content of the comment into the textarea
            textarea_$commentID.value = '".addslashes($commentContent) ."';
            // show textarea
            textarea_$commentID.style.display = 'block';
            // show update button
            let updateBtn_$commentID = document.getElementById('update_$commentID');
            updateBtn_$commentID.style.display = 'block';

            // when update button is clicked, submit the form
            let hiddenSubmitBtn_$commentID = document.getElementById('submit_$commentID');
            // when update button is clicked, click the hidden submit button
            updateBtn_$commentID.onclick = function () {
                hiddenSubmitBtn_$commentID.click();
            }

        }
            
            </script>";
    }
        } else {
    
        echo "<div class='likedislike-container'><i class=' greyed-out fa-solid fa-thumbs-up'></i><p><i>".htmlspecialchars($likes)."</i></p><i class=' greyed-out fa-solid fa-thumbs-down '></i></div>";
        }
        echo '</div>';
        echo "<div class='thin-line'></div>";
        
    }

    if ( !$thereAreComments ) {
        echo "
        <p id='nocomments-message'><i>No comments yet! Be the first to chime in!</i></p>";
    }
    echo "</div>";
    echo '</center>';
    echo "</div>";
    $stmt->close();
    

    
} else {
    echo '<p>No article selected</p>';
}


if (isset($_GET['scroll'])) {
    echo "
    <script>
        document.getElementsByClassName('comment-form')[0].scrollIntoView({
            behavior: 'auto'
        });
        </script>";
}
    ?>

</div>
</body>