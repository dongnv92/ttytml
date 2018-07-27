<?php
require_once 'includes/core.php';
$data=json_encode(array('PostmandId'=>'2214'));
$urlGetCustomer = 'http://115.84.183.206:8099/api/DeviceReceiveLading/GetPostmans';
$curl = curl_init($urlGetCustomer);
curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
curl_setopt($curl,CURLOPT_POSTFIELDS, $data);
curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-GB; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6');

$result = curl_exec($curl);
curl_close($curl);
// Dạng object
//$arr = (json_decode($result));
echo '<pre>'.print_r($result).'</pre>';
