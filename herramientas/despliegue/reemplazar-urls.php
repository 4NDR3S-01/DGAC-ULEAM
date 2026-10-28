<?php
/**
 * Reemplaza URLs de desarrollo en una base de WordPress, respetando los datos serializados.
 *
 * Uso (solo sobre una copia local, nunca sobre la base de producción):
 *   MYSQL_PWD=<clave> php reemplazar-urls.php <host> <puerto> <usuario> <base> <nueva-url> [--aplicar]
 *
 * La clave va en la variable de entorno MYSQL_PWD, no en los argumentos (no se ve en ps).
 * Sin --aplicar solo cuenta. --aplicar se rechaza si el host no es local.
 * Las URLs a sustituir están en $ORIGENES.
 */

if ( PHP_SAPI !== 'cli' ) {
	exit( 1 );
}

$ORIGENES = array(
	'http://localhost:8000',
	'http://10.7.25.51:8000',
	'https://realize-incident-geographic-victor.trycloudflare.com',
);

// Orígenes extra (túneles de desarrollo nuevos), separados por comas: URLS_ORIGEN_EXTRA=https://x.trycloudflare.com
foreach ( array_filter( array_map( 'trim', explode( ',', (string) getenv( 'URLS_ORIGEN_EXTRA' ) ) ) ) as $extra ) {
	if ( preg_match( '#^https?://[A-Za-z0-9.-]+(:[0-9]+)?$#', $extra ) ) {
		$ORIGENES[] = $extra;
	}
}

if ( $argc < 6 ) {
	fwrite( STDERR, "Uso: MYSQL_PWD=<clave> php reemplazar-urls.php <host> <puerto> <usuario> <base> <nueva-url> [--aplicar]\n" );
	exit( 2 );
}

list( , $host, $puerto, $usuario, $base, $nueva ) = $argv;
$clave   = (string) getenv( 'MYSQL_PWD' );
$aplicar = in_array( '--aplicar', $argv, true );
$nueva   = rtrim( $nueva, '/' );

// Protección: nunca escribir en una base remota (p. ej. la de producción o la del compañero).
if ( $aplicar && ! in_array( $host, array( '127.0.0.1', 'localhost' ), true ) ) {
	fwrite( STDERR, "✘ --aplicar solo se permite contra un host local (recibido: $host)\n" );
	exit( 6 );
}

/**
 * Variantes de cada origen: normal y con barras escapadas (\/), como en JSON de bloques.
 */
function variantes( array $origenes ) {
	$v = array();
	foreach ( $origenes as $o ) {
		$v[] = $o;
		$v[] = str_replace( '/', '\/', $o );
	}
	return $v;
}

/**
 * Sustituye en un valor, reserializando si es serializado (así no se rompen las longitudes).
 */
function reemplazar_valor( $valor, array $de, array $a ) {
	if ( is_string( $valor ) ) {
		if ( ! preg_match( '/^(a|O|s|i|b|d|N):/', $valor ) ) {
			return str_replace( $de, $a, $valor );
		}
		$datos = @unserialize( $valor, array( 'allowed_classes' => false ) );
		if ( false === $datos && 'b:0;' !== $valor ) {
			return str_replace( $de, $a, $valor ); // No serializado de verdad: texto plano.
		}
		if ( is_object( $datos ) ) {
			return str_replace( $de, $a, $valor ); // Objetos: no se reserializan; se revisa a mano.
		}
		return serialize( reemplazar_valor( $datos, $de, $a ) );
	}
	if ( is_array( $valor ) ) {
		foreach ( $valor as $k => $v ) {
			$valor[ $k ] = reemplazar_valor( $v, $de, $a );
		}
	}
	return $valor;
}

mysqli_report( MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT ); // Errores de SQL como excepciones: no se sigue con datos a medias.
$mysqli = new mysqli( $host, $usuario, $clave, $base, (int) $puerto );
$mysqli->set_charset( 'utf8mb4' );

$de = variantes( $ORIGENES );
$a  = array();
foreach ( $de as $o ) {
	$a[] = str_contains( $o, '\/' ) ? str_replace( '/', '\/', $nueva ) : $nueva; // Mismo orden que $de.
}

/**
 * Patrón LIKE literal para $o (la barra invertida es escape en LIKE y en SQL).
 */
function patron_like( mysqli $mysqli, $o ) {
	$like = str_replace( array( '\\', '%', '_' ), array( '\\\\', '\%', '\_' ), $o );
	return "'%" . $mysqli->real_escape_string( $like ) . "%'";
}

$tablas = $mysqli->query( "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = '" . $mysqli->real_escape_string( $base ) . "' AND TABLE_NAME LIKE 'wp\\_%'" );
$total  = 0;

while ( $t = $tablas->fetch_row() ) {
	$tabla = $t[0];

	$cols = array();
	$res  = $mysqli->query( "SHOW COLUMNS FROM `$tabla`" );
	while ( $c = $res->fetch_assoc() ) {
		// guid es un identificador (varchar 255) que WordPress no usa para enlazar: no se toca.
		if ( preg_match( '/char|text/i', $c['Type'] ) && 'guid' !== $c['Field'] ) {
			$cols[] = $c['Field'];
		}
	}
	if ( ! $cols ) {
		continue;
	}

	$pk = array();
	$res = $mysqli->query( "SHOW KEYS FROM `$tabla` WHERE Key_name = 'PRIMARY'" );
	while ( $k = $res->fetch_assoc() ) {
		$pk[] = $k['Column_name'];
	}
	if ( ! $pk ) {
		continue; // Sin clave primaria no se puede actualizar de forma segura.
	}

	$where = array();
	foreach ( $cols as $col ) {
		foreach ( $de as $o ) {
			$where[] = "`$col` LIKE " . patron_like( $mysqli, $o );
		}
	}
	$filas = $mysqli->query( "SELECT `" . implode( '`,`', array_merge( $pk, $cols ) ) . "` FROM `$tabla` WHERE " . implode( ' OR ', $where ) );

	while ( $fila = $filas->fetch_assoc() ) {
		$cambios = array();
		foreach ( $cols as $col ) {
			$orig = $fila[ $col ];
			if ( null === $orig ) {
				continue;
			}
			$nuevo = reemplazar_valor( $orig, $de, $a );
			if ( $nuevo !== $orig ) {
				$cambios[ $col ] = $nuevo;
			}
		}
		if ( ! $cambios ) {
			continue;
		}
		$total++;
		if ( ! $aplicar ) {
			continue;
		}
		$sets = array();
		foreach ( $cambios as $col => $val ) {
			$sets[] = "`$col` = '" . $mysqli->real_escape_string( $val ) . "'";
		}
		$cond = array();
		foreach ( $pk as $p ) {
			$cond[] = "`$p` = '" . $mysqli->real_escape_string( $fila[ $p ] ) . "'";
		}
		if ( ! $mysqli->query( "UPDATE `$tabla` SET " . implode( ', ', $sets ) . ' WHERE ' . implode( ' AND ', $cond ) ) ) {
			fwrite( STDERR, "Error en $tabla: " . $mysqli->error . "\n" );
			exit( 3 );
		}
	}
}

// guid no enlaza nada: WordPress lo genera como base/?p=ID, así que se regenera igual (no hay límite de 255 que romper).
$guid_where = array();
foreach ( $ORIGENES as $o ) {
	$guid_where[] = "guid LIKE " . patron_like( $mysqli, $o );
}
$guid_where = implode( ' OR ', $guid_where );
if ( $aplicar ) {
	$mysqli->query( "UPDATE wp_posts SET guid = CONCAT('" . $mysqli->real_escape_string( $nueva ) . "/?p=', ID) WHERE $guid_where" );
	$total += $mysqli->affected_rows;
} else {
	$total += (int) $mysqli->query( "SELECT COUNT(*) FROM wp_posts WHERE $guid_where" )->fetch_row()[0];
}

echo ( $aplicar ? 'Filas actualizadas' : 'Filas que cambiarían' ) . ": $total\n";

// Autocomprobación: no deben quedar orígenes.
$quedan = 0;
$res = $mysqli->query( "SELECT information_schema.TABLES.TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = '" . $mysqli->real_escape_string( $base ) . "' AND TABLE_NAME LIKE 'wp\\_%'" );
while ( $t = $res->fetch_row() ) {
	$cols = $mysqli->query( "SHOW COLUMNS FROM `{$t[0]}`" );
	while ( $c = $cols->fetch_assoc() ) {
		if ( ! preg_match( '/char|text/i', $c['Type'] ) ) {
			continue;
		}
		foreach ( $de as $o ) {
			$n = $mysqli->query( "SELECT COUNT(*) FROM `{$t[0]}` WHERE `{$c['Field']}` LIKE " . patron_like( $mysqli, $o ) )->fetch_row()[0];
			$quedan += (int) $n;
		}
	}
}
echo "Ocurrencias de orígenes que quedan: $quedan\n";
exit( $quedan && $aplicar ? 4 : 0 );
