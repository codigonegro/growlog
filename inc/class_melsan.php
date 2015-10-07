<?php
require_once("mysql.php");
class melsan extends mysql
{
// muestra filas en bruto, sin paginacion se asume que la consulta no incluye claves foráneas
function listar_con_radio($sql, $tabla, $titulos=false)
	{
		
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
		$id=$fila[0];
		for($x=1;$x<$nr_campos;$x++)
			{
				$campos .="<td>". $fila[$x]. "</td>";
			}
		if($tabla=='id_producto')
			{
		// ostraoselradiocon la funcion de buscqueda de modelos relacionados
		$salida .="\n\t\t\t<tr>\n\t\t\t<td><input type=\"radio\" value=\"$id\" name=\"$tabla\" onClick=\"\$$tabla=$id;mostrar($id);\"></td>\n\t\t\t" . $this->to_utf8($campos) . "\n\t\t\t</tr>\n";
				
			}else{
				
		// solo mostramos el radio con el seteo del id del modelo
		$salida .="\n\t\t\t<tr>\n\t\t\t<td><input type=\"radio\" value=\"$id\" name=\"$tabla\" onClick=\"\$$tabla=$id;\"></td>\n\t\t\t" . $this->to_utf8($campos) . "\n\t\t\t</tr>\n";
		}
		$n++;
		}

	$salida .="\t\t</table>\n";

	if($n == 0){
		if (strlen($this->error)>0){
							return($this->error);
						}else{
							return("<div class=\"form-control\"><div class=\"panel-headding\"><h1>Aun no hay datos registrados</h1></div><div class=\"panel-body\"></div></div>");
						}
				}
	return($salida);
	}


}
?>