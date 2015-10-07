<?php
session_start();
$uid=$_SESSION['uid'];
$priv=$_SESSION['priv'];
if ($priv>1){
	header("location: index.php?e=1");
}
?>