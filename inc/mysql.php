<?php
/**************************************************************************************************
ARCHIVO		: mysql.php

USO			: Clase que proporciona la conectividad a mysql, cuenta con funciones de consulta basica
			  se utiliza incluyendole en el script que la utilizará ej: include("ruta/mysql.php");

CREADO POR	: Raúl Díaz Fernández <rauldiazf@gmail.com>
**************************************************************************************************/
 // para eliminar algunos notices
$error ='';
class mysql
{
var $dbuser, $dbpasswd, $dbhost, $dbase, $link, $db, $sql, $dato, $error, $SELF;
// FUNCION CONSTRUCTORA
function mysql()
{
$this->dbuser='root';
$this->dbpasswd='nomeacuerdo';

$this->dbase='growlog';
$this->link=mysql_connect($this->dbhost,$this->dbuser, $this->dbpasswd);
mysql_select_db($this->dbase);
$this->SELF= "http://" . $_SERVER['SERVER_NAME']  . $_SERVER['PHP_SELF'];
}


function ejecutar($sql)
	{
	$re=mysql_query($sql);
	if (!$re)
		{
		$this->error .= mysql_error() ."$sql<br />";
		return false;
		}
	return true;
	}

// REALIZA UNA CONSULTA DE INSERSION, ACTUALIZACION O BORRADO CON BLOQUEO DE LA TABLA
function consultar($sql, $tabla)
{
	$sq="LOCK TABLES $tabla WRITE";
	$re=mysql_query($sq);

	$res=mysql_query($sql);
	if (strlen(mysql_error())>0){
    $this->error .= mysql_error() . "<br />" . $sql ."<br />";
    }

    $id=$this->obtener_id($tabla);

	$sq="UNLOCK TABLES";
	$re=mysql_query($sq);
    $this->error .=mysql_error() . "<br />";


	return($id);

}

//VACIA UNA TABLA DEJANDO EL AUTOINCREMENT EN 0
function vaciar_tabla($tabla)
	{
	$sql="TRUNCATE TABLE $tabla";
	mysql_query( $sql);
	$this->error .=mysql_error() ."<br />";
	}

// DEVUELVE EL PUNTERO DE UNA CONSULTA
function obtener_puntero($sql)
{
	$res=mysql_query($sql);
	if(!$res){
	$this->error .= mysql_error() ."<br />";
	return(false);
	}
	return($res);
}

// COMPATIBILIDAD REVERSA
function crear_puntero($sql)
{
	$res=mysql_query($sql);
	 $this->error .= mysql_error() ."<br />";;
	return($res);
}

// DEVUELVE UN DATO UNICO A PARTIR DE UNA CONSULTA SQL QUE SE LE PASA COMO PARAMETRO
// Si se consulta por varios campos se puede especificar el indice delcampo requerido
function obtener_dato($sql, $indCampo=0)
{
	$res = $this->obtener_puntero($sql);
	if (!$res ){
	$this->error .= $sql ."<br />";;
	return(0);
	}

	while($fil = mysql_fetch_array($res))
		{
		$dato = $fil[$indCampo];
		}
	return($dato);
}

// PREVENIR ATAQUE DE INYECCION SQL
function evitar_inyeccion($cadena)
	{
	$salida = mysql_real_escape_string( $cadena);
	return($salida);
	}

// OBTIENE EL TIPO DE DATO DE UNA COLUMNA
function obtener_tipo_campo($tabla, $campo)
	{
		$sql = "SHOW FIELDS FROM $tabla";
		$res=$this->obtener_puntero($sql);
		while($f=mysql_fetch_array($res))
		{
		if ($f[0]==$campo){
			$cpo=$f[1];
			}
		}
		$arTipo = explode("(", $cpo);
	return($arTipo[0]);
	}



// OBTIENE EL ULTIMO ID DE $TABLA
function obtener_id($tabla)
	{
	$sql="SELECT AUTO_INCREMENT AS id FROM information_schema.Tables WHERE TABLE_SCHEMA = '". $this->dbase ."' AND table_name =  '" .$tabla . "' LIMIT 0 , 30";
	$n= $this->obtener_dato($sql);
	$n-=1;
	return($n);
	}

// OBTIENE EL NUMERO DE FILAS QUE DEVOLVERA UNA CONSULTA SQL
function obtener_nr_filas($sql)
	{
	$res=$this->obtener_puntero($sql);
	if (!$res){return(0);}
	$nr=mysql_num_rows($res);
	return($nr);
	}

// OBTIENE EL NUMERO DE CAMPOS DE UNA CONSULTA SQL
function obtener_nr_campos($sql)
	{
	$res=$this->obtener_puntero($sql);
	if(!$res){ return(0); }
	$nr=mysql_num_fields($res);
	return($nr);
	}

// PRODUCE UNA LISTA DE CAMPOS DE UNA TABLA SEPARADOS POR COMA
function listar_campos($tabla)
	{
	$result = mysql_query("SHOW COLUMNS FROM $tabla");
	$this->error .=mysql_error();
	if (mysql_num_rows($result) > 0)
	{
     while ($f = mysql_fetch_assoc($result))
		 {
         $lista .="$f,";
		}
	}
	$lista=substr($lista, 0, strlen($lista)-1);
	return($lista);
	}

//SI ENCUENTRA DATOS DEVUELVE 1, SINO DEVUELVE 0 (para validar si encontro algo)
function encontrar($sql)
{
	if($this->obtener_nr_filas($sql)>0){
		return(1);
		}else{
		return(0);
		}
}

// CREA LAS OPCIONES DE UN SELECT A PARTIR DE DATOS DE UNA CONSULTA SQL, DEJA SELECCIONADO EL REGISTRO
// CUYO ID ES PASADO COMO ARGUMENTO
// PONER ENC EN 1 SI SE LLAMA CON AJAX
function crear_opciones($sql, $id=0, $enc=0)
	{
	$res=$this->obtener_puntero($sql);

	if(!($res)){
		return ("<option>Sin datos</option>");
		}

	while ($fil=mysql_fetch_array($res))
		{
		if ($fil[0]==$id){ $s="SELECTED";  }else{ $s="";  }
			$opciones .="<option $s value=\"" . $fil[0] . "\">". ucfirst( $this->to_utf8( $fil[1] ) ) ."</option>\n";
		}
		return ($opciones);
	}

// RETORNA OPCIONDES DE COMBO A PARTIR DE UN CAMPO TIPO ENUM
function crear_opciones_enum($tabla, $campo)
{
$this->tabla=$tabla;
$res=$this->obtener_puntero("SHOW COLUMNS FROM $this->tabla LIKE '$campo'");
if(mysql_num_rows($res)>0){
						$fila=mysql_fetch_row($res);
						// enum('%','$');
						$tipo=$fila[1];
						$tipo=str_replace("'", "", substr($tipo, 5, strlen($tipo)-6));
						$ar_options=explode(',', $tipo);
						}
for ($x=0;$x<count($ar_options);$x++)
		{
		$opciones .="<option $s value=\"" . $ar_options[$x] . "\">". ucfirst($ar_options[$x]) ."</option>\n";
		}
return($opciones);
}

function enviar_mail($destino, $remitente, $asunto, $mensaje )
	{
	$headers = "From: $remitente\r\n";
	// El mensaje puede ser con codigo html
	$headers .='Content-type: text/html; charset=iso-8859-1' . "\r\n";
	mail($destino, $asunto, $mensaje, $headers );
	}


//Esta función convierte la fecha del formato DATETIME de SQL a formato DD-MM-YYYY HH:mm:ss
function convertir_fecha($fecha_datetime)
	{
	$fecha = split("-",$fecha_datetime);
	$fecha_convertida= $fecha[2] .'-'. $fecha[1] .'-'. $fecha[0];
	return($fecha_convertida);
	}

// recibe dd/mm/YYYY y retorna los segundos (timestamp)
function fecha_ts($fecha_datetime)
	{
	$fecha = split("/",$fecha_datetime);
	$dia=$fecha[0];
	$mes=$fecha[1];
	$anio=$fecha[2];
	$fecha_convertida= mktime(0,0,0,$mes,$dia,$anio);
	return($fecha_convertida);
	}

// recibe dd-mm-YYYY y retorna los segundos (timestamp)
function fecha_ts2($fecha_datetime)
	{
	$fecha = split("-",$fecha_datetime);
	$dia=$fecha[0];
	$mes=$fecha[1];
	$anio=$fecha[2];
	$fecha_convertida= mktime(0,0,0,$mes,$dia,$anio);
	return($fecha_convertida);
	}

// Acumula valor a campo de tabla en id
function acumular($tabla, $campo, $valor, $id)
	{
	$sql1	=	"select $campo from $tabla where id=$id";
	$dato	=	$this->obtener_dato($sql1);
	$valor += $dato;
	$sql2	=	"update $tabla set $campo=$valor where id=$id";
	$this->consultar($sql2, $tabla);
	}

// Resta valor a campo de tabla en id
function disminuir($tabla, $campo, $valor, $id)
	{
	$sql1	=	"select $campo from $tabla where id=$id";
	$dato	=	$this->obtener_dato($sql1);
	$nuevo = $dato - $valor;
	$sql2	=	"update $tabla set $campo=$nuevo where id=$id";
	$this->consultar($sql2, $tabla);
	}
// transforma un valor a formato pesos chilenos
function monetiza($valor)
{
setlocale(LC_MONETARY, 'CL');
$v =  "$ " . number_format($valor, 0, ',', '.');
return($v);
}

function to_utf8 ($str) {
    if (mb_detect_encoding($str, 'UTF-8', true) === false) {
    $str = utf8_encode($str);
    }
    return($str);
}

// muestra filas en bruto, sin paginacion se asume que la consulta no incluye claves foráneas
function tabular_datos($sql, $header='', $borrar=0)
	{

	if ($borrar){$thb="<th>&nbsp;</th>"; }

	$res=$this->obtener_puntero($sql);
	if (!$res){
		return("error: $this->error <br> $sql");
	}

	$nr_campos=mysql_num_fields($res);
	$salida = "<table class=\"simple\">";
	if ($header=='')
	{// usamos los nombres de los campos
	for($x=0;$x<$nr_campos;$x++)
		{
		$nombre_campo=mysql_fetch_field_direct( $res, $x )->name;

		if (substr($nombre_campo,0,2)=='id')
			{
			$nombre_campo =	substr($nombre_campo,2,strlen($nombre_campo));
			}

		if ($nombre_campo !='id')
			{
				$header .="<th>$nombre_campo</th>";
			}

		}
	}

	$salida .="<tr>$header $thb</tr> \n";

	$n=0;

	while($fila=mysql_fetch_array($res))
		{
		$campos='';
		if ($borrar){$b ="<td><a href=\"$this->self?b=". $fila[0] ."\">[x]</a></td>"; }
		for($x=0;$x<$nr_campos;$x++)
			{
			$nombre_campo=mysql_fetch_field_direct( $res, $x )->name;
			if ($nombre_campo!='id') // no mostramos el id
					{
					$campos .="<td>" . $fila[$x] . "</td>";
					}
			}

		$salida .="<tr>" . utf8_encode($campos) . "$b</tr>\n";
		$n++;
		}

	$salida .="</table>\n";

	if($n == 0){
		if (strlen($this->error)>0){
							return($this->error);
						}else{
							return("<div class=\"form-control\"><div class=\"panel-headding\"><p>Aun no hay datos</p></div><div class=\"panel-body\"></div></div>");
						}
				}
	return($salida);
	}

// MUESTRA FOTOS EN UN ALBUM, RUTA ES EL CAMPO DONDE SE ENCUENTRA GUARDADA LA RUTA DE LA FOTO
// AL HACER CLICK EN LA FOTO SE LLAMA A LA FUNCION MOSTRAR (QUE HAY QUE CREAR EN JAVASCRIPT)
// ESTA FUNCIÓN HACE VISIBLE EL DIV QUE MUESTRA LA FOTO MÁS GRANDE. (CON EL PREFIJO "gig" VER CLASE imagenes.php)

function album_fotos($sql, $cols=4, $clase='', $editar=0, $ruta='ruta')
	{
	$nr_registros=$this->obtener_nr_filas($sql);
	$res=$this->obtener_puntero($sql);
	$salida .=$header;
	$celda=1;
	while($fila=mysql_fetch_array($res))
		{
		$campos="";
		$id=$fila[0];
		$id_album=$fila['id_album'];
		$fecha=$fila['fecha'];
		$vendido=$fila['vendido'];

		if($editar==0)
		{
		// no editamos, por lo que la funcion javascript sera para mostrar
		$funcion='mostrar';
		$valor=$fecha;
		if($vendido==1)
			{
			$v="<div id=\"mensaje\"></div>";
			}
		}else{
		$funcion='eliminar';
		$valor=$id;
		if($vendido==0)
			{
			$v="<p><a href=\"editar_galeria.php?vendido=$id\">Vendido</a></p>";
			}else{
			$v="<p><a href=\"editar_galeria.php?no_vendido=$id\">No Vendido</a></p>";
			}
		}

		$salida .="<div class=\"foto\">$v<img class=\"redonda\" width=\"198\" src=\"" . $fila[$ruta] . "\" onClick=\"$funcion($valor, $id_album)\"></div>";	$v="";
		$celda++;
		}

//	$salida .="</tr>\n</table>";
	if($celda==1){ return ("<h1>Aun no hay fotos en este album</h1>");}
	return($salida);
	}

function generaPass($longitudPass=6){
    //Se define una cadena de caractares. Te recomiendo que uses esta.
    $cadena = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
    //Obtenemos la longitud de la cadena de caracteres
    $longitudCadena=strlen($cadena);
    //Se define la variable que va a contener la contraseña
    $pass = "";

    //Creamos la contraseña
    for($i=1 ; $i<=$longitudPass ; $i++){
        //Definimos numero aleatorio entre 0 y la longitud de la cadena de caracteres-1
        $pos=rand(0,$longitudCadena-1);

        //Vamos formando la contraseña en cada iteraccion del bucle, añadiendo a la cadena $pass la letra correspondiente a la posicion $pos en la cadena de caracteres definida.
        $pass .= substr($cadena,$pos,1);
    }
    return $pass;
}



}// fin clase

class despliegue extends mysql
{
/*
Todas las tablas se nombrar con letra inicial mayuscula y en singular: ejemplo :Producto, Proveedor
Todas las tablas tienen un primer campo llamado id, que es clave unica y autonumerico
Todos los campos que son claves foraneas se nombran idTabla donde Tabla es el nombre de la tabla relacionada
Incluir la clases css css/panel.css css/bootstrap.css css/normalize.css y el js/panel.js
*/
var $rutaJS="js"; // o cambiar a "../js" en caso necesario
var $boton_editar=1;
var $boton_borrar=1;
var $registros_por_pagina=20;
var $pag_actual=1;
var $nr_filas=0;
var $nr_paginas=0;
var $self ="";
var $ordena="id";
var $sentido="desc";
var $btn_borrar=1;
var $btn_editar=1;
var $tabla;
var $apodoTabla;
var $id_select;
var $borrar=0;
var $indfoto=100; // cambiar desde el objero creado al nr de campo que tiene una ruta de foto
var $rutafoto;
var $anchofoto;
var $editor;
var $campoid='';
var $where=''; // filtro
var $encode=true; // codificamos las cadenas con utf8 ?
var $no_mostrar=array();

function __construct()
	{
	$this->mysql();
	$this->self= "http://" . $_SERVER['SERVER_NAME']  . $_SERVER['PHP_SELF'];
	// $this->conectarse();
	}

// maneja los eventos del usuario como borrar, editar etc...
function manejar_eventos()
	{
		// borrar
		if($this->borrar != 0)
		{
		$sql="delete from " . $this->tabla . " where $this->campoid=" . $this->borrar;
		$this->consultar($sql, $this->tabla);
		}
	}

function mostraroNoelCampo($campo)
	{
	$b=true;
	for ($x=0;$x<=count($this->no_mostrar); $x++)
		{
			if ($this->no_mostrar[$x] == $campo)
			{
				$b=false;
			}
		}
	// retornara falso si el campo esta en el arreglo de los prohibidos
	return($b); 
	}

// Crea una grilla con paginacion y ordenamiento por campos
function paginar_datos($sql)
	{
	$this->manejar_eventos();
	$sql .= $this->where;
	$this->nr_filas=$this->obtener_nr_filas($sql);

	if ($this->nr_filas == 0){
		return("<div class=\"form-control\"><div class=\"panel-headding\"><h1>Aun no hay datos</h1></div></div>");
	}

	if (strlen($this->pag_actual)==0){$this->pag_actual=1;}
	if (strlen($this->ordena)==0){$this->ordena=$this->campoid;}
	if (strlen($this->sentido)==0){$this->sentido="asc";}
	// cuantos registros por pagina
	if($nr_filas%$this->registros_por_pagina !=0)
	{
	$this->nr_paginas = floor($nr_filas/$this->registros_por_pagina) + 1;
	}else{
	$this->nr_paginas = floor($nr_filas/$this->registros_por_pagina);
	}
	$nr_campos=$this->obtener_nr_campos($sql);
	$sql=$this->modificar_query($sql);
	$res=$this->obtener_puntero($sql);

	$salida .="<div class=\"panel panel-default maxancho\">";
	$salida .="<div class=\"panel-heading\">\n";

	if (strlen($this->apodoTabla)>0){ 
			$salida .="<h2><a name=\"gr\">&nbsp;</a>Listado de ". $this->apodoTabla ."</h2> \n"; 
		}else{
			$salida .="<h2><a name=\"gr\">&nbsp;</a>Listado de " . $this->tabla ."</h2> \n";
		}


	$salida .="</div>\n";
	$salida .="<div class=\"panel-body\">\n";

	$salida .="<div id=\"grilla\" class=\"table-responsive\">\n";
	$salida .= "<table class=\"table table-hover grilla\">\n<thead>\n";
	$salida .= $this->crear_encabezados($res,$nr_campos);
	$salida .="</thead>\n";
	$salida .="<tbody>\n";

	$nrColumnas=$nr_campos;
	if($this->btn_borrar==1){ $nrColumnas++; }
	if($this->btn_editar==1){ $nrColumnas++; }

	while($fila=mysql_fetch_array($res))
		{


		$salida .="<tr>\n";
		$campos="";
		$id=$fila[0];

		for($x=0;$x<$nr_campos;$x++)
			{
			$col=$x+1;
			if ($this->indfoto==$x)
				{
				if(strlen($fila[$x])==0)
					{
					$campos .="<td>[ sin imagen ]</td>\n";
					}else{
					$campos .="<td><img src=\"" .$this->rutafoto . $fila[$x] . "\" width=\"". $this->anchofoto ."\"></td>\n";
					}

				}else{

				$nombreCampo=mysql_fetch_field_direct($res, $x)->name;

				$test = substr($nombreCampo, 0, 2);

				$tablaForanea=	substr($nombreCampo, 3, strlen($nombreCampo));

				if ($test=='id' && $x>0)
					{// clave foranea

					$valCampo = $this->obtenerDatoForanea($fila[$x], $tablaForanea);
					$valCampo= $this->to_utf8($valCampo);
					$campos .="<td>$valCampo</td>\n";

					}else{

					if(strlen($fila[$x])==0){		$fila[$x]='&nbsp;';		}
					if ($this->mostraroNoelCampo($nombreCampo))
						{
						$campos .="<td class=\"$nombreCampo\">" . $this->to_utf8($fila[$x]) .  "</td>\n";
						}

					}

				}
			}

		if($this->btn_borrar==1){
			$btn = "<td class=\"btn\"><a href=\"#\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Eliminar este registro\" onclick=\"borrar('$this->self' + '?borrar=$id');\"><span class=\"glyphicon glyphicon-remove-circle red\"></span></a></td>\n" ;
						}

		if($this->btn_editar==1){
			$btn2 = "<td class=\"btn\"><a href=\"" .$this->editor."?editar=$id"."&tabla=". $this->tabla ."\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Modificar este registro\"><span class=\"glyphicon glyphicon-edit\"></span></a></td>\n" ;

			}

		$salida .= $campos . "$btn $btn2</tr>\n";
		}// fin while

	$salida .= "<tr><td class=\"footer\" colspan=\"$nrColumnas\">" . $this->crear_fila_indice(); // numeracion de paginas
	$salida .="</td></tr>\n</tbody>\n</table>\n </div></div></div>\n";
	return($salida);
	}

// Numeracion de paginas de la grilla, va al pie
function crear_fila_indice()
	{
	$salida="<ul class=\"pagination\">";
	$salida .="<li><a href=\"#\">&laquo;</a></li>";
	$c=0;
	for($a = 0;	$a < $this->nr_filas;	$a += $this->registros_por_pagina)
		{
		$c++;
		if ($this->actual==$a){$cssClass="class=\"active\"";}else{ $cssClass="";}

		$salida .="<li $cssClass><a href=\"$this->self" . "?actual=$a&ordena=$this->ordena&sentido=".$this->sentido."#gr\">$c</a></li>";
		}
	$salida .="<li><a href=\"#\">&raquo;</a></li>";
	$salida .="</ul>";
	return($salida);
	}

// Agrega opciones de ordenamiento a la grilla
function modificar_query($sql)
	{
	$sql .= " order by " . $this->ordena ." ". $this->sentido;
	if ($this->actual==0){$this->actual=1;}
	$inicio=$this->actual - 1;
	$sql .=" limit $inicio," . $this->registros_por_pagina;
	return($sql);
	}

// Crea los encabezados de la grilla
function crear_encabezados($res, $n)
	{
	$actual=$this->actual;
	if($this->sentido=='asc'){
		$s='desc';
		$icon="class=\"glyphicon glyphicon-triangle-bottom\"";
	}else{
		$icon="class=\"glyphicon glyphicon-triangle-top\"";
		$s='asc';
	}

	$salida ="<tr>";
	for($a=0; $a<$n; $a++)
		{
		$campos=mysql_fetch_field_direct($res, $a)->name;
		$test = substr($campos, 0, 2);
		if ($test=='id'&& (strlen($campos)>2)){ $Ncampo = substr($campos, 3, strlen($campos)); }else{ $Ncampo=$campos; }
		if ($this->ordena==$campos){$class="activo"; $ic=$icon;}else{$class="enc";$ic="";}
		/*********** ************/
		if ($this->mostraroNoelCampo($campos))
		   {
			$salida .="<th><a class=\"$class\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Ordenar por $campos\" href=$this->self?&actual=$actual&ordena=$campos&sentido=$s#gr>$Ncampo <span $ic></span></a></th>\n";
			}
		/************/
		}
	if ($this->boton_editar==1){ $salida .="<th> &nbsp; </th>\n"; }
	if ($this->boton_borrar==1){ $salida .="<th> &nbsp; </th>\n"; }
	$salida.="</tr>\n\n";

	return($salida);
	}
function crear_formu($tabla, $idEditar=0)
	{

		$sql="describe $tabla";
		if ($idEditar!=0){
		// si estamos editando un registro creamos otro formulario ad hoc
			return($this->crear_formuEd($tabla, $idEditar));
		}

		$res=$this->obtener_puntero($sql);
		


		$salida .="<div class=\"panel panel-default\">";
		$salida .="<div class=\"panel-heading\">\n";

		
		if (strlen($this->apodoTabla)>0){ 
			$salida .="<h2>Ingreso de " .$this->apodoTabla ." </h2> \n"; 
		}else{
			$salida .="<h2>Ingreso de " . $this->tabla . "</h2> \n";
		}

		$salida .="</div>\n";
		$salida .="<div class=\"panel-body\">\n";
		$salida .="<form class=\"form\" name=\"f1\" method=\"POST\" action=\"".$this->self."\">";

		while($fila=mysql_fetch_array($res))
			{


				if ($idEditar!=0)
					{

					$valor=$fila[$i];

					}

				$nombreCampo = $fila[0];

				$tipoCampo = $this->obtener_tipo_campo($tabla, $nombreCampo);

				if(substr($nombreCampo,0,2)=='id')
					{
					$label    = substr($nombreCampo,3,strlen($nombreCampo));
					}else{
					$label = $nombreCampo;
					}

				if ($nombreCampo !='id')
					{
					if ($this->mostraroNoelCampo($nombreCampo))
			   			{
						$control = $this->crearControl($nombreCampo, $tipoCampo, $label, $valor);
						$salida .="<div class=\"form-group\">\n";
						$salida .="<label for=\"$nombreCampo\">$label :</label> $control\n </div>\n";
						}
					}


			}

	$input .="<input type=\"hidden\" name=\"guardar\" value=\"1\">";
	$input .="<div class=\"form-group\"><button name=\"guardar\" class=\"btn btn-primary\"> Guardar</button></div>";
	$salida .= $input . "\n</form></div></div>\n";
	$sqlGrilla="select * from $tabla ";

	
	$salida .= $this->paginar_datos($sqlGrilla);

	$salida .="<script type=\"text/javascript\" src=\"". $this->rutaJS. "/panel.js\"></script>"; // El Script
	return($salida);
	}

// idem a la funcion anterior pero esta no incluye la etiqueta form
function crear_formu_controles($tabla, $idEditar=0)
	{

		$sql="describe $tabla";
		if ($idEditar!=0){
		// si estamos editando un registro creamos otro formulario ad hoc
			return($this->crear_formuEd($tabla, $idEditar));
		}

		$res=$this->obtener_puntero($sql);

		while($fila=mysql_fetch_array($res))
			{


				if ($idEditar!=0)
					{

					$valor=$fila[$i];

					}

				$nombreCampo = $fila[0];

				$tipoCampo = $this->obtener_tipo_campo($tabla, $nombreCampo);

				if(substr($nombreCampo,0,2)=='id')
					{
					$label    = substr($nombreCampo,3,strlen($nombreCampo));
					}else{
					$label = $nombreCampo;
					}

				if ($nombreCampo !='id')
					{
					if ($this->mostraroNoelCampo($nombreCampo))
			   			{
						$control = $this->crearControl($nombreCampo, $tipoCampo, $label, $valor);
						$salida .="<div class=\"form-group\">\n";
						$salida .="<label for=\"$nombreCampo\">$label :</label> $control\n </div>\n";
						}
					}


			}

	
	return($salida);
	}

// crea el control de formulario segun el tipo de campo de la tabla
function crearControl($nombreCampo, $tipoCampo, $label, $valor)
{
	$tabla=$label;
	if(substr($nombreCampo,0,2)=='id')
		{ // si es foránea
			$sqlForanea="select * from $label limit 1";
			$nr=$this->obtener_nr_campos($sqlForanea);
		if ($nr<1) // si no tiene datos
			{
			$control="<div id=\"$label\"><select name=\"$nombreCampo\" id=\"$nombreCampo\"><option vlaue=\"0\">Sin datos en $label</option></select><div id=\"$label\">";
			return($control);
			}

			// Si tabla foranea es normal
			$sql= "select id, nombre from $label order by nombre";
			// si tabla foranea es una empresa
			if (strtolower($label)=='empresa' || strtolower($label)=='proveedor' )
				{ $sql ="select id as idTabla, rzSocial as nombre from $label"; }

			// de las personas nombre y apellido
			if(strtolower($label)=='persona' || strtolower($label)=='personas' )
				{ $sql ="select id as id , concat(nombres, \" \", apellidos) as nombre from $label"; }

			$control="<div id=\"$label\"><select name=\"$nombreCampo\" id=\"$nombreCampo\">\n";
			$control .="<option value=\"\">Elija:</option>\n";
			if (strlen($valor)==0){$vaor=0;}
			$control .=$this->crear_opciones($sql, $valor, 1);
			$control .="</select></div>\n";

		}elseif ($tipoCampo=='enum'){
			$control="<div id=\"$label\"><select name=\"$nombreCampo\" id=\"$nombreCampo\">\n";
			$label="$nombreCampo";
			$control .="<option value=\"\">Elija:</option>\n";
			$control .=$this->crear_opciones_enum($this->tabla, $nombreCampo);
			$control .="</select>\n</div>\n";

		}elseif ($tipoCampo=='date'){
			$label	=	$nombreCampo;

			$valor = $this->to_utf8($valor);

			$control="<input class=\"form-control\" data-tbl=\"".$this->tabla."\" type=\"date\" id=\"$label\" placeholder=\"$label\" name=\"$label\" title=\"$label\" value=\"$valor\"/><div id=\"E_$label\" class=\"msg\"></div>\n";		

		}else{// no es clave foranea ni tipo enum ni date

			$label	=	$nombreCampo;

			$valor = $this->to_utf8($valor);

			$control="<input class=\"form-control\" data-tbl=\"".$this->tabla."\" type=\"text\" id=\"$label\" placeholder=\"$label\" name=\"$label\" title=\"$label\" value=\"$valor\"/><div id=\"E_$label\" class=\"msg\"></div>\n";
		}
return($control);
}

function procesar_datos($POST, $como="guardar", $tabla)
	{
	// $como: guadar o editar
	foreach ($POST as $campo => $valor)
		{
			if (isset($POST['guardar'])){
			if ($campo!='id' && $campo != 'guardar'){
				$campos .= "$campo, ";
				$tipo=$this->obtener_tipo_campo($tabla, $campo);
				if ($tipo=="double" || $tipo=="int" || $tipo=="float")
					{
					$valores .=	"'$valor', ";
					}else{
					$valor=mysql_real_escape_string( $this->to_utf8($valor));
					$valores .="'$valor', ";
					}

				}
			}

			if (isset($POST['editar'])){
			if ($campo!='id' && $campo != 'editar'){
				$tipo=$this->obtener_tipo_campo($tabla, $campo);
				if ($tipo=="double" || $tipo=="int" || $tipo=="float")
					{
					$actualiza .= "$campo=$valor, ";
					}else{
					$valor = $this->to_utf8($valor);
					$actualiza .= "$campo='$valor', ";
					}
				}
			}
		}

		if (isset($POST['guardar'])){
				$campos=substr($campos, 0, strlen($campos)-2);
				$valores=substr($valores, 0, strlen($valores)-2);
				$sql ="insert into $tabla ($campos) values ($valores)";
				}

		if (isset($POST['editar'])){
				$actualiza=substr($actualiza, 0, strlen($actualiza)-2);
				$sql ="update $tabla set $actualiza where id=" . $POST['id'];
				}

	$this->consultar($sql, $tabla);
	return($sql);
	}


function obtenerDatoForanea($id, $tablaForanea){
// default .. campo nombre
$sql ="select nombre from $tablaForanea where id=$id";
// de las empresas mostramos la razon social
if (strtolower($tablaForanea)=='empresa' || strtolower($tablaForanea)=='empresas' )
	{ $sql ="select rzSocial as nombre from $tablaForanea where id=$id"; }
// de las personas nombre y apellido
if(strtolower($tablaForanea)=='persona' || strtolower($tablaForanea)=='personas' )
{ $sql ="select concat(nombres, \" \", apellidos) as nombre from $tablaForanea where id=$id"; }
$nr=$this->obtener_nr_filas($sql);

if($nr>0){
	$nombre= $this->obtener_dato($sql);
}else{
// si aun no hay registros en la tabla foranea solo mostramos el nombre de ella
	$nombre= $tablaForanea;
}

return($nombre);
}

// Formulario para edicion, es llamado solo desde crear_formu cuando se edita un registro
// creamos esta funcion aparte para solucionar el problema de las tablas vacías
function crear_formuEd($tabla, $idEditar)
	{
		$sql="select * from $tabla  where id=$idEditar limit 1";
		$res=$this->obtener_puntero($sql);
		$salida .="<div class=\"panel panel-default\">";
		$salida .="<div class=\"panel-heading\">\n";

		if (strlen($this->apodoTabla)>0){ 
			$salida .="<h2>Ingreso de " . $this->apodoTabla ."</h2> \n"; 
		}else{
			$salida .="<h2>Ingreso de ". $this->tabla. " </h2> \n";
		}
		

		$salida .="</div>\n";
		$salida .="<div class=\"panel-body\">\n";
		$salida .="<form class=\"form\" name=\"f1\" method=\"POST\" action=\"".$this->self."\">";
		while($fila=mysql_fetch_array($res))
			{
					$nrCampos=count($fila)/2;
					$nrCol=floor($nrCampos/2);

					for($i=0;$i<$nrCampos; $i++)
						{
						if ($idEditar!=0){
						$valor=$fila[$i];
						}
						$nombreCampo=mysql_fetch_field_direct($res, $i)->name;
						$tipoCampo=$this->obtener_tipo_campo($tabla,$nombreCampo);

						if(substr($nombreCampo,0,2)=='id')
										{
											$label    = substr($nombreCampo,3,strlen($nombreCampo));
										}else{
											$label = $nombreCampo;
										}

						if ($nombreCampo !='id')
										{

										$control = $this->crearControl($nombreCampo, $tipoCampo, $label, $valor);
										$salida .="<div class=\"form-group\">\n";
										$salida .="<label for=\"$label\">$label :</label> $control\n </div>\n";

										}

						}
			}

	$input  ="<button name=\"editar\" class=\"btn btn-primary\"> Guardar Cambios</button>";
	$input .="<input type=\"hidden\" name=\"id\" value=\"$idEditar\">";

	$salida .= $input . "\n</form></div></div>\n";

	$sqlGrilla="select * from $tabla ";

	$salida .= $this->paginar_datos($sqlGrilla);

	return($salida);
	}

}// fin clase
