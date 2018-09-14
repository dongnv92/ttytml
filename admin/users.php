<?php
require_once "../includes/core.php";
if(!$user_id){
    header('location:'._URL_LOGIN);
}
$active_menu = 'users';
switch ($act){
    case 'delete_users_manager_room':
        $get_users  = $_GET['get_users'];
        $group      = getGlobal(_TABLE_GROUP, array('id' => $id, 'group_type' => 'users_manager_room', 'group_id' => $get_users));
            // Check GROUP
        if(!$group){
            $admin_title    = 'Xóa phòng ban';
            require_once 'header.php';
            echo getAdminPanelError(array('header' => $lang['label_notice'], 'message' => 'Phòng ban nay không có'));
            break;
        }
        if(deleteGlobal(_TABLE_GROUP, array('id' => $id))){
            header('location:'._URL_ADMIN.'/users.php?act=update&id='.$get_users);
        }else{
            $admin_title    = 'Xóa phòng ban';
            require_once 'header.php';
            echo getAdminPanelError(array('header' => $lang['label_notice'], 'message' => 'Phòng ban nay không có'));
            break;
        }
        break;
    case 'change_info':
        $users = getGlobal(_TABLE_USERS, array('users_id' => $user_id));
        if($submit) {
            $users_pass         = isset($_POST['users_pass'])       ? trim($_POST['users_pass'])        : '';
            $users_repass       = isset($_POST['users_repass'])     ? trim($_POST['users_repass'])      : '';
            $users_email        = isset($_POST['users_email'])      ? trim($_POST['users_email'])       : '';
            $users_phone        = isset($_POST['users_phone'])      ? trim($_POST['users_phone'])       : '';
            $users_name         = isset($_POST['users_name'])       ? trim($_POST['users_name'])        : '';

            // Check Error
            $error = array();
            if(($users_pass && $users_repass) && $users_pass != $users_repass){
                $error['users_pass'] = getViewError($lang['users_pass_nosame']);
            }
            if(!$users_email){
                $error['users_email'] = getViewError($lang['error_empty_this_fiel']);
            }
            if($users_email != $users['users_email'] && checkGlobal(_TABLE_USERS, array('users_email' => $users_email)) > 0){
                $error['users_name_login'] = $lang['users_email_exits'];
            }
            if(!$users_name){
                $error['users_name'] = getViewError($lang['error_empty_this_fiel']);
            }

            if(!$error){
                // Upload File
                require_once '../includes/lib/class.uploader.php';
                $uploader = new Uploader();
                $data_upload = $uploader->upload($_FILES['users_avatar'], array(
                    'limit'         => 10, //Maximum Limit of files. {null, Number}
                    'maxSize'       => 10, //Maximum Size of files {null, Number(in MB's)}
                    'extensions'    => null, //Whitelist for file extension. {null, Array(ex: array('jpg', 'png'))}
                    'required'      => false, //Minimum one file is required for upload {Boolean}
                    'uploadDir'     => '../images/users/', //Upload directory {String}
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
                if($data_upload['isComplete']){
                    $files = $data_upload['data']['files'];
                    foreach ($files AS $file){
                        $file_name = explode('/', $file);
                        $file_name = $file_name[(count($file_name) - 1)];
                    }
                }
                // Upload File
                $data = array(
                    'users_email'   => $users_email,
                    'users_phone'   => $users_phone,
                    'users_avatar'  => $file_name ? 'images/users/'.$file_name : $users['users_avatar'],
                    'users_pass'    => ($users_pass && $users_repass) && ($users_pass == $users_repass) ? md5($users_pass) : $users['users_pass'],
                    'users_name'    => $users_name
                );
                updateGlobal(_TABLE_USERS, $data, array('users_id' => $user_id));
                $users = getGlobal(_TABLE_USERS, array('users_id' => $user_id));
            }
        }
        $admin_title = $lang['users_update'];
        require_once 'header.php';
        ?>
        <form action="" class="form form-horizontal" method="post" enctype="multipart/form-data">
            <div class="row horizontal-form-layouts">
                <div class="col-md-8">
                    <div class="card"> <!--Content-->
                        <div class="card-header"><h4 class="card-title"><?php echo $lang['users_add'];?></h4></div>
                        <div class="card-body">
                            <div class="text-danger text-left"><small>* Nếu không thay đổi mật khẩu thì không cần nhập vào ô mất khẩu, nếu thay đổi mật khẩu, bạn sẽ phải đăng nhập lại.</small></div><br />
                            <?php if($submit && !$error){echo getAlert('success', 'Thông tin đã được thay đổi');} ?>
                            <div class="form-group row">
                                <label class="col-md-3 label-control" for="projectinput1">Tên đăng nhập</label>
                                <div class="col-md-9"><input type="text" class="form-control" name="" disabled value="<?php echo $users['users_user'];?>"></div>
                            </div>
                            <?php echo inputFormPassword(array('label' => $lang['label_password'], 'name' => 'users_pass', 'value' => $users_pass, 'error' => $error['users_pass'])); ?>
                            <?php echo inputFormPassword(array('label' => $lang['users_repass'], 'name' => 'users_repass', 'value' => $users_repass)); ?>
                            <?php echo inputFormText(array('type' => 'hozi', 'label' => $lang['users_email'], 'name' => 'users_email', 'value' => $users['users_email'], 'require' => TRUE, 'error' => $error['users_email'])); ?>
                            <?php echo inputFormText(array('type' => 'hozi', 'label' => $lang['users_phone'], 'name' => 'users_phone', 'value' => $users['users_phone'], 'error' => $error['users_phone'])); ?>
                            <?php echo inputFormText(array('type' => 'hozi', 'label' => $lang['users_name'], 'name' => 'users_name', 'value' => $users['users_name'], 'require' => TRUE, 'error' => $error['users_name'])); ?>
                            <div class="form-group row">
                                <label class="col-md-3 label-control">Ảnh đại diện</label>
                                <div class="col-md-9"><fieldset class="form-group"><div class="col"><input type="file" class="form-control-file" name="users_avatar"></div></fieldset></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header"><h4 class="card-title">Chỉnh sửa</h4> </div>
                        <div class="card-body">
                            <div class="form-actions text-center">
                                <input type="submit" name="submit" class="btn btn-outline-success round" value="Chỉnh sửa thông tin">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <?php
        break;
    case 'del':
        // Check Roles
        if(!checkRole('users_edit_del')){
            $admin_title    = $lang['error_access'];
            require_once 'header.php';
            echo getAdminPanelError(array('header' => $lang['label_notice'], 'message' => $lang['error_no_accep']));
            require_once 'footer.php';
            exit();
        }
        $users = getGlobal(_TABLE_USERS, array('users_id' => $id));
        if(!$users){
            header('location:'._URL_ADMIN.'/users.php');
        }

        if($submit){
            deleteGlobal(_TABLE_USERS, array('users_id' => $id));
            header('location:'._URL_ADMIN.'/users.php');
        }

        $admin_title = 'Xóa thành viên';
        require_once 'header.php';
        ?>
        <form action="" method="post">
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-header"><h4 class="card-title"><?php echo $lang['post_del'];?></h4></div>
                        <div class="card-body text-center">
                            Bạn có chắc chắn muốn xóa thành viên <strong><?php echo $users['users_name'];?></strong> không?<hr/>
                            <?php echo _BUTTON_BACK;?> <input type="submit" name="submit" class="btn btn round btn-outline-success" value="<?php echo $lang['label_del'];?>">
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <?php
        break;
    case 'detail':
        // Check Roles
        if($id != $user_id && !checkRole('users_edit_del')){
            $admin_title    = $lang['error_access'];
            require_once 'header.php';
            echo getAdminPanelError(array('header' => $lang['label_notice'], 'message' => $lang['error_no_accep']));
            require_once 'footer.php';
            exit();
        }
        $id             = $id ? $id : $user_id;
        $profile        = getGlobal(_TABLE_USERS, array('users_id' => $id));
        $css_plus       = array('app-assets/css/pages/users.css');
        $admin_title    = 'Trang cá nhân';
        require_once 'header.php';
        ?>
        <!-- COVER -->
        <div id="user-profile">
            <div class="row">
                <div class="col-12">
                    <div class="card profile-with-cover">
                        <div class="card-img-top img-fluid bg-cover height-300" style="background: url('../images/system/cover.jpg') 50%;"></div>
                        <div class="media profil-cover-details w-100">
                            <div class="media-left pl-2 pt-2">
                                <a href="#" class="profile-image"><img src="<?php echo $profile['users_avatar'] ? _URL_HOME.'/'.$profile['users_avatar'] : 'images/avatar.png' ?>" class="rounded-circle img-border height-100" alt="Card image"></a>
                            </div>
                            <div class="media-body pt-3 px-2">
                                <div class="row">
                                    <div class="col"><h3 class="card-title"><?php echo $profile['users_name'];?></h3></div>
                                    <div class="col text-right">
                                        <a href="<?php echo _URL_ADMIN.'/users.php?act=update&id='.$id;?>"><button class="btn btn-primary d-">Cập nhật</button></a>
                                        <a href="<?php echo _URL_ADMIN.'/users.php?act=del&id='.$id;?>"><button class="btn btn-primary d-">Xóa</button></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <nav class="navbar navbar-light navbar-profile align-self-end">
                            <button class="navbar-toggler d-sm-none" type="button" data-toggle="collapse" aria-expanded="false" aria-label="Toggle navigation"></button>
                            <nav class="navbar navbar-expand-lg">
                                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                                    <ul class="navbar-nav mr-auto">
                                        <li><br /></li>
                                    </ul>
                                </div>
                            </nav>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <!-- COVER -->
        <!-- CONTENT -->
        <div class="row">
            <div class="col-md-4">
                <div class="card"><div class="card-header"> <h4 class="card-title">Thông tin cơ bản</h4> </div>
                    <div class="card-body">
                        <i class="la la-phone"></i> <?php echo $profile['users_phone'];?><hr />
                        <i class="la la-inbox"></i> <?php echo $profile['users_email'];?><hr />
                        <i class="la la-institution"></i> <?php echo '<a href="'. _URL_ADMIN .'/category.php?act=update&id='. $profile['users_room'] .'&type='. getGlobalAll(_TABLE_CATEGORY, array('id' => $profile['users_room']), array('onecolum' => 'category_type')) .'">'. getGlobalAll(_TABLE_CATEGORY, array('id' => $profile['users_room']), array('onecolum' => 'category_name')) .'</a>';?><hr />
                        <i class="la la-stethoscope"></i> <?php echo '<a href="'. _URL_ADMIN .'/category.php?act=update&id='. $profile['users_level'] .'&type='. getGlobalAll(_TABLE_CATEGORY, array('id' => $profile['users_level']), array('onecolum' => 'category_type')) .'">'. getGlobalAll(_TABLE_CATEGORY, array('id' => $profile['users_level']), array('onecolum' => 'category_name')) .'</a>';?>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <?php if(checkGlobal(_TABLE_USERS, array('users_manager' => $id)) > 0){?>
                <div class="card"><div class="card-header"> <h4 class="card-title">Nhân viên <?php echo $profile['users_name'];?> quản lý</h4> </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Tên</th>
                                        <th>Phòng ban</th>
                                        <th>Chức vụ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                foreach (getGlobalAll(_TABLE_USERS, array('users_manager' => $id)) AS $user_manager){
                                    echo '<tr>';
                                    echo '<td><a href="'. _URL_ADMIN .'/users.php?act=detail&id='. $user_manager['users_id'] .'">'. $user_manager['users_name'] .'</a> </td>';
                                    echo '<td><a href="'. _URL_ADMIN .'/category.php?act=update&id='. $user_manager['users_room'] .'&type='. getGlobalAll(_TABLE_CATEGORY, array('id' => $user_manager['users_room']), array('onecolum' => 'category_type')) .'">'. getGlobalAll(_TABLE_CATEGORY, array('id' => $user_manager['users_room']), array('onecolum' => 'category_name')) .'</a></td>';
                                    echo '<td><a href="'. _URL_ADMIN .'/category.php?act=update&id='. $user_manager['users_level'] .'&type='. getGlobalAll(_TABLE_CATEGORY, array('id' => $user_manager['users_level']), array('onecolum' => 'category_type')) .'">'. getGlobalAll(_TABLE_CATEGORY, array('id' => $user_manager['users_level']), array('onecolum' => 'category_name')) .'</a></td>';
                                    echo '</tr>';
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php }?>
                <div class="card">
                    <div class="card-header"><h4 class="card-title">Công việc làm gần nhất</h4> </div>
                    <div class="card-body">

                    </div>
                </div>
            </div>
        </div>
        <!-- CONTENT -->
        <?php
        break;
    case 'group':
        // Check Roles
        if(!checkRole('users_group')){
            $admin_title    = $lang['error_access'];
            require_once 'header.php';
            echo getAdminPanelError(array('header' => $lang['label_notice'], 'message' => $lang['error_no_accep']));
            require_once 'footer.php';
            exit();
        }
        switch ($type){
            case 'del':
                $group = getGlobal(_TABLE_CATEGORY, array('id' => $id, 'category_type' => 'users_group'));
                if(!$group){
                    header('location:'._URL_ADMIN.'/users.php?act=group');
                    break;
                }
                if($submit){
                    deleteGlobal(_TABLE_CATEGORY, array('id' => $id));
                    deleteGlobal(_TABLE_GROUP, array('group_id' => $id, 'group_type' => 'users_group'));
                    header('location:'._URL_ADMIN.'/users.php?act=group');
                    break;
                }
                break;
            case 'add':
                if($submit){
                    $group_users    = isset($_POST['group_users'])  ? $_POST['group_users']         : '';
                    foreach ($group_users AS $users_list){
                        $data_group = array(
                            'group_type'    => 'users_group',
                            'group_id'      => $id,
                            'group_value'   => $users_list,
                            'group_users'   => $user_id,
                            'group_time'    => _CONFIG_TIME
                        );
                        if(checkGlobal(_TABLE_GROUP, array('group_id' => $id, 'group_type' => 'users_group', 'group_value' => $users_list)) == 0){
                            insertGlobal('dong_group', $data_group);
                        }
                    }
                    header('location:'._URL_ADMIN.'/users.php?act='.$act.'&id='.$id);
                }
                break;
            default:
                if($submit){
                    $group_name     = isset($_POST['group_name'])   ? trim($_POST['group_name'])    : '';
                    $group_users    = isset($_POST['group_users'])  ? $_POST['group_users']         : '';
                    $data = array(
                        'category_name' => $group_name,
                        'category_url'  => makeSlug($group_name),
                        'category_des'  => '',
                        'category_info' => '',
                        'category_sub'  => 0,
                        'category_type' => 'users_group',
                        'category_time' => _CONFIG_TIME,
                        'category_user' => $user_id
                    );
                    $data = insertGlobal('dong_category', $data);
                    foreach ($group_users AS $users_list){
                        $data_group = array(
                            'group_type'    => 'users_group',
                            'group_id'      => $data,
                            'group_value'   => $users_list,
                            'group_users'   => $user_id,
                            'group_time'    => _CONFIG_TIME
                        );
                        insertGlobal('dong_group', $data_group);
                    }
                }
                if($_POST['submit_delete']){
                    $group_users    = isset($_POST['group_users'])  ? $_POST['group_users']         : '';
                    foreach ($group_users AS $users_list){
                        deleteGlobal('dong_group', array('group_id' => $id, 'group_value' => $users_list, 'group_type' => 'users_group'));
                    }
                }
                break;
        }
        $group          = getGlobal('dong_category', array('id' => $id, 'category_type' => 'users_group'));
        $admin_title    = 'Nhóm thành viên '.$group['category_name'];
        $css_plus       = array('app-assets/vendors/css/forms/icheck/icheck.css','app-assets/css/plugins/forms/checkboxes-radios.css');
        require_once 'header.php';
        ?>
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header"><h4 class="card-title"><?php echo $admin_title;?></h4> </div>
                    <div class="card-body">
                        <form action="" class="form-horizontal" method="post">
                            <div>
                                <?php
                                switch ($type){
                                    default:
                                        echo '<div class="row">';
                                        if($id && !$type){
                                            echo '<div class="col-md-6 text-left"><input type="submit" name="submit_delete" class="text-right btn btn-outline-success round btn-min-width mr-1 mb-1" value="Xóa thành viên khỏi nhóm"></div>';
                                            echo '<div class="col-md-6 text-right"><button class="text-right btn btn-outline-success round btn-min-width mr-1 mb-1"><a href="'. _URL_ADMIN .'/users.php?act='. $act .'&type=add&id='. $id .'">Thêm thành viên vào nhóm</a></button></div>';
                                        }else if(!$id && !$type){
                                            echo '<div class="col-md-9"><input type="text" required="required" class="form-control border-primary" placeholder="Nhập tên nhóm" name="group_name"></div>';
                                            echo '<div class="col-md-3"><input type="submit" name="submit" class="text-right btn btn-outline-success round btn-min-width mr-1 mb-1" value="Thêm nhóm"></div>';
                                        }else if($id && $type == 'add'){
                                            echo '<div class="col-md-6 text-left"><a href="'. _URL_ADMIN .'/users.php?act='. $act .'&id='. $id .'">Xem nhóm</a></div><div class="col-md-6 text-right"><input type="submit" name="submit" class="text-right btn btn-outline-success round btn-min-width mr-1 mb-1" value="Thêm thành viên vào nhóm"></div>';
                                        }
                                        echo '</div>';
                                        break;
                                    case "del":
                                        ?>
                                        <form action="" method="post">
                                            <p class="text-center">Bạn có chắc chắn muốn xóa nhóm <strong><?php echo $group['category_name'];?></strong> này không?</p>
                                            <p class="text-center"><?php echo _BUTTON_BACK;?> <input class="btn round btn-outline-blue" type="submit" name="submit" value="Xóa nhóm này"></p>
                                        </form>
                                        <?php
                                        break;
                                }
                                ?>
                            </div>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Tên</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if($id && !$type){
                                        $users_list = getGlobalAll('dong_group', array('group_type' => 'users_group', 'group_id' => $id));
                                        foreach ($users_list AS $user){
                                            $users = getGlobal('dong_users', array('users_id' => $user['group_value']));
                                            $i++;
                                            if($users){
                                                echo '<tr><td><input class="icheckbox_flat-blue" type="checkbox" name="group_users[]" value="'. $users['users_id'] .'"></td><td>'. $users['users_name'] .'</td></tr>';
                                            }
                                        }
                                    }else if(!$id || !$type){
                                        $users_list = getGlobalAll('dong_users', '');
                                        foreach ($users_list AS $users){
                                            echo '<tr><td><input class="icheckbox_flat-blue" type="checkbox" name="group_users[]" value="' . $users['users_id'] . '"></td><td>' . $users['users_name'] . '</td></tr>';
                                        }
                                    }else if($id && $type == 'add'){
                                        $users_group    = getGlobalAll('dong_group', array('group_type' => 'users_group', 'group_id' => $id));
                                        foreach ($users_group as $key) {
                                            $colums[] = "`users_id` <> '" . checkInsert($key['group_value']) . "'";
                                        }
                                        $colums_list = implode(' AND ', $colums);
                                        $users_list     = getGlobalAll('dong_users', '', array('query' => 'SELECT * FROM `dong_users` WHERE '.$colums_list));
                                        foreach ($users_list AS $users){
                                            echo '<tr><td><input class="icheckbox_flat-blue" type="checkbox" name="group_users[]" value="'. $users['users_id'] .'"></td><td>'. $users['users_name'] .'</td></tr>';
                                        }
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Danh sách nhóm</h4>
                        <div class="heading-elements">
                            <a href="<?php echo _URL_ADMIN.'/users.php?act=group'?>">Thêm nhóm mới</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                <?php
                                foreach (getGlobalAll('dong_category', array('category_type' => 'users_group')) AS $list){
                                    echo '<tr><td><a class="success" href="'. _URL_ADMIN .'/users.php?act='. $act .'&id='. $list['id'] .'">'. $list['category_name'] .'</a> <a href="'. _URL_ADMIN .'/users.php?act=group&type=del&id='. $list['id'] .'"><small><i class="danger">Xóa</i></small> </a> </td></tr>';
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        break;
    case 'update':
        // Check Roles
        if(!checkRole('users_edit_del')){
            $admin_title    = $lang['error_access'];
            require_once 'header.php';
            echo getAdminPanelError(array('header' => $lang['label_notice'], 'message' => $lang['error_no_accep']));
            require_once 'footer.php';
            exit();
        }
        $users = getGlobal('dong_users', array('users_id' => $id));
        if($submit) {
            $users_name_login   = isset($_POST['users_name_login']) ? trim($_POST['users_name_login'])  : '';
            $users_pass         = isset($_POST['users_pass'])       ? trim($_POST['users_pass'])        : '';
            $users_repass       = isset($_POST['users_repass'])     ? trim($_POST['users_repass'])      : '';
            $users_email        = isset($_POST['users_email'])      ? trim($_POST['users_email'])       : '';
            $users_phone        = isset($_POST['users_phone'])      ? trim($_POST['users_phone'])       : '';
            $users_name         = isset($_POST['users_name'])       ? trim($_POST['users_name'])        : '';
            $users_level        = isset($_POST['users_level'])      ? trim($_POST['users_level'])       : '';
            $users_manager      = isset($_POST['users_manager'])    ? trim($_POST['users_manager'])     : '';
            $users_room         = isset($_POST['users_room'])       ? trim($_POST['users_room'])        : '';
            $users_status       = isset($_POST['users_status'])     ? trim($_POST['users_status'])      : 1;
            $users_room_manager = isset($_POST['users_room_manager'])?$_POST['users_room_manager']      : null;
            // Check Error
            $error = array();
            if(!$users_name_login){
                $error['users_name_login'] = getViewError($lang['error_empty_this_fiel']);
            }
            if($users_name_login != $users['users_user'] && checkGlobal('dong_users', array('users_user' => $users_name_login)) > 0){
                $error['users_name_login'] = $lang['users_user_exits'];
            }
            if(($users_pass && $users_repass) && $users_pass != $users_repass){
                $error['users_pass'] = getViewError($lang['users_pass_nosame']);
            }
            if(!$users_email){
                $error['users_email'] = getViewError($lang['error_empty_this_fiel']);
            }
            if($users_email != $users['users_email'] && checkGlobal('dong_users', array('users_email' => $users_email)) > 0){
                $error['users_name_login'] = $lang['users_email_exits'];
            }
            if(!$users_name){
                $error['users_name'] = getViewError($lang['error_empty_this_fiel']);
            }
            if(!$users_level){
                $error['users_level'] = getViewError($lang['error_empty_this_fiel']);
            }

            if(!$error){
                // Upload File
                require_once '../includes/lib/class.uploader.php';
                $uploader = new Uploader();
                $data_upload = $uploader->upload($_FILES['users_avatar'], array(
                    'limit'         => 10, //Maximum Limit of files. {null, Number}
                    'maxSize'       => 10, //Maximum Size of files {null, Number(in MB's)}
                    'extensions'    => null, //Whitelist for file extension. {null, Array(ex: array('jpg', 'png'))}
                    'required'      => false, //Minimum one file is required for upload {Boolean}
                    'uploadDir'     => '../images/users/', //Upload directory {String}
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
                if($data_upload['isComplete']){
                    $files = $data_upload['data']['files'];
                    foreach ($files AS $file){
                        $file_name = explode('/', $file);
                        $file_name = $file_name[(count($file_name) - 1)];
                    }
                }
                // Upload File
                $data = array(
                    'users_user'    => $users_name_login,
                    'users_email'   => $users_email,
                    'users_phone'   => $users_phone,
                    'users_pass'    => ($users_pass && $users_repass) && $users_pass == $users_repass ? md5($users_pass) : $users['users_pass'],
                    'users_name'    => $users_name,
                    'users_avatar'  => $file_name ? 'images/users/'.$file_name : $users['users_avatar'],
                    'users_level'   => $users_level,
                    'users_manager' => $users_manager,
                    'users_room'    => $users_room,
                    'users_status'  => $users_status
                );
                // Lệnh chỉnh sửa thành viên
                if(!updateGlobal('dong_users', $data, array('users_id' => $id))){
                    $admin_title    = 'Chỉnh sửa thành viên';
                    require_once 'header.php';
                    echo getAdminPanelError(array('header' => $lang['label_notice'], 'message' => 'Lỗi khi sửa thành viên'));
                    break;
                }
                // Thêm danh sách những phòng ban được thành viên này quản lý
                foreach ($users_room_manager AS $list_room){
                    if(checkGlobal(_TABLE_GROUP, array('group_type' => 'users_manager_room', 'group_id' => $id, 'group_value' => $list_room)) == 0){
                        $data_room = array(
                            'group_type'    => 'users_manager_room',
                            'group_id'      => $id,
                            'group_value'   => $list_room,
                            'group_users'   => $user_id,
                            'group_time'    => _CONFIG_TIME
                        );
                        if(!insertGlobal(_TABLE_GROUP, $data_room)){
                            $admin_title    = 'Chỉnh sửa thành viên';
                            require_once 'header.php';
                            echo getAdminPanelError(array('header' => $lang['label_notice'], 'message' => 'Lỗi khi thêm các phòng thành viên quản lý'));
                            break;
                        }
                    }
                }
                $users = getGlobal(_TABLE_USERS, array('users_id' => $id));
            }
        }
        $css_plus       = array('app-assets/css/chosen.css');
        $js_plus        = array(
            'app-assets/js/chosen.jquery.js',
            'app-assets/js/prism.js',
            'app-assets/js/init.js');
        $admin_title = $lang['users_update'];
        require_once 'header.php';
        ?>
        <form action="" class="form form-horizontal" method="post" enctype="multipart/form-data">
        <div class="row horizontal-form-layouts">
            <div class="col-md-8">
                <div class="card"> <!--Content-->
                    <div class="card-header"><h4 class="card-title">Cập nhập thông tin thành viên</h4></div>
                    <div class="card-body">
                        <?php if($submit && !$error){echo getAlert('success', "Cập nhập thành viên thành công");} ?>
                            <?php echo inputFormText(array('type' => 'hozi', 'label' => $lang['users_name_login'], 'name' => 'users_name_login', 'value' => $users['users_user'], 'require' => TRUE, 'error' => $error['users_name_login'])); ?>
                            <?php echo inputFormPassword(array('label' => $lang['label_password'], 'name' => 'users_pass', 'value' => $users_pass, 'error' => $error['users_pass'])); ?>
                            <?php echo inputFormPassword(array('label' => $lang['users_repass'], 'name' => 'users_repass', 'value' => $users_repass)); ?>
                            <?php echo inputFormText(array('type' => 'hozi', 'label' => $lang['users_email'], 'name' => 'users_email', 'value' => $users['users_email'], 'require' => TRUE, 'error' => $error['users_email'])); ?>
                            <?php echo inputFormText(array('type' => 'hozi', 'label' => $lang['users_phone'], 'name' => 'users_phone', 'value' => $users['users_phone'], 'error' => $error['users_phone'])); ?>
                            <?php echo inputFormText(array('type' => 'hozi', 'label' => $lang['users_name'], 'name' => 'users_name', 'value' => $users['users_name'], 'require' => TRUE, 'error' => $error['users_name'])); ?>
                            <div class="form-group row">
                                <label class="col-md-3 label-control">Phòng ban</label>
                                <div class="col-md-9">
                                    <select name="users_room" class="form-control">
                                        <?php
                                        $data_room = getGlobalAll('dong_category', array('category_type' => 'room'));
                                        showCategories(array('data' => $data_room, 'type' => 'select', 'selected' => $users['users_room']))
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 label-control">Chức vụ</label>
                                <div class="col-md-9">
                                    <select name="users_level" class="form-control">
                                        <?php
                                        $data_room = getGlobalAll('dong_category', array('category_type' => 'role'));
                                        showCategories(array('data' => $data_room, 'type' => 'select', 'selected' => $users['users_level']))
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 label-control">Người quản lý</label>
                                <div class="col-md-9">
                                    <select name="users_manager" class="form-control">
                                        <option value="">Trống</option>
                                        <?php
                                        $data_users = getGlobalAll('dong_users', array());
                                        showCategories(array('data' => $data_users, 'type' => 'select', 'selected' => $users['users_manager'], 'form_value' => 'users_id', 'form_text' => 'users_name'))
                                        ?>
                                    </select>
                                </div>
                            </div>
                        <div class="form-group row">
                            <label class="col-md-3 label-control">Ảnh đại diện</label>
                            <div class="col-md-9"><fieldset class="form-group"><div class="col"><input type="file" class="form-control-file" name="users_avatar"></div></fieldset></div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 label-control">Thêm quản lý các phòng ban</label>
                            <div class="col-md-9">
                                <fieldset class="form-group">
                                    <select name="users_room_manager[]" data-placeholder="Nhập các phòng ban" multiple class="chosen-select-width form-control">
                                        <option value=""></option>
                                        <?php
                                        $role_list = getGlobalAll(_TABLE_CATEGORY, array('category_type' => 'room'));
                                        foreach ($role_list AS $role_group){
                                            echo '<option value="'. $role_group['id'] .'" '. (checkGlobal(_TABLE_GROUP, array('group_type' => 'users_manager_room', 'group_id' => $id, 'group_value' => $role_group['id'])) == 1 ? 'selected' : '') .'>'. $role_group['category_name'] .'</option>';
                                        }
                                        ?>
                                    </select>
                                </fieldset>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header"><h4 class="card-title">Chỉnh sửa</h4> </div>
                    <div class="card-body">
                        <fieldset>
                          <input type="radio" name="users_status" value="1" <?php echo $users['users_status'] == 1 ? ' checked="checked" ' : ''; ?> id="input-radio-11">
                          <label for="input-radio-11">Hoạt động</label>
                        </fieldset>
                        <fieldset>
                          <input type="radio" name="users_status" value="0" <?php echo $users['users_status'] == 0 ? ' checked="checked" ' : ''; ?> id="input-radio-12">
                          <label for="input-radio-12">Khóa tài khoản</label>
                        </fieldset>
                        <div class="form-actions text-center">
                            <?php echo _BUTTON_BACK;?>
                            <input type="submit" name="submit" class="btn btn-outline-success round" value="<?php echo $admin_title;?>">
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header"><h4 class="card-title">Quản lý các phòng ban</h4> </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                <?php
                                foreach (getGlobalAll(_TABLE_GROUP, array('group_type' => 'users_manager_room', 'group_id' => $id)) AS $list_disp_users_manager_room){
                                    $cate = getGlobal(_TABLE_CATEGORY, array('id' => $list_disp_users_manager_room['group_value']));
                                    echo '<tr><td>'. $cate['category_name'] .' ... <small class="text-danger"><i><a href="users.php?act=delete_users_manager_room&id='. $list_disp_users_manager_room['id'] .'&get_users='. $id .'">Xóa</a></i></small></td></tr>';
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </form>
        <?php
        break;
    case 'add':
        // Check Roles
        if(!checkRole('users_add')){
            $admin_title    = $lang['error_access'];
            require_once 'header.php';
            echo getAdminPanelError(array('header' => $lang['label_notice'], 'message' => $lang['error_no_accep']));
            require_once 'footer.php';
            exit();
        }
        if($submit) {
            $users_name_login   = isset($_POST['users_name_login']) ? trim($_POST['users_name_login'])  : '';
            $users_pass         = isset($_POST['users_pass'])       ? trim($_POST['users_pass'])        : '';
            $users_repass       = isset($_POST['users_repass'])     ? trim($_POST['users_repass'])      : '';
            $users_email        = isset($_POST['users_email'])      ? trim($_POST['users_email'])       : '';
            $users_phone        = isset($_POST['users_phone'])      ? trim($_POST['users_phone'])       : '';
            $users_name         = isset($_POST['users_name'])       ? trim($_POST['users_name'])        : '';
            $users_level        = isset($_POST['users_level'])      ? trim($_POST['users_level'])       : '';
            $users_manager      = isset($_POST['users_manager'])    ? trim($_POST['users_manager'])     : '';
            $users_room         = isset($_POST['users_room'])       ? trim($_POST['users_room'])        : '';

            // Check Error
            $error = array();
            if(!$users_name_login){
                $error['users_name_login'] = getViewError($lang['error_empty_this_fiel']);
            }
            if(checkGlobal('dong_users', array('users_user' => $users_name_login)) > 0){
                $error['users_name_login'] = $lang['users_user_exits'];
            }
            if(!$users_pass || !$users_repass){
                $error['users_pass'] = getViewError($lang['error_empty_this_fiel']);
            }
            if($users_pass != $users_repass){
                $error['users_pass'] = getViewError($lang['users_pass_nosame']);
            }
            if(!$users_email){
                $error['users_email'] = getViewError($lang['error_empty_this_fiel']);
            }
            if(checkGlobal('dong_users', array('users_email' => $users_email)) > 0){
                $error['users_email'] = $lang['users_email_exits'];
            }
            if(!$users_name){
                $error['users_name'] = getViewError($lang['error_empty_this_fiel']);
            }
            if(!$users_level){
                $error['users_level'] = getViewError($lang['error_empty_this_fiel']);
            }

            if(!$error){
                // Upload File
                require_once '../includes/lib/class.uploader.php';
                $uploader = new Uploader();
                $data_upload = $uploader->upload($_FILES['users_avatar'], array(
                    'limit'         => 10, //Maximum Limit of files. {null, Number}
                    'maxSize'       => 10, //Maximum Size of files {null, Number(in MB's)}
                    'extensions'    => null, //Whitelist for file extension. {null, Array(ex: array('jpg', 'png'))}
                    'required'      => false, //Minimum one file is required for upload {Boolean}
                    'uploadDir'     => '../images/users/', //Upload directory {String}
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
                if($data_upload['isComplete']){
                    $files = $data_upload['data']['files'];
                    foreach ($files AS $file){
                        $file_name = explode('/', $file);
                        $file_name = $file_name[(count($file_name) - 1)];
                    }
                }
                // Upload File
                $data = array(
                    'users_user'    => $users_name_login,
                    'users_email'   => $users_email,
                    'users_phone'   => $users_phone,
                    'users_pass'    => md5($users_pass),
                    'users_name'    => $users_name,
                    'users_manager' => $users_manager ? $user_manager : 0,
                    'users_room'    => $users_room,
                    'users_avatar'  => $file_name ? 'images/users/'.$file_name : '',
                    'users_level'   => $users_level,
                    'users_status'  => 1,
                    'users_time'    => _CONFIG_TIME
                );
                $result = insertGlobal('dong_users', $data);
                if($result){
                    header('location:'._URL_ADMIN.'/users.php?act=detail&id='.$result);
                    exit();
                }
            }
        }
        $admin_title = $lang['users_add'];
        require_once 'header.php';
        ?>
        <div class="row horizontal-form-layouts">
            <div class="col">
                <div class="card"> <!--Content-->
                    <div class="card-header"><h4 class="card-title"><?php echo $lang['users_add'];?></h4></div>
                    <div class="card-body">
                        <?php if($submit && !$error){echo getAlert('success', $lang['users_add_success']);} ?>
                        <form action="" class="form form-horizontal" method="post" enctype="multipart/form-data">
                            <?php echo inputFormText(array('type' => 'hozi', 'label' => $lang['users_name_login'], 'name' => 'users_name_login', 'value' => $users_name_login, 'require' => TRUE, 'error' => $error['users_name_login'])); ?>
                            <?php echo inputFormPassword(array('label' => $lang['label_password'], 'name' => 'users_pass', 'value' => $users_pass, 'error' => $error['users_pass'])); ?>
                            <?php echo inputFormPassword(array('label' => $lang['users_repass'], 'name' => 'users_repass', 'value' => $users_repass)); ?>
                            <?php echo inputFormText(array('type' => 'hozi', 'label' => $lang['users_email'], 'name' => 'users_email', 'value' => $users_email, 'require' => TRUE, 'error' => $error['users_email'])); ?>
                            <?php echo inputFormText(array('type' => 'hozi', 'label' => $lang['users_phone'], 'name' => 'users_phone', 'value' => $users_phone, 'error' => $error['users_phone'])); ?>
                            <?php echo inputFormText(array('type' => 'hozi', 'label' => $lang['users_name'], 'name' => 'users_name', 'value' => $users_name, 'require' => TRUE, 'error' => $error['users_name'])); ?>
                            <div class="form-group row">
                                <label class="col-md-3 label-control">Phòng ban</label>
                                <div class="col-md-9">
                                    <select name="users_room" class="form-control">
                                        <?php
                                        $data_room = getGlobalAll('dong_category', array('category_type' => 'room'));
                                        showCategories(array('data' => $data_room, 'type' => 'select'))
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 label-control">Chức vụ</label>
                                <div class="col-md-9">
                                    <select name="users_level" class="form-control">
                                        <?php
                                        $data_role = getGlobalAll('dong_category', array('category_type' => 'role'));
                                        showCategories(array('data' => $data_role, 'type' => 'select'))
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 label-control">Người quản lý</label>
                                <div class="col-md-9">
                                <select name="users_manager" class="form-control" data-size="7">
                                    <option value="">Trống</option>
                                    <?php
                                    $data_users = getGlobalAll('dong_users', array());
                                    showCategories(array('data' => $data_users, 'type' => 'select', 'form_value' => 'users_id', 'form_text' => 'users_name'))
                                    ?>
                                </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 label-control">Ảnh đại diện</label>
                                <div class="col-md-9"><fieldset class="form-group"><div class="col"><input type="file" class="form-control-file" name="users_avatar"></div></fieldset></div>
                            </div>
                            <div class="form-actions text-center">
                                <?php echo _BUTTON_BACK;?>
                                <input type="submit" name="submit" class="btn btn-outline-success round" value="<?php echo $lang['users_add'];?>">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php
        break;
    default:
        // Check Roles
        if(!checkRole('users')){
            $admin_title    = $lang['error_access'];
            require_once 'header.php';
            echo getAdminPanelError(array('header' => $lang['label_notice'], 'message' => $lang['error_no_accep']));
            require_once 'footer.php';
            exit();
        }
        $users_room     = isset($_REQUEST['users_room'])    ? trim($_REQUEST['users_room']) : '';
        $users_level    = isset($_REQUEST['users_level'])   ? trim($_REQUEST['users_level']) : '';
        $users_name     = isset($_REQUEST['users_name'])    ? trim($_REQUEST['users_name']) : '';
        $admin_title    = $lang['users_manager'];
        require_once 'header.php';
        ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"><h4 class="card-title"><?php echo $lang['users_list'];?></h4> </div>
                    <div class="card-body">
                        <!-- Search Bar -->
                        <form action="" method="get">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="search-input open">
                                        <input class="input form-control round" type="text" value="<?php echo $users_name ? $users_name : '';?>" name="users_name" placeholder="Tên thành viên">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <fieldset class="form-group">
                                        <select class="form-control round" name="users_room">
                                            <option value="">Tất Cả phòng ban</option>
                                            <?php
                                            $data = getGlobalAll(_TABLE_CATEGORY, array('category_type' => 'room'));
                                            showCategories(array('data' => $data, 'type' => 'select', 'form_value' => 'id', 'form_text' => 'category_name', 'selected' => $users_room))
                                            ?>
                                        </select>
                                    </fieldset>
                                </div>
                                <div class="col-md-3">
                                    <fieldset class="form-group">
                                        <select class="form-control round" name="users_level">
                                            <option value="">Tất Cả chức vụ</option>
                                            <?php
                                            $data = getGlobalAll(_TABLE_CATEGORY, array('category_type' => 'role'));
                                            showCategories(array('data' => $data, 'type' => 'select', 'form_value' => 'id', 'form_text' => 'category_name', 'selected' => $users_level))
                                            ?>
                                        </select>
                                    </fieldset>
                                </div>
                                <div class="col-md-3 text-right">
                                    <input type="submit" class="btn btn-outline-blue round" value="Lọc dữ liệu">
                                </div>
                            </div>
                        </form>
                        <!-- Search Bar -->
                        <?php
                        $para = array('users_room','users_level','users_name');
                        foreach ($para AS $paras){
                            if(isset($_REQUEST[$paras]) && !empty($_REQUEST[$paras])){
                                    $parameters[$paras] = $_REQUEST[$paras];
                            }
                        }
                        if($parameters){
                            foreach ($parameters as $key => $value) {
                                if($key == 'users_name'){
                                    $colums[] = '`'.$key .'` LIKE "%'. checkInsert($value) .'%"';
                                }else{
                                    $colums[] = '`'.$key .'` = "'. checkInsert($value) .'"';
                                }
                            }
                            $parameters_list = ' WHERE '.implode(' AND ', $colums);
                        }

                        // Tạo Url Parameter động
                        foreach ($parameters as $key => $value) {
                            $para_url[] = $key .'='. $value;
                        }
                        $para_list                      = implode('&', $para_url);
                        // Tạo Url Parameter động
                        $config_pagenavi['page_row']    = 40;
                        $config_pagenavi['page_num']    = ceil(checkGlobal(_TABLE_USERS, $parameters)/$config_pagenavi['page_row']);
                        $config_pagenavi['url']         = _URL_ADMIN.'/users.php?'.$para_list.'&';
                        $page_start                     = ($page-1) * $config_pagenavi['page_row'];
                        $data   = getGlobalAll(_TABLE_USERS, $parameters,array(
                            'order_by_row'  => 'users_id',
                            'order_by_value'=> 'DESC',
                            'query_like'    => 'users_name',
                            'limit_start'   => $page_start,
                            'limit_number'  => $config_pagenavi['page_row']
                        ));

                        echo '<div id="simple-user-cards-with-border" class="row mt-2">';
                        foreach ($data AS $datas){
                        ?>
                            <div class="col-xl-3 col-md-6 col-12">
                                <div class="card border-blue border-lighten-2">
                                    <div class="text-center">
                                        <div class="card-body"><a href="<?php echo 'users.php?act=detail&id='. $datas['users_id'];?>"><img src="<?php echo $datas['users_avatar'] ? _URL_HOME.'/'.$datas['users_avatar'] : 'images/avatar.png' ?>" class="rounded-circle  height-150" alt="<?php echo $datas['users_name']?>"></a></div>
                                        <div class="card-body">
                                            <h4 class="card-title"><?php echo '<a href="'. _URL_ADMIN .'/users.php?act=detail&id='. $datas['users_id']  .'">'. $datas['users_name'] .'</a>';?></h4>
                                            <h6 class="card-subtitle text-muted">
                                                <?php echo '<a href="'. _URL_ADMIN .'/category.php?act=update&id='. $datas['users_room'] .'&type='. getGlobalAll(_TABLE_CATEGORY, array('id' => $datas['users_room']), array('onecolum' => 'category_type')) .'">'. getGlobalAll(_TABLE_CATEGORY, array('id' => $datas['users_room']), array('onecolum' => 'category_name')) .'</a>';?><br>
                                                <?php echo '<a href="'. _URL_ADMIN .'/category.php?act=update&id='. $datas['users_level'] .'&type='. getGlobalAll(_TABLE_CATEGORY, array('id' => $datas['users_level']), array('onecolum' => 'category_type')) .'">'. getGlobalAll(_TABLE_CATEGORY, array('id' => $datas['users_level']), array('onecolum' => 'category_name')) .'</a>';?>
                                            </h6>
                                        </div>
                                        <div class="card-body text-center">
                                            <a class="btn btn-outline-danger round" href="users.php?act=del&id=<?php echo $datas['users_id'];?>"><i class="la la-remove"></i> Xóa</a>
                                            <a class="btn btn-outline-cyan round" href="users.php?act=update&id=<?php echo $datas['users_id'];?>"><i class="la la-check"></i> Sửa</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php
                        }
                        echo '</div>';
                        //echo $data; exit();
                        /*echo '<div class="table-responsive">';
                        echo '<table class="table">';
                        echo '<thread>';
                        echo '<tr>';
                        echo '<th>Tên hiển thị</th>';
                        echo '<th>Email</th>';
                        echo '<th>Số điện thoại</th>';
                        echo '<th>Phòng ban</th>';
                        echo '<th>Chức vụ</th>';
                        echo '<th>Người quản lý</th>';
                        echo '<th>Đăng ký lúc</th>';
                        echo '</tr>';
                        echo '</thread>';
                        echo '<tbody>';
                        foreach ($data AS $datas){
                            echo '<tr>';
                            echo '<td><a href="'. _URL_ADMIN .'/users.php?act=detail&id='. $datas['users_id']  .'">'. $datas['users_name'] .'</a><br />';
                            echo '<a class="green" href=" '._URL_ADMIN.'/users.php?act=update&id='. $data's[users_id'] .'">Sửa</a> | ';
                            echo '<a class="danger" href=" '._URL_ADMIN.'/users.php?act=del&id='.$datas['users_id'] .'">Xóa</a>';
                            echo '</td>';
                            echo '<td>'. $datas['users_email'] .'</td>';
                            echo '<td>'. $datas['users_phone'] .'</td>';
                            echo '<td><a href="'. _URL_ADMIN .'/category.php?act=update&id='. $datas['users_room'] .'&type='. getGlobalAll(_TABLE_CATEGORY, array('id' => $datas['users_room']), array('onecolum' => 'category_type')) .'">'. getGlobalAll(_TABLE_CATEGORY, array('id' => $datas['users_room']), array('onecolum' => 'category_name')) .'</a></td>';
                            echo '<td><a href="'. _URL_ADMIN .'/category.php?act=update&id='. $datas['users_level'] .'&type='. getGlobalAll(_TABLE_CATEGORY, array('id' => $datas['users_level']), array('onecolum' => 'category_type')) .'">'. getGlobalAll(_TABLE_CATEGORY, array('id' => $datas['users_level']), array('onecolum' => 'category_name')) .'</a></td>';
                            echo '<td><a href="'. _URL_ADMIN .'/users.php?act=update&id='. $datas['users_manager'] .'">'. getGlobalAll(_TABLE_USERS, array('users_id' => $datas['users_manager']), array('onecolum' => 'users_name')) .'</a></td>';
                            echo '<td>'. getViewTime($datas['users_time']) .'</td>';
                            echo '</tr>';
                        }
                        echo '</tbody>';
                        echo '</table>';
                        echo '</div>';*/
                        echo '<nav aria-label="Page navigation">'.pagination($config_pagenavi).'</nav>';
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
        break;
}
require_once 'footer.php';