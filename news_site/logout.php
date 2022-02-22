<?php
session_start();
?>

<?php
session_unset();
session_destroy();

// redirect to index.php
header("Location: index.php");
exit;
?>