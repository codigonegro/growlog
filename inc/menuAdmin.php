
<!-- menu -->
<!-- <nav class="navbar  bajalo"> -->
<nav class="navbar">
<div class="container-fluid">
	<ul class="nav nav-tabs">
		<li><a href="panel.php">Inicio</a></li>
		<li><a href="proveedores.php">Proveedores</a></li>

		<li><a href="clientes.php">Clientes</a></li>
		<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">Bodega <span class="caret"></span></a>
			<ul class="dropdown-menu">

				<li><a href="familias.php">Familias</a></li>
				<li><a href="marcas.php">Marcas</a></li>
				<li><a href="modelos.php">Modelos</a></li>
				<li><a href="modelo_producto.php">Relacionador</a></li>
				<li><a href="productos.php">Productos</a></li>
			</ul>
		</li>

		<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">Administración <span class="caret"></span></a>
			<ul class="dropdown-menu">
				
				<li><a href="parametros.php">Parametros</a></li>
			    <li><a href="usuarios.php">Usuarios</a></li>

			</ul>
		</li>
				<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">Reportes <span class="caret"></span></a>
			<ul class="dropdown-menu">
			<li><a href="repProductos.php">Productos</a></li>
				<li><a href="repFacturas.php">Facturas</a></li>
				<li><a href="repBalance.php">Balance</a></li>
			</ul>
		</li>

		<li><a href="logout.php">Salir</a></li>
	</ul>
<!-- BREADCUM
<ul class="breadcrumb">
    <li><a href="panel.php">Inicio</a></li>
    <li class="active"><a href="usuarios.php">Usuarios</a></li>
</ul>
-->

</div>
</nav>
<script type="text/javascript" charset="utf-8" async defer>
	$('[data-toggle="tooltip"]').tooltip();
	$('#miModal').modal({
		show:true
	})
</script>
<!-- Fin Menu -->
