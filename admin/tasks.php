<?php
/**
 * Created by PhpStorm.
 * User: nguye
 * Date: 04/04/2018
 * Time: 21:17
 */
require_once "../includes/core.php";
require_once "../includes/lib/class.uploader.php";

if(!$user_id){
    header('location:'._URL_LOGIN);
}
$active_menu    = 'tasks';
$css_plus       = array('app-assets/vendors/css/forms/icheck/icheck.css','app-assets/css/plugins/forms/checkboxes-radios.css','app-assets/css/chosen.css');
$js_plus        = array('app-assets/js/scripts/tinymce/js/tinymce/tinymce.min.js','app-assets/js/chosen.jquery.js','app-assets/js/prism.js','app-assets/js/init.js');

switch ($act){
    case 'del':
        $tasks = getGlobal(_TABLE_TASKS, array('id' => $id));

        // Kiểm tra nếu việc làm không có thì thông báo lỗi
        if (!$tasks){
            $admin_title    = $lang['tasks_detail'];
            require_once 'header.php';
            echo getAdminPanelError(array('header' => $lang['label_notice'], 'message' => $lang['tasks_empty_content']));
            break;
        }

        // Kiểm tra nếu user dăng nhập không phải người gửi không xóa được
        if($tasks['task_from'] != $user_id){
            $admin_title    = $lang['tasks_detail'];
            require_once 'header.php';
            echo getAdminPanelError(array('header' => $lang['label_notice'], 'message' => $lang['tasks_empty_content']));
            break;
        }

        if($submit){
            deleteGlobal('dong_comment', array('comment_type' => 'tasks', 'comment_value' => $id));
            deleteGlobal('dong_files', array('files_type' => 'tasks', 'files_value' => $id));
            deleteGlobal('dong_group', array('group_type' => 'tasks', 'group_id' => $id));
            deleteGlobal('dong_notification', array('notification_type' => 'tasks', 'notification_value' => $id));
            deleteGlobal('dong_task', array('id' => $id));
            header('location:'._URL_ADMIN.'/tasks.php');
        }

        $admin_title = $lang['post_del'];
        require_once 'header.php';
        ?>
        <form action="" method="post">
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-header"><h4 class="card-title"><?php echo $lang['post_del'];?></h4></div>
                        <div class="card-body text-center">
                            Bạn có chắc chắn muốn xóa bài viết <strong><?php echo $tasks['task_name'];?></strong> không<hr/>
                            <?php echo _BUTTON_BACK;?> <input type="submit" name="submit" class="btn btn round btn-outline-success" value="<?php echo $lang['label_del'];?>">
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <?php
        break;
    case 'report':
        if($submit){
            $tasks_name 	    = isset($_POST['tasks_name'])       ? trim($_POST['tasks_name'])        : '';
            $tasks_content      = isset($_POST['tasks_content'])    ? trim($_POST['tasks_content'])     : '';
            $to_star            = isset($_POST['to_star'])          ? trim($_POST['to_star'])           : '';
            $tasks_users 	    = isset($_POST['tasks_users'])      ? $_POST['tasks_users']             : '';
            $error              = array();
            if(!$to_star){
                $error['to_star'] = 'Bạn cần nhập đánh giá của mình';
            }
            if(!$tasks_name){
                $error['tasks_name'] = 'Bạn cần nhập tiêu đề';
            }
            if(!$tasks_content){
                $error['tasks_content'] = 'Bạn cần nhập nội dung';
            }

            if(!$error){
                $data = array(
                    'task_name'         => $tasks_name,
                    'task_content'      => $tasks_content,
                    'task_type'         => 'report',
                    'task_from'         => $user_id,
                    'task_start'        => date('Y/m/d', _CONFIG_TIME),
                    'task_end'          => date('Y/m/d', _CONFIG_TIME),
                    'task_time_rep'     => _CONFIG_TIME,
                    'task_from_star'    => $to_star,
                    'task_to_star'      => $to_star,
                    'task_to_star_user' => $user_id,
                    'task_status'       => 2,
                    'task_time'         => _CONFIG_TIME,
                    'task_guide'        => ''
                );
                // Thêm công việc
                $add = insertGlobal(_TABLE_TASKS, $data);
                if(!$add){
                    $admin_title    = $admin_title;
                    require_once 'header.php';
                    echo getAdminPanelError(array('header' => $lang['label_notice'], 'message' => 'Lỗi khi thêm công việc'));
                    break;
                }

                // Thêm người nhận là chính mình
                if(!insertGlobal(_TABLE_GROUP, array(
                    'group_type'    => 'tasks',
                    'group_id'      => $add,
                    'group_value'   => $user_id,
                    'group_users'   => $user_id,
                    'group_time'    => _CONFIG_TIME
                ))){
                    $admin_title    = $admin_title;
                    require_once 'header.php';
                    echo getAdminPanelError(array('header' => $lang['label_notice'], 'message' => 'Lỗi khi thêm người nhận việc'));
                    break;
                }

                // Gửi thông báo cho người nhận thông báo
                foreach ($tasks_users AS $list_users){
                    $data_notification = array(
                        'notification_send'     => $user_id,
                        'notification_to'       => $list_users['users_id'],
                        'notification_type'     => $active_menu,
                        'notification_value'    => $add,
                        'notification_content'  => 'notification_add_report',
                        'notification_time'     => _CONFIG_TIME
                    );
                    if($user_id != $list_users['users_id']){
                        if(!insertGlobal(_TABLE_NOTIFICATION, $data_notification)){
                            require_once 'header.php';
                            echo getAdminPanelError(array('header' => $admin_title, 'message' => 'Notification User: '.$lang['error_mysql'] ));
                            break;
                        }
                    }
                }
                // Xử lý Upload File
                require_once '../includes/lib/class.uploader.php';
                $uploader   = new Uploader();
                $path       =  '../files/tasks';
                $data_upload = $uploader->upload($_FILES['tasks_files'], array(
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
                            'files_url'     => 'files/tasks/'.$file_name,
                            'files_value'   => $add,
                            'files_type'    => 'tasks',
                            'files_users'   => $user_id,
                            'files_time'    => _CONFIG_TIME
                        ));
                    }
                }
                // Xử lý Upload File
                header('location:'._URL_ADMIN.'/tasks.php?act=detail&id='.$add);
            }
        }
        $admin_title = 'Tạo báo cáo';
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
                                    <?php echo inputFormText(array('name' => 'tasks_name', 'value' => $tasks_name, 'require' => TRUE, 'label' => 'Nhập tiêu đề', 'error' => $error['tasks_name']));?>
                                    <?php echo inputFormTextarea(array('name' => 'tasks_content', 'value' => $tasks_content, 'error' => $error['tasks_content']));?>
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
                            <h4 class="card-title">Những người nhận thông báo</h4>
                        </div>
                        <div class="card-body">
                            <select name="tasks_users[]" data-placeholder="Nhập tên người nhận thông báo" multiple class="chosen-select-width form-control">
                                <option value=""></option>
                                <?php
                                $users_list = getGlobalAll('dong_users', '', array('order_by_row' => 'users_name', 'order_by_value' => 'ASC'));
                                foreach ($users_list AS $users){
                                    echo '<option value="'. $users['users_id'] .'" '. ((in_array($users['users_id'], $tasks_users)) ? 'selected' : '') .'>'. $users['users_name'] .'</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header"><h4 class="card-title">Tự đánh giá kết quả công việc</h4> </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col text-center">
                                    <label class="text-success"><input type="radio" name="to_star" value="3"><hr />
                                    <strong><?php echo $lang['tasks_star_3']?></strong></label>
                                </div>
                                <div class="col text-center">
                                    <label class="text-info"><input type="radio" name="to_star" value="2"><hr />
                                    <strong><?php echo $lang['tasks_star_2']?></strong></label>
                                </div>
                                <div class="col text-center">
                                    <label class="text-danger"><input type="radio" name="to_star" value="1"><hr />
                                    <strong><?php echo $lang['tasks_star_1']?></strong></label>
                                </div>
                            </div>
                            <hr />
                            <?php echo $error['to_star'] ? $error['to_star'] : '';?>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title"><?php echo $lang['tasks_input_files']?></h4>
                        </div>
                        <div class="card-body">
                            <fieldset class="form-group"><input type="file" class="form-control-file" name="tasks_files[]" multiple="multiple"></fieldset>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <?php
        break;
    case 'result':
        $date           = date('Y-m-d', strtotime('monday this week', _CONFIG_TIME)); // monday
        $time_sunday    = date("Y-m-d", strtotime('sunday this week', strtotime($date)));
        if($date == $time_sunday){
            $week_1 = $date;
            $week_2 = date('Y-m-d', strtotime("$date -6 day"));
            $week_3 = date('Y-m-d', strtotime("$date -13 day"));
            $week_4 = date('Y-m-d', strtotime("$date -20 day"));
            $week_5 = date('Y-m-d', strtotime("$date -27 day"));
        }else{
            $week_1 = $time_sunday;
            $week_2 = date('Y-m-d', strtotime("$time_sunday -6 day"));
            $week_3 = date('Y-m-d', strtotime("$time_sunday -13 day"));
            $week_4 = date('Y-m-d', strtotime("$time_sunday -20 day"));
            $week_5 = date('Y-m-d', strtotime("$time_sunday -27 day"));
        }

        $admin_title = $lang['tasks_result'];
        require_once 'header.php';
        ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title"><?php echo $lang['tasks_result_header'];?></h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 text-center"><a href="<?php echo _URL_ADMIN .'/report.php?act=export&type=me&start='. $week_2 .'&end='. $week_1;?>"><button class="btn btn-rose"><i class="material-icons">save_alt</i> <?php echo 'Tuần từ '. date('d-m-Y', strtotime($week_2)) .' đến '. date('d-m-Y', strtotime($week_1));?></button></a></div>
                            <div class="col-md-3 text-center"><a href="<?php echo _URL_ADMIN .'/report.php?act=export&type=me&start='. $week_3 .'&end='. $week_2;?>"><button class="btn btn-rose"><i class="material-icons">save_alt</i> <?php echo 'Tuần từ '. date('d-m-Y', strtotime($week_3)) .' đến '. date('d-m-Y', strtotime($week_2));?></button></a></div>
                            <div class="col-md-3 text-center"><a href="<?php echo _URL_ADMIN .'/report.php?act=export&type=me&start='. $week_4 .'&end='. $week_3;?>"><button class="btn btn-rose"><i class="material-icons">save_alt</i> <?php echo 'Tuần từ '. date('d-m-Y', strtotime($week_4)) .' đến '. date('d-m-Y', strtotime($week_3));?></button></a></div>
                            <div class="col-md-3 text-center"><a href="<?php echo _URL_ADMIN .'/report.php?act=export&type=me&start='. $week_5 .'&end='. $week_4;?>"><button class="btn btn-rose"><i class="material-icons">save_alt</i> <?php echo 'Tuần từ '. date('d-m-Y', strtotime($week_5)) .' đến '. date('d-m-Y', strtotime($week_4));?></button></a></div>
                        </div>
                        <span style="color: red">* <i>Chú ý: Các bạn cần xem kết quả công việc trong khoảng thời gian khác. Liên hệ với BQT</i></span>
                    </div>
                </div>
                <?php
                    if(checkGlobal('dong_users', array('users_manager' => $user_id)) > 0){
                        ?>
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title"><?php echo $lang['tasks_result_manager'];?></h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table">
                                        <tr>
                                            <td><strong>Tất cả</strong></td>
                                            <td><a href="<?php echo _URL_ADMIN .'/report.php?act=export&type=users_manager_all&start='. $week_2 .'&end='. $week_1;?>"><button class="btn btn-success"><i class="material-icons">save_alt</i> <?php echo 'Tuần từ '. date('d-m-Y', strtotime($week_2)) .' đến '. date('d-m-Y', strtotime($week_1));?></button></a></td>
                                            <td><a href="<?php echo _URL_ADMIN .'/report.php?act=export&type=users_manager_all&start='. $week_3 .'&end='. $week_2;?>"><button class="btn btn-success"><i class="material-icons">save_alt</i> <?php echo 'Tuần từ '. date('d-m-Y', strtotime($week_3)) .' đến '. date('d-m-Y', strtotime($week_2));?></button></a></td>
                                            <td><a href="<?php echo _URL_ADMIN .'/report.php?act=export&type=users_manager_all&start='. $week_4 .'&end='. $week_3;?>"><button class="btn btn-success"><i class="material-icons">save_alt</i> <?php echo 'Tuần từ '. date('d-m-Y', strtotime($week_4)) .' đến '. date('d-m-Y', strtotime($week_3));?></button></a></td>
                                            <td><a href="<?php echo _URL_ADMIN .'/report.php?act=export&type=users_manager_all&start='. $week_5 .'&end='. $week_4;?>"><button class="btn btn-success"><i class="material-icons">save_alt</i> <?php echo 'Tuần từ '. date('d-m-Y', strtotime($week_5)) .' đến '. date('d-m-Y', strtotime($week_4));?></button></a></td>
                                        </tr>
                                        <?php
                                        foreach (getGlobalAll('dong_users', array('users_manager' => $user_id)) AS $task_users){
                                        ?>
                                        <tr>
                                            <td><strong><?php echo $task_users['users_name'];?></strong></td>
                                            <td><a href="<?php echo _URL_ADMIN .'/report.php?act=export&type=users_manager&id='. $task_users['users_id'] .'&start='. $week_2 .'&end='. $week_1;?>"><button class="btn btn-rose"><i class="material-icons">save_alt</i> <?php echo 'Tuần từ '. date('d-m-Y', strtotime($week_2)) .' đến '. date('d-m-Y', strtotime($week_1));?></button></a></td>
                                            <td><a href="<?php echo _URL_ADMIN .'/report.php?act=export&type=users_manager&id='. $task_users['users_id'] .'&start='. $week_3 .'&end='. $week_2;?>"><button class="btn btn-rose"><i class="material-icons">save_alt</i> <?php echo 'Tuần từ '. date('d-m-Y', strtotime($week_3)) .' đến '. date('d-m-Y', strtotime($week_2));?></button></a></td>
                                            <td><a href="<?php echo _URL_ADMIN .'/report.php?act=export&type=users_manager&id='. $task_users['users_id'] .'&start='. $week_4 .'&end='. $week_3;?>"><button class="btn btn-rose"><i class="material-icons">save_alt</i> <?php echo 'Tuần từ '. date('d-m-Y', strtotime($week_4)) .' đến '. date('d-m-Y', strtotime($week_3));?></button></a></td>
                                            <td><a href="<?php echo _URL_ADMIN .'/report.php?act=export&type=users_manager&id='. $task_users['users_id'] .'&start='. $week_5 .'&end='. $week_4;?>"><button class="btn btn-rose"><i class="material-icons">save_alt</i> <?php echo 'Tuần từ '. date('d-m-Y', strtotime($week_5)) .' đến '. date('d-m-Y', strtotime($week_4));?></button></a></td>
                                        </tr>
                                        <?php
                                        }
                                        ?>
                                    </table>
                                </div>
                                <span style="color: red">* <i>Chú ý: Các bạn cần xem kết quả công việc trong khoảng thời gian khác. Liên hệ với BQT</i></span>
                            </div>
                        </div>
                        <?php
                    }
                ?>
            </div>
        </div>
        <?php
        break;
    case 'add':
        if($submit){
            $tasks_name 	    = isset($_POST['tasks_name'])       ? trim($_POST['tasks_name'])        : '';
            $tasks_content      = isset($_POST['tasks_content'])    ? trim($_POST['tasks_content'])     : '';
            $tasks_start 	    = isset($_POST['tasks_start'])      ? trim($_POST['tasks_start'])       : '';
            $tasks_end 		    = isset($_POST['tasks_end'])        ? trim($_POST['tasks_end'])         : '';
            $tasks_guide 	    = isset($_POST['tasks_guide'])      ? trim($_POST['tasks_guide'])       : '';

            // Lấy danh sách các thành viên
            $tasks_users 	    = isset($_POST['tasks_users'])      ? $_POST['tasks_users']       : '';
            $tasks_users_group 	= isset($_POST['tasks_users_group'])? $_POST['tasks_users_group'] : '';
            $tasks_users_role 	= isset($_POST['tasks_users_role']) ? $_POST['tasks_users_role']  : '';
            $tasks_users_room 	= isset($_POST['tasks_users_room']) ? $_POST['tasks_users_room']  : '';
            $users_list         = array();
            // Thành viên được chọn
            $users_list         = $tasks_users;

            // Nhóm thành viên
            foreach($tasks_users_group AS $list_group){
                $for_group = getGlobalAll(_TABLE_GROUP, array('group_type' => 'users_group', 'group_id' => $list_group));
                foreach($for_group AS $for_group_list){
                    $users_list[] = $for_group_list['group_value'];
                }
            }
            // Chức vụ
            foreach($tasks_users_role AS $list_role){
                foreach(getGlobalAll(_TABLE_USERS, array('users_level' => $list_role)) AS $for_role_list){
                    $users_list[] = $for_role_list['users_id'];
                }
            }

            // Phòng Ban
            foreach($tasks_users_room AS $list_room){
                foreach(getGlobalAll(_TABLE_USERS, array('users_room' => $list_room)) AS $for_room_list){
                    $users_list[] = $for_room_list['users_id'];
                }
            }
            $users_list = array_unique($users_list);
            $users_list = array_diff($users_list, [$user_id]);
            $error              = array();

            // Kiểm tra lỗi
            if(!$tasks_name){
                $error['tasks_name']    = getViewError($lang['error_empty_this_fiel']);
            }
            if(!$tasks_content){
                $error['tasks_content'] = getViewError($lang['error_empty_this_fiel']);
            }
            if(!$tasks_start){
                $error['tasks_start']   = getViewError($lang['error_empty_this_fiel']);
            }else{
                $tasks_start = DateTime::createFromFormat('d/m/Y', $tasks_start);
                $tasks_start = $tasks_start->format('Y/m/d');
            }
            if(!$tasks_end){
                $error['tasks_end']     = getViewError($lang['error_empty_this_fiel']);
            }else{
                $tasks_end = DateTime::createFromFormat('d/m/Y', $tasks_end);
                $tasks_end = $tasks_end->format('Y/m/d');
            }

            if(!$users_list){
                $error['tasks_users']   = getViewError('Bạn cần nhập người nhận việc');
            }

            if(!checkDateFormart($tasks_start)){
                $error['tasks_start']   = getViewError('Định dạng ngày tháng sai. (VD 2018/12/31)');
            }
            if(!checkDateFormart($tasks_end)){
                $error['tasks_end']   = getViewError('Định dạng ngày tháng sai. (VD 2018/12/31)');
            }

            if(!$error){
                $data = array(
                        'task_name'         => $tasks_name,
                        'task_content'      => $tasks_content,
                        'task_type'         => 'tasks',
                        'task_from'         => $user_id,
                        'task_start'        => $tasks_start,
                        'task_end'          => $tasks_end,
                        'task_time_rep'     => 0,
                        'task_from_star'    => 0,
                        'task_to_star'      => 0,
                        'task_to_star_user' => 0,
                        'task_status'       => 0,
                        'task_time'         => _CONFIG_TIME,
                        'task_guide'        => $tasks_guide
                );
                $add = insertGlobal(_TABLE_TASKS, $data);
                if($add){
                    // Send user receive
                    foreach ($users_list AS $users_receive){
                        insertGlobal(_TABLE_GROUP, array(
                            'group_type'    => 'tasks',
                            'group_id'      => $add,
                            'group_value'   => $users_receive,
                            'group_users'   => $data_user['users_id'],
                            'group_time'    => _CONFIG_TIME
                        ));

                        // Send Notification
                        insertGlobal('dong_notification', array(
                            'notification_send'     => $user_id,
                            'notification_to'       => $users_receive,
                            'notification_type'     => $active_menu,
                            'notification_value'    => $add,
                            'notification_status'   => 0,
                            'notification_content'  => 'notification_add_task',
                            'notification_time'     => _CONFIG_TIME
                        ));
                    }
                    // Xử lý Upload File
                    require_once '../includes/lib/class.uploader.php';
                    $uploader   = new Uploader();
                    $path       =  '../files/tasks';
                    $data_upload = $uploader->upload($_FILES['tasks_files'], array(
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
                                'files_url'     => 'files/tasks/'.$file_name,
                                'files_value'   => $add,
                                'files_type'    => 'tasks',
                                'files_users'   => $user_id,
                                'files_time'    => _CONFIG_TIME
                            ));
                        }
                    }
                    // Xử lý Upload File
                }
                header('location:'._URL_ADMIN.'/tasks.php?act=detail&id='.$add);
            }

        }
        $css_plus       = array(
            'app-assets/vendors/css/extensions/datedropper.min.css',
            'app-assets/vendors/css/extensions/timedropper.min.css',
            'app-assets/vendors/css/forms/icheck/icheck.css',
            'app-assets/css/plugins/forms/checkboxes-radios.css',
            'app-assets/css/chosen.css');
        $js_plus        = array(
            'app-assets/js/scripts/tinymce/js/tinymce/tinymce.min.js',
            'app-assets/vendors/js/extensions/datedropper.min.js',
            'app-assets/vendors/js/extensions/timedropper.min.js',
            'app-assets/js/chosen.jquery.js',
            'app-assets/js/prism.js',
            'app-assets/js/init.js');
        $admin_title    = $lang['tasks_add'];
        require_once 'header.php';
        ?>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title"><?php echo $admin_title?></h4>
                                </div>
                                <div class="card-body">
                                    <?php echo inputFormText(array('name' => 'tasks_name', 'value' => $tasks_name, 'require' => TRUE, 'label' => $lang['tasks_input_title'], 'error' => $error['tasks_name']));?>
                                    <?php echo inputFormTextarea(array('name' => 'tasks_content', 'value' => $tasks_content, 'error' => $error['tasks_content']));?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title"><?php echo $lang['tasks_date_start']?> <small>(Ngày/Tháng/Năm)</small></h4>
                                </div>
                                <div class="card-body">
                                    <input value="<?php echo date('d/m/Y', _CONFIG_TIME);?>" type="text" name="tasks_start" class="form-control input-lg" id="animate" placeholder="Date Dropper">
                                    <?php if($error['tasks_start']){ echo $error['tasks_start']; }?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title"><?php echo $lang['tasks_date_end']?> <small>(Ngày/Tháng/Năm)</small></h4>
                                </div>
                                <div class="card-body">
                                    <input type="text" name="tasks_end" class="form-control input-lg" id="animate_1" value="<?php echo $tasks_end;?>" placeholder="Date Dropper">
                                    <?php if($error['tasks_end']){ echo $error['tasks_end']; }?>
                                    <script language="JavaScript">
                                        $(document).ready(function(){
                                            $('#animate').dateDropper({
                                                dropWidth: 200,
                                                lang: 'vi',
                                                format: 'd/m/Y'
                                            });
                                            $('#animate_1').dateDropper({
                                                dropWidth: 200,
                                                lang: 'vi',
                                                format: 'd/m/Y'
                                            });
                                        });
                                    </script>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="card">
                                <div class="card-header"><h4 class="card-title"><?php echo $lang['tasks_input_guide']?></h4></div>
                                <div class="card-body">
                                    <textarea name="tasks_guide" style="width: 100%" rows="6"></textarea>
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
                    <!-- Danh sách thành viên -->
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title"><?php echo $lang['tasks_input_users'];?></h4>
                        </div>
                        <div class="card-body">
                            <select name="tasks_users[]" data-placeholder="Nhập tên người nhận" multiple class="chosen-select-width form-control">
                                <option value=""></option>
                                <?php
                                $users_list = getGlobalAll('dong_users', '', array('order_by_row' => 'users_name', 'order_by_value' => 'ASC'));
                                foreach ($users_list AS $users){
                                    echo '<option value="'. $users['users_id'] .'" '. ((in_array($users['users_id'], $tasks_users)) ? 'selected' : '') .'>'. $users['users_name'] .'</option>';
                                }
                                ?>
                            </select>
                            <?php if($error['tasks_users']){ echo $error['tasks_users']; }?>
                        </div>
                    </div>
                    <!-- Danh sách nhóm thành viên -->
                    <div class="card">
                        <div class="card-header"><h4 class="card-title">Group nhận việc</h4></div>
                        <div class="card-body">
                            <select name="tasks_users_group[]" data-placeholder="Nhập Group người nhận việc" multiple class="chosen-select-width form-control">
                                <option value=""></option>
                                <?php
                                $group_list = getGlobalAll(_TABLE_CATEGORY, array('category_type' => 'users_group'));
                                foreach ($group_list AS $users_group){
                                    echo '<option value="'. $users_group['id'] .'" '. ((in_array($users_group['id'], $tasks_users_group)) ? 'selected' : '') .'>'. $users_group['category_name'] .'</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <!-- Danh sách chức vụ -->
                    <div class="card">
                        <div class="card-header"><h4 class="card-title">Chức vụ</h4></div>
                        <div class="card-body">
                            <select name="tasks_users_role[]" data-placeholder="Nhập các chức vụ nhận việc" multiple class="chosen-select-width form-control">
                                <option value=""></option>
                                <?php
                                $role_list = getGlobalAll(_TABLE_CATEGORY, array('category_type' => 'role'));
                                foreach ($role_list AS $role_group){
                                    echo '<option value="'. $role_group['id'] .'" '. ((in_array($role_group['id'], $tasks_users_role)) ? 'selected' : '') .'>'. $role_group['category_name'] .'</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <!-- Danh sách phòng ban -->
                    <div class="card">
                        <div class="card-header"><h4 class="card-title">Phòng ban</h4></div>
                        <div class="card-body">
                            <select name="tasks_users_room[]" data-placeholder="Nhập các phòng ban nhận việc" multiple class="chosen-select-width form-control">
                                <option value=""></option>
                                <?php
                                $room_list = getGlobalAll(_TABLE_CATEGORY, array('category_type' => 'room'));
                                foreach ($room_list AS $room_group){
                                    echo '<option value="'. $room_group['id'] .'" '. ((in_array($room_group['id'], $tasks_users_room)) ? 'selected' : '') .'>'. $room_group['category_name'] .'</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title"><?php echo $lang['tasks_input_files']?></h4>
                        </div>
                        <div class="card-body">
                            <fieldset class="form-group"><input type="file" class="form-control-file" name="tasks_files[]" multiple="multiple"></fieldset>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <?php
        break;
    case 'detail':
        $tasks = getGlobal('dong_task', array('id' => $id));

        // Kiểm tra nếu user dăng nhập không phải người gửi hoặc người nhận thì không xem được
        if($tasks['task_from'] != $user_id && checkGlobal(_TABLE_GROUP, array('group_type' => 'tasks', 'group_id' => $id, 'group_value' => $user_id)) == 0 && $tasks['task_type'] == 'tasks'){
            $admin_title    = $lang['tasks_detail'];
            require_once 'header.php';
            echo getAdminPanelError(array('header' => $lang['label_notice'], 'message' => $lang['tasks_empty_content']));
            break;
        }

        // Kiểm tra nếu việc làm không có thì thông báo lỗi
        if (!$tasks){
            $admin_title    = $lang['tasks_detail'];
            require_once 'header.php';
            echo getAdminPanelError(array('header' => $lang['label_notice'], 'message' => $lang['tasks_empty_content']));
            break;
        }

        // Nếu xem status lần đầu thì mặc định chuyển trạng thái status thành đã nhận việc
        if($tasks['task_status'] == 0 && checkGlobal(_TABLE_GROUP, array('group_type' => 'tasks', 'group_id' => $id, 'group_value' => $user_id)) > 0){
            updateGlobal(_TABLE_TASKS, array('task_status' => 1), array('id' => $id));
            // Gửi thông báo cho người gửi là đã nhận việc
            $data_notification = array(
                'notification_send'     => $user_id,
                'notification_to'       => $tasks['task_from'],
                'notification_type'     => $active_menu,
                'notification_value'    => $id,
                'notification_content'  => 'notification_task_seen',
                'notification_time'     => _CONFIG_TIME
            );
            if(!insertGlobal(_TABLE_NOTIFICATION, $data_notification)){
                require_once 'header.php';
                echo getAdminPanelError(array('header' => $admin_title, 'message' => 'Notification Add: '.$lang['error_mysql'] ));
                require_once 'footer.php';
                exit();
            }
        }

        // Cập nhập thông báo thành đã đọc
        updateGlobal(_TABLE_NOTIFICATION, array('notification_status' => 1), array('notification_type' => $active_menu, 'notification_value' => $id, 'notification_to' => $user_id));

        // Người nhận việc đã hoàn thành và tự đánh giá
        if($_POST['submit_close']){
            $to_star    = isset($_POST['to_star'])    ? trim($_POST['to_star']) : '';
            $error      = array();
            if(!$to_star){
                $error['to_star']    = getViewError('Bạn cần tự đánh giá kết quả của mình trước khi hoàn thành công việc');
            }
            if(!$error){
                updateGlobal(_TABLE_TASKS, array('task_status' => 2, 'task_time_rep' => _CONFIG_TIME, 'task_to_star' => $to_star, 'task_to_star_user' => $user_id), array('id' => $id));
                // Gửi thông báo cho thành viên khác nhận việc
                foreach (getGlobalAll(_TABLE_GROUP, array('group_type' => 'tasks', 'group_id' => $id)) AS $send_notification){
                    $data_notification = array(
                        'notification_send'     => $user_id,
                        'notification_to'       => $send_notification['group_value'],
                        'notification_type'     => $active_menu,
                        'notification_value'    => $id,
                        'notification_content'  => 'notification_task_close',
                        'notification_time'     => _CONFIG_TIME
                    );
                    if($user_id != $send_notification['group_value']){
                        if(!insertGlobal(_TABLE_NOTIFICATION, $data_notification)){
                            require_once 'header.php';
                            echo getAdminPanelError(array('header' => $admin_title, 'message' => 'Notification User: '.$lang['error_mysql'] ));
                            require_once 'footer.php';
                            exit();
                        }
                    }
                }
                // Gửi thông báo cho người gửi  việc
                $data_notification = array(
                    'notification_send'     => $user_id,
                    'notification_to'       => $tasks['task_from'],
                    'notification_type'     => $active_menu,
                    'notification_value'    => $id,
                    'notification_content'  => 'notification_task_close',
                    'notification_time'     => _CONFIG_TIME
                );
                if(!insertGlobal(_TABLE_NOTIFICATION, $data_notification)){
                    require_once 'header.php';
                    echo getAdminPanelError(array('header' => $admin_title, 'message' => 'Notification Add: '.$lang['error_mysql'] ));
                    require_once 'footer.php';
                    exit();
                }
            }
        }

        // Người gửi việc đánh giá
        if($_POST['submit_from_star']){
            $from_star    = isset($_POST['from_star'])    ? trim($_POST['from_star']) : '';
            $error      = array();
            if(!$from_star){
                $error['from_star']    = getViewError('Bạn cần đánh giá kết quả trước khi gửi dữ liệu');
            }
            if(!$error){
                updateGlobal(_TABLE_TASKS, array('task_from_star' => $from_star), array('id' => $id));
                foreach (getGlobalAll(_TABLE_GROUP, array('group_type' => 'tasks', 'group_id' => $id)) AS $send_notification){
                    $data_notification = array(
                        'notification_send'     => $user_id,
                        'notification_to'       => $send_notification['group_value'],
                        'notification_type'     => $active_menu,
                        'notification_value'    => $id,
                        'notification_content'  => 'notification_task_send_close',
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
        }

        if($submit){
            // Thêm bình luận
            $tasks_content  = isset($_POST['tasks_content'])    ? trim($_POST['tasks_content']) : '';
            $data = array(
                    'comment_content'   => $tasks_content,
                    'comment_type'      => 'tasks',
                    'comment_value'     => $id,
                    'comment_users'     => $user_id,
                    'comment_time'      => _CONFIG_TIME
            );
            if($tasks_content){
                // Thêm bình luận
                if(!insertGlobal('dong_comment', $data)){
                    require_once 'header.php';
                    echo getAdminPanelError(array('header' => $admin_title, 'message' => 'Comment: '.$lang['error_mysql']));
                    require_once 'footer.php';
                    exit();
                }

                // Gửi thông báo đến các thành viên
                if($user_id == $tasks['task_from']){
                    foreach (getGlobalAll(_TABLE_GROUP, array('group_type' => 'tasks', 'group_id' => $id)) AS $send_notification){
                        $data_notification = array(
                            'notification_send'     => $user_id,
                            'notification_to'       => $send_notification['group_value'],
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
                }else{
                    // Gửi thông báo cho thành viên khác nhận việc
                    foreach (getGlobalAll(_TABLE_GROUP, array('group_type' => 'tasks', 'group_id' => $id)) AS $send_notification){
                        $data_notification = array(
                            'notification_send'     => $user_id,
                            'notification_to'       => $send_notification['group_value'],
                            'notification_type'     => $active_menu,
                            'notification_value'    => $id,
                            'notification_content'  => 'notification_add_comment',
                            'notification_time'     => _CONFIG_TIME
                        );
                        if($user_id != $send_notification['group_value']){
                            if(!insertGlobal(_TABLE_NOTIFICATION, $data_notification)){
                                require_once 'header.php';
                                echo getAdminPanelError(array('header' => $admin_title, 'message' => 'Notification User: '.$lang['error_mysql'] ));
                                require_once 'footer.php';
                                exit();
                            }
                        }
                    }
                    // Gửi thông báo cho người gửi  việc
                    $data_notification = array(
                        'notification_send'     => $user_id,
                        'notification_to'       => $tasks['task_from'],
                        'notification_type'     => $active_menu,
                        'notification_value'    => $id,
                        'notification_content'  => 'notification_add_comment',
                        'notification_time'     => _CONFIG_TIME
                    );
                    if(!insertGlobal(_TABLE_NOTIFICATION, $data_notification)){
                        require_once 'header.php';
                        echo getAdminPanelError(array('header' => $admin_title, 'message' => 'Notification Add: '.$lang['error_mysql'] ));
                        require_once 'footer.php';
                        exit();
                    }
                }

            }
            // Xử lý Upload File
            require_once '../includes/lib/class.uploader.php';
            $uploader   = new Uploader();
            $path       =  '../files/tasks';
            $data_upload = $uploader->upload($_FILES['tasks_files'], array(
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
                        'files_url'     => 'files/tasks/'.$file_name,
                        'files_value'   => $id,
                        'files_type'    => 'tasks',
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
        $tasks = getGlobal('dong_task', array('id' => $id));
        $admin_title = $tasks['task_name'];
        require_once 'header.php';
        ?>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header"><h4 class="card-title"><?php echo $lang['tasks_date_start']?></h4></div>
                                <div class="card-body"><input type="text" disabled class="form-control" value="<?php echo date('d/m/Y', strtotime($tasks['task_start']));?>" /></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header"><h4 class="card-title"><?php echo $lang['tasks_date_end']?></h4></div>
                                <div class="card-body"><input type="text" disabled class="form-control" value="<?php echo date('d/m/Y', strtotime($tasks['task_end']));?>" /></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header"><h4 class="card-title"><?php echo $admin_title;?></h4></div>
                                <div class="card-body">
                                    <?php
                                    $tasks_users = getGlobal('dong_users', array('users_id' => $tasks['task_from']));
                                    echo 'Từ: <a href="'. _URL_ADMIN .'/users.php?type=update&id='. $tasks_users['users_id'] .'"><strong>'. $tasks_users['users_name'] .'</strong></a> lúc '.getViewTime($tasks['task_time']).'<hr />';
                                    echo $tasks['task_content'];
                                    ?>
                                </div>
                            </div>
                            <?php
                            foreach (getGlobalAll('dong_comment', array('comment_type' => 'tasks', 'comment_value' => $id)) AS $comment) {
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
                                        <textarea style="width: 100%" name="tasks_content"><?php echo $tasks_content;?></textarea><hr>
                                        <div class="row">
                                            <div class="col"><input type="file" class="form-control-file" name="tasks_files[]" multiple="multiple"></div>
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
                        <div class="card-header"><h4 class="card-title"> <?php echo $lang['label_info'];?></h4> </div>
                        <div class="card-body">
                            <table class="table">
                                <tr>
                                    <td><?php echo $lang['tasks_users_send'];?></td>
                                    <td><?php echo getGlobalAll('dong_users', array('users_id' => $tasks['task_from']), array('onecolum' => 'users_name'));?></td>
                                </tr>
                                <tr>
                                    <td><?php echo $lang['tasks_users_receive'];?></td>
                                    <td>
                                        <?php
                                        foreach (getGlobalAll(_TABLE_GROUP, array('group_type' => 'tasks', 'group_id' => $id)) AS $lists_users_receive){
                                            $list_users_receive[] = '<a href="'. _URL_ADMIN .'/users.php?act=detail&id='. $lists_users_receive['group_value'] .'">'. getGlobalAll(_TABLE_USERS, array('users_id' => $lists_users_receive['group_value']), array('onecolum' => 'users_name')) .'</a>';
                                        }
                                        echo implode(', ', $list_users_receive);
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php echo $lang['post_status'];?></td>
                                    <td><?php echo $lang['tasks_status_'.$tasks['task_status']];?></td>
                                </tr>
                                <tr>
                                    <td><?php echo $lang['tasks_date_start'];?></td>
                                    <td><?php echo getViewTime($tasks['task_time']);?></td>
                                </tr>
                                <?php if($tasks['task_status'] == 2){
                                    $day = (strtotime($tasks['task_end']) - strtotime(date('Y-m-d', $tasks['task_time_rep']))) / (60 * 60 * 24);
                                    if($day < 0){
                                        $text_day = $lang['tasks_progess_1'];
                                        $text_day = str_replace('{day}', ($day * -1), $text_day);
                                    }else if($day == 0){
                                        $text_day = $lang['tasks_progess_2'];
                                    }else if($day > 0){
                                        $text_day = $lang['tasks_progess_3'];
                                        $text_day = str_replace('{day}', $day, $text_day);
                                    }
                                    ?>
                                    <tr>
                                        <td><?php echo $lang['tasks_date_end'];?></td>
                                        <td><?php echo getViewTime($tasks['task_time_rep']);?></td>
                                    </tr>
                                    <tr>
                                        <td><?php echo $lang['tasks_progess'];?></td>
                                        <td><?php echo $text_day;?></td>
                                    </tr>
                                <?php }
                                if($tasks['task_from_star'] != 0){
                                    echo '<tr>
                                    <td>Người giao việc đánh giá</td>
                                    <td>'. $lang['tasks_star_'.$tasks['task_from_star']] .'</td>
                                    </tr>';
                                }
                                if($tasks['task_to_star'] != 0){
                                    echo '<tr>
                                    <td>Tự đánh giá</td>
                                    <td>'. $lang['tasks_star_'.$tasks['task_to_star']] .'</td>
                                    </tr>';
                                }
                                if($tasks['task_to_star_user'] != 0){
                                    echo '<tr>
                                    <td>Người kết thúc công việc</td>
                                    <td><a href="'. _URL_ADMIN .'/users.php?act=detail&id='. $tasks['task_to_star_user'] .'">'. getGlobalAll(_TABLE_USERS, array('users_id' => $tasks['task_to_star_user']), array('onecolum' => 'users_name')) .'</a></td>
                                    </tr>';
                                }
                                if($tasks['task_from'] == $user_id){
                                    echo '<td colspan="2"><a href="tasks.php?act=del&id='. $id .'" class="text-danger"><i>* Xóa công việc này</i></a> </td>';
                                }
                                ?>
                            </table>
                        </div>
                    </div>
                    <?php
                    if($tasks['task_status'] != 2 && $user_id != $tasks['task_from'] && $tasks['task_to_star'] == 0){?>
                        <div class="card">
                            <div class="card-header"><h4 class="card-title">Tự đánh giá kết quả công việc</h4> </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col text-center"><label class="text-success"><input type="radio" name="to_star" value="3"><hr /><strong><?php echo $lang['tasks_star_3']?></strong></label></div>
                                    <div class="col text-center"><label class="text-info"><input type="radio" name="to_star" value="2"><hr /><strong><?php echo $lang['tasks_star_2']?></strong></label></div>
                                    <div class="col text-center"><label class="text-danger"><input type="radio" name="to_star" value="1"><hr /><strong><?php echo $lang['tasks_star_1']?></strong></label></div>
                                </div><hr />
                                <div class="text-center"><input type="submit" name="submit_close" class="btn round btn-success" value="Kết thúc công việc"></div>
                                <?php echo $error['to_star'] ? $error['to_star'] : '';?>
                            </div>
                        </div>
                        <?php
                    }
                    if($user_id == $tasks['task_from'] && $tasks['task_status'] == 2 && $tasks['task_from_star'] == 0){?>
                        <div class="card">
                            <div class="card-header"><h4 class="card-title">Đánh giá kết quả công việc</h4> </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 text-center"><label class="text-success"><input type="radio" name="from_star" value="3"><hr /><strong><?php echo $lang['tasks_star_3']?></strong></label></div>
                                    <div class="col-md-4 text-center"><label class="text-info"><input type="radio" name="from_star" value="2"><hr /><strong><?php echo $lang['tasks_star_2']?></strong></label></div>
                                    <div class="col-md-4 text-center"><label class="text-danger"><input type="radio" name="from_star" value="1"><hr /><strong><?php echo $lang['tasks_star_1']?></strong></label></div>
                                </div><hr />
                                <div class="text-center"><input type="submit" name="submit_from_star" class="btn btn-round btn-success" value="Đánh giá kết quả công việc"></div>
                                <?php echo $error['from_star'] ? $error['from_star'] : '';?>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                    <div class="card">
                        <div class="card-header"><h4 class="card-title"> <?php echo $lang['tasks_file'];?></h4> </div>
                        <div class="card-body">
                            <table class="table">
                                <tbody>
                                <?php
                                $tasks_files = getGlobalAll('dong_files', array('files_type' => 'tasks', 'files_value' => $id));
                                foreach ($tasks_files AS $files){
                                    $files_users = getGlobal('dong_users', array('users_id' => $files['files_users']));
                                    echo '<tr><td><a href="'. _URL_HOME .'/dl/'. $files['id'] .'"><strong>'. $files['files_name'] .'</strong></a> (<a href="'. _URL_ADMIN.'/users.php?type=update&id='. $files_users['users_name'] .'">'. $files_users['users_name'] .'</a> - '. getViewTime($files['files_time']) .')</td></tr>';
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header"><h4 class="card-title"> <?php echo $lang['tasks_input_guide'];?></h4> </div>
                        <div class="card-body">
                            <?php echo $tasks['task_guide'];?>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <?php
        break;
    default:
        $task_from      = $_GET['task_from'];
        $task_status    = $_GET['task_status'];
        $task_from_star = $_GET['task_from_star'];

        $para = array('task_from','task_status','task_from_star');
        foreach ($para AS $paras){
            if(isset($_REQUEST[$paras]) && !empty($_REQUEST[$paras])){
                $parameters[$paras] = $_REQUEST[$paras];
                if($parameters['task_status'] == 3){
                    $parameters['task_status'] = 0;
                }
                if($parameters['task_from_star'] == 'donot'){
                    $parameters['task_from_star'] = 0;
                }
            }
        }
        $parameters_list = '';
        if($type == 'send'){
            $parameters['task_from'] = $user_id;
        }
        if($parameters){
            foreach ($parameters as $key => $value) {
                if(!$type){
                    $colums[]  = _TABLE_TASKS.'.'.$key .' = "'. checkInsert($value) .'"';
                    $parameters_list   = ' '.implode(' AND ', $colums).' ';
                }else{
                    $colums[]  = '`'.$key .'` = "'. checkInsert($value) .'"';
                    $parameters_list   = ' WHERE '.implode(' AND ', $colums).' ';
                }
            }
        }

        // Tạo Url Parameter động
        foreach ($parameters as $key => $value) {
            $para_url[] = $key .'='. $value;
        }
        $para_list                      = implode('&', $para_url);
        // Tạo Url Parameter động

        $config_pagenavi['page_row']    = _CONFIG_PAGINATION;
        $config_pagenavi['url']         = _URL_ADMIN.'/tasks.php'.( $type ? '?type='.$type : '').( $parameters ? ($type ? '&'.$para_list : '?'.$para_list) : '' ).( (!$type && !$parameters) ? '?' : '&' );
        $page_start                     = ($page-1) * $config_pagenavi['page_row'];

        $css_plus       = array('app-assets/css/chosen.css');
        $js_plus        = array(
            'app-assets/js/chosen.jquery.js',
            'app-assets/js/prism.js',
            'app-assets/js/init.js'
        );
        $admin_title = $lang['tasks_manager'];
        require_once 'header.php';
        ?>
        <div class="row">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header"><h4 class="card-title"> Quản lý công việc </h4> </div>
                    <div class="card-content collapse show">
                        <ul class="list-group">
                            <li class="list-group-item">
                                <span class="float-left">
                                    <i class="la la-star-o mr-1"></i>
                                </span>
                                <a href="<?php echo _URL_ADMIN.'/tasks.php';?>" <?php echo !$type ? 'style="color: red; font-weight: bold"' : '';?>> Việc của bạn</a>
                            </li>
                            <li class="list-group-item">
                                <span class="float-left">
                                    <i class="la la-envelope-o mr-1"></i>
                                </span>
                                <a href="<?php echo _URL_ADMIN.'/tasks.php?type=send';?>" <?php echo $type == 'send' ? 'style="color: red; font-weight: bold"' : '';?>> Việc Đã Gửi</a>
                            </li>
                            <li class="list-group-item">
                                <span class="float-left">
                                    <i class="la la-check-circle-o mr-1"></i>
                                </span>
                                <a href="<?php echo _URL_ADMIN.'/tasks.php?type=report';?>" <?php echo $type == 'report' ? 'style="color: red; font-weight: bold"' : '';?>> Báo cáo của bạn</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <form action="" method="get">
                    <div class="row">
                        <?php
                        switch ($type){
                            case 'send':
                                $query                          = 'SELECT * FROM `'. _TABLE_TASKS .'` '.$parameters_list.' ORDER BY `id` DESC LIMIT '.$page_start.','.$config_pagenavi['page_row'];
                                $config_pagenavi['page_num']    = ceil(mysqli_num_rows(mysqli_query($db_connect, 'SELECT `id` FROM `'. _TABLE_TASKS .'` '.$parameters_list))/$config_pagenavi['page_row']);
                                $data_send_star                 = array('task_from' => $user_id, 'task_status' => 2, 'task_from_star' => 0);
                                $number_send_star               = checkGlobal(_TABLE_TASKS, $data_send_star);
                                $query_deadline                 = 'SELECT * FROM `'. _TABLE_TASKS .'` WHERE `task_from` = "'. $user_id .'" AND `task_end` < "'. date('Y-m-d', time()) .'" AND `task_status` != 2 ORDER BY `id` DESC LIMIT '. $page_start .','.$config_pagenavi['page_row'];
                                $number_deadline                = mysqli_num_rows(mysqli_query($db_connect, $query_deadline));
                                // Gán status = 4 thì query các việc bị trễ deadline
                                if($task_status == 4){
                                    $query = $query_deadline;
                                }
                                ?>
                                <div class="col text-left">
                                    <select name="task_status" class="form-control round">
                                        <option value="" <?php echo empty($task_status) ? 'selected="selected"' : '';?>>Chọn trạng thái</option>
                                        <option value="2" <?php echo $task_status == 2 ? 'selected="selected"' : '';?>>Đã hoàn thành</option>
                                        <option value="1" <?php echo $task_status == 1 ? 'selected="selected"' : '';?>>Đã nhận việc</option>
                                        <option value="3" <?php echo $task_status == 3 ? 'selected="selected"' : '';?>>Chưa nhận việc</option>
                                    </select>
                                    <input type="hidden" name="type" value="send">
                                </div>
                                <?php
                                if($number_send_star > 0){
                                    echo '<div class="col text-left"><a class="btn btn-outline-cyan round" href="tasks.php?type=send&task_status=2&task_from_star=donot">'. $number_send_star .' việc chưa đánh giá</a> </div>';
                                }
                                if($number_deadline > 0){
                                    echo '<div class="col text-left"><a class="btn btn-outline-cyan round" href="tasks.php?type=send&task_status=4">'. $number_deadline .' việc trễ Deadline</a> </div>';
                                }
                                echo '<div class="col text-right"><input type="submit" value="Lọc kết quả" class="btn btn-outline-cyan round"></div>';
                                break;
                            case 'report':
                                $query = 'SELECT * FROM `'. _TABLE_TASKS .'` WHERE `task_type` = "report" AND `task_from` = "'. $user_id .'"  ORDER BY `id` DESC LIMIT '.$page_start.','.$config_pagenavi['page_row'];
                                $config_pagenavi['page_num']    = ceil(mysqli_num_rows(mysqli_query($db_connect, 'SELECT `id` FROM `'. _TABLE_TASKS .'` WHERE `task_type` = "report" AND `task_from` = "'. $user_id .'"'))/$config_pagenavi['page_row']);
                                echo '<input type="hidden" name="type" value="report">';
                                break;
                            default:
                                $query = 'SELECT dong_group.group_id, dong_group.group_value, dong_task.* 
                                      FROM dong_group
                                      INNER JOIN dong_task 
                                      ON dong_group.group_id = dong_task.id 
                                      WHERE dong_group.group_type = "tasks" 
                                      AND dong_group.group_value = '. $user_id .' '. ($parameters_list ? ' AND '.$parameters_list : '') .' 
                                      ORDER BY dong_task.id DESC LIMIT '.$page_start.','.$config_pagenavi['page_row'];
                                $query_1 = 'SELECT dong_group.group_id, dong_group.group_value, dong_task.* 
                                      FROM dong_group
                                      INNER JOIN dong_task 
                                      ON dong_group.group_id = dong_task.id 
                                      WHERE dong_group.group_type = "tasks" 
                                      AND dong_group.group_value = '. $user_id .' '. ($parameters_list ? ' AND '.$parameters_list : '') .' 
                                      ORDER BY dong_task.id DESC';
                                $config_pagenavi['page_num']    = ceil(mysqli_num_rows(mysqli_query($db_connect, $query_1))/$config_pagenavi['page_row']);
                                ?>
                                <div class="col text-left">
                                    <select name="task_from" data-placeholder="Chọn người gửi" class="chosen-select-width form-control">
                                        <option value=""></option>
                                        <?php
                                        $users_list = getGlobalAll(_TABLE_USERS, '', array('order_by_row' => 'users_name', 'order_by_value' => 'ASC'));
                                        foreach ($users_list AS $users){
                                            echo '<option value="'. $users['users_id'] .'" '. (($task_from == $users['users_id']) ? 'selected' : '') .'>'. $users['users_name'] .'</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col text-left">
                                    <select name="task_status" class="form-control round">
                                        <option value="" <?php echo empty($task_status) ? 'selected="selected"' : '';?>>Chọn trạng thái</option>
                                        <option value="2" <?php echo $task_status == 2 ? 'selected="selected"' : '';?>>Đã hoàn thành</option>
                                        <option value="1" <?php echo $task_status == 1 ? 'selected="selected"' : '';?>>Đã nhận việc</option>
                                        <option value="3" <?php echo $task_status == 3 ? 'selected="selected"' : '';?>>Chưa nhận việc</option>
                                    </select>
                                </div>
                                <div class="col text-right"><input type="submit" value="Lọc kết quả" class="btn btn-outline-cyan round"></div>
                                <?php
                                break;
                        }
                        ?>
                    </div>
                    <br />
                </form>
                <div class="card">
                    <div class="card-body">
                        <?php
                        echo '<div class="table-responsive">';
                        echo '<table class="table">';
                        if($type == 'send'){
                            echo '<thead>';
                                echo '<th>Tiêu đề</th>';
                                echo '<th>Người nhận</th>';
                                echo '<th>Trạng thái</th>';
                                echo '<th>Thời gian</th>';
                            echo '</thead>';
                        }else if($type == 'report'){
                            echo '<thead>';
                            echo '<th width="80%">Tiêu đề</th>';
                            echo '<th width="20%">Thời gian</th>';
                            echo '</thead>';
                        }else{
                            echo '<thead>';
                            echo '<th>Người gửi</th>';
                            echo '<th>Tiêu đề</th>';
                            echo '<th>Trạng thái</th>';
                            echo '<th>Thời gian</th>';
                            echo '</thead>';
                        }
                        echo '<tbody>';
                        $data   = getGlobalAll(_TABLE_TASKS, array(), array('query' => $query));
                        if(!$data){
                            echo '<tr><td colspan="4" width="100%" class="text-center">Chưa có công việc nào.</td></tr>';
                        }
                        foreach ($data AS $datas){
                            switch ($datas['task_status']){
                                case 0:
                                    $color_text = 'text-danger';
                                    break;
                                case 1:
                                    $color_text = 'teal';
                                    break;
                                case 2:
                                    $color_text = 'purple';
                                    break;
                            }
                            if($type == 'send'){
                                $users_receive_data = array('group_id' => $datas['id'], 'group_type' => 'tasks');
                                $users_receives = '';
                                // Get danh sách người nhận
                                if(checkGlobal(_TABLE_GROUP, $users_receive_data) <= 3){
                                    foreach (getGlobalAll(_TABLE_GROUP, $users_receive_data) AS $users_list){
                                        $users_receives[] = '<a href="'. _URL_ADMIN .'/users.php?act=detail&id='. $users_list['group_value'] .'">'. getGlobalAll(_TABLE_USERS, array('users_id' => $users_list['group_value']), array('onecolum' => 'users_name')) .'</a>';
                                    }
                                    $users_receive = implode($users_receives, ', ');
                                }else{
                                    foreach (getGlobalAll(_TABLE_GROUP, $users_receive_data, array('limit_number' => 3, 'order_by_row' => 'id', 'order_by_value' => 'DESC')) AS $users_list){
                                        $users_receives[] = '<a href="'. _URL_ADMIN .'/users.php?act=detail&id='. $users_list['group_value'] .'">'. getGlobalAll(_TABLE_USERS, array('users_id' => $users_list['group_value']), array('onecolum' => 'users_name')) .'</a>';
                                    }
                                    $users_receive = implode($users_receives, ', ').' và '.(checkGlobal(_TABLE_GROUP, $users_receive_data) - 3).' người nữa';
                                }

                                echo '<tr>';
                                    echo '<td width="50%"><a href="'. _URL_ADMIN .'/tasks.php?act=detail&id='. $datas['id'] .'">'. ($datas['task_status'] == 0 ? '<strong class="'. $color_text .'">'. $datas['task_name'] .'</strong>' : '<font class="'. $color_text .'">'.$datas['task_name']) .'</a></td>';
                                    echo '<td width="20%">'. $users_receive .'</td>';
                                    echo '<td width="20%">'. $lang['tasks_status_'.$datas['task_status']]  .'</td>';
                                    echo '<td width="10%">'. getViewTime($datas['task_time']) .'</td>';
                                echo '</tr>';
                            }else if($type == 'report'){
                                echo '<tr>';
                                    echo '<td width="80%"><a href="'. _URL_ADMIN .'/tasks.php?act=detail&id='. $datas['id'] .'">'. ($datas['task_status'] == 0 ? '<strong class="'. $color_text .'">'. $datas['task_name'] .'</strong>' : '<font class="'. $color_text .'">'.$datas['task_name']) .'</a></a></td>';
                                    echo '<td width="20%">'. getViewTime($datas['task_time']) .'</td>';
                                echo '</tr>';
                            }
                            else{
                                echo '<tr>';
                                    echo '<td width="20%"><a href="'. _URL_ADMIN .'/users.php?act=detail&id='. $datas['task_from'] .'">'. getGlobalAll(_TABLE_USERS, array('users_id' => $datas['task_from']), array('onecolum' => 'users_name')) .'</a></td>';
                                    echo '<td width="50%"><a href="'. _URL_ADMIN .'/tasks.php?act=detail&id='. $datas['id'] .'">'. ($datas['task_status'] == 0 ? '<strong class="'. $color_text .'">'. $datas['task_name'] .'</strong>' : '<font class="'. $color_text .'">'.$datas['task_name']) .'</a></a></td>';
                                    echo '<td width="20%">'. $lang['tasks_status_'.$datas['task_status']]  .'</td>';
                                    echo '<td width="10%">'. getViewTime($datas['task_time']) .'</td>';
                                echo '</tr>';
                            }
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
        break;
}
require_once 'footer.php';