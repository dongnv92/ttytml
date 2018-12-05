<?php
/**
 * Created by PhpStorm.
 * User: nguye
 * Date: 06/06/2018
 * Time: 23:54
 */

require_once '../includes/core.php';

if(isset($_POST['file'])){
    $year       = date("Y");
    $month      = date("m");
    $path       = '../files/upload/';
    $filename   = $path.$year;
    $filename2  = $path.$year."/".$month.'/';
    $file       = $filename2.$_POST['file'];
    $file_check = str_replace('../', '', $file);

    if(file_exists($file)){
        $files = getGlobal('dong_files', array('files_url' => $file_check));
        if($files){
            deleteGlobal('dong_files', array('id' => $files['id']));
        }
        unlink($file);
    }
}