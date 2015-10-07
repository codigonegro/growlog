<?php
require_once("mysql.php");
/*
Esta clase solo se utiliza para manejar los llamados desde ajax.
*/

class jqAjax extends mysql
{


	function relacionar($cadena)
	{
		parse_str($cadena, $ar_data);
		$idProducto= $ar_data['idProducto'];
		$idModelo = $ar_data['idModelo'];

		$existe=0;
		$existe=$this->obtener_dato("select count(id) from rel_modelo_producto where id_producto=$idProducto and id_modelo=$idModelo");
		if($existe==0)
			{
			$this->ejecutar("insert into rel_modelo_producto(id_modelo, id_producto) values($idModelo, $idProducto)");
			}
		$actuales="";
		return ($this->mostrarRelacionados("idProducto=$idProducto&link=true"));
	}

	function mostrarRelacionados($cadena)
	{
		parse_str($cadena, $ar_data);
		
		$idProducto= $ar_data['idProducto'];
		$link=$ar_data['link'];

		// mostrar asociados
		$sql ="select r.id as id, m.nombre as modelo, r.id_producto as idP from modelo as m, rel_modelo_producto as r where r.id_producto=$idProducto and m.id=r.id_modelo";
		$res= $this->obtener_puntero($sql);
		$salida="<ul class=\"modelos\">\n";
		$n=0;
		while ($fila=mysqli_fetch_array($res))
		{
			$id=$fila[0];
			$modelo=$fila[1];
			$idP=$fila[2];

		if($link=='true'){
			
			$salida .="<li><a onClick=\"borrarModelo($id, $idP);\">$modelo</a></li>\n";
			 
			}else{
			
			$salida .="<li>$modelo </li>\n";
			
			}

			$n++;
		}
		
		$salida.="</ul>\n";
		
		if ($n==0){ $salida ="<p>No tiene modelos</p>";}

		return($salida);
	}

	function borrarRelacion($cadena)
	{
		parse_str($cadena, $ar_data);
		
		$idRelacion = $ar_data['idRel'];
		$idProducto = $ar_data['idP'];

		$sql="delete from rel_modelo_producto where id=$idRelacion";
		if($this->ejecutar($sql)){
		return($this->mostrarRelacionados("idProducto=$idProducto&link=true"));
		}
		return("error en $sql");
	}
	
	// para la busqueda de elementos de las listas del relacionador 
	function listarRadio($cadena)
		{
		// $data lleva la sentencia sql $sql, el nombre de la tabla $tabla y $titulos(true/false)  
		/*
		Ej del lado javascript
		var data = {"sql":"select xxx from yyy","tabla":"nombre_tabla", "titulos":"true"};
		*/	
		parse_str($cadena, $ar_data);
		
		$sql = $ar_data['sql'];
		
		$tabla=$ar_data['tabla'];
		
		$titulos=$ar_data['titulos'];

		$link=$ar_data['link'];

		$res=$this->obtener_puntero($sql);

		if (! $res){
			return ("error en: <br> $sql");
		}

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

			$id=$fila[0];

			for($x=1;$x<$nr_campos;$x++)
				{
					$campos .="<td>". $fila[$x]. "</td>";
				}

			$onclick="";


				if($tabla=='producto')
				{
				// mostramos el radio con la funcion de buscqueda de modelos relacionados
				$onclick=" onClick=\"\$id_producto=$id;mostrar($id);\" ";
					
				}else{
				$onclick=" onClick=\"\$id_modelo=$id;\"";
				// modelos
				}

			// solo mostramos el radio con el seteo del id del modelo
			$salida .="\n\t\t\t<tr>\n\t\t\t<td><input type=\"radio\" value=\"$id\" name=\"$tabla\" $onclick ></td>\n\t\t\t" . $this->to_utf8($campos) . "\n\t\t\t</tr>\n";

			$n++;
			}

		$salida .="\t\t</table>\n";
		// debug
		//$salida .="<br> $sql";
		// si no hay resultados
		if($n == 0){
				return("Lo siento, no se encuentra :-(");
				}

	return($salida);
	}
 
}// fin clase

/* NO MODIFICAR, ES VALIDO PARA CUALQUIER FUNCION Y CODIGO */

$data  =  file_get_contents("php://input");


$cadena =  urldecode($data);

parse_str($cadena, $ar_data);;
	
$metodo =	$ar_data['metodo'];

$jq = new jqAjax;

echo $jq->$metodo($cadena);
?>
