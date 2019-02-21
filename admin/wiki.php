<?php
/**
 * Created by PhpStorm.
 * User: DONG
 * Date: 2019-02-15
 * Time: 09:40
 */
require_once "../includes/core.php";
if(!$user_id){
    header('location:'._URL_LOGIN);
}
$active_menu = 'wiki';
switch ($act){
    case 'edit':
        // Check Roles
        $wiki = $db->from(_TABLE_WIKI)->where('wiki_id', $id)->fetch_first();
        if(!$wiki){
            $admin_title    = 'Tài liệu không tồn tại';
            require_once 'header.php';
            echo getAdminPanelError(array('header' => $admin_title, 'message' => 'Tài kiệu không tồn tại, vui lòng kiểm tra lại đường dẫn'));
            require_once 'footer.php';
            break;
        }
        if($wiki['wiki_users'] != $user_id){
            $admin_title    = 'Không được truy cập';
            require_once 'header.php';
            echo getAdminPanelError(array('header' => $admin_title, 'message' => 'Bạn không thể sửa dược tài liệu này'));
            require_once 'footer.php';
            break;
        }
        $wiki_room 	    = isset($_POST['wiki_room'])        ? trim($_POST['wiki_room'])         : $wiki['wiki_room'];
        $wiki_title 	= isset($_POST['wiki_title'])       ? trim($_POST['wiki_title'])        : $wiki['wiki_title'];
        $wiki_content 	= isset($_POST['wiki_content'])     ? trim($_POST['wiki_content'])      : $wiki['wiki_content'];
        $wiki_procedure = isset($_POST['wiki_procedure'])   ? trim($_POST['wiki_procedure'])    : $wiki['wiki_procedure'];
        $wiki_standard 	= isset($_POST['wiki_standard'])    ? trim($_POST['wiki_standard'])     : $wiki['wiki_standard'];
        if($submit){
            $error = array();
            if(!$wiki_title){
                $error['wiki_title'] = 'Bạn cần nhập tiêu đề';
            }
            if(!$wiki_content){
                $error['wiki_content'] = 'Bạn cần nhập nội dung';
            }

            if(!$error){
                $data = array(
                    'wiki_title'        => checkInsert($wiki_title),
                    'wiki_content'      => checkInsert($wiki_content),
                    'wiki_room'         => $wiki_room,
                    'wiki_standard'     => checkInsert($wiki_standard),
                    'wiki_procedure'    => checkInsert($wiki_procedure)
                );
                $wiki = $db->where('wiki_id', $id)->update(_TABLE_WIKI, $data)->execute();
                    // Xử lý Upload File
                    require_once '../includes/lib/class.uploader.php';
                    $uploader   = new Uploader();
                    $path       =  '../files/wiki';
                    $data_upload = $uploader->upload($_FILES['wiki_files'], array(
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
                            insertGlobal(_TABLE_FILES, array(
                                'files_name'    => $file_name,
                                'files_url'     => 'files/wiki/'.$file_name,
                                'files_value'   => $id,
                                'files_type'    => 'wiki',
                                'files_users'   => $user_id,
                                'files_time'    => _CONFIG_TIME
                            ));
                        }
                    }
                    // Xử lý Upload File
                $function->redirect(_URL_ADMIN.'/tasks.php?act=wiki_detail&id='.$id);
            }

        }
        $admin_title    = 'Sửa tài liệu công việc';
        $js_plus        = array('app-assets/js/scripts/tinymce/js/tinymce/tinymce.min.js');
        $css_plus       = array('app-assets/vendors/css/forms/icheck/icheck.css','app-assets/css/plugins/forms/checkboxes-radios.css');
        require_once 'header.php';
        ?>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header"><h4 class="card-title"><?=$admin_title?></h4> </div>
                        <div class="card-body">
                            <div class="form-group label-floating">
                                <label class="control-label">Tiêu đề</label>
                                <input required="required" type="text" value="<?=$wiki_title?>" autofocus name="wiki_title" class="form-control round">
                                <?=$error['wiki_title'] ? getViewError($error['wiki_title']) : ''?>
                            </div>
                            <div class="form-group label-floating">
                                <label class="control-label">Mô tả</label>
                                <textarea style="width: 100%" rows="10" name="wiki_content"><?=$wiki_content?></textarea>
                                <?=$error['wiki_content'] ? getViewError($error['wiki_content']) : ''?>
                            </div>
                            <div class="form-group label-floating">
                                <label class="control-label">Các quy trình làm việc</label>
                                <textarea class="form-control" name="wiki_procedure"><?=$wiki_procedure?></textarea>
                            </div>
                            <div class="form-group label-floating">
                                <label class="control-label">Tiêu chuẩn hoàn thành</label>
                                <input required="required" type="text" value="<?=$wiki_standard?>" name="wiki_standard" class="form-control round">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header"><h4 class="card-title"><?php echo $admin_title;?></h4> </div>
                        <div class="card-body text-center">
                            <input type="submit" name="submit" class="btn round btn-outline-success" value="Sửa tài liệu">
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header"><h4 class="card-title">PHÒNG BAN</h4> </div>
                        <div class="card-body">
                            <fieldset class="form-group">
                                <select class="form-control" name="wiki_room">
                                    <?php
                                    if(in_array($function->levelDetail($data_user['users_level']), array('GD', 'PGD'))){
                                        $data = $db->select()->from(_TABLE_CATEGORY)->where('category_type', 'room')->fetch();
                                        foreach ($data as $room){
                                            echo '<option value="'. $room['id'] .'" '. ($wiki_room == $room['id'] ? 'selected' : '') .'>'. $room['category_name'] .'</option>';
                                        }
                                    }
                                    if(in_array($function->levelDetail($data_user['users_level']), array('TP'))){
                                        $data = $db->select()->from(_TABLE_CATEGORY)->where('id', $data_user['users_room'])->fetch_first();
                                        echo '<option value="'. $data['id'] .'">'. $data['category_name'] .'</option>';
                                    }
                                    ?>
                                </select>
                            </fieldset>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header"><h4 class="card-title">FILE BIỂU MẪU</h4> </div>
                        <div class="card-body">
                            <fieldset class="form-group"><div class="col"><input type="file" class="form-control-file" multiple name="wiki_files[]"></div></fieldset>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <?php
        require_once 'footer.php';
        break;
    case 'del':
        // Check Roles
        $wiki = $db->from(_TABLE_WIKI)->where('wiki_id', $id)->fetch_first();
        if(!$wiki){
            $admin_title    = 'Tài liệu không tồn tại';
            require_once 'header.php';
            echo getAdminPanelError(array('header' => $admin_title, 'message' => 'Tài kiệu không tồn tại, vui lòng kiểm tra lại đường dẫn'));
            require_once 'footer.php';
            break;
        }
        if($wiki['wiki_users'] != $user_id){
            $admin_title    = 'Không được truy cập';
            require_once 'header.php';
            echo getAdminPanelError(array('header' => $admin_title, 'message' => 'Bạn không thể sửa dược tài liệu này'));
            require_once 'footer.php';
            break;
        }

        if($submit){
            $db->delete()->from(_TABLE_WIKI)->where('wiki_id', $id)->execute();
            $db->delete()->from(_TABLE_FILES)->where(array('files_type' => 'wiki', 'files_value' => $id))->execute();
            header('location:'._URL_ADMIN.'/tasks.php?act=wiki&id='.$wiki['wiki_room']);
        }

        $admin_title = 'Xóa tài liệu '.$wiki['wiki_title'];
        require_once 'header.php';
        ?>
        <form action="" method="post">
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-header"><h4 class="card-title"><?=$admin_title?></h4></div>
                        <div class="card-body text-center">
                            Bạn có chắc chắn muốn xóa tài liệu <?=$wiki['wiki_title']?> không?<hr/>
                            <?php echo _BUTTON_BACK;?> <input type="submit" name="submit" class="btn btn round btn-outline-danger" value="<?php echo $lang['label_del'];?>">
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <?php
        break;
    case 'add':
        if(in_array($function->levelDetail($data_user['users_level']), array('TP')) && $data_user['users_room'] != $id){
            $admin_title    = 'Nội dung không tồn tại';
            require_once 'header.php';
            echo getAdminPanelError(array('header' => $admin_title, 'message' => 'Bạn không có quyền truy cập vào Module này'));
            require_once 'footer.php';
            break;
        }
        if($submit){
            $wiki_room 	    = isset($_POST['wiki_room'])        ? trim($_POST['wiki_room'])         : '';
            $wiki_title 	= isset($_POST['wiki_title'])       ? trim($_POST['wiki_title'])        : '';
            $wiki_content 	= isset($_POST['wiki_content'])     ? trim($_POST['wiki_content'])      : '';
            $wiki_procedure = isset($_POST['wiki_procedure'])   ? trim($_POST['wiki_procedure'])    : '';
            $wiki_standard 	= isset($_POST['wiki_standard'])    ? trim($_POST['wiki_standard'])     : '';
            $error = array();
            if(!$wiki_title){
                $error['wiki_title'] = 'Bạn cần nhập tiêu đề';
            }
            if(!$wiki_content){
                $error['wiki_content'] = 'Bạn cần nhập nội dung';
            }

            if(!$error){
                $data = array(
                    'wiki_title'        => checkInsert($wiki_title),
                    'wiki_content'      => checkInsert($wiki_content),
                    'wiki_room'         => $wiki_room,
                    'wiki_users'        => $user_id,
                    'wiki_standard'     => checkInsert($wiki_standard),
                    'wiki_procedure'    => checkInsert($wiki_procedure),
                    'wiki_time'         => date('Y/m/d H:i:s', _CONFIG_TIME)
                );
                $wiki = $db->insert(_TABLE_WIKI, $data);
                if($wiki){
                    // Xử lý Upload File
                    require_once '../includes/lib/class.uploader.php';
                    $uploader   = new Uploader();
                    $path       =  '../files/wiki';
                    $data_upload = $uploader->upload($_FILES['wiki_files'], array(
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
                            insertGlobal(_TABLE_FILES, array(
                                'files_name'    => $file_name,
                                'files_url'     => 'files/wiki/'.$file_name,
                                'files_value'   => $wiki,
                                'files_type'    => 'wiki',
                                'files_users'   => $user_id,
                                'files_time'    => _CONFIG_TIME
                            ));
                        }
                    }
                    // Xử lý Upload File
                }
                $function->redirect(_URL_ADMIN.'/tasks.php?act=wiki_detail&id='.$wiki);
            }

        }
        $admin_title    = 'Thêm tài liệu công việc';
        $js_plus        = array('app-assets/js/scripts/tinymce/js/tinymce/tinymce.min.js');
        $css_plus       = array('app-assets/vendors/css/forms/icheck/icheck.css','app-assets/css/plugins/forms/checkboxes-radios.css');
        require_once 'header.php';
        ?>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header"><h4 class="card-title"><?=$admin_title?></h4> </div>
                        <div class="card-body">
                            <div class="form-group label-floating">
                                <label class="control-label">Tiêu đề</label>
                                <input required="required" type="text" value="<?=$wiki_title?>" autofocus name="wiki_title" class="form-control round">
                                <?=$error['wiki_title'] ? getViewError($error['wiki_title']) : ''?>
                            </div>
                            <div class="form-group label-floating">
                                <label class="control-label">Mô tả</label>
                                <textarea style="width: 100%" rows="10" name="wiki_content"></textarea>
                                <?=$error['wiki_content'] ? getViewError($error['wiki_content']) : ''?>
                            </div>
                            <div class="form-group label-floating">
                                <label class="control-label">Các quy trình làm việc</label>
                                <textarea class="form-control" name="wiki_procedure"></textarea>
                            </div>
                            <div class="form-group label-floating">
                                <label class="control-label">Tiêu chuẩn hoàn thành</label>
                                <input required="required" type="text" value="" name="wiki_standard" class="form-control round">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header"><h4 class="card-title"><?php echo $admin_title;?></h4> </div>
                        <div class="card-body text-center">
                            <input type="submit" name="submit" class="btn round btn-outline-success" value="Thêm tài liệu">
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header"><h4 class="card-title">PHÒNG BAN</h4> </div>
                        <div class="card-body">
                            <fieldset class="form-group">
                                <select class="form-control" name="wiki_room">
                                    <?php
                                    if(in_array($function->levelDetail($data_user['users_level']), array('GD', 'PGD'))){
                                        $data = $db->select()->from(_TABLE_CATEGORY)->where('category_type', 'room')->fetch();
                                        foreach ($data as $room){
                                            echo '<option value="'. $room['id'] .'" '. ($wiki_room == $room['id'] ? 'selected' : '') .'>'. $room['category_name'] .'</option>';
                                        }
                                    }
                                    if(in_array($function->levelDetail($data_user['users_level']), array('TP'))){
                                        $data = $db->select()->from(_TABLE_CATEGORY)->where('id', $data_user['users_room'])->fetch_first();
                                        echo '<option value="'. $data['id'] .'">'. $data['category_name'] .'</option>';
                                    }
                                    ?>
                                </select>
                            </fieldset>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header"><h4 class="card-title">FILE BIỂU MẪU</h4> </div>
                        <div class="card-body">
                            <fieldset class="form-group"><div class="col"><input type="file" class="form-control-file" multiple name="wiki_files[]"></div></fieldset>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <?php
        require_once 'footer.php';
        break;
    default:

        break;
}
?>