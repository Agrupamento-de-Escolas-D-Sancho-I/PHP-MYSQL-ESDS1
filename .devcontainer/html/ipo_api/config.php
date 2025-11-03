<?php

session_start();

/** Nome da Base de Dados*/
define('DB_NAME', 'ipo');

/** Utilizador da Base de Dados - MySQL */
define('DB_USER', 'root');

/** Palavra-passe */
define('DB_PASSWORD', 'mariadb');

/** Nome do host MySQL */
define('DB_HOST', '127.0.0.1');

/** Caminho absoluto para a pasta do sistema **/
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');
	
/** Caminho no Servidor para a pasta do web site **/
if ( !defined('BASEURL') )
	define('BASEURL', '/ipo_api/');
	
/** Caminho do ficheiro com funções da Base de dados **/
if ( !defined('DBAPI') )
    define('DBAPI', ABSPATH . 'database.php');

// Não mostrar erros
//error_reporting(0);

// Mostrar erros de execução
//error_reporting(E_ERROR | E_WARNING | E_PARSE);

// Mostrar todos os erros
error_reporting(E_ALL);

?>
