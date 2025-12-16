	<?php
$matches = [];                          // Crea un arreglo vacío que almacenará los resultados de las expresiones regulares

$url = $_SERVER['REQUEST_URI'];         // Obtiene la URL completa que el cliente solicitó al servidor
$url_limpia = str_replace(              // Quita la parte fija "/ws_ucsa/route.php" de la URL para quedarte solo con la parte dinámica
    "/route.php",
    "",
    $url
);                                      

if (preg_match('/\/([^\/]+)\/([^\/]+)/', // Verifica si la URL limpia tiene la forma /tipo/id  (por ejemplo: /libros/12 )
    $url_limpia,                         // La URL sin la parte fija
    $matches                             // Guarda los fragmentos encontrados dentro del arreglo $matches
)) {	
    $_GET['resource_type'] = $matches[1]; // El primer grupo capturado es el nombre del recurso (ej: "libros")
    $_GET['resource_id']   = $matches[2]; // El segundo grupo capturado es el ID del recurso (ej: "12")
    require 'server.php';                // Incluye el archivo server.php para procesar la solicitud
	
}
elseif (preg_match('/\/([^\/]+)\/?/',     // Alternativa: si solo viene el recurso sin ID (ej: /libros )
    $url_limpia,
    $matches
)) {
    $_GET['resource_type'] = $matches[1]; // Guarda solo el tipo de recurso
    require 'server.php';                // Ejecuta el mismo archivo para procesar, sin ID

} else {                                 // Si ninguna de las expresiones coincide con el formato esperado
    error_log('No matches');             // Registra un mensaje en el log del servidor indicando que no hubo coincidencia
    http_response_code(404);             // Devuelve un código HTTP 404 (no encontrado)
}

?>
