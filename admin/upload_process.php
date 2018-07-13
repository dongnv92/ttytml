<?php
/**
 * Created by PhpStorm.
 * User: nguye
 * Date: 06/06/2018
 * Time: 22:46
 */
require_once '../includes/core.php';
require_once '../includes/class.uploader.php';
$uploader = new Uploader();

$year   = date("Y");
$month  = date("m");
$path   = '../files/upload/';
$filename = $path.$year;
$filename2 = $path.$year."/".$month.'/';

if(file_exists($filename)){
    if(file_exists($filename2)==false){
        mkdir($filename2,0777);
    }
}else{
    mkdir($filename,0777);
    mkdir($filename2,0777);
}

$data = $uploader->upload($_FILES['files'], array(
    'limit'         => 10, //Maximum Limit of files. {null, Number}
    'maxSize'       => 10, //Maximum Size of files {null, Number(in MB's)}
    'extensions'    => null, //Whitelist for file extension. {null, Array(ex: array('jpg', 'png'))}
    'required'      => false, //Minimum one file is required for upload {Boolean}
    'uploadDir'     => $filename2, //Upload directory {String}
    'title'         => array('auto', 10), //New file name {null, String, Array} *please read documentation in README.md
    'removeFiles'   => true, //Enable file exclusion {Boolean(extra for jQuery.filer), String($_POST field name containing json data with file names)}
    'replace'       => false, //Replace the file if it already exists  {Boolean}
    'perms'         => null, //Uploaded file permisions {null, Number}
    'onCheck'       => null, //A callback function name to be called by checking a file for errors (must return an array) | ($file) | Callback
    'onError'       => null, //A callback function name to be called if an error occured (must return an array) | ($errors, $file) | Callback
    'onSuccess'     => null, //A callback function name to be called if all files were successfully uploaded | ($files, $metas) | Callback
    'onUpload'      => null, //A callback function name to be called if all files were successfully uploaded (must return an array) | ($file) | Callback
    'onComplete'    => null, //A callback function name to be called when upload is complete | ($file) | Callback
    'onRemove'      => 'onFilesRemoveCallback' //A callback function name to be called by removing files (must return an array) | ($removed_files) | Callback
));

if($data['isComplete']){
    $files = $data['data'];
    echo json_encode($files['metas'][0]['name']);
    foreach($files['metas'] as $a){
        $file_url = $filename2.$a['name'];
        $file_url = str_replace('../','', $file_url);
        insertGlobal('dong_files', array(
            'files_name'    => $a['name'],
            'files_url'     => $file_url,
            'files_value'   => 0,
            'files_type'    => 'upload',
            'files_users'   => $user_id,
            'files_time'    => _CONFIG_TIME
        ));
    }
}