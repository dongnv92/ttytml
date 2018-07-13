<?php
/**
 * Created by PhpStorm.
 * User: nguye
 * Date: 25/02/2018
 * Time: 20:21
 */
require_once('includes/core.php');
// Check Login
if($user_id){
    header("location:"._URL_ADMIN);
    exit();
}
$username 	= $_REQUEST['username'] 	? trim($_REQUEST['username']) : '';
$password 	= $_REQUEST['password'] 	? trim($_REQUEST['password']) : '';
$remember 	= $_REQUEST['remember'] 	? trim($_REQUEST['remember']) : '';

if($submit){
    $error = array();
    if(!$username || !$password){
        $error['login'] = $lang['error_empty_input'];
    }
    if(checkGlobal('dong_users', array('users_user' => $username, 'users_pass' => md5($password), 'users_status' => 1)) == 0 && checkGlobal('dong_users', array('users_email' => $username, 'users_pass' => md5($password), 'users_status' => 1)) == 0){
        $error['login'] = '<font color="red">'.$lang["login_error"].'</font><br />';
    }

    /* Login Success */
    if(!$error){
        if(checkGlobal('dong_users', array('users_email' => $username, 'users_pass' => md5($password))) > 0){
            $user = getGlobal('dong_users', array('users_email' => $username, 'users_pass' => md5($password)), '`users_id`,`users_pass`');
        }else if(checkGlobal('dong_users', array('users_user' => $username, 'users_pass' => md5($password))) > 0){
            $user = getGlobal('dong_users', array('users_user' => $username, 'users_pass' => md5($password)), '`users_id`,`users_pass`');
        }
        if($remember){
            setcookie("user", $user['users_id'], time() + 15*24*60*60);
            setcookie('pass', $user['users_pass'],time() + 15*24*60*60);
        }else{
            $_SESSION['user'] = $user['users_id'];
            $_SESSION['pass'] = $user['users_pass'];
        }
        header("location:"._URL_ADMIN);
        exit();
    }
}
?>
<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="Modern admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template with unlimited possibilities with bitcoin dashboard.">
    <meta name="keywords" content="admin template, modern admin template, dashboard template, flat admin template, responsive admin template, web app, crypto dashboard, bitcoin dashboard">
    <meta name="author" content="PIXINVENT">
    <title>Đăng nhập</title>
    <link rel="apple-touch-icon" href="admin/app-assets/images/ico/apple-icon-120.png">
    <link rel="shortcut icon" type="image/x-icon" href="images/star.png">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Quicksand:300,400,500,700"
          rel="stylesheet">
    <link href="https://maxcdn.icons8.com/fonts/line-awesome/1.1/css/line-awesome.min.css"
          rel="stylesheet">
    <!-- BEGIN VENDOR CSS-->
    <link rel="stylesheet" type="text/css" href="admin/app-assets/css/vendors.css">
    <link rel="stylesheet" type="text/css" href="admin/app-assets/vendors/css/forms/icheck/icheck.css">
    <link rel="stylesheet" type="text/css" href="admin/app-assets/vendors/css/forms/icheck/custom.css">
    <!-- END VENDOR CSS-->
    <!-- BEGIN MODERN CSS-->
    <link rel="stylesheet" type="text/css" href="admin/app-assets/css/app.css">
    <!-- END MODERN CSS-->
    <!-- BEGIN Page Level CSS-->
    <link rel="stylesheet" type="text/css" href="admin/app-assets/css/core/menu/menu-types/vertical-menu.css">
    <link rel="stylesheet" type="text/css" href="admin/app-assets/css/core/colors/palette-gradient.css">
    <link rel="stylesheet" type="text/css" href="admin/app-assets/css/pages/login-register.css">
    <!-- END Page Level CSS-->
    <!-- BEGIN Custom CSS-->
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <!-- END Custom CSS-->
</head>
<body class="vertical-layout vertical-menu 1-column   menu-expanded blank-page blank-page"
      data-open="click" data-menu="vertical-menu" data-col="1-column">
<!-- ////////////////////////////////////////////////////////////////////////////-->
<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
        </div>
        <div class="content-body">
            <section class="flexbox-container">
                <div class="col-12 d-flex align-items-center justify-content-center">
                    <div class="col-md-4 col-10 box-shadow-2 p-0">
                        <div class="card border-grey border-lighten-3 m-0">
                            <div class="card-header border-0">
                                <div class="card-title text-center">
                                    <div class="p-1">
                                        <img src="images/system/avatar.png" style="max-height: 100px" alt="branding logo">
                                    </div>
                                </div>
                                <h6 class="card-subtitle line-on-side text-muted text-center font-small-3 pt-2">
                                    <span>Đăng nhập hệ thống</span>
                                </h6>
                            </div>
                            <div class="card-content">
                                <?php
                                if($submit && $error['login']){
                                    echo '<p class="card-text text-center"><i>'.$error['login'].'</i></p>';
                                }
                                ?>
                                <div class="card-body">
                                    <form class="form-horizontal form-simple" action="" method="post">
                                        <fieldset class="form-group position-relative has-icon-left mb-0">
                                            <input type="text" class="form-control form-control-lg input-lg" name="username" value="<?php echo $username;?>" placeholder="Tên đăng nhập" required>
                                            <div class="form-control-position">
                                                <i class="ft-user"></i>
                                            </div>
                                        </fieldset>
                                        <fieldset class="form-group position-relative has-icon-left">
                                            <input type="password" class="form-control form-control-lg input-lg" name="password" placeholder="Nhập mật khẩu" required>
                                            <div class="form-control-position">
                                                <i class="la la-key"></i>
                                            </div>
                                        </fieldset>
                                        <div class="form-group row">
                                            <div class="col-md-6 col-12 text-center text-md-left">
                                                <fieldset>
                                                    <label for="remember-me"><input type="checkbox" name="remember" value="1" class="chk-remember"> Ghi nhớ đăng nhập</label>
                                                </fieldset>
                                            </div>
                                            <div class="col-md-6 col-12 text-center text-md-right"><a href="javascript:;" class="card-link">Quên mật khẩu?</a></div>
                                        </div>
                                        <input type="submit" name="submit" class="btn btn-info btn-lg btn-block" value="Đăng nhập">
                                    </form>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="">
                                    <p class="float-sm-left text-center m-0"><a href="<?php echo _URL_HOME;?>" class="card-link">TRUNG TÂM Y TẾ MÊ LINH</a></p>
                                    <p class="float-sm-right text-center m-0"><a href="<?php echo _URL_HOME;?>" class="card-link">2018</a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
<!-- ////////////////////////////////////////////////////////////////////////////-->
<!-- BEGIN VENDOR JS-->
<script src="admin/app-assets/vendors/js/vendors.min.js" type="text/javascript"></script>
<!-- BEGIN VENDOR JS-->
<!-- BEGIN PAGE VENDOR JS-->
<script src="admin/app-assets/vendors/js/forms/icheck/icheck.min.js" type="text/javascript"></script>
<script src="admin/app-assets/vendors/js/forms/validation/jqBootstrapValidation.js"
        type="text/javascript"></script>
<!-- END PAGE VENDOR JS-->
<!-- BEGIN MODERN JS-->
<script src="admin/app-assets/js/core/app-menu.js" type="text/javascript"></script>
<script src="admin/app-assets/js/core/app.js" type="text/javascript"></script>
<!-- END MODERN JS-->
<!-- BEGIN PAGE LEVEL JS-->
<script src="admin/app-assets/js/scripts/forms/form-login-register.js" type="text/javascript"></script>
<!-- END PAGE LEVEL JS-->
</body>
</html>