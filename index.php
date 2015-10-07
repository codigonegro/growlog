<?php

if (isset($_POST['email']))
{
	include("inc/mysql.php");
	
	$cnx = new mysql(); 
	
	$email	= $cnx->evitar_inyeccion($_POST['email']);
	
	$clave 	= $cnx->evitar_inyeccion($_POST['clave']);
	
	$sql="select id, activa from usuario where email='$email' and clave='$clave'";
	
	$id=$cnx->obtener_dato($sql,0);

	$activa=$cnx->obtener_dato($sql,1);

	// $error .="id=$id, nivel=$nivel";
	if (strlen($id)==0 || $id==0 )
	{
		$error = "Hay un error en su email ($email) o en su clave $clave <br /> $sql";
	}else{
		if($activa==1){
			session_start();
			$_SESSION['uid']=$id;
		 	header("location: panel.php");
		 }else{
		 	$error ="Su cuenta no ha sido activada, por favor revise su correo";
		 }
	}

}
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

    <link href="css/login.css" rel="stylesheet">
	  </head>
  <body>

    <div class="container-fluid">    
    	<div class="row">
		<div class="col-md-12">
			<div class="page-header">
			<?php echo $error; ?>
				<h1>
					<img class="profile-img" src="ico/weed_logo.png" height="40"> GrowLog <small>Organizate y cosecha en grande</small>
				</h1>
			</div>
			</div>
		</div> <!-- //row -->

	<div class="row">
		<div class="col-md-5">
			 <h2>Crea tu cuenta en un solo paso:</h2>
			 <div class="container">
				    <div class="row">
				        <div class="col-sm-4 col-md-4">
				            <div class="account-wall">
			 				<div class="tab-pane" id="register">
			 				<div class="tab-pane active" id="login">
				            <img class="profile-img" src="ico/agregar_contacto.png" height="80">
							<form class="form-signin" action="crearCuenta.php" method="post">
								<input name="apodo" type="text" class="form-control" placeholder="Apodo" required autofocus>
								<input name="email" type="email" class="form-control" placeholder="Email" required>
								<input name="clave" type="password" class="form-control" placeholder="Clave" required>
								<p>
								<input type="button" id="btnHumano" class="default" value="soy humano">
									<div id="soyhumano">
										<canvas id="key" width="100" height="30"></canvas><br>
										Ingrese el texto de la imagen: <input type="text" id="desafio" name="desafio" value=""><br>	
									</div>
								</p>
								<input id="btnSubmit" type="button" class="btn btn-lg btn-default btn-block" value="Registrate" />
							</form>
						</div>
					</div>
					</div>
					</div>
					</div>
					</div>
		</div>

		<div class="col-md-5">
			 <h2>¿Ya tienes cuenta? ... Ingresa aqui:</h2>
			 <div class="container">
				    <div class="row">
				        <div class="col-sm-4 col-md-4">
				            <div class="account-wall">
				                <div id="my-tab-content" class="tab-content">
									<div class="tab-pane active" id="login">
				               		    <img class="profile-img" src="ico/user.png" height="80">
				               			<form class="form-signin" action="index.php" method="post">
				               				<input name="email" type="text" class="form-control" placeholder="email" required autofocus>
				               				<input name="clave" type="password" class="form-control" placeholder="clave" required>
				               				<input type="submit" class="btn btn-lg btn-default btn-block" value="Acceder" />
				               			</form>
				               		</div>
								</div>
				            </div>
				        </div>
				    </div>
				</div>
		</div>
	</div>

	<div class="row priv">
		<div class="col-md-12">

			<div class="jumbotron">
				<h2>
					Protegemos tu Privacidad
				</h2>
				<p>
					Ninguno de tus datos será divulgado jamás y en cualquier momento puedes borrar todo con un solo click.
				<p>
					<a class="btn btn-primary btn-large" href="#">Ver más</a>
				</p>
			</div>
		</div>
	</div>


</div> <!-- container fluid -->

    <script src="js/jquery-1.11.2.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/human.js"></script>
     </body>
</html>