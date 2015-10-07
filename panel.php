<?php
include "inc/sesion.php";

include "inc/mysql.php";

$cnx= new mysql;

// Agregar nuevo banco
if ($_POST['banco']){
	$banco= $cnx->evitar_inyeccion($_POST['banco']);
	$existe=$cnx->encontrar("select id from banco where nombre like '%banco%'");
	if (!$existe)
		{
			$sql="insert into banco(nombre) values('$banco')";
			$cnx->ejecutar($sql);
		}
}

// Agregar planta
if ($_POST['id_banco'])
{
	$id_banco		=$_POST['id_banco'];
	$variedad 		=$cnx->evitar_inyeccion($_POST['variedad']);
	$tipo 			=$cnx->evitar_inyeccion($_POST['tipo']);
	$rendimiento 	=$cnx->evitar_inyeccion($_POST['rendimiento']);
	$semanas		=$cnx->evitar_inyeccion($_POST['semanas']);
	$fecha_siembra	=$_POST['siembra'];

$sql="insert into planta(id_usuario, id_banco, nombre, tipo, rinde, fecha_siembra) ";
$sql .="values($uid, $id_banco, '$variedad', '$tipo', '$rendimiento', '$fecha_siembra')";

$cnx->ejecutar($sql);

$error = $cnx->error - "$sql";
}

$opBancos = $cnx->crear_opciones("select * from bancos");

$apodo=$cnx->obtener_dato("select nick from usuario where id=$uid");


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
	
		<div class="modal fade" id="miModal" >
			<div class="modal-dialog">
				<div class="modal-content">
					<form action="panel.php" method="post">
						<div class="modal-header">

							<button class="close" aria-hidden="true" data-dismiss="modal">&times;</button>
											<h4 class="modal-title">Nuevo banco:</h4>
						</div>
						<div class="modal-body">

							 Nombre Banco: <input type="text" name="banco"> 
							 <button type="submit" class="btn btn-success btn-xs">
							 	<span class="glyphicon glyphicon-plus"></span> 
							 </button>
						</div>
					</form>
				</div>
			</div>
		</div>

		<div class="col-md-6">
			<div class="panel panel-default">

				<div class="panel-heading">
				<?php if (strlen($error)>1){ echo "<p>$error</p>"; } ?>
					<h3 class="panel-title"> Ingresar Planta </h3>
				</div>

				<div class="panel-default formu">
					<form role="form" action="panel.php" method="post">
						<div class="form-group">
							 
							<label for="exampleInputEmail1">
								Banco de semilla:
							</label>
							<select class="form-control med" name="id_banco">
								<?php echo $opBancos; ?>
							</select>
							<button type="button" data-toggle="modal" data-target="#miModal" class="btn btn-success">
							<span class="glyphicon glyphicon-plus"></span> 
							Agregar</button>
						</div>

						<div class="form-group">
							 
							<label for="exampleInputPassword1">
								Variedad:
							</label>
							<input name = "variedad" type="text" class="form-control med" id="exampleInputPassword1" />
						</div>

						<div class="form-group">
							 
							<label for="exampleInputPassword1">
								Rendimiento por Planta (grs):
							</label>
							<input name="rendimiento" type="text" class="form-control med" id="exampleInputPassword1" />
						</div>

						<div class="form-group">
							 
							<label for="exampleInputPassword1">
								Tiempo desde semilla a cosecha (semanas):
							</label>
							<input name="semanas" type="text" class="form-control med" id="exampleInputPassword1" />
						</div>

						<div class="checkbox">
							<label for="exampleInputFile">
								Tipo:
							</label>					 
							<label>
								<input name="tipo" value="Feminizada" type="radio" checked /> Feminizada						
								<input name="tipo" value="Automatica" type="radio" /> Autofloreciente
								<input name="tipo" value="regular" type="radio" /> Regular

							</label>

						</div> 

						<div class="form-group">
						<label for="datetimepicker1">
								Fecha de siembra:
							</label>
		                <div class='input-group med date' id='datetimepicker1'>
		                        <input type="date" name="siembra" value="<?php echo date("Y-m-d",time());?>">
		                        <!-- step="1" min="2013-01-01" max="2013-12-31" value="2013-01-01" -->
		                </div>
		            </div>
		            <div class="form-group">
						<label for="exampleInputPassword1">
							Cantidad de plantas:
						</label>
						<input type="number" name="cantidad" min="1" max="20">
					</div>

						<button type="submit" class="btn btn-primary">
							Guardar
						</button>
					</form>
				</div> <!-- //"panel-active" -->
			</div>

		</div>
		<div class="col-md-2 publicidad">
			<h2>Espacio Publicitario</h2>
		</div>

	</div><!-- //row cuerpo -->
</div>

	<script src="js/jquery-1.11.2.js"></script>
    <script src="js/bootstrap.min.js"></script>

  
</body>
</html>