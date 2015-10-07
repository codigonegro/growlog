<?php
include "inc/mysql.php";

$cnx= new mysql;

$apodo=$cnx->evitar_inyeccion($_POST['apodo']);

$email=$cnx->evitar_inyeccion($_POST['email']);

$clave=$cnx->evitar_inyeccion($_POST['clave']);

$code= generateRandomString();

$sql="insert into usuario (nick, email, clave, codigoActivacion) values('$apodo', '$email', '$clave', '$code')";

$cnx->ejecutar($sql);

$error .=$cnx->error;

$code= generateRandomString();


$to = $email;

$subject = 'Activacion Cuenta GrowLog';

$headers = "From: no-responder@growlog.cl \r\n";

$headers .= "Reply-To: no-responder@growlog.cl \r\n";

$headers .= "CC: rauldiazf@gmail.com\r\n";

$headers .= "MIME-Version: 1.0\r\n";

$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

$message = '<html><body>';

$message .= '<h2>Para activar su cuenta siga el siguiente enlace</h2>';

$message .= "<a href=\'http://webingenio.cl/growlog/activarCuenta.php?code=$code'>Activar</a>";

$message .= "</body></html>";

mail($to, $subject, $message, $headers);


function generateRandomString($length = 20) {

    return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);

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
		    <div class="col-md-3"></div>
				<div class="col-md-6">
					<h1>Para activar su cuenta revise su correo y siga el enlace</h1>
					<br />
					<?php echo $error; ?>
		    	</div>
			<div class="col-md-3"></div>	    
	    </div>
	</div>
    <script src="js/jquery-1.11.2.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/human.js"></script>
     </body>
</body>
</html>