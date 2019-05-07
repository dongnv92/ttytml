<?php
/**
 * Created by PhpStorm.
 * User: DONG
 * Date: 06/05/2019
 * Time: 10:05
 */
require_once 'function.php';
$token          = isset($_GET['token'])     && !empty($_GET['token'])       ? $_GET['token']        : '';
$token_Blynk    = isset($_GET['tokenblynk'])&& !empty($_GET['tokenblynk'])  ? $_GET['tokenblynk']   : '';
$pin_Blynk      = isset($_GET['pinblynk'])  && !empty($_GET['pinblynk'])    ? $_GET['pinblynk']     : '';
$action         = isset($_GET['action'])    && !empty($_GET['action'])      ? $_GET['action']       : '';
$type           = isset($_GET['type'])      && !empty($_GET['type'])        ? $_GET['type']         : '';
$function       = new functionChatfuel();
$access_token   = ['dongchinh', 'chinhdong', 'dongdong', 'chinhchinh'];
if(!in_array($token, $access_token)){
    echo $function->sendText('Error: You need a token!');
    exit();
}

switch ($action){
    case 'blynk';
        switch ($type){
            case 'switch':
                if(!$token_Blynk || !$pin_Blynk){
                    echo $function->sendText('Error: You need a token Blynk or Pin Blynk!');
                    break;
                }
                var_dump($function->cURL('http://blynk-cloud.com/88b6ebc02cfb43ecbe093d4fb5ee640a/get/D0'));
                break;
            default:
                echo $function->sendText('Error: No Action For Blynk');
                break;
        }
    break;
    default:
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://blynk-cloud.com:8080/88b6ebc02cfb43ecbe093d4fb5ee640a/get/D0");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
        $headers = array();
        $headers[] = "Content-Type: application/json";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        var_dump($result);
        //echo 'Test: '.file_get_contents('http://blynk-cloud.com/88b6ebc02cfb43ecbe093d4fb5ee640a/update/D0?value=1');
        //echo $function->sendText('No Action For Action!');
        break;
}

