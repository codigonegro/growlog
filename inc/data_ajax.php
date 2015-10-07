<?php
require_once("mysql.php");
/*
Esta clase solo se utiliza para manejar los llamados desde ajax.
*/

class myajax extends mysql
{

// Busca si existe un rut coincidente con patron en la tabla mclientes
	function buscarRut($rut)
		{
		$sql="select count(id) from proveedor where rut='$rut'";
		/*return($this->error);*/
		$salida = $this->obtener_dato($sql);
		if ($salida >= 1){ return("Este rut ya existe"); }
		}

// LLena el combo de comunas
	function llenarComunas($id)
	{
		$sql="select id, nombre from comuna where id_region=$id";
		$op = $this->crear_opciones($sql,0,1);
		$select ="<select id=\"id_comuna\" name=\"id_comuna\">\n";
		$select .="<option value=\"0\">Elija Comuna</option>\n";
		$select .=$op;
		// Debug // $select .="<option>$sql</option>";
		$select .="</select>\n";
		return($select);
	}

// LLena el combo de giros
	function llenarGiros($id)
	{
		$sql="select id, nombre from giro where id_rubro=$id";
		$op = $this->crear_opciones($sql,0,1);
		$select ="<select id=\"id_giro\" name=\"id_giro\">\n";
		$select .="<option value=\"0\">Elija Giro</option>\n";
		$select .=$op;
		$select .="</select>\n";
		return($select);
	}


	function llenarSubfamilia($id)
	{
		$sql="select id, nombre from subFamilia where id_familia=$id";
		$op = $this->crear_opciones($sql,0,1);
		$select ="<select id=\"id_subFamilia\" name=\"id_subFamilia\">\n";
		$select .="<option value=\"0\">Elija Sub Familia</option>\n";
		$select .=$op;
		$select .="</select>\n";
		return($select);
	}

	function llenarFactura($codigoBarra)
	{
		// Validar stock
		$stock=$this->obtener_dato("select stock from producto where codigo_barra='$codigoBarra'");
		
		$stock-=1;
		
		if ($stock<=0){ return ('0'); }

		
		$this->consultar("update producto set stock=$stock where codigo_barra='$codigoBarra'", 'producto');

		
		// Si hay stock enviamos los datos del producto a la factura/boleta
		$sql  ="select p.id, m.nombre as marca, p.nombre as nombre, p.precio + (p.precio * p.margenpublico/100) as precio ";
		$sql .="from marca as m, producto as p where p.id_marcaRepuesto=m.id and p.codigo_barra='$codigoBarra'";
		$res=$this->obtener_puntero($sql);
		if (!$res) { return($sql);}
		$cont=0;
		while ($fila=mysqli_fetch_array($res)) {
		$id=$fila[0];
		$marca=$fila[1];
		$nombre=$fila[2];
		$precio=floor($fila[3]);		
		$cont++;
		}
		if($cont==0){return("");}
		$salida = "{\"id\":\"$id\", \"nombre\":\"$nombre\", \"marca\":\"$marca\",\"precio\":\"$precio\" }";
		return($salida);
	}

	function llenarCliente($rut)
	{
		$sql  ="select id, concat(nombres,\" \" ,apellidos), fono, email, rzSocial, id_region, id_comuna, direccion_despacho, obs from cliente where rut='$rut'";
		$res=$this->obtener_puntero($sql);
		$cont=0;
		while ($fila=mysqli_fetch_array($res)) {
		$id=$fila[0];
		$nombres=$fila[1];
		$fono=$fila[2];
		$email=$fila[3];		
		$rzSocial=$fila[4];
		$idRegion=$fila[5];
		$idComuna=$fila[6];
		$comuna=$this->obtener_dato("select nombre from comuna where id=$idComuna");
		$direccion=$fila[7];
		$obs=$fila[8];
		$cont++;
		}
		if($cont==0){return("$sql");}

		$salida = "{"; 
		$salida .= "\"id\":\"$id\", \"rut\":\"$rut\", \"nombres\":\"$nombres\", \"fono\":\"$fono\",\"email\":\"$email\",";
		$salida .= "\"rzSocial\":\"$rzSocial\", \"idRegion\":\"$idRegion\", \"idComuna\":\"$idComuna\", \"comuna\":\"$comuna\",";
		$salida .= "\"direccion\":\"$direccion\", \"obs\":\"$obs\" ";
		$salida .= "}";
		return($salida);
	}
	

	/* Envia los datos para llenar un select en formato json, se procesan en la clase ajax */
	function opcionesSelectJson($sql)
		{
		$res=$this->obtener_puntero($sql);
		$cont=0;
		$salida = "{ \"opciones\" :["; 
		while ($fila=mysqli_fetch_array($res)) 
			{
			$id=$fila[0];
			$nombre=$this->to_utf8($fila[1]);

			$salida .= "{\"value\":\"$id\", \"text\":\"$nombre\"},";

			$cont++;
			}
			// retiramos la ultima coma
			$salida = substr($salida, 0, strlen($salida) - 1);
			$salida .= "]}";
		if($cont==0){ return("$sql"); }
		return($salida);
	}

	// Genera elementos <li> con evento onClick 
	function opcionesSubmenu($data){
	$json = json_decode($data);
	$sql = "select " .$json->campos->c1 .", " .$json->campos->c2 . " from " . $json->tabla;  
	$sql .= " where " .$json->campos->c2 ." like '%" . $json->patron ."%'";

	$res=$this->obtener_puntero($sql);
	$cont=0;
		while ($fila=mysqli_fetch_array($res)) 
			{
			$id     =$fila[0];
			$nombre =$this->to_utf8($fila[1]);
			// fijarGiro es una funcion javascript que pone el id del giro en un hidden y el nombre del giro en el input[text]
			$salida .="<li><a href=\"#\" onClick=\"fijarGiro('$id', '$nombre');\">$nombre</a></li>\n";
			$cont++;
			}
	// if($cont==0){ return( $this->to_utf8($sql)); } // depuracion

	if($cont==0){ return( "<li>Sin Resultados</li>"); }

	return($salida);

	}

	// Copiar el giro a la tabla misGiros y llama a opcionesSelectJson para generar las opciones en formato json
	function copiarGiro($id){

		$sql="INSERT INTO misGiros SELECT * FROM giro WHERE id =$id";
		$this->ejecutar($sql);
	// Este llamado retornara las opciones json
		$salida=$this->opcionesSelectJson("select id, nombre from misGiros");
		return($salida);

	}

	function borrarGiro($id){
	// Copiar el giro y devolver opciones json
		$sql="DELETE FROM misGiros WHERE id =$id";
		$this->ejecutar($sql);
	// Este llamado retornara las opciones json
		$salida=$this->opcionesSelectJson("select id, nombre from misGiros");
		return($salida);

	}
	// para la busqueda de elementos de las listas del relacionador 
	function listar_con_radio($data)
		{
		// $data lleva la sentencia sql $sql, el nombre de la tabla $tabla y $titulos(true/false)  
		/*
		Ej del lado javascript
		var data = {"sql":"select xxx from yyy","tabla":"nombre_tabla", "titulos":"true"};
		*/	

		$json = json_decode($data);
		$sql = $json->{'sql'};
		$tabla=$json->{'tabla'};
		$titulos=$json->{'titulos'};

		$res=$this->obtener_puntero($sql);
		$nr_campos=mysqli_num_fields($res);
		$salida = "\n \t\t<table class=\"relacion\">";
		if ($titulos)
			{// usamos los nombres de los campos
			for($x=0;$x<$nr_campos;$x++)
				{
				$nombre_campo=mysqli_fetch_field_direct( $res, $x )->name;

				if (substr($nombre_campo,0,2)=='id')
					{
					$nombre_campo =	substr($nombre_campo,2,strlen($nombre_campo));
					}

				if ($nombre_campo !='id')
					{
						$header .="<th>$nombre_campo</th>";
					}

				}
			$salida .="<tr>$header $thb</tr> \n";
			}

		$n=0;

		while($fila=mysqli_fetch_array($res))
			{
			$campos='';
			$id=$fila[$x];
			for($x=1;$x<$nr_campos;$x++)
				{
					$campos .="<td>". $fila[$x]. "</td>";
				}

			$salida .="\n\t\t\t<tr>\n\t\t\t<td><input type=\"radio\" value=\"$id\" name=\"$tabla\"></td>\n\t\t\t" . $this->to_utf8($campos) . "\n\t\t\t</tr>\n";
			$n++;
			}

		$salida .="\t\t</table>\n";

		if($n == 0){
			if (strlen($this->error)>0)
				{
					return($this->error);
		
					}else{
		
					return("<div class=\"form-control\"><div class=\"panel-headding\"><h1>Aun no hay datos registrados</h1></div><div class=\"panel-body\"></div></div>");
					}
				}
	return($salida);
	}
 

}// fin clase


/* NO MODIFICAR, ES VALIDO PARA CUALQUIER FUNCION Y CODIGO */
$metodo = $_GET['metodo'];
$codigo  =  $_GET['dato'];
$miajax= new myajax;
echo $miajax->$metodo($codigo);
?>
