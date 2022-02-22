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


// listen for post request to like comment
if (isset($_POST['likecomment'])) {
    $original_article = $_POST['origin_article_id'];
    $commentID = $_POST['comment_id'];
    $token = $_POST['token'];


    // check that token is valid
    if (!hash_equals($_SESSION['token'], $token)) {
        echo "Invalid token. Please try again.";

        header("location: article.php?id=$original_article&scroll=true");
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
        header("location: article.php?id=$original_article&scroll=true");
        exit;
    }

    // check if user has already liked comment
    $stmt = $mysqli->prepare("SELECT likerID FROM emotes WHERE likerID=? AND likedCommentID=? AND isLike=1");
    if (!$stmt) {
        echo "Query Prep Failed: " . $mysqli->error;
        exit;
    }
    $stmt->bind_param('ii', $_SESSION['current_user'], $commentID);
    $stmt->execute();
    $stmt->bind_result($likerID);
    $stmt->fetch();
    $stmt->close();

    // if user has already liked comment, delete like
    if ($likerID != null) {
        $stmt = $mysqli->prepare("DELETE FROM emotes WHERE likerID=? AND likedCommentID=? AND isLike=1");
        if (!$stmt) {
            echo "Query Prep Failed: " . $mysqli->error;
            exit;
        }
        $stmt->bind_param('ii', $_SESSION['current_user'], $commentID);
        $stmt->execute();
        $stmt->close();
    
    
        // subtract 1 from like count
        $stmt = $mysqli->prepare("UPDATE comments SET likes=likes-1 WHERE commentID=?");
        if (!$stmt) {
            echo "Query Prep Failed: " . $mysqli->error;
            exit;
        }
        $stmt->bind_param('i', $commentID);
        $stmt->execute();
        $stmt->close();

        // redirect to origin_page
        header("location: article.php?id=$original_article&scroll=true");
        exit;
    
    } else {
        // if user has NOT liked the comment, add like
        $stmt = $mysqli->prepare("INSERT INTO emotes (likerID, likedCommentID, isLike) VALUES (?, ?, 1)");
        if (!$stmt) {
            echo "Query Prep Failed: " . $mysqli->error;
            exit;
        }
        $stmt->bind_param('ii', $_SESSION['current_user'], $commentID);
        $stmt->execute();
        $stmt->close();

        // add 1 to like count
        $stmt = $mysqli->prepare("UPDATE comments SET likes=likes+1 WHERE commentID=?");
        if (!$stmt) {
            echo "Query Prep Failed: " . $mysqli->error;
            exit;
        }
        $stmt->bind_param('i', $commentID);
        $stmt->execute();
        $stmt->close();

        // redirect to origin_page
        header("location: article.php?id=$original_article&scroll=true");
        exit;

    }


}


// listen for post request to like comment
if (isset($_POST['dislikecomment'])) {
    $original_article = $_POST['origin_article_id'];
    $commentID = $_POST['comment_id'];
    $token = $_POST['token'];

    // check that token is valid
    if (!hash_equals($_SESSION['token'], $token)) {
        echo "Invalid token. Please try again.";

        header("location: article.php?id=$original_article&scroll=true");
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
        header("location: article.php?id=$original_article&scroll=true");
        exit;
    }

    // check if user has already disliked comment
    $stmt = $mysqli->prepare("SELECT likerID FROM emotes WHERE likerID=? AND likedCommentID=? AND isDislike=1");
    if (!$stmt) {
        echo "Query Prep Failed: " . $mysqli->error;
        exit;
    }
    $stmt->bind_param('ii', $_SESSION['current_user'], $commentID);
    $stmt->execute();
    $stmt->bind_result($likerID);
    $stmt->fetch();
    $stmt->close();

    // if user has already disliked comment, delete dislike (add like)
    if ($likerID != null) {
        $stmt = $mysqli->prepare("DELETE FROM emotes WHERE likerID=? AND likedCommentID=? AND isDislike=1");
        if (!$stmt) {
            echo "Query Prep Failed: " . $mysqli->error;
            exit;
        }
        $stmt->bind_param('ii', $_SESSION['current_user'], $commentID);
        $stmt->execute();
        $stmt->close();
    
    
        // subtract 1 from like count
        $stmt = $mysqli->prepare("UPDATE comments SET likes=likes+1 WHERE commentID=?");
        if (!$stmt) {
            echo "Query Prep Failed: " . $mysqli->error;
            exit;
        }
        $stmt->bind_param('i', $commentID);
        $stmt->execute();
        $stmt->close();

        // redirect to origin_page
        header("location: article.php?id=$original_article&scroll=true");
        exit;
    
    } else {
        // if user has NOT disliked the comment, add dislike (subtract like)
        $stmt = $mysqli->prepare("INSERT INTO emotes (likerID, likedCommentID, isDislike) VALUES (?, ?, 1)");
        if (!$stmt) {
            echo "Query Prep Failed: " . $mysqli->error;
            exit;
        }
        $stmt->bind_param('ii', $_SESSION['current_user'], $commentID);
        $stmt->execute();
        $stmt->close();

        // add 1 to like count
        $stmt = $mysqli->prepare("UPDATE comments SET likes=likes-1 WHERE commentID=?");
        if (!$stmt) {
            echo "Query Prep Failed: " . $mysqli->error;
            exit;
        }
        $stmt->bind_param('i', $commentID);
        $stmt->execute();
        $stmt->close();

        // redirect to origin_page
        header("location: article.php?id=$original_article&scroll=true");
        exit;

    }


}





    // redirect to original article after 2 seconds
    header("refresh: 3; url=article.php?id=$original_article");
    exit;

?>