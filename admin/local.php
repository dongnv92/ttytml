<?php
/**
 * Created by PhpStorm.
 * User: DONG
 * Date: 11/1/2018
 * Time: 9:29 AM
 */
require_once "../includes/core.php";
if(!$user_id){
    header('location:'._URL_LOGIN);
}
$active_menu = 'local';

switch ($act){
    case 'add':
        if($submit){
            $local_title 	= isset($_POST['local_title'])      ? trim($_POST['local_title'])   : '';
            $local_content  = isset($_POST['local_content'])    ? trim($_POST['local_content']) : '';
            $error          = array();
            if(!$local_title){
                $error['local_title'] = 'Bạn cần nhập tiêu đề';
            }
            if(!$local_content){
                $error['local_content'] = 'Bạn cần nhập nội dung';
            }

            if(!$error){
                $data = array(
                    'local_title'       => $local_title,
                    'local_content'     => $local_content,
                    'local_users'       => $user_id,
                    'local_star'        => 0,
                    'local_star_users'  => 0,
                    'local_status'      => 1,
                    'local_date'        => date('Y-m-d', time()),
                    'local_time'        => _CONFIG_TIME
                );
                // Thêm công việc
                $add = insertGlobal(_TABLE_TASKS, $data);
                if(!$add){
                    $admin_title    = $admin_title;
                    require_once 'header.php';
                    echo getAdminPanelError(array('header' => $lang['label_notice'], 'message' => 'Lỗi khi thêm công việc'));
                    break;
                }
            }
        }

        $css_plus       = array('app-assets/vendors/css/forms/icheck/icheck.css','app-assets/css/plugins/forms/checkboxes-radios.css','app-assets/css/chosen.css');
        $js_plus        = array('app-assets/js/scripts/tinymce/js/tinymce/tinymce.min.js','app-assets/js/chosen.jquery.js','app-assets/js/prism.js','app-assets/js/init.js');
        $admin_title    = 'Thêm kế hoạch nội bộ';
        require_once 'header.php';
        ?>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header"><h4 class="card-title"><?php echo $admin_title?></h4></div>
                                <div class="card-body">
                                    <input required type="text" value="<?php echo $local_title;?>" class="form-control round border-primary" placeholder="Tiêu Đề Kế Hoạch" name="local_title" />
                                    <?php echo inputFormTextarea(array('name' => 'local_content', 'value' => $local_content, 'error' => $error['local_content']));?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header"><h4 class="card-title"><?php echo $admin_title;?></h4> </div>
                        <div class="card-body text-center"><input type="submit" name="submit" class="btn round btn-outline-cyan" value="<?php echo $admin_title;?>"></div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title"><?php echo $lang['local_input_files']?></h4>
                        </div>
                        <div class="card-body">
                            <fieldset class="form-group"><input type="file" class="form-control-file" name="tasks_files[]" multiple="multiple"></fieldset>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <?php
        require_once 'footer.php';
        break;
}