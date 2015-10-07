
	<div class="panel-heading">
		<h3 class="panel-title">
			Operaciones
		</h3>
	</div>

<?php 
if ($actual =='') {
?>
	<div class="panel-active">
		<a href="panel.php">Ingresar Planta</a>
	</div>
	<div class="panel-body">
		<a href="riego.php">Riegos</a>
	</div>
	<div class="panel-body">
		<a href="abono.php">Abono</a>
	</div>
	<div class="panel-body">
		<a href="poda.php">Poda</a>
	</div>
<?php } ?>

<?php 
if ($actual =='riego') {
?>
	<div class="panel-body">
		<a href="panel.php">Ingresar Planta</a>
	</div>
	<div class="panel-active">
		<a href="riego.php">Riegos</a>
	</div>
	<div class="panel-body">
		<a href="abono.php">Abono</a>
	</div>
	<div class="panel-body">
		<a href="poda.php">Poda</a>
	</div>
<?php } ?>

<?php 
if ($actual =='abono') {
?>
	<div class="panel-body">
		<a href="panel.php">Ingresar Planta</a>
	</div>
	<div class="panel-body">
		<a href="riego.php">Riegos</a>
	</div>
	<div class="panel-active">
		<a href="abono.php">Abono</a>
	</div>
	<div class="panel-body">
		<a href="poda.php">Poda</a>
	</div>
<?php } ?>
<?php 
if ($actual =='poda') {
?>
	<div class="panel-body">
		<a href="panel.php">Ingresar Planta</a>
	</div>
	<div class="panel-body">
		<a href="riego.php">Riegos</a>
	</div>
	<div class="panel-body">
		<a href="abono.php">Abono</a>
	</div>
	<div class="panel-active">
		<a href="poda.php">Poda</a>
	</div>
<?php } ?>