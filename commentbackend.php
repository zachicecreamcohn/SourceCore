<?php
session_start();
?>

<?php
// if session variable current_user is not set, display error message
if (!isset($_SESSION['current_user'])) {
    // redirect to index.php
    header("Location: index.php");
    exit;
}
require 'database.php';


// listen for post request to post a comment
if (isset($_POST['postcomment'])) {
    $origin_article = $_POST['origin_article'];
    $comment_text = $_POST['comment'];
    $user_id = $_SESSION['current_user'];
    $token = $_POST['token'];

    // check that token is valid
    if (!hash_equals($_SESSION['token'], $token)) {
        echo "Invalid token. Please try again.";
        // redirect to origin article
        header("Location: article.php?id=$origin_article&scroll=true");
        exit;
    }

    // check that comment is not empty
    if (empty($comment_text)) {
        echo "Comment cannot be empty.";
        // redirect to origin article
        header("Location: article.php?id=$origin_article&scroll=true");
        exit;
    }

    // insert comment into comments table
    $stmt = $mysqli->prepare("INSERT INTO comments (articleID, posterID, commentContent) VALUES (?, ?, ?)");
    if (!$stmt) {
        echo "Query Prep Failed: " . $mysqli->error;
        // redirect to origin article
        header("Location: article.php?id=$origin_article");
        exit;
    }
    $stmt->bind_param('iis', $origin_article, $user_id, $comment_text);
    $stmt->execute();
    $stmt->close();

    // redirect to origin article
    header("Location: article.php?id=$origin_article&scroll=true");
    exit;



}



// listen for post request
if (isset($_POST['deletecomment'])) {
    $commentID = $_POST['comment_id'];
    $token = $_POST['token'];
    $origin_article = $_POST['origin_article_id'];
    // check that token is valid
    if (!hash_equals($_SESSION['token'], $token)) {
        echo "Invalid token. Please try again.";
        // redirect to index after 3 seconds
        header("location: article.php?id=$origin_article");
        exit;
    }
    
    
    // check that comment_id is valid
    $stmt = $mysqli->prepare("SELECT commentID FROM comments WHERE commentID=?");
    if (!$stmt) {
        echo "Query Prep Failed: " . $mysqli->error;
        exit;
    }
    $stmt->bind_param('i', $commentID);
    $stmt->execute();
    $stmt->bind_result($commentID);
    $stmt->fetch();
    $stmt->close();

    if ($commentID == null) {
        echo "Invalid comment ID. Please try again.";
        // redirect to index after 3 seconds
        header("location: article.php?id=$origin_article&scroll=true");
        exit;
    }

    // delete all emotes associated with comment
    $stmt = $mysqli->prepare("DELETE FROM emotes WHERE likedCommentID=?");
    if (!$stmt) {
        echo "Query Prep Failed: " . $mysqli->error;
        exit;
    }
    $stmt->bind_param('i', $commentID);
    $stmt->execute();
    $stmt->close();
    

    // delete comment
    $stmt = $mysqli->prepare("DELETE FROM comments WHERE commentID=?");
    if (!$stmt) {
        echo "Query Prep Failed: " . $mysqli->error;
        exit;
    }
    $stmt->bind_param('i', $commentID);
    $stmt->execute();
    $stmt->close();

    // redirect to origin_page after
    header("location: article.php?id=$origin_article&scroll=true");
    exit;

}

// listen for post request to edit comment

if (isset($_POST['editcomment'])) {
    $commentID = $_POST['comment_id'];
    $token = $_POST['token'];
    $origin_article = $_POST['origin_article_id'];
    $comment_text = $_POST['comment_content'];
    // check that token is valid
    if (!hash_equals($_SESSION['token'], $token)) {
        echo "Invalid token. Please try again.";
        // redirect to index after 3 seconds
        header("location: article.php?id=$origin_article&scroll=true");
        exit;
    }

    
    // check that comment_id is valid
    $stmt = $mysqli->prepare("SELECT commentID FROM comments WHERE commentID=?");
    if (!$stmt) {
        echo "Query Prep Failed: " . $mysqli->error;
        exit;
    }
    $stmt->bind_param('i', $commentID);
    $stmt->execute();
    $stmt->bind_result($commentID);
    $stmt->fetch();
    $stmt->close();

    if ($commentID == null) {
        echo "Invalid comment ID. Please try again.";
        // redirect to index after 3 seconds
        header("location: article.php?id=$origin_article&scroll=true");
        exit;
    }

    // edit comment
    $stmt = $mysqli->prepare("UPDATE comments SET commentContent=?, isEdited=1 WHERE commentID=?");

    if (!$stmt) {
        echo "Query Prep Failed: " . $mysqli->error;
        exit;
    }
    $stmt->bind_param('si', $comment_text, $commentID);
    $stmt->execute();
    $stmt->close();

    // redirect to origin_page after
    header("location: article.php?id=$origin_article&scroll=true");
    exit;
}



// if user likes comment:
if (isset($_POST['likecomment'])) {
    echo "Post received to like comment";

    $commentID = $_POST['comment_id'];
    $token = $_POST['token'];
    $origin_article = $_POST['origin_article_id'];
    $user_id = $_SESSION['current_user'];
    // check that token is valid
    if (!hash_equals($_SESSION['token'], $token)) {
        echo "Invalid token. Please try again.";
        // redirect to index after 3 seconds
        header("location: article.php?id=$origin_article&scroll=true");
        exit;
    }


    // check if user has already liked comment
    $stmt = $mysqli->prepare("SELECT likerID FROM emotes WHERE likedCommentID=? AND likerID=? AND isLike=1 AND isDislike=0");
    if (!$stmt) {
        echo "Query Prep Failed: " . $mysqli->error;
        exit;
    }

    $stmt->bind_param('ii', $commentID, $user_id);
    $stmt->execute();
    $stmt->bind_result($likerID);
    $stmt->fetch();
    $stmt->close();



    // if they have liked comment
    if ($likerID != null) {
        echo "You have already liked this comment.";

        // delete like from emotes
        $stmt = $mysqli->prepare("DELETE FROM emotes WHERE likedCommentID=? AND likerID=? AND isLike=1 AND isDislike=0");
        if (!$stmt) {
            echo "Query Prep Failed: " . $mysqli->error;
            exit;
        }

        $stmt->bind_param('ii', $commentID, $user_id);
        $stmt->execute();
        $stmt->close();
        

        // subract 1 from like count
        $stmt2 = $mysqli->prepare("UPDATE comments SET likes=likes-1 WHERE commentID=?");
        if (!$stmt2) {
            echo "Query Prep Failed: " . $mysqli->error;
            exit;
        }

        $stmt2->bind_param('i', $commentID);
        $stmt2->execute();
        $stmt2->close();

        //send back to article page
        
        header("location: article.php?id=$origin_article&scroll=true");
        exit;

        // make sure like button is not selected (THIS IS A THING FOR article.php)
    } else {
    // if they have not liked comment
        echo "You have not liked this comment.";

        // check to see if user has already disliked comment
        $stmt = $mysqli->prepare("SELECT likerID FROM emotes WHERE likedCommentID=? AND likerID=? AND isLike=0 AND isDislike=1");
        if (!$stmt) {
            echo "Query Prep Failed: " . $mysqli->error;
            exit;
        }

        $stmt->bind_param('ii', $commentID, $user_id);
        $stmt->execute();
        $stmt->bind_result($likerID);
        $stmt->fetch();
        $stmt->close();

        // if they have disliked comment

        if ($likerID != null) {
            echo "You have already disliked this comment.";
            // delete dislike from emotes
            $stmt = $mysqli->prepare("DELETE FROM emotes WHERE likedCommentID=? AND likerID=? AND isLike=0 AND isDislike=1");
            if (!$stmt) {
                echo "Query Prep Failed: " . $mysqli->error;
                exit;
            }
            $stmt -> bind_param('ii', $commentID, $user_id);
            $stmt -> execute();
            $stmt -> close();

            // subtract 1 from dislike count
            $stmt2 = $mysqli->prepare("UPDATE comments SET dislikes=dislikes-1 WHERE commentID=?");
            if (!$stmt2) {
                echo "Query Prep Failed: " . $mysqli->error;
                exit;
            }
            $stmt2 -> bind_param('i', $commentID);
            $stmt2 -> execute();
            $stmt2 -> close();

        }
        // add like to emotes
        $stmt = $mysqli->prepare("INSERT INTO emotes (likedCommentID, likerID, isLike, isDislike, articleID) VALUES (?, ?, 1, 0, ?)");
        if (!$stmt) {
            echo "Query Prep Failed: " . $mysqli->error;
            exit;
        }
        $stmt -> bind_param('iii', $commentID, $user_id, $origin_article);
        // catch error
        if (!$stmt -> execute()) {
            echo "Execute failed: (" . $stmt -> errno . ") " . $stmt -> error;
        }
        // $stmt -> execute();
        $stmt -> close();

        // add 1 to like count
        $stmt2 = $mysqli->prepare("UPDATE comments SET likes=likes+1 WHERE commentID=?");
        if (!$stmt2) {
            echo "Query Prep Failed: " . $mysqli->error;
            exit;
        }
        $stmt2 -> bind_param('i', $commentID);
        $stmt2 -> execute();
        $stmt2 -> close();

        echo "added like to emotes";
        
        //send back to article page
        header("location: article.php?id=$origin_article&scroll=true");
        exit;
        // make sure like button is selected

    }
}



// if user dislikes comment:
if (isset($_POST['dislikecomment'])) {
    echo "Post received to dislike comment";

    $commentID = $_POST['comment_id'];
    $token = $_POST['token'];
    $origin_article = $_POST['origin_article_id'];
    $user_id = $_SESSION['current_user'];

    // check that token is valid
    if (!hash_equals($_SESSION['token'], $token)) {
        echo "Invalid token. Please try again.";
        // redirect to index after 3 seconds
        header("location: article.php?id=$origin_article&scroll=true");
        exit;
    }


    // check if user has already disliked comment
    $stmt = $mysqli->prepare("SELECT likerID FROM emotes WHERE likedCommentID=? AND likerID=? AND isLike=0 AND isDislike=1");
    if (!$stmt) {
        echo "Query Prep Failed: " . $mysqli->error;
        exit;
    }
    $stmt->bind_param('ii', $commentID, $user_id);
    $stmt->execute();
    $stmt->bind_result($likerID);
    $stmt->fetch();
    $stmt->close();

    // if they have disliked comment
    if ($likerID != null) {
        echo "You have already disliked this comment.";

        // delete dislike from emotes
        $stmt = $mysqli->prepare("DELETE FROM emotes WHERE likedCommentID=? AND likerID=? AND isLike=0 AND isDislike=1");
        if (!$stmt) {
            echo "Query Prep Failed: " . $mysqli->error;
            exit;
        }
        $stmt -> bind_param('ii', $commentID, $user_id);
        $stmt -> execute();
        $stmt -> close();

        // subract 1 from dislike count
        $stmt2 = $mysqli->prepare("UPDATE comments SET dislikes=dislikes-1 WHERE commentID=?");
        if (!$stmt2) {
            echo "Query Prep Failed: " . $mysqli->error;
            exit;
        }
        $stmt2 -> bind_param('i', $commentID);
        // $stmt2 -> execute();
        // catch error
        if (!$stmt2 -> execute()) {
            echo "Execute failed: (" . $stmt2 -> errno . ") " . $stmt2 -> error;
        }
        $stmt2 -> close();

        // send back to article page
        header("location: article.php?id=$origin_article&scroll=true");
        exit;
        // make sure dislike button is not selected (THIS IS A THING FOR article.php)

    // if they have not disliked comment
    } else {
        echo "You have not disliked this comment.";
        // check to see if user has already liked comment
        $stmt = $mysqli->prepare("SELECT likerID FROM emotes WHERE likedCommentID=? AND likerID=? AND isLike=1 AND isDislike=0");
        if (!$stmt) {
            echo "Query Prep Failed: " . $mysqli->error;
            exit;
        }
        $stmt->bind_param('ii', $commentID, $user_id);
        $stmt->execute();
        $stmt->bind_result($likerID);
        $stmt->fetch();
        $stmt->close();


        // if they have liked comment
        if ($likerID != null) {
            echo "You have already liked this comment.";

            // delete like from emotes
            $stmt = $mysqli->prepare("DELETE FROM emotes WHERE likedCommentID=? AND likerID=? AND isLike=1 AND isDislike=0");
            if (!$stmt) {
                echo "Query Prep Failed: " . $mysqli->error;
                exit;
            }

            $stmt -> bind_param('ii', $commentID, $user_id);
            $stmt -> execute();
            $stmt -> close();

            // subract 1 from like count
            $stmt2 = $mysqli->prepare("UPDATE comments SET likes=likes-1 WHERE commentID=?");
            if (!$stmt2) {
                echo "Query Prep Failed: " . $mysqli->error;
                exit;
            }
            $stmt2 -> bind_param('i', $commentID);
            $stmt2 -> execute();
            $stmt2 -> close();
        }

        

        // add dislike to emotes
        $stmt = $mysqli->prepare("INSERT INTO emotes (likedCommentID, likerID, isLike, isDislike, articleID) VALUES (?, ?, 0, 1, ?)");
        if (!$stmt) {
            echo "Query Prep Failed: " . $mysqli->error;
            exit;
        }
        $stmt -> bind_param('iii', $commentID, $user_id, $origin_article);
        $stmt -> execute();
        $stmt -> close();

        // add 1 to dislike count
        $stmt2 = $mysqli->prepare("UPDATE comments SET dislikes=dislikes+1 WHERE commentID=?");
        if (!$stmt2) {
            echo "Query Prep Failed: " . $mysqli->error;
            exit;
        }
        $stmt2 -> bind_param('i', $commentID);
        $stmt2 -> execute();
        $stmt2 -> close();

        echo "added dislike";

        // make sure dislike button is selected (THIS IS A THING FOR article.php)
        // send back to article page
        
        header("location: article.php?id=$origin_article&scroll=true");
        exit;
    }
}









    // redirect to original article after 2 seconds
    header("refresh: 3; url=article.php?id=$original_article");
    exit;

?>