<?php
session_start();
$uid=$_SESSION['uid'];
if ($uid==0||$uid==''){
session_destroy();
header('location:index.php');
}
?>