<?php
/**
 * Created by PhpStorm.
 * User: nguye
 * Date: 02/03/2018
 * Time: 20:34
 */
require_once '../includes/core.php';

if(!$user_id){
    header('location:'._URL_LOGIN);
}

header('location:'._URL_ADMIN.'/post.php');

