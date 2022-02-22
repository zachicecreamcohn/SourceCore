<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Testing</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div id="mainContent">
        <p>This is a test again</p>
    </div>

<?php

require 'database.php';


// // check if username 'zcohn' in the database
// $stmt = $mysqli->prepare("SELECT COUNT(*) FROM users");

// if (!$stmt) {
//     printf("Query Prep Failed: %s\n", $mysqli->error);
//     exit;
// }

// $stmt->execute();

// $stmt->bind_result($email);
// $stmt->fetch();
// echo htmlspecialchars($email);
// // echo"<ul>";
// // while ($stmt->fetch()) {
//     // echo "<li>".htmlspecialchars($email)."</li>";
// // }

// // echo "</ul>";

// $stmt->close();

function getSingleData($mysqli, $query) {
    $stmt = $mysqli->prepare($query);
    $stmt->execute();
    $stmt->bind_result($result);
    $stmt->fetch();
   
    $stmt->close();
    return $result;
}


$userCount = getSingleData($mysqli, "SELECT first_name, last_name FROM users");
echo $userCount;

?>
</body>
