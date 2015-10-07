<?php
/*
Incluir en los mantenedores para generar el formulario de ingreso y edicion
así como la grilla de busqueda.

Ojo Requiere de inc/mysql.php

Variables de paso:
$op, $actual, $ordena, $campos, $sentido
*/

$cnx= new despliegue;
/*
// Los botones de edicion y borrado se muestran por defecto a no ser que descomentemos esto
$cnx->boton_editar=0;

$cnx->boton_borrar=0;
*/

$cnx->registros_por_pagina=10;

/* ¡¡¡ Cuidado, no cambiar cosas de aqui en adelante !!!!!*/
$cnx->tabla = $tabla;

if (strlen($apodoTabla)>0){
$cnx->apodoTabla = $apodoTabla;
}

if (count($no_mostrar)>0){
$cnx->no_mostrar=$no_mostrar;
}

$idEditar=$_GET['editar'];

$idBorrar=$_GET['borrar'];

if (isset($_GET['actual'])){
$cnx->actual=$_GET['actual'];
}

if (isset($_GET['ordena'])){
$cnx->ordena=$_GET['ordena'];
// $js="var elem = document.getElementById('grilla')\n;elem.scrollTop = elem.scrollHeight;\n";
}

if (isset($_GET['campos'])){
$cnx->campos=$_GET['campos'];
}

if (isset($_GET['sentido'])){
$cnx->sentido=$_GET['sentido'];
}

if (strlen($idBorrar)>0){ $cnx->consultar("delete from $tabla where id=$idBorrar", $tabla); }

if (strlen($idEditar)==0){ $idEditar=0; }

if(isset($_POST['guardar'])){
	$error .="guardando ..";
$salidaGuardar= $cnx->procesar_datos($_POST, 'guardar', $tabla);
}

if(isset($_POST['editar'])){
$salidaEditar = $cnx->procesar_datos($_POST, 'editar', $tabla);
	$error .="editando ..";
}

$formu=$cnx->crear_formu($tabla, $idEditar);
echo "<script type=\"text/javascript\">\n var tab = '$tabla';\n $js</script>\n";

?>
