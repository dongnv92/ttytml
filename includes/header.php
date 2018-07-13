<?php
/**
 * Created by PhpStorm.
 * User: nguye
 * Date: 23/01/2018
 * Time: 23:13
 */
require_once 'core.php';
?>
<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="description" content=""/>
    <meta name="keywords" content="Worldnews,7uptheme"/>
    <meta name="robots" content="noodp,index,follow"/>
    <meta name='revisit-after' content='1 days'/>
    <title><?php echo $header_title?></title>
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,700" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Oswald:300,400,700" rel="stylesheet"/>
    <link rel="stylesheet" type="text/css" href="<?php echo _URL_HOME.'/styles';?>/css/libs/font-awesome.min.css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo _URL_HOME.'/styles';?>/css/libs/ionicons.css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo _URL_HOME.'/styles';?>/css/libs/bootstrap.min.css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo _URL_HOME.'/styles';?>/css/libs/owl.carousel.css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo _URL_HOME.'/styles';?>/css/libs/hover.css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo _URL_HOME.'/styles';?>/color.css" media="all"/>
    <link rel="stylesheet" type="text/css" href="<?php echo _URL_HOME.'/styles';?>/theme.css" media="all"/>
    <link rel="stylesheet" type="text/css" href="<?php echo _URL_HOME.'/styles';?>/responsive.css" media="all"/>
    <link rel="stylesheet" type="text/css" href="<?php echo _URL_HOME.'/styles';?>/preload.css"/>
</head>
<body class="preload">
<div class="wrap">
    <div id="header" class="margin-b30">
        <div class="top-header">
            <div class="container">
                <div class="row">
                    <div class="col-sm-8">
                        <div class="top-left">
                            <p class="current-time main-bg"><?php echo getViewTime(_CONFIG_TIME);?></p>
                            <ul class="top-header-link color-text-link list-inline-block">
                                <li><?php echo $user_id ? '<a href="'._URL_ADMIN.'">'. $lang['admin_title'] .'</a>' : '<a href="'._URL_LOGIN.'">'. $lang['label_login'] .'</a>';?></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-4 hidden-xs">
                        <form class="newsletter-form text-right">
                            <input type="email" name="email" placeholder="Tìm kiếm"/>
                            <input class="main-bg" type="submit" value="Tìm kiếm" />
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Top Header -->
        <div class="main-header">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 col-sm-6">
                        <div class="logo">
                            <h1 class="hidden">Worldnews Wordpress Theme</h1>
                            <a href="#">
                                <img src="http://trungtamytemelinh.com/wp-content/uploads/2017/12/24474879_1754028818000829_1875522114_o.jpg" alt=""/>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Main Header -->
        <div class="white-nav">
            <div class="container">
                <div class="row">
                    <div class="col-xs-12">
                        <nav class="main-nav">
                            <ul class="list-none">
                                <li class="current-menu-item"><a href="<?php echo _URL_HOME;?>">Trang chủ</a></li>
                                <?php
                                    $cate_m = getGlobalAll('dong_category', array('category_sub' => 0, 'category_type' => 'post'));
                                    foreach ($cate_m AS $cate_mm){
                                        echo '<li class="menu-item-has-children"><a href="#">'. $cate_mm['category_name'] .'</a> <ul class="sub-menu menu-animation">';
                                        foreach (getGlobalAll('dong_category', array('category_sub' => $cate_mm['id'])) AS $cate_s){
                                            echo '<li><a href="'. getUrlCategory($cate_s['id']) .'">'. $cate_s['category_name'] .'</a></li>';
                                        }
                                        echo '</ul></li>';
                                    }
                                ?>
                            </ul>
                            <span class="ion-navicon-round oswald-font">Menu</span>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Header Nav -->
    </div>
    <!-- End Header -->
    <div id="content">
        <div class="container">
