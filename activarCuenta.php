<?php
include "inc/mysql.php";
$cnx=new mysql;

$code= $cnx->evitar_inyeccion($_GET['code']);

$uid=$cnx->obtener_dato("select id from usuario where codigoActivacion='$code'");

$sql="update usuario set activa=1 where id=$uid";

$cnx->ejecutar($sql);

session_start();

$_SESSION['uid']=$uid;

echo $cnx->error;

header("location: panel.php");
?>