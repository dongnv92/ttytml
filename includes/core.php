<?php
/**
 * Created by PhpStorm.
 * User: nguye
 * Date: 05/01/2018
 * Time: 23:04
 */
session_start();
error_reporting(0);
date_default_timezone_set('Asia/Ho_Chi_Minh');

require_once 'db.php';
require_once 'function_system.php';
require_once 'function_view.php';
require_once 'lang.php';

/** Manager Session, Cookie User */
if ($_COOKIE['user'] && $_COOKIE['pass'])
{
    $user_id = ($_COOKIE['user']);
    $user_pass = $_COOKIE['pass'];
    $_SESSION['user'] = $user_id;
    $_SESSION['pass'] = $user_pass;
}
$user_id 	= $_SESSION['user'] ? $_SESSION['user'] : '';
$user_pass 	= $_SESSION['pass'] ? $_SESSION['pass'] : '';

/** Check user and setting user  */
if ($user_id && $user_pass)
{
    $check_login = checkGlobal('dong_users', array('users_id' => $user_id, 'users_pass' => $user_pass,'users_status' => 1));
    if($check_login > 0){
        $data_user 	= getGlobal('dong_users', array('users_id' => $user_id));
    }
    else{
        unset ($_SESSION['user']);
        unset ($_SESSION['pass']);
        setcookie('user', '');
        setcookie('pass', '');
        $user_id 	= false;
        $user_pass	= false;
    }
}

define('_TABLE_POST', 'dong_post');
define('_TABLE_CATEGORY', 'dong_category');
define('_TABLE_USERS', 'dong_users');
define('_TABLE_GROUP', 'dong_group');
define('_TABLE_TASKS', 'dong_task');
define('_TABLE_NOTIFICATION', 'dong_notification');

define('_URL_ADMIN',_URL_HOME.'/admin');
define('_URL_SEARCH',_URL_HOME.'/search');
define('_URL_LOGIN', _URL_HOME.'/login');
define('_URL_LOGOUT', _URL_HOME.'/logout');
define('_SLUG_CATEGORY', 'category');
define('_CONFIG_TIME', time());
define('_CONFIG_PAGINATION', 50);
$_SESSION["back_url"] = $_SERVER["HTTP_REFERER"] ? $_SERVER["HTTP_REFERER"] : _URL_ADMIN;
define('_URL_BACK', $_SESSION["back_url"]);
define('_BUTTON_BACK', "<button type='button' class='btn btn-outline-success round' onclick='javascript:location.href=\"". _URL_BACK ."\"'>Quay lại</button>");


// Get Parameter
$submit	= $_POST['submit'];
$id 	    = isset($_REQUEST['id']) 	    ? abs(intval($_REQUEST['id'])) 	: false;
$page 	    = isset($_REQUEST['page']) 	    ? abs(intval($_REQUEST['page'])): 1;
$act 	    = isset($_REQUEST['act']) 	    ? trim($_REQUEST['act']) 		: '';
$type 	    = isset($_REQUEST['type']) 	    ? trim($_REQUEST['type']) 		: '';
$url 	    = isset($_REQUEST['url']) 	    ? trim($_REQUEST['url']) 		: '';
$controls   = isset($_REQUEST['controls']) 	? trim($_REQUEST['controls'])   : '';
$q          = isset($_REQUEST['q']) 	    ? trim($_REQUEST['q'])          : '';