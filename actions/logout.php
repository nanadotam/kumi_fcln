<?php
session_start();
$_SESSION['signout'] = "You have successfully signed out.";
session_unset(); 
session_destroy();
header("Location: ../views/");
exit;
