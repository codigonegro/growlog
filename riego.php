<?php
include "inc/sesion.php";
include "inc/mysql.php";
$cnx= new mysql;

if ($_POST['id_planta']){
$id_planta=$_POST['id_planta'];
$fecha=$_POST['riego']; // op 2
$raleo=$_POST['raleo']; // op 3

$sql= "insert into actividades (id_usuario, id_planta, id_operacion, fecha, hojas_muertas)values($uid, $id_planta, 2, '$fecha', $raleo)";
$cnx->ejecutar($sql);
}


$sql="select p.id, concat(p.nombre,\"  \", b.nombre) from planta as p, bancos as b where p.id_usuario=$uid and p.id_banco=b.id";
$opPlantas= $cnx->crear_opciones($sql);
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

			<?php 
			$actual="riego";
			include "inc/menu.php"; 
			?>

			</div><!-- // panel -->
		</div><!-- //col-md-22 -->
		<div class="col-md-6">
					<div class="panel panel-default">
						<div class="panel-heading">
						<?php if (strlen($error)>1){ echo "<p>$error</p>"; } ?>
							<h3 class="panel-title"> Seleccione su planta e ingrese la fecha del riego</h3>
						</div>
						<div class="panel-default formu">
							<form role="form" action="riego.php" method="post">
								<div class="form-group">
									 
									<label for="exampleInputEmail1">
										Plantas:
									</label>
									<select class="form-control" name="id_planta">
										<option value="">Elija:</option>
										<?php echo $opPlantas; ?>
									</select>
								</div>
								<div class="form-group">
								<label for="datetimepicker1">
										Fecha de Riego:
									</label>
				                		<div class='input-group med date' id='datetimepicker1'>
				                        <input type="date" name="riego" value="<?php echo date("Y-m-d",time());?>">
				                        <!-- step="1" min="2013-01-01" max="2013-12-31" value="2013-01-01" -->
				                		</div>
				                </div>
				                <div class="form-group">
								<label for="datetimepicker1">
										Raleo (nr hojas muertas):
									</label>
				                		<div class='input-group med date' id='datetimepicker1'>
				                        <input type="number" name="raleo" value="0">
				                        <!-- step="1" min="2013-01-01" max="2013-12-31" value="2013-01-01" -->
				                		</div>
				                </div>
				               
				            <button type="submit" class="btn btn-primary">Guardar</button>
							</form>
						</div>
					</div>	
		</div>

		<div class="col-md-2 publicidad">
			<h2>Espacio Publicitario</h2>
		</div>
	</div><!-- //row cuerpo -->
</div><!-- // container fluid -->

	<script src="js/jquery-1.11.2.js"></script>
    <script src="js/bootstrap.min.js"></script>

  
</body>
</html>