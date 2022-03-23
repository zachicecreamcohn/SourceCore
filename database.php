<?php


// code from https://classes.engineering.wustl.edu/cse330/index.php?title=PHP_and_MySQL
$mysqli = new mysqli('localhost', 'news_site_user', 'wefh13hf', 'news_site');

if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: ". $mysqli->connect_error;
    exit;
}
?>

