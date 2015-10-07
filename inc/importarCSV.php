<?php
include ("inc/mysql");

if (isset($_POST['archivo'])
{
$fila = 1;
	if (($gestor = fopen("test.csv", "r")) !== FALSE) {
	    while (($datos = fgetcsv($gestor, 1000, ",")) !== FALSE) {
	        $numero = count($datos);
	        echo "<p> $numero de campos en la línea $fila: <br /></p>\n";
	        $fila++;
	        for ($c=0; $c < $numero; $c++) {
	            echo $datos[$c] . "<br />\n";
	        }
	    }
	    fclose($gestor);
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Panel de control- importadora</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/bootstrap-theme.css" rel="stylesheet">
	<link href="css/panel.css" rel="stylesheet">
	<!-- script references -->
	<script src="js/jquery-1.11.2.js"></script>
	<script src="js/bootstrap.min.js"></script>
</head>
<body>
<form name="f1" method="POST" action="importarCSV.php" enctype="multipart/form-data">
<div class="form-group">
<label for="rzSocial">Archivo:</label> 
<input class="form-control" type="file" id="archivo" placeholder="ruta al archivo" name="archivo" title="Suba un archivo" value=""/>
<button name="guardar" class="btn btn-primary"> SUbir</button>
</form>
</body>
</html>