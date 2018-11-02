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
        $admin_title = 'Kế hoạch nội bộ của bạn';
        require_once 'header.php';
        ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"><h4 class="card-title"><?php echo $lang['post_list'];?> <a href="<?php echo _URL_ADMIN.'/local.php?act=add'?>" class="btn round btn-outline-cyan">Thêm kế hoạch nội bộ</a> </h4></div>
                    <div class="card-body">
                        <?php
                        $para = array('local_users' => $user_id);
                        foreach ($para AS $paras){
                            if(isset($_REQUEST[$paras]) && !empty($_REQUEST[$paras])){
                                $parameters[$paras] = $_REQUEST[$paras];
                            }
                        }
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
                        echo '<div class="table-responsive">';
                            echo '<table class="table">';
                                echo '<thread>';
                                    echo '<tr>';
                                        echo '<th width="80%" class="text-left">Tiêu Đề</th>';
                                        echo '<th width="20%" class="text-center">Thời Gian</th>';
                                    echo '</tr>';
                                echo '</thread>';
                                '<tbody>';
                                foreach ($data AS $datas){
                                    echo '<tr>';
                                    echo '<td class="text-left"><a href="'. _URL_ADMIN .'/local.php?act=detail&id='. $datas['id'] .'">'. $datas['local_title'] .'</a></td>';
                                    echo '<td class="text-center">'. getViewTime($datas['local_time']) .'</td>';
                                    echo '</tr>';
                                }
                                echo '</tbody>';
                            echo '</table>';
                        echo '</div>';
                        echo '<nav aria-label="Page navigation">'.pagination($config_pagenavi).'</nav>';
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
        require_once 'footer.php';
        break;
}