<?php
/**
 * Created by PhpStorm.
 * User: nguye
 * Date: 25/03/2018
 * Time: 18:23
 */
require_once "../includes/core.php";
if(!$user_id){
    header('location:'._URL_LOGIN);
}
$js_plus        = array('app-assets/js/scripts/tinymce/js/tinymce/tinymce.min.js');
$css_plus       = array('app-assets/vendors/css/forms/icheck/icheck.css','app-assets/css/plugins/forms/checkboxes-radios.css');
$active_menu = 'settings';
// Check Roles
if(!checkRole('setting')){
    $admin_title    = $lang['error_access'];
    require_once 'header.php';
    echo getAdminPanelError(array('header' => $lang['label_notice'], 'message' => $lang['error_no_accep']));
    require_once 'footer.php';
    exit();
}

switch ($act){
    case 'general':
        if($submit){
            $url_home           = isset($_POST['url_home'])         ? trim($_POST['url_home'])          : '';
            $index_title        = isset($_POST['index_title'])      ? trim($_POST['index_title'])       : '';
            $category_index_1   = isset($_POST['category_index_1']) ? trim($_POST['category_index_1'])  : '';
            $category_index_2   = isset($_POST['category_index_2']) ? trim($_POST['category_index_2'])  : '';
            $category_index_3   = isset($_POST['category_index_3']) ? trim($_POST['category_index_3'])  : '';
            $category_index_4   = isset($_POST['category_index_4']) ? trim($_POST['category_index_4'])  : '';
            $category_index_5   = isset($_POST['category_index_5']) ? trim($_POST['category_index_5'])  : '';
            $block_1            = isset($_POST['block_1'])          ? trim($_POST['block_1'])           : '';
            $block_2            = isset($_POST['block_2'])          ? trim($_POST['block_2'])           : '';
            $block_3            = isset($_POST['block_3'])          ? trim($_POST['block_3'])           : '';
            $block_4            = isset($_POST['block_4'])          ? trim($_POST['block_4'])           : '';
            $block_5            = isset($_POST['block_5'])          ? trim($_POST['block_5'])           : '';
            $block_6            = isset($_POST['block_6'])          ? trim($_POST['block_6'])           : '';

            updateGlobal('dong_config', array('config_value' => $url_home), array('config_name' => 'url_home'));
            updateGlobal('dong_config', array('config_value' => $index_title), array('config_name' => 'index_title'));
            updateGlobal('dong_config', array('config_value' => $category_index_1), array('config_name' => 'category_index_1'));
            updateGlobal('dong_config', array('config_value' => $category_index_2), array('config_name' => 'category_index_2'));
            updateGlobal('dong_config', array('config_value' => $category_index_3), array('config_name' => 'category_index_3'));
            updateGlobal('dong_config', array('config_value' => $category_index_4), array('config_name' => 'category_index_4'));
            updateGlobal('dong_config', array('config_value' => $category_index_5), array('config_name' => 'category_index_5'));
            updateGlobal('dong_config', array('config_value' => $block_1), array('config_name' => 'block_1'));
            updateGlobal('dong_config', array('config_value' => $block_2), array('config_name' => 'block_2'));
            updateGlobal('dong_config', array('config_value' => $block_3), array('config_name' => 'block_3'));
            updateGlobal('dong_config', array('config_value' => $block_4), array('config_name' => 'block_4'));
            updateGlobal('dong_config', array('config_value' => $block_5), array('config_name' => 'block_5'));
            updateGlobal('dong_config', array('config_value' => $block_6), array('config_name' => 'block_6'));

            // SET CONFIG
            $query_config = mysqli_query($db_connect, "SELECT * FROM `dong_config`;");
            $table_config = array();
            while ($table_config_res = mysqli_fetch_array($query_config)) $table_config[$table_config_res[1]] = $table_config_res[2];
            mysqli_free_result($query_config);

        }

        $admin_title = $lang['settings_general'];
        require_once 'header.php';
        ?>
        <form method="post" action="">
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header"><h4 class="card-title"> <?php echo $lang['settings_general'];?></h4></div>
                        <div class="card-body">
                            <ul class="nav nav-tabs nav-iconfall nav-justified">
                                <li class="nav-item"><a href="#pill1" data-toggle="tab">
                                    <a class="nav-link active" id="activeIcon32-tab1" data-toggle="tab" href="#one" aria-controls="activeIcon32" aria-expanded="true"><i class="ft-heart"></i> Cài đặt chung</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="activeIcon32-tab1" data-toggle="tab" href="#tab1" aria-controls="activeIcon32" aria-expanded="true"><i class="ft-heart"></i> Cài đặt trang chủ</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="activeIcon32-tab1" data-toggle="tab" href="#tab2" aria-controls="activeIcon32" aria-expanded="true"><i class="ft-heart"></i> Cài đặt Block</a>
                                </li>
                            </ul>
                            <div class="tab-content px-1 pt-1">
                                <div role="tabpanel" class="tab-pane active" id="one" aria-labelledby="activeIcon32-tab1" aria-expanded="true">
                                    <?php echo inputFormText(array('name' => 'url_home', 'value' => $table_config['url_home'], 'require' => TRUE, 'label' => $lang['settings_url_home'], 'error' => $error['url_home']));?>
                                    <?php echo inputFormText(array('name' => 'index_title', 'value' => $table_config['index_title'], 'require' => TRUE, 'label' => $lang['settings_title_home'], 'error' => $error['index_title']));?>
                                </div>
                                <div class="tab-pane" id="tab1" role="tabpanel" aria-labelledby="tab1" aria-expanded="false">
                                    <fieldset class="form-group">
                                        <p>Chọn chuyên mục 1</p>
                                        <select name="category_index_1" class="form-control">
                                            <?php
                                            $data = getGlobalAll('dong_category', array('category_type' => 'post'));
                                            showCategories(array('data' => $data, 'type' => 'select', 'form_value' => 'id', 'form_text' => 'category_name', 'selected' => $table_config['category_index_1']))
                                            ?>
                                        </select>
                                    </fieldset>
                                    <fieldset class="form-group">
                                        <p>Chọn chuyên mục 2</p>
                                        <select name="category_index_2" class="form-control">
                                            <?php
                                            $data = getGlobalAll('dong_category', array('category_type' => 'post'));
                                            showCategories(array('data' => $data, 'type' => 'select', 'form_value' => 'id', 'form_text' => 'category_name', 'selected' => $table_config['category_index_2']))
                                            ?>
                                        </select>
                                    </fieldset>
                                    <fieldset class="form-group">
                                        <p>Chọn chuyên mục 3</p>
                                        <select name="category_index_3" class="form-control">
                                            <?php
                                            $data = getGlobalAll('dong_category', array('category_type' => 'post'));
                                            showCategories(array('data' => $data, 'type' => 'select', 'form_value' => 'id', 'form_text' => 'category_name', 'selected' => $table_config['category_index_3']))
                                            ?>
                                        </select>
                                    </fieldset>
                                    <fieldset class="form-group">
                                        <p>Chọn chuyên mục 4</p>
                                        <select name="category_index_4" class="form-control">
                                            <?php
                                            $data = getGlobalAll('dong_category', array('category_type' => 'post'));
                                            showCategories(array('data' => $data, 'type' => 'select', 'form_value' => 'id', 'form_text' => 'category_name', 'selected' => $table_config['category_index_4']))
                                            ?>
                                        </select>
                                    </fieldset>
                                    <fieldset class="form-group">
                                        <p>Chọn chuyên mục 5</p>
                                        <select name="category_index_5" class="form-control">
                                            <?php
                                            $data = getGlobalAll('dong_category', array('category_type' => 'post'));
                                            showCategories(array('data' => $data, 'type' => 'select', 'form_value' => 'id', 'form_text' => 'category_name', 'selected' => $table_config['category_index_5']))
                                            ?>
                                        </select>
                                    </fieldset>
                                </div>
                                <div class="tab-pane" id="tab2" role="tabpanel" aria-labelledby="tab1" aria-expanded="false">
                                    <?php echo inputFormTextarea(array('label' => 'Block 1', 'name' => 'block_1', 'value' => $table_config['block_1'], 'error' => ''));?>
                                    <?php echo inputFormTextarea(array('label' => 'Block 2', 'name' => 'block_2', 'value' => $table_config['block_2'], 'error' => ''));?>
                                    <!--<script>CKEDITOR.replace( 'block_2' );</script>-->
                                    <?php echo inputFormTextarea(array('label' => 'Block 3', 'name' => 'block_3', 'value' => $table_config['block_3'], 'error' => ''));?>
                                    <!--<script>CKEDITOR.replace( 'block_3' );</script>-->
                                    <?php echo inputFormTextarea(array('label' => 'Block 4', 'name' => 'block_4', 'value' => $table_config['block_4'], 'error' => ''));?>
                                    <!--<script>CKEDITOR.replace( 'block_4' );</script>-->
                                    <?php echo inputFormTextarea(array('label' => 'Block 5', 'name' => 'block_5', 'value' => $table_config['block_5'], 'error' => ''));?>
                                    <!--<script>CKEDITOR.replace( 'block_5' );</script>-->
                                    <?php echo inputFormTextarea(array('label' => 'Block 6', 'name' => 'block_6', 'value' => $table_config['block_6'], 'error' => ''));?>
                                    <!--<script>CKEDITOR.replace( 'block_6' );</script>-->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header"><h4 class="card-title"><?php echo $lang['settings_general'];?></h4> </div>
                        <div class="card-body">
                            <p class="text-center"> <input type="submit" name="submit" class="btn btn-outline-blue round" value="<?php echo $lang['label_update'];?>"></p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <?php
        break;
    case 'role':
        $roles = getGlobal('dong_category', array('id' => $id));
        $value = unserialize($roles['category_info']);
        if($submit){
            $role = array();
            $role['post_add']           = $_POST['post_add']            ? $_POST['post_add']            : 0;
            $role['post_edit']          = $_POST['post_edit']           ? $_POST['post_edit']           : 0;
            $role['post_del']           = $_POST['post_del']            ? $_POST['post_del']            : 0;
            $role['setting']            = $_POST['setting']             ? $_POST['setting']             : 0;
            $role['category_post']      = $_POST['category_post']       ? $_POST['category_post']       : 0;
            $role['users']              = $_POST['users']               ? $_POST['users']               : 0;
            $role['users_group']        = $_POST['users_group']         ? $_POST['users_group']         : 0;
            $role['users_add']          = $_POST['users_add']           ? $_POST['users_add']           : 0;
            $role['users_detail']       = $_POST['users_detail']     ? $_POST['users_detail']           : 0;
            $role['users_edit_del']     = $_POST['users_edit_del']      ? $_POST['users_edit_del']      : 0;
            $role['post_add_approval']  = $_POST['post_add_approval']   ? $_POST['post_add_approval']   : 0;
            $role['report']             = $_POST['report']              ? $_POST['report']              : 0;
            $role['category_room']      = $_POST['category_room']       ? $_POST['category_room']       : 0;
            $role['category_role']      = $_POST['category_role']       ? $_POST['category_role']       : 0;
            $role = serialize($role);
            updateGlobal('dong_category', array('category_info' => $role), array('id' => $roles['id']));
            $roles = getGlobal('dong_category', array('id' => $id));
            $value = unserialize($roles['category_info']);
        }
        $admin_title = $lang['settings_role'];
        require_once 'header.php';
        ?>
        <form action="" method="post">
            <div class="row">
                <div class="col-md-8">
                    <div class="card"> <!--Content-->
                        <div class="card-header"><h4 class="card-title"><?php echo $lang['settings_role'].' - '.$roles['category_name'];?></h4></div>
                        <div class="card-body">
                            <?php
                            if(!$id){
                                echo '<div class="text-center">'. $lang['settings_role_no_id'] .'</div>';
                            }else{
                                ?>
                                    <label><input class="icheckbox_flat-blue" <?php echo $value['post_add'] == 1 ? 'checked ' : ''?> type="checkbox" name="post_add" value="1"> Quyền đăng bài mới</label><hr>
                                    <label><input class="icheckbox_flat-blue" <?php echo $value['post_add_approval'] == 1 ? 'checked ' : ''?> type="checkbox" name="post_add_approval" value="1"> Quyền đăng bài mới không cần duyệt</label><hr>
                                    <label><input class="icheckbox_flat-blue" <?php echo $value['post_edit'] == 1 ? 'checked ' : ''?>  type="checkbox" name="post_edit" value="1"> Quyền sửa bài viết</label><hr>
                                    <label><input class="icheckbox_flat-blue" <?php echo $value['post_del'] == 1 ? 'checked ' : ''?>  type="checkbox" name="post_del" value="1"> Quyền xóa bài viết</label><hr>
                                    <label><input class="icheckbox_flat-blue" <?php echo $value['category_post'] == 1 ? 'checked ' : ''?> type="checkbox" name="category_post" value="1"> Quyền chỉnh sửa chuyên mục bài viết</label><hr>
                                    <label><input class="icheckbox_flat-blue" <?php echo $value['setting'] == 1 ? 'checked ' : ''?>  type="checkbox" name="setting" value="1"> Quyền cài đặt</label><hr>
                                    <label><input class="icheckbox_flat-blue" <?php echo $value['users'] == 1 ? 'checked ' : ''?>  type="checkbox" name="users" value="1"> Xem danh sách thành viên</label><hr>
                                    <label><input class="icheckbox_flat-blue" <?php echo $value['users_add'] == 1 ? 'checked ' : ''?>  type="checkbox" name="users_add" value="1"> Quyền thêm thành viên</label><hr>
                                    <label><input class="icheckbox_flat-blue" <?php echo $value['users_edit_del'] == 1 ? 'checked ' : ''?>  type="checkbox" name="users_edit_del" value="1"> Quyền sửa, xóa thành viên</label><hr>
                                    <label><input class="icheckbox_flat-blue" <?php echo $value['users_group'] == 1 ? 'checked ' : ''?>  type="checkbox" name="users_group" value="1"> Quản trị nhóm thành viên</label><hr>
                                    <label><input class="icheckbox_flat-blue" <?php echo $value['users_detail'] == 1 ? 'checked ' : ''?>  type="checkbox" name="users_detail" value="1"> Xem trang cá nhân thành viên khác</label><hr>
                                    <label><input class="icheckbox_flat-blue" <?php echo $value['category_room'] == 1 ? 'checked ' : ''?>  type="checkbox" name="category_room" value="1"> Quản trị phòng ban</label><hr>
                                    <label><input class="icheckbox_flat-blue" <?php echo $value['category_role'] == 1 ? 'checked ' : ''?>  type="checkbox" name="category_role" value="1"> Quản trị chức vụ</label><hr>
                                    <label><input class="icheckbox_flat-blue" <?php echo $value['report'] == 1 ? 'checked ' : ''?>  type="checkbox" name="report" value="1"> Xem và xuất công việc</label><hr>
                            <?php }?>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header"><h4 class="card-title"><?php echo $lang['settings_role'];?></h4> </div>
                        <div class="card-body">
                            <input type="submit" name="submit" class="btn btn-outline-blue round" value="<?php echo $lang['settings_role'];?>">
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header"><h4 class="card-title"><?php echo $lang['settings_role_choose'];?></h4></div>
                        <div class="card-body">
                            <?php
                            $data_user = getGlobalAll('dong_category', array('category_type' => 'role'));
                            $table_data     = array();
                            $table_header   = array('');
                            foreach ($data_user AS $data_users){
                                array_push($table_data, array('<a href="'. _URL_ADMIN .'/settings.php?act='. $act .'&id='. $data_users['id'] .'">'.$data_users['category_name'].'</a>'));
                            }
                            echo getDataTable($table_header, $table_data);
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <?php
        break;
}
require_once 'footer.php';
?>


