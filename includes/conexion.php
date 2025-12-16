<?php
$host = "localhost";                       // Nombre o dirección del servidor MySQL (en este caso, el servidor local)
$user = "root";                            // Usuario de MySQL con el que se intentará conectar
$database = "recetario";                  // Nombre de la base de datos a la que se desea acceder
$pass = "";                                // Contraseña del usuario (vacía para XAMPP/WAMP por defecto)

$conexion = mysqli_connect($host, $user, $pass, $database);  
                                            // Crea la conexión a MySQL usando las credenciales anteriores

if(!$conexion){                             // Verifica si la conexión falló
	echo "Error en la conexion ".mysqli_connect_errno();   // Muestra el código numérico del error
	echo "<br>";                             // Salto de línea para mejorar la visualización
	echo "Depuracion del error ".mysqli_connect_error();   // Muestra descripción detallada del error
	exit();                                   // Detiene la ejecución del programa
}

?>
