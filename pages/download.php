<?php
/**
 * Created by PhpStorm.
 * User: nguye
 * Date: 13/04/2018
 * Time: 23:05
 */

require_once '../includes/core.php';
header('Content-Type: text/html; charset=utf-8');
$download   = getGlobal('dong_files', array('id' => $id));
$file       = '../'.$download['files_url'];

if (file_exists($file)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.($file).'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    readfile($file);
    exit;
}else{
    echo'error: '.$file;
}