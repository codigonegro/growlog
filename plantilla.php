<?php
include "inc/sesion.php";

include "inc/mysql.php";

$cnx= new mysql;

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>GrowLog - Organizate y cosecha en grande</title>

    <meta name="description" content="Source code generated using layoutit.com">
    <meta name="author" content="LayoutIt!">

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/normalize.css" rel="stylesheet">

    <link href="css/panel.css" rel="stylesheet">
	  </head>
  <body>

<div class="container-fluid">
	<div class="row">
		<div class="col-md-12">
			<ul class="nav nav-tabs">
				<li class="active">
					<a href="#">Inicio</a>
				</li>
				<li>
					<a href="#">Ajustes</a>
				</li>
				<li >
					<a href="#">Mensajes</a>
				</li>
				<li class="pull-right">
					Bienvenido: <strong> <?php echo $apodo; ?> &nbsp;&nbsp;<a href="salir.php">[salir]</a></strong>
				</li>
			</ul>
		</div>
	</div>

	<div class="row cuerpo">
		<div class="col-md-2">
			<div class="panel panel-default">
			<?php include "inc/menu.php"; ?>

			</div><!-- // panel -->
		</div><!-- //col-md-22 -->


	</div><!-- //row cuerpo -->
</div><!-- // container fluid -->

	<script src="js/jquery-1.11.2.js"></script>
    <script src="js/bootstrap.min.js"></script>

  
</body>
</html>