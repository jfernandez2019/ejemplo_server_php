<?php
include 'includes/conexion.php';                       // Incluye el archivo de conexión a la base de datos MySQL
header('Content-Type: application/json');              // Define que la respuesta será en formato JSON

$allowdResourceTypes = [ 'comida'];   // Lista de tipos de recursos permitidos

$resourceType = $_GET['resource_type'];                // Obtiene el recurso solicitado desde la URL

if( !in_array( $resourceType, $allowdResourceTypes) ){ // Verifica si el recurso está dentro de los permitidos
	
	http_response_code( 400 );	                      // Devuelve error HTTP 400 (solicitud incorrecta)
	echo json_encode(                                  // Devuelve mensaje JSON explicando el error
		[
			'error' => "$resourceType recurso desconocido"
		]	
	);
	die;                                                // Detiene la ejecución del script
}

$resourceId = array_key_exists('resource_id', $_GET)    // Verifica si se envió resource_id
              ? $_GET['resource_id'] 
              : '';

$method = $_SERVER['REQUEST_METHOD'];                  // Obtiene el método HTTP usado en la petición

switch( strtoupper($method) ) {                        // Convierte el método a mayúsculas y evalúa

/* =======================================================================================
   =                                       GET                                           =
   ======================================================================================= */
	case 'GET': 	
		if( !empty( $resourceId ) ){                   // Si se pidió un libro específico con ID

			$query = "SELECT * FROM comida WHERE id_comida = $resourceId ";  		// Consulta por ID
			$result = mysqli_query($conexion, $query);                     			// Ejecuta la consulta			
			$datos = [];                                                   			// Array donde se guardarán los resultados

			if($result){                                                   			// Si la consulta no falló
				while($reg = mysqli_fetch_array($result)){                 			// Recorre cada fila devuelta
					$datos_aux['id_comida'] 		= $reg['id_comida'];            // Extrae id_libro
					$datos_aux['nombre_comida']   	= $reg['nombre_comida'];        // Extrae nombre
					$datos_aux['autor']    			= $reg['autor'];                // Extrae autor
					$datos_aux['costo_comida']   	= $reg['costo_comida'];         // Extrae género					
					array_push($datos, $datos_aux);                        			// Agrega al arreglo final
				}				
			}

			if(!empty($datos)){                                            // Si se encontró información
				echo json_encode($datos);                                  // Devuelve los datos en JSON
				die;
			}else{                                                         // Si no se encontró el ID solicitado
				http_response_code(404);                                   // Respuesta HTTP: no encontrado
				echo json_encode([ 'error' => 'No se encontraron registros' ]);
			}
			mysqli_close($conexion);                                       // Cierra conexión
				
		}else{                                                             // Si NO se pasó ningún ID → devuelve todos los libros

			$query = "SELECT * FROM comida";                                // Consulta todo
			$result = mysqli_query($conexion, $query);			
			$datos = [];

			if($result){
				while($reg = mysqli_fetch_array($result)){
					$datos_aux['id_comida'] 	 = $reg['id_comida'];
					$datos_aux['nombre_comida']  = $reg['nombre_comida'];
					$datos_aux['autor']    		 = $reg['autor'];
					$datos_aux['costo_comida']   = $reg['costo_comida'];					
					array_push($datos, $datos_aux);
				}				
			}

			if(!empty($datos)){
				echo json_encode($datos);                                   // Respuesta JSON completa
				die;
			}else{
				http_response_code(404);                                   // Si no hay registros
				echo json_encode([ 'error' => 'No se encontraron registros' ]);
			}
			mysqli_close($conexion);
		}
		die;                                                               // Corta ejecución tras GET
	break;

/* =======================================================================================
   =                                      POST                                           =
   ======================================================================================= */
	case 'POST':

		$json 	  = file_get_contents('php://input');      // Obtiene el cuerpo RAW de la solicitud (JSON enviado por el cliente)
		$comida[] = json_decode($json);                 // Decodifica JSON a objeto PHP y lo pone en un array
		$data 	  = $comida[0];                             // Obtiene el primer elemento

		// Recupera los datos enviados por JSON
		$nombre_comida = $data->nombre_comida;                       
		$autor  	   = $data->autor;
        $costo_comida  = $data->costo_comida;
		
		if(!$conexion){                                 // Verifica que la conexión exista
			echo 'Error en la conexion';
			exit;
		}
		
		// Inserta un nuevo registro en la tabla libro
		$insert = "INSERT INTO comida(nombre_comida, autor, costo_comida) VALUES('$nombre_comida', '$autor', '$costo_comida')";
		
		$result = mysqli_query($conexion, $insert);    // Ejecuta inserción		

		if($result){                                    // Si la inserción fue exitosa
			$respuesta['status']  = true;
			$respuesta['mensaje'] = 'Registro insertado correctamente';
			echo json_encode($respuesta);
		}else{
			echo 'Error al insertar el registro';
		}

		mysqli_close($conexion);                       // Cierra conexión		
	break;

/* =======================================================================================
   =                                     DELETE                                          =
   ======================================================================================= */
	case 'DELETE':

		if( !empty( $resourceId ) ){                   // Solo se puede borrar si se envía un ID
			
			if(!$conexion){
				echo 'Error en la conexion';
				exit;
			}
		
			$query = "DELETE FROM comida WHERE id_comida = $resourceId";  // Orden de borrado
			
			$result = mysqli_query($conexion, $query);			
			if($result){
				$respuesta['status']  = true;
				$respuesta['mensaje'] = "Registro con identificador $resourceId fue eliminado exitosamente";
				echo json_encode($respuesta);
			}else{
				echo 'Error al eliminar el registro';
			}

			mysqli_close($conexion);			

	    } else {                                       // Si no se envió ID → error
			http_response_code(404);
			echo json_encode([ 'error' => 'No se encontraron registros' ]);
	    }
	
	break;

/* =======================================================================================
   =                                      PUT                                            =
   ======================================================================================= */
	case 'PUT':
	
	// Aquí debería ir la lógica para actualizar un registro existente
	// Ejemplo:  UPDATE libro SET nombre='Nuevo' WHERE id_libro=3;
		$json = file_get_contents('php://input');      // Obtiene el cuerpo RAW de la solicitud (JSON enviado por el cliente)
		$libro[] = json_decode($json);                 // Decodifica JSON a objeto PHP y lo pone en un array
		$data = $libro[0];                             // Obtiene el primer elemento

		// Recupera los datos enviados por JSON
		$nombre_comida = $data->nombre_comida;                       
		$autor  	   = $data->autor;
        $costo_comida  = $data->costo_comida;
		
		if(!$conexion){                                 // Verifica que la conexión exista
			echo 'Error en la conexion';
			exit;
		}
		
		$query = "UPDATE comida
		SET nombre = '$nombre_comida' , autor = '$autor', genero = '$costo_comida'
		WHERE id_libro = $resourceId";
		
		//ejecutar consulta
		$result = mysqli_query($conexion, $query); 
		if($result){
			$respuesta['status']  = true;
			$respuesta['mensaje'] = 'Registro fue modificado correctamente';
			echo json_encode($respuesta);
			
		}else{
			http_response_code(404);
			echo json_encode([ 'error' => 'No se encontraron registros' ]);
		}
		
		mysqli_close($conexion);                       // Cierra conexión
	break;

/* =======================================================================================
   =                                   DEFAULT                                           =
   ======================================================================================= */
	default:
		echo 'METODO NO IMPLEMENTADO';                 // Si se usa un método fuera de GET/POST/DELETE/PUT
	break;
}

?>
