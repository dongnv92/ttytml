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
require_once 'class_db_mysqli.php';
require_once 'class.function.php';
$function = new ttytmlFunction();
// Đặt các giá trị hằng số cho thông tin kết nối cơ sở dữ liệu
$db     = new Database(_DB_SERVER, _DB_USER,_DB_PASS,_DB_NAME);

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
define('_TABLE_LOCAL', 'dong_local');
define('_TABLE_FILES', 'dong_files');

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
define('_URL_REFERER', $_SERVER['HTTP_REFERER'] ? $_SERVER['HTTP_REFERER'] : _URL_HOME);


// Get Parameter
$submit	= $_POST['submit'];
$id 	    = isset($_REQUEST['id']) 	    ? abs(intval($_REQUEST['id'])) 	: false;
$page 	    = isset($_REQUEST['page']) 	    ? abs(intval($_REQUEST['page'])): 1;
$act 	    = isset($_REQUEST['act']) 	    ? trim($_REQUEST['act']) 		: '';
$type 	    = isset($_REQUEST['type']) 	    ? trim($_REQUEST['type']) 		: '';
$url 	    = isset($_REQUEST['url']) 	    ? trim($_REQUEST['url']) 		: '';
$controls   = isset($_REQUEST['controls']) 	? trim($_REQUEST['controls'])   : '';
$q          = isset($_REQUEST['q']) 	    ? trim($_REQUEST['q'])          : '';
$token      = isset($_REQUEST['token']) 	? trim($_REQUEST['token'])      : '';

$time_today         = date('Y/m/d', _CONFIG_TIME);
$time_week_start    = date('Y/m/d', strtotime('monday this week', _CONFIG_TIME));
$time_week_end      = date('Y/m/d 23:59:59', strtotime('sunday this week', _CONFIG_TIME));
$time_month_start   = date('Y/m/d', strtotime('first day of this month', _CONFIG_TIME));
$time_month_end     = date('Y/m/d 23:59:59', strtotime('last day of this month', _CONFIG_TIME));
$time_year_start    = date('Y/m/d', strtotime('first day of January', _CONFIG_TIME));
$time_year_end      = date('Y/m/d 23:59:59', strtotime('last day of December', _CONFIG_TIME));