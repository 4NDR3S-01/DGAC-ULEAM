<?php
/**
 * Vacía las claves de API del asistente (api_key, groq_key) guardadas en la opción `uleam_asistente`
 * de la base de TRABAJO, para que no viajen a producción. En producción van como constantes en wp-config.php
 * (ULEAM_GROQ_API_KEY / ULEAM_CLAUDE_API_KEY). Maneja el dato serializado sin romperlo.
 *
 * Uso: MYSQL_PWD=<clave> php quitar-claves-asistente.php <host> <puerto> <usuario> <base>
 */
if ( PHP_SAPI !== 'cli' || $argc < 5 ) {
	fwrite( STDERR, "Uso: MYSQL_PWD=<clave> php quitar-claves-asistente.php <host> <puerto> <usuario> <base>\n" );
	exit( 2 );
}
list( , $host, $puerto, $usuario, $base ) = $argv;
if ( ! in_array( $host, array( '127.0.0.1', 'localhost' ), true ) ) {
	fwrite( STDERR, "✘ Solo se permite una base local (recibido: $host)\n" );
	exit( 6 );
}
$db = new mysqli( $host, $usuario, (string) getenv( 'MYSQL_PWD' ), $base, (int) $puerto );
$db->set_charset( 'utf8mb4' );
$fila = $db->query( "SELECT option_value FROM wp_options WHERE option_name = 'uleam_asistente'" )->fetch_row();
if ( ! $fila ) {
	echo "Sin opción uleam_asistente: nada que limpiar\n";
	exit( 0 );
}
$cfg = unserialize( $fila[0], array( 'allowed_classes' => false ) );
if ( ! is_array( $cfg ) ) {
	fwrite( STDERR, "✘ uleam_asistente no es un arreglo serializado\n" );
	exit( 3 );
}
$quitadas = 0;
foreach ( array( 'api_key', 'groq_key' ) as $k ) {
	if ( ! empty( $cfg[ $k ] ) ) {
		$cfg[ $k ] = '';
		++$quitadas;
	}
}
$nuevo = serialize( $cfg );
$st    = $db->prepare( "UPDATE wp_options SET option_value = ? WHERE option_name = 'uleam_asistente'" );
$st->bind_param( 's', $nuevo );
$st->execute();
echo "Claves del asistente vaciadas: $quitadas\n";
