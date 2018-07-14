<?php
/**
 * Created by PhpStorm.
 * User: nguye
 * Date: 02/03/2018
 * Time: 20:33
 */
require_once '../includes/core.php';
    $notification       = checkGlobal('dong_notification', array('notification_to' => $user_id, 'notification_status' => 0));
    $admin_title        = isset($admin_title) ? $admin_title : 'Trung tâm y tế huyện Mê Linh';
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
    <title><?php echo $admin_title;?></title>
    <link rel="apple-touch-icon" href="app-assets/images/ico/apple-icon-120.png">
    <link rel="shortcut icon" type="image/x-icon" href="images/star.png">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Quicksand:300,400,500,700" rel="stylesheet">
    <link href="https://maxcdn.icons8.com/fonts/line-awesome/1.1/css/line-awesome.min.css" rel="stylesheet">
    <!-- BEGIN VENDOR CSS-->
    <link rel="stylesheet" type="text/css" href="app-assets/css/vendors.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/plugins/forms/wizard.css">

    <script src="app-assets/vendors/js/vendors.min.js" type="text/javascript"></script>
    <!--<script src="http://code.jquery.com/jquery-latest.min.js"></script>-->
    <script src="../includes/js/jquery.filer.min.js"></script>
    <!-- END VENDOR CSS-->
    <!-- BEGIN MODERN CSS-->
    <link rel="stylesheet" type="text/css" href="app-assets/css/app.css">
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/tables/datatable/datatables.min.css">
    <!-- END MODERN CSS-->
    <!-- BEGIN Page Level CSS-->
    <link rel="stylesheet" type="text/css" href="app-assets/css/core/menu/menu-types/vertical-menu.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/core/colors/palette-gradient.css">
    <link rel="stylesheet" type="text/css" href="../includes/css/jquery.filer.css">
    <link rel="stylesheet" type="text/css" href="../includes/css/themes/jquery.filer-dragdropbox-theme.css">
    <?php
    foreach ($css_plus AS $css){
        echo '<link rel="stylesheet" type="text/css" href="'. $css .'">';
    }
    ?>
</head>
<body class="vertical-layout vertical-menu 2-columns   menu-expanded fixed-navbar"
      data-open="click" data-menu="vertical-menu" data-col="2-columns">
<!-- fixed-top-->
<nav class="header-navbar navbar-expand-md navbar navbar-with-menu navbar-without-dd-arrow fixed-top navbar-semi-light bg-info navbar-shadow">
    <div class="navbar-wrapper">
        <div class="navbar-header">
            <ul class="nav navbar-nav flex-row">
                <li class="nav-item mobile-menu d-md-none mr-auto"><a class="nav-link nav-menu-main menu-toggle hidden-xs" href="#"><i class="ft-menu font-large-1"></i></a></li>
                <li class="nav-item">
                    <a class="navbar-brand" href="<?php echo _URL_ADMIN;?>">
                        <img class="brand-logo" alt="modern admin logo" src="images/logo.png">
                        <h3 class="brand-text">TTYT MÊ LINH</h3>
                    </a>
                </li>
                <li class="nav-item d-md-none">
                    <a class="nav-link open-navbar-container" data-toggle="collapse" data-target="#navbar-mobile"><i class="la la-ellipsis-v"></i></a>
                </li>
            </ul>
        </div>
        <div class="navbar-container content">
            <div class="collapse navbar-collapse" id="navbar-mobile">
                <ul class="nav navbar-nav mr-auto float-left">
                    <li class="nav-item d-none d-md-block"><a class="nav-link nav-menu-main menu-toggle hidden-xs" href="#"><i class="ft-menu"></i></a></li>
                    <li class="nav-item nav-search"><a class="nav-link nav-link-search" href="#"><i class="ficon ft-search"></i></a>
                        <div class="search-input">
                            <input class="input" type="text" placeholder="Tìm kiếm ...">
                        </div>
                    </li>
                </ul>
                <ul class="nav navbar-nav float-right">
                    <li class="dropdown dropdown-user nav-item">
                        <a class="dropdown-toggle nav-link dropdown-user-link" href="#" data-toggle="dropdown">
                            <span class="mr-1">Hi,<span class="user-name text-bold-700"><?php echo $data_user['users_name'];?></span></span>
                            <span class="avatar avatar-online"><img src="images/avatar.png" alt="avatar"><i></i></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right"><a class="dropdown-item" href="<?php echo _URL_ADMIN.'/users.php?act=detail&id='.$data_user['users_id'];?>"><i class="ft-user"></i> Sửa hồ sơ</a>
                            <div class="dropdown-divider"></div><a class="dropdown-item" href="<?php echo _URL_ADMIN;?>"><i class="ft-power"></i> Thoát</a>
                        </div>
                    </li>
                    <li class="dropdown dropdown-notification nav-item">
                        <a class="nav-link nav-link-label" href="#" data-toggle="dropdown">
                            <i class="ficon ft-bell"></i>
                            <?php echo $notification > 0 ? '<span class="badge badge-pill badge-default badge-danger badge-default badge-up badge-glow">'.$notification.'</span>' : '';?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-media dropdown-menu-right">
                            <li class="dropdown-menu-header">
                                <h6 class="dropdown-header m-0"><span class="grey darken-2">Thông báo</span></h6>
                                <span class="notification-tag badge badge-default badge-danger float-right m-0"></span>
                            </li>
                            <li class="scrollable-container media-list w-100">
                                <?php
                                foreach (getGlobalAll('dong_notification', array('notification_to' => $user_id), array('limit_number' => 15, 'order_by_row' => 'id', 'order_by_value' => 'DESC')) AS $notice){
                                    switch ($notice['notification_type']){
                                        case 'tasks':
                                            $notice_url     = _URL_ADMIN.'/tasks.php?act=detail&id='.$notice['notification_value'];
                                            $notice_icon    = 'la la-envelope-o';
                                            $notice_content = $lang[$notice['notification_content']];
                                            $notice_content = str_replace('{users_send}', getGlobalAll(_TABLE_USERS, array('users_id' => $notice['notification_send']), array('onecolum' => 'users_name')) , $notice_content);
                                            $notice_content = $notice['notification_status'] == 0 ? '<strong>'. $notice_content .'</strong>' : $notice_content;
                                            $notice_sub     = '<p class="notification-text font-small-3 text-muted">'. getGlobalAll(_TABLE_TASKS, array('id' => $notice['notification_value']), array('onecolum' => 'task_name')) .'</p>';
                                            break;
                                    }
                                    ?>
                                    <a href="<?php echo $notice_url;?>">
                                        <div class="media">
                                            <div class="media-left align-self-center"><i class="<?php echo $notice_icon;?>"></i></div>
                                            <div class="media-body">
                                                <h6 class="media-heading"><?php echo $notice_content;?></h6>
                                                <?php echo $notice_sub;?>
                                                <small><time class="media-meta text-muted"><?php echo getViewTime($notice['notification_time'])?></time></small>
                                            </div>
                                        </div>
                                    </a>
                                    <?php
                                }
                                ?>
                            </li>
                            <!--<li class="dropdown-menu-footer"><a class="dropdown-item text-muted text-center" href="javascript:void(0)">Read all notifications</a></li> Đọc tất cả thông báo-->
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>
<!-- ////////////////////////////////////////////////////////////////////////////-->
<div class="main-menu menu-fixed menu-light menu-accordion    menu-shadow " data-scroll-to-active="true">
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
            <li class=" navigation-header">
                <span data-i18n="nav.category.layouts">Quản lý tổng hợp</span><i class="la la-ellipsis-h ft-minus" data-toggle="tooltip" data-placement="right" data-original-title="Layouts"></i>
            </li>
            <li class=" nav-item">
                <a href="#"><i class="la la-pencil-square-o"></i><span class="menu-title" data-i18n="nav.page_layouts.main"><?php echo $lang['label_post'];?></span></a>
                <ul class="menu-content">
                    <li <?php echo ($active_menu == 'post' && (!$act || in_array($act, array('update','del')))) ? 'class="active"' : ''; ?>><a class="menu-item" href="<?php echo _URL_ADMIN;?>/post.php"><?php echo $lang['post_list'];?></a></li>
                    <?php if(checkRole('post_add')){?>
                        <li <?php echo ($active_menu == 'post' && in_array($act, array('add'))) ? 'class="active"' : ''; ?>><a class="menu-item" href="<?php echo _URL_ADMIN;?>/post.php?act=add&type=post"><?php echo $lang['post_add'];?></a></li>
                    <?php }?>
                    <?php if(checkRole('category_post')){?>
                        <li <?php echo ($active_menu == 'category' && in_array($type, array('post'))) ? 'class="active"' : ''; ?>><a class="menu-item" href="<?php echo _URL_ADMIN;?>/category.php?type=post"><?php echo $lang['label_category'];?></a></li>
                    <?php }?>
                </ul>
            </li>
            <li class=" nav-item">
                <a href="#"><i class="la la-leaf"></i><span class="menu-title" data-i18n="nav.page_layouts.main"><?php echo $lang['label_tasks'];?></span></a>
                <ul class="menu-content">
                    <li <?php echo ($active_menu == 'tasks' && in_array($act, array('', 'detail'))) ? 'class="active"' : ''; ?>><a class="menu-item" href="<?php echo _URL_ADMIN;?>/tasks.php"><?php echo $lang['tasks_manager'];?></a></li>
                    <li <?php echo ($active_menu == 'tasks' && in_array($act, array('add'))) ? 'class="active"' : ''; ?>><a class="menu-item" href="<?php echo _URL_ADMIN;?>/tasks.php?act=add"><?php echo $lang['tasks_add'];?></a></li>
                    <li <?php echo ($active_menu == 'tasks' && in_array($type, array('result'))) ? 'class="active"' : ''; ?>><a class="menu-item" href="<?php echo _URL_ADMIN;?>/tasks.php?act=result"><?php echo $lang['tasks_result'];?></a></li>
                </ul>
            </li>
            <li class=" nav-item">
                <a href="#"><i class="la la-user-secret"></i><span class="menu-title" data-i18n="nav.page_layouts.main"><?php echo $lang['label_users'];?></span></a>
                <ul class="menu-content">
                    <?php if(checkRole('users')){?>
                        <li <?php echo ($active_menu == 'users' && in_array($act, array('update', '', 'del'))) ? 'class="active"' : ''; ?>><a class="menu-item" href="<?php echo _URL_ADMIN;?>/users.php"><?php echo $lang['users_list'];?></a></li>
                    <?php }?>
                    <?php if(checkRole('users_add')){?>
                        <li <?php echo ($active_menu == 'users' && in_array($act, array('add'))) ? 'class="active"' : ''; ?>><a class="menu-item" href="<?php echo _URL_ADMIN;?>/users.php?act=add"><?php echo $lang['users_add'];?></a></li>
                    <?php }?>
                    <?php if(checkRole('users_detail')){?>
                        <li <?php echo ($active_menu == 'users' && in_array($act, array('detail'))) ? 'class="active"' : ''; ?>><a class="menu-item" href="<?php echo _URL_ADMIN;?>/users.php?act=detail">Trang cá nhân</a></li>
                    <?php }?>
                    <?php if(checkRole('users_group')){?>
                        <li <?php echo ($active_menu == 'users' && in_array($act, array('group'))) ? 'class="active"' : ''; ?>><a class="menu-item" href="<?php echo _URL_ADMIN;?>/users.php?act=group">Nhóm thành viên</a></li>
                    <?php }?>
                    <?php if(checkRole('category_room')){?>
                        <li <?php echo ($active_menu == 'category' && in_array($type, array('room'))) ? 'class="active"' : ''; ?>><a class="menu-item" href="<?php echo _URL_ADMIN.'/category.php?type=room'?>">Phòng ban</a></li>
                    <?php }?>
                    <?php if(checkRole('category_role')){?>
                        <li <?php echo ($active_menu == 'category' && in_array($type, array('role'))) ? 'class="active"' : ''; ?>><a class="menu-item" href="<?php echo _URL_ADMIN.'/category.php?type=role'?>">Chức vụ</a></li>
                    <?php }?>
                </ul>
            </li>
            <?php if(checkRole('setting')){?>
            <li class=" nav-item">
                <a href="#"><i class="la la-cog"></i><span class="menu-title" data-i18n="nav.page_layouts.main"><?php echo $lang['label_setting'];?></span></a>
                <ul class="menu-content">
                    <li <?php echo ($active_menu == 'settings' && in_array($act, array('general')) ? 'class="active"' : ''); ?>><a class="menu-item" href="<?php echo _URL_ADMIN;?>/settings.php?act=general"><?php echo $lang['settings_general'];?></a></li>
                    <li <?php echo ($active_menu == 'settings' && in_array($act, array('role')) ? 'class="active"' : ''); ?>><a class="menu-item" href="<?php echo _URL_ADMIN;?>/settings.php?act=role"><?php echo $lang['settings_role'];?></a></li>
                </ul>
            </li>
            <?php }?>
            <li class=" nav-item">
                <a href="#"><i class="la la-image"></i><span class="menu-title" data-i18n="nav.page_layouts.main">Bộ sưu tập</span></a>
                <ul class="menu-content">
                    <li <?php echo ($active_menu == 'upload' && !$act ? 'class="active"' : ''); ?>><a class="menu-item" href="<?php echo _URL_ADMIN;?>/upload.php">Danh sách tập tin</a></li>
                    <li <?php echo ($active_menu == 'upload' && in_array($act, array('upload')) ? 'class="active"' : ''); ?>><a class="menu-item" href="<?php echo _URL_ADMIN;?>/upload.php?act=upload">Upload File</a></li>
                </ul>
            </li>
            <li class=" nav-item">
                <a href="#"><i class="la la-angellist"></i><span class="menu-title" data-i18n="nav.page_layouts.main"><?php echo $lang['label_report'];?></span></a>
                <ul class="menu-content">
                    <li <?php echo ($active_menu == 'report' && !$act ? 'class="active"' : ''); ?>><a class="menu-item" href="<?php echo _URL_ADMIN;?>/report">Xem báo cáo</a></li>
                    <li <?php echo ($active_menu == 'report' && in_array($act, array('admins')) ? 'class="active"' : ''); ?>><a class="menu-item" href="<?php echo _URL_ADMIN;?>/upload.php?act=admins">Xuất báo cáo</a></li>
                </ul>
            </li>
            <li class=" nav-item"><a href="<?php echo _URL_HOME;?>"><i class="la la-home"></i><span class="menu-title">Trang Chủ</span></a></li>
            <li class=" nav-item"><a href="<?php echo _URL_LOGOUT;?>"><i class="la la-long-arrow-left"></i><span class="menu-title">Đăng xuất</span></a></li>
        </ul>
    </div>
</div>
<div class="app-content content">
    <div class="content-wrapper">