<?php
/**
 * Created by PhpStorm.
 * User: nguye
 * Date: 19/06/2018
 * Time: 21:28
 */

// Define Config Database
define('_DB_SERVER', 'localhost');
define('_DB_USER', 'root');
define('_DB_PASS', '');
define('_DB_NAME', 'ttytml');

/*define('_DB_SERVER', 'localhost');
define('_DB_USER', 'xoiduaco');
define('_DB_PASS', 'Nkthanh88...');
define('_DB_NAME', 'xoiduaco_melinh');*/

$db_connect = mysqli_connect(_DB_SERVER, _DB_USER, _DB_PASS, _DB_NAME) or die('Cant Connect To Database');
mysqli_query($db_connect,"SET NAMES 'utf8'");

// SET CONFIG
$query_config = mysqli_query($db_connect, "SELECT * FROM `dong_config`;");
$table_config = array();
while ($table_config_res = mysqli_fetch_array($query_config)) $table_config[$table_config_res[1]] = $table_config_res[2];
mysqli_free_result($query_config);

// Define URL
define('_URL_HOME', 'http://localhost/dong/ttytml');
define('_IOT', $table_config['iot']);