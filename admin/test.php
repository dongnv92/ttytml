<?php
/**
 * Created by PhpStorm.
 * User: DONG
 * Date: 13/07/2018
 * Time: 17:02
 */
$css_plus   = array('app-assets/css/chosen.css');
$js_plus    = array('app-assets/js/chosen.jquery.js','app-assets/js/prism.js','app-assets/js/init.js');
require_once 'header.php';
?>
<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header"><h4 class="card-text">Test Code</h4> </div>
            <div class="card-body">
                <?php
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
                ?>
            </div>
        </div>
    </div>
</div>
<?php
require_once 'footer.php';