<?php
/**
 * Created by PhpStorm.
 * User: DONG
 * Date: 13/07/2018
 * Time: 17:02
 */
require_once '../includes/core.php';
if($submit){
    $name = $_POST['name'];
    foreach ($name AS $names){
        echo $names.'<br />';
    }
    exit();
}

$css_plus   = array('app-assets/css/chosen.css');
$js_plus    = array('app-assets/js/chosen.jquery.js','app-assets/js/prism.js','app-assets/js/init.js');
require_once 'header.php';
?>
<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header"><h4 class="card-text">Test Code</h4> </div>
            <div class="card-body">
                <form action="" method="post">
                    <select name="name[]" data-placeholder="Nhập tên người nhận" multiple class="chosen-select-width form-control" tabindex="18" id="multiple-label-example">
                        <option value=""></option>
                        <option value="1">Đông</option>
                        <option value="2">Chinh</option>
                        <option value="3">Hương</option>
                        <option value="4">Mai</option>
                        <option value="5">Hoa</option>
                        <option value="6">Đồng</option>
                        <option value="7">Lâm</option>
                        <option value="8">Thăng</option>
                    </select><hr />
                    <input type="submit" name="submit" value="OK" class="btn btn-outline-blue round">
                </form>
            </div>
        </div>
    </div>
</div>
<?php
require_once 'footer.php';