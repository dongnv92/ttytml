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
    case 'intro':
        $admin_title = 'Tổng Quan Kế Hoạch Nội Bộ';
        require_once 'header.php';
        ?>
        <div class="row">
            <div class="col-xl-6 col-md-12">
                <div class="card overflow-hidden">
                    <div class="card-content">
                        <div class="media align-items-stretch bg-gradient-x-info text-white rounded">
                            <div class="p-2 media-middle">
                                <i class="ft-layers font-large-2 text-white"></i>
                            </div>
                            <div class="media-body p-2">
                                <a href="<?=_URL_ADMIN?>/local.php?type=users&id=<?=$data_user['users_id']?>"><h4 class="text-white">Kế Hoạch Nội Bộ Của Bạn</h4></a>
                                <span>Kế hoạch cá nhân</span>
                            </div>
                            <div class="media-right p-2 media-middle">
                                <h1 class="text-white"><?=checkGlobal(_TABLE_LOCAL, array('local_type' => 'users', 'local_users' => $data_user['users_id']));?></h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            switch ($data_user['users_level']){
                case in_array($data_user['users_level'], array(88,80,119,83,120,72,124)):
                    $room = getGlobal(_TABLE_CATEGORY, array('id' => $data_user['users_room']));
                    ?>
                    <div class="col-xl-6 col-md-12">
                        <div class="card">
                            <div class="card-content">
                                <div class="media align-items-stretch bg-gradient-x-warning text-white rounded">
                                    <div class="p-2 media-middle">
                                        <i class="ft-layers font-large-2 text-white"></i>
                                    </div>
                                    <div class="media-body p-2">
                                        <a href="<?=_URL_ADMIN?>/local.php?type=room&id=<?=$room['id']?>">
                                            <h4 class="text-white"><?php echo $room['category_name']?></h4>
                                        </a>
                                        <span>Kế hoạch nội bộ tập thể</span>
                                    </div>
                                    <div class="media-right p-2 media-middle">
                                        <h1 class="text-white"><?=checkGlobal(_TABLE_LOCAL, array('local_type' => 'room', 'local_users' => $data_user['users_room']));?></h1>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                break;
            case in_array($data_user['users_level'], array(69, 70)):
                foreach (getGlobalAll(_TABLE_CATEGORY, array('category_type' => 'room')) AS $category){
                ?>
                    <div class="col-xl-6 col-md-12">
                        <div class="card">
                            <div class="card-content">
                                <div class="media align-items-stretch bg-gradient-x-warning text-white rounded">
                                    <div class="p-2 media-middle">
                                        <i class="ft-layers font-large-2 text-white"></i>
                                    </div>
                                    <div class="media-body p-2">
                                        <a href="<?=_URL_ADMIN?>/local.php?type=room&id=<?=$category['id']?>">
                                            <h4 class="text-white"><?php echo $category['category_name']?></h4>
                                        </a>
                                        <span>Kế hoạch nội bộ tập thể</span>
                                    </div>
                                    <div class="media-right p-2 media-middle">
                                        <h1 class="text-white"><?=checkGlobal(_TABLE_LOCAL, array('local_type' => 'room', 'local_users' => $category['id']));?></h1>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
                }
                break;
            }
            ?>
        </div>
        <?php
        require_once 'footer.php';
        break;
    case 'detail':
        $local = getGlobal(_TABLE_LOCAL, array('id' => $id));
        // Kiểm tra nếu kế hoạch không có thì thông báo lỗi
        if (!$local){
            $admin_title    = 'Nội dung không tồn tại';
            require_once 'header.php';
            echo getAdminPanelError(array('header' => $lang['label_notice'], 'message' => $lang['tasks_empty_content']));
            require_once 'footer.php';
            break;
        }
        if($submit){
            // Thêm bình luận
            $local_comment  = isset($_POST['local_comment'])    ? trim($_POST['local_comment']) : '';
            $data = array(
                'comment_content'   => $local_comment,
                'comment_type'      => 'local',
                'comment_value'     => $id,
                'comment_users'     => $user_id,
                'comment_time'      => _CONFIG_TIME
            );
            if($local_comment){
                // Thêm bình luận
                if(!insertGlobal('dong_comment', $data)){
                    require_once 'header.php';
                    echo getAdminPanelError(array('header' => $admin_title, 'message' => 'Comment: '.$lang['error_mysql']));
                    require_once 'footer.php';
                    exit();
                }

                // Gửi thông báo đến các thành viên
                if($user_id != $local['local_users']){
                    $data_notification = array(
                        'notification_send'     => $user_id,
                        'notification_to'       => $local['local_users'],
                        'notification_type'     => $active_menu,
                        'notification_value'    => $id,
                        'notification_content'  => 'notification_add_comment',
                        'notification_time'     => _CONFIG_TIME
                    );
                    if(!insertGlobal(_TABLE_NOTIFICATION, $data_notification)){
                        require_once 'header.php';
                        echo getAdminPanelError(array('header' => $admin_title, 'message' => 'Notification Admin: '.$lang['error_mysql'] ));
                        require_once 'footer.php';
                        exit();
                    }
                }
            }
            // Xử lý Upload File
            require_once '../includes/lib/class.uploader.php';
            $uploader   = new Uploader();
            $path       =  '../files/local';
            $data_upload = $uploader->upload($_FILES['local_files'], array(
                'limit'         => 10, //Maximum Limit of files. {null, Number}
                'maxSize'       => 10, //Maximum Size of files {null, Number(in MB's)}
                'extensions'    => null, //Whitelist for file extension. {null, Array(ex: array('jpg', 'png'))}
                'required'      => false, //Minimum one file is required for upload {Boolean}
                'uploadDir'     => $path.'/', //Upload directory {String}
                'title'         => array('auto', 20), //New file name {null, String, Array} *please read documentation in README.md
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
                    if(!insertGlobal('dong_files', array(
                        'files_name'    => $file_name,
                        'files_url'     => 'files/local/'.$file_name,
                        'files_value'   => $id,
                        'files_type'    => 'local',
                        'files_users'   => $user_id,
                        'files_time'    => _CONFIG_TIME
                    ))){
                        require_once 'header.php';
                        echo getAdminPanelError(array('header' => $admin_title, 'message' => 'Insert File Attack: '.$lang['error_mysql']));
                        require_once 'footer.php';
                        exit();
                    }
                }
            }
            // Xử lý Upload File
        }
        $local          = getGlobal(_TABLE_LOCAL, array('id' => $id));
        $local_users    = getGlobal(_TABLE_USERS, array('users_id' => $local['local_users']));
        $admin_title    = $local['local_title'];
        require_once 'header.php';
        ?>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header"><h4 class="card-title"><?php echo $local['local_title'];?> (<?php echo $local_users['users_name'];?> - <?php echo getViewTime($local['local_time'])?>)</h4></div>
                                <div class="card-body"><?php echo $local['local_content'];?></div>
                            </div>
                            <?php
                            foreach (getGlobalAll('dong_comment', array('comment_type' => 'local', 'comment_value' => $id)) AS $comment) {
                                $comment_users = getGlobal('dong_users', array('users_id' => $comment['comment_users']));
                                ?>
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">[REP] <?php echo $admin_title; ?><hr>
                                            <small><?php echo 'Từ: <a href="' . _URL_ADMIN . '/users.php?type=update&id=' . $comment_users['users_id'] . '"><strong>' . $comment_users['users_name'] . '</strong></a> lúc ' . getViewTime($comment['comment_time']);?></small>
                                        </h4>
                                    </div>
                                    <div class="card-body"><?php echo $comment['comment_content'];?></div>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                    <?php if($tasks['task_status'] != 2){?>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header"><h4 class="card-title">Trả lời</h4> </div>
                                    <div class="card-body">
                                        <textarea style="width: 100%" name="local_comment"></textarea><hr>
                                        <div class="row">
                                            <div class="col"><input type="file" class="form-control-file" name="local_files[]" multiple="multiple"></div>
                                            <div class="col"><div class="text-right"><input type="submit" name="submit" class="btn btn-round btn-success" value="<?php echo $lang['label_reply'];?>"></div></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php }?>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header"><h4 class="card-title"> <?php echo $lang['tasks_file'];?></h4> </div>
                        <div class="card-body">
                            <table class="table">
                                <tbody>
                                <?php
                                $tasks_files = getGlobalAll('dong_files', array('files_type' => 'local', 'files_value' => $id));
                                foreach ($tasks_files AS $files){
                                    $files_users = getGlobal('dong_users', array('users_id' => $files['files_users']));
                                    echo '<tr><td><a href="'. _URL_HOME .'/dl/'. $files['id'] .'"><strong>'. $files['files_name'] .'</strong></a> (<a href="'. _URL_ADMIN.'/users.php?type=update&id='. $files_users['users_name'] .'">'. $files_users['users_name'] .'</a> - '. getViewTime($files['files_time']) .')</td></tr>';
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <?php
        require_once 'footer.php';
        break;
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
                    'local_type'        => 'users',
                    'local_users'       => $user_id,
                    'local_star'        => 0,
                    'local_star_users'  => 0,
                    'local_status'      => 1,
                    'local_date'        => date('Y-m-d', time()),
                    'local_time'        => _CONFIG_TIME
                );
                // Thêm Kế Hoạch
                $add = insertGlobal(_TABLE_LOCAL, $data);
                if(!$add){
                    $admin_title    = $admin_title;
                    require_once 'header.php';
                    echo getAdminPanelError(array('header' => $lang['label_notice'], 'message' => 'Lỗi khi thêm công việc'));
                    require_once 'footer.php';
                    break;
                }
                // Xử lý Upload File
                require_once '../includes/lib/class.uploader.php';
                $uploader   = new Uploader();
                $path       =  '../files/local';
                $data_upload = $uploader->upload($_FILES['local_files'], array(
                    'limit'         => 10, //Maximum Limit of files. {null, Number}
                    'maxSize'       => 10, //Maximum Size of files {null, Number(in MB's)}
                    'extensions'    => null, //Whitelist for file extension. {null, Array(ex: array('jpg', 'png'))}
                    'required'      => false, //Minimum one file is required for upload {Boolean}
                    'uploadDir'     => $path.'/', //Upload directory {String}
                    'title'         => null, //New file name {null, String, Array} *please read documentation in README.md
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
                        insertGlobal('dong_files', array(
                            'files_name'    => $file_name,
                            'files_url'     => 'files/local/'.$file_name,
                            'files_value'   => $add,
                            'files_type'    => 'local',
                            'files_users'   => $user_id,
                            'files_time'    => _CONFIG_TIME
                        ));
                    }
                }
                // Xử lý Upload File
                header('location:'._URL_ADMIN);
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
                            <fieldset class="form-group"><input type="file" class="form-control-file" name="local_files[]" multiple="multiple"></fieldset>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <?php
        require_once 'footer.php';
        break;
    default:
        if($type == 'users'){
            $user   = getGlobal(_TABLE_USERS, array('users_id' => $id));
            $admin_title = 'Kế Hoạch Nội Bộ Của '.$user['users_name'];
        }else if($type == 'room'){
            $room   = getGlobal(_TABLE_CATEGORY, array('id' => $id));
            $admin_title = 'Kế Hoạch Nội Bộ Của '.$room['category_name'];
        }
        // Pagination
        foreach ($para AS $paras){
            if(isset($_REQUEST[$paras]) && !empty($_REQUEST[$paras])){
                $parameters[$paras] = $_REQUEST[$paras];
            }
        }
        $parameters['local_type']     = $type;
        $parameters['local_users']    = $id;
        if($parameters){
            foreach ($parameters as $key => $value) {
                $colums[] = '`'.$key .'` = "'. checkInsert($value) .'"';
            }
            $parameters_list = ' WHERE '.implode(' AND ', $colums);
        }

        // Tạo Url Parameter động
        foreach ($parameters as $key => $value) {
            $para_url[] = $key .'='. $value;
        }
        $para_list                      = implode('&', $para_url);
        // Tạo Url Parameter động
        $config_pagenavi['page_row']    = _CONFIG_PAGINATION;
        $config_pagenavi['page_num']    = ceil(checkGlobal(_TABLE_LOCAL, $parameters)/$config_pagenavi['page_row']);
        $config_pagenavi['url']         = _URL_ADMIN.'/local.php?'.$para_list.'&';
        $page_start                     = ($page-1) * $config_pagenavi['page_row'];
        $data   = getGlobalAll(_TABLE_LOCAL, $parameters,array(
            'order_by_row'  => 'id',
            'order_by_value'=> 'DESC',
            'limit_start'   => $page_start,
            'limit_number'  => $config_pagenavi['page_row']
        ));
        // Pagination
        require_once 'header.php';
        ?>
        <div class="row">
            <div id="recent-transactions" class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title"><?=$admin_title?></h4>
                        <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                        <div class="heading-elements">
                            <ul class="list-inline mb-0">
                                <li><a class="btn btn-sm btn-outline-blue box-shadow-2 round btn-min-width pull-right" href="<?php echo _URL_ADMIN.'/local.php?act=add&type='.$type?>" target="_blank">Thêm Kế Hoạch</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-content">
                        <?php
                        if(count($data) == 0){
                            echo '<div class="text-center text-danger">Không có dữ liệu để hiển thị</div><br>';
                        }else{
                        ?>
                        <div class="table-responsive">
                            <table id="recent-orders" class="table table-hover table-xl mb-0">
                                <thead>
                                <tr>
                                    <th class="border-top-0" width="50%">Tiêu đề</th>
                                    <th class="border-top-0">Người Đăng</th>
                                    <th class="border-top-0">Đánh Giá</th>
                                    <th class="border-top-0">Thời Gian</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                foreach ($data AS $row){
                                    $user_local = getGlobal(_TABLE_USERS, array('users_id' => $row['local_users']));
                                    ?>
                                    <tr>
                                        <td class="text-truncate"><i class="la la-dot-circle-o success font-medium-1 mr-1"></i> <a href="<?=_URL_ADMIN?>/local.php?act=detail&id=<?=$row['id']?>"><?=$row['local_title']?></a></td>
                                        <td class="text-truncate">
                                            <span class="avatar avatar-xs"><img class="box-shadow-2" src="<?php echo $user_local['users_avatar'] ? _URL_HOME.'/'.$user_local['users_avatar'] : 'images/avatar.png' ?>" alt="avatar"></span>
                                            <span><?=$user_local['users_name']?></span>
                                        </td>
                                        <td>
                                            <?php
                                            if($row['local_star'] == 0){
                                                echo '<button type="button" class="btn btn-sm btn-outline-danger round">Chưa đánh giá</button>';
                                            }
                                            ?>
                                        </td>
                                        <td class="text-truncate"><?=getViewTime($row['local_time'])?></td>
                                    </tr>
                                <?php
                                }
                                ?>
                                </tbody>
                            </table>
                            <?php echo '<nav aria-label="Page navigation">'.pagination($config_pagenavi).'</nav>';?>
                        </div>
                        <?php }?>
                    </div>
                </div>
            </div>
        </div>
        <?php
        require_once 'footer.php';
        break;
}