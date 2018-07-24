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
                    $query = 'SELECT * FROM `'. _TABLE_GROUP .'` INNER JOIN `'. _TABLE_TASKS .'` ON `'._TABLE_GROUP.'.group_id` = `'. _TABLE_TASKS .'.id` WHERE `'. _TABLE_GROUP .'.group_type` = "tasks" AND `'. _TABLE_GROUP .'.value` = "7"';
                    echo $query;//mysqli_num_rows(mysqli_query($db_connect, $query));
                ?>
            </div>
        </div>
    </div>
</div>
<?php
require_once 'footer.php';