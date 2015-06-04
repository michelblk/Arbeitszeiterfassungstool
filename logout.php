<?php
require('include/php/auth.php');
$_SESSION['angemeldet'] = FALSE;
session_unset();
session_destroy();

header('LOCATION: index.php');
?>
