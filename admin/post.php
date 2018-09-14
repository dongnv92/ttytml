<?php
/**
 * Created by PhpStorm.
 * User: nguye
 * Date: 03/03/2018
 * Time: 21:56
 */
require_once "../includes/core.php";
if(!$user_id){
    header('location:'._URL_LOGIN);
}
$active_menu    = 'post';
$js_plus        = array('app-assets/js/scripts/tinymce/js/tinymce/tinymce.min.js');
$css_plus       = array('app-assets/vendors/css/forms/icheck/icheck.css','app-assets/css/plugins/forms/checkboxes-radios.css');
switch ($act){
    case 'del':
        // Check Roles
        if(!checkRole('post_del')){
            $admin_title    = $lang['error_access'];
            require_once 'header.php';
            echo getAdminPanelError(array('header' => $lang['label_notice'], 'message' => $lang['error_no_accep']));
            require_once 'footer.php';
            exit();
        }

        if($submit){
            deleteGlobal('dong_post', array('id' => $id));
            header('location:'._URL_ADMIN.'/post.php');
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
                            Bạn có chắc chắn muốn xóa bài viết này không<hr/>
                            <?php echo _BUTTON_BACK;?> <input type="submit" name="submit" class="btn btn round btn-outline-success" value="<?php echo $lang['label_del'];?>">
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <?php
        break;
    case 'add':
        // Check Roles
        if(!checkRole('post_add')){
            $admin_title    = $lang['error_access'];
            require_once 'header.php';
            echo getAdminPanelError(array('header' => $lang['label_notice'], 'message' => $lang['error_no_accep']));
            require_once 'footer.php';
            exit();
        }

        if($submit){
            $post_name 		= isset($_POST['post_name'])        ? trim($_POST['post_name'])         : '';
            $post_content 	= isset($_POST['post_content'])     ? trim($_POST['post_content'])      : '';
            $post_headlines = isset($_POST['post_headlines'])   ? trim($_POST['post_headlines'])    : 0;
            $post_category 	= isset($_POST['post_category'])    ? trim($_POST['post_category'])     : '';
            $post_url 		= isset($_POST['post_url'])         ? trim($_POST['post_url'])          : '';
            $post_type 		= isset($_POST['post_type'])        ? trim($_POST['post_type'])         : 'news';
            $post_source 	= isset($_POST['post_source'])      ? trim($_POST['post_source'])       : '';
            $post_status    = checkRole('post_add_approval') ? 0 : 1;
            $error = array();
            if(!$post_name){
                $error['post_name'] = getViewError($lang['error_empty_this_fiel']);
            }
            if(!$post_content){
                $error['post_content'] = getViewError($lang['error_empty_this_fiel']);
            }
            if(!$post_url){
                $post_url =  makeSlug($post_name);
            }
            if($post_url && checkGlobal('dong_post', array('post_url' => $post_url)) > 0){
                $error['post_url'] = getViewError($lang['error_url_exits']);
            }

            if(!$error){
                // Upload File
                require_once '../includes/lib/class.uploader.php';
                $uploader = new Uploader();
                $data_upload = $uploader->upload($_FILES['post_images'], array(
                    'limit'         => 10, //Maximum Limit of files. {null, Number}
                    'maxSize'       => 10, //Maximum Size of files {null, Number(in MB's)}
                    'extensions'    => null, //Whitelist for file extension. {null, Array(ex: array('jpg', 'png'))}
                    'required'      => false, //Minimum one file is required for upload {Boolean}
                    'uploadDir'     => '../images/post/', //Upload directory {String}
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
                    'post_title'    => $post_name,
                    'post_content'  => $post_content,
                    'post_url'      => $post_url,
                    'post_category' => $post_category,
                    'post_user'     => $user_id,
                    'post_key'      => $post_name,
                    'post_des'      => createDescription($post_content, 200),
                    'post_status'   => $post_status,
                    'post_type'     => $post_type,
                    'post_headlines'=> $post_headlines,
                    'post_view'     => 0,
                    'post_source'   => $post_source,
                    'post_images'   => $file_name ?  'images/post/'.$file_name : '',
                    'post_time'     => _CONFIG_TIME
                );
                $category_result = insertGlobal(_TABLE_POST, $data);
                //echo $category_result; exit();
                header('location:'._URL_ADMIN.'/post.php?act=update&id='.$category_result);
            }
        }

        $admin_title = $lang['post_add'];
        require_once 'header.php';
        ?>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header"><h4 class="card-title"><?php echo $lang['post_add'];?></h4> </div>
                        <div class="card-body">
                            <?php echo inputFormText(array('name' => 'post_name', 'value' => $post_name, 'require' => TRUE, 'label' => $lang['post_title'], 'error' => $error['post_title']));?>
                            <?php echo inputFormTextarea(array('name' => 'post_content', 'value' => $post_content, 'error' => $error['post_content']));?>
                            <?php echo inputFormText(array('name' => 'post_url', 'value' => $post_url, 'label' => $lang['label_url'].' (Có thể không điền)'));?>
                            <?php echo inputFormText(array('name' => 'post_source', 'value' => $post_source, 'label' => 'Trang nguồn tin (Có thể không điền)'));?>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header"><h4 class="card-title"><?php echo $lang['post_add'];?></h4> </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col text-left">
                                    <div class="form-group"><label><input type="checkbox" class="icheckbox_flat-blue" name="post_headlines" value="1"> <?php echo $lang['post_headlines'];?></label></div>
                                </div>
                                <div class="col text-right">
                                    <input type="submit" name="submit" class="btn btn-outline-success" value="<?php echo $lang['post_add'];?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header"><h4 class="card-title"><?php echo $lang['label_category'];?></h4> </div>
                        <div class="card-body">
                            <fieldset class="form-group">
                                <select class="form-control" name="post_category" title="<?php echo $lang['post_select_category'];?>">
                                    <?php
                                    $data = getGlobalAll('dong_category', array('category_type' => $type));
                                    showCategories(array('data' => $data, 'type' => 'select', 'selected' => $post_category))
                                    ?>
                                </select>
                            </fieldset>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header"><h4 class="card-title"><?php echo $lang['post_type'];?></h4> </div>
                        <div class="card-body">
                            <div class="checkbox-radios">
                                <?php echo inputFormRadio(array('checked' => TRUE, 'name' => 'post_type', 'value' => 'news', 'label' => $lang['post_type_news']));?>
                                <?php echo inputFormRadio(array('name' => 'post_type', 'value' => 'docs', 'label' => $lang['post_type_docs']));?>
                                <?php echo inputFormRadio(array('name' => 'post_type', 'value' => 'report', 'label' => $lang['post_type_report']));?>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header"><h4 class="card-title">Ảnh bài viết</h4> </div>
                        <div class="card-body">
                            <fieldset class="form-group"><div class="col"><input type="file" class="form-control-file" name="post_images"></div></fieldset>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <?php
        break;
    case 'update':
        $post           = getGlobal(_TABLE_POST, array('id' => $id));
        // Check Roles
        if(!checkRole('post_edit')){
            $admin_title    = $lang['post_update'];
            require_once 'header.php';
            echo getAdminPanelError(array('header' => $lang['label_notice'], 'message' => $lang['error_no_accep']));
            require_once 'footer.php';
            exit();
        }

        if($submit){
            $post_name 		= isset($_POST['post_name'])        ? trim($_POST['post_name'])         : $post['post_title'];
            $post_content 	= isset($_POST['post_content'])     ? trim($_POST['post_content'])      : $post['post_content'];
            $post_headlines = isset($_POST['post_headlines'])   ? trim($_POST['post_headlines'])    : 0;
            $post_category 	= isset($_POST['post_category'])    ? trim($_POST['post_category'])     : $post['post_category'];
            $post_url 		= isset($_POST['post_url'])         ? trim($_POST['post_url'])          : $post['post_url'];
            $post_type 		= isset($_POST['post_type'])        ? trim($_POST['post_type'])         : $post['post_type'];
            $post_status 	= isset($_POST['post_status'])      ? trim($_POST['post_status'])       : $post['post_status'];
            $post_source 	= isset($_POST['post_source'])      ? trim($_POST['post_source'])       : $post['post_source'];

            $error = array();
            if(!$post_name){
                $error['post_name'] = getViewError($lang['error_empty_this_fiel']);
            }
            if(!$post_content){
                $error['post_content'] = getViewError($lang['error_empty_this_fiel']);
            }
            if(!$post_url){
                $post_url =  makeSlug($post_name);
            }
            if($post_url != $post['post_url'] && checkGlobal('dong_post', array('post_url' => $post_url)) > 0){
                $error['post_url'] = getViewError($lang['error_url_exits']);
            }

            if(!$error){
                // Upload File
                require_once '../includes/lib/class.uploader.php';
                $uploader = new Uploader();
                $data_upload = $uploader->upload($_FILES['post_images'], array(
                    'limit'         => 10, //Maximum Limit of files. {null, Number}
                    'maxSize'       => 10, //Maximum Size of files {null, Number(in MB's)}
                    'extensions'    => null, //Whitelist for file extension. {null, Array(ex: array('jpg', 'png'))}
                    'required'      => false, //Minimum one file is required for upload {Boolean}
                    'uploadDir'     => '../images/post/', //Upload directory {String}
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
                    'post_title'    => $post_name,
                    'post_content'  => $post_content,
                    'post_url'      => $post_url,
                    'post_category' => $post_category,
                    'post_type'     => $post_type,
                    'post_status'   => $post_status,
                    'post_source'   => $post_source,
                    'post_headlines'=> $post_headlines,
                    'post_images'   => $file_name ? 'images/post/'.$file_name : $post['post_images']
                );
                $update_result = updateGlobal('dong_post', $data, array('id' => $id));
                $post           = getGlobal('dong_post', array('id' => $id));
            }
        }

        $admin_title    = $lang['post_update'];
        require_once 'header.php';
        ?>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header"><h4 class="card-title"><?php echo $lang['post_update'];?> <a href="<?php echo getUrlPost($id);?>" class="btn round btn-outline-blue">Xem bài viết</a> </h4> </div>
                        <div class="card-body">
                            <?php if($submit && !$error){echo getAlert('success', $lang['post_update_success']);} ?>
                            <?php echo inputFormText(array('name' => 'post_name', 'value' => $post['post_title'], 'require' => TRUE, 'label' => $lang['post_title'], 'error' => $error['post_title']));?>
                            <?php echo inputFormTextarea(array('name' => 'post_content', 'value' => $post['post_content'], 'error' => $error['post_content']));?>
                            <?php echo inputFormText(array('name' => 'post_source', 'value' => $post['post_source'], 'label' => 'Trang nguồn tin (Có thể không điền)'));?>
                            <script>CKEDITOR.replace( 'post_content' );</script>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header"><h4 class="card-title"><?php echo $lang['post_update'];?></h4> </div>
                        <div class="card-body">
                            <div class="togglebutton">
                                <label><input type="checkbox" name="post_headlines" value="1" <?php echo $post['post_headlines'] == 1 ? 'checked': '';?>> <?php echo $lang['post_headlines'];?></label>
                            </div>
                            <legend><?php echo $lang['post_status'];?></legend>
                            <div class="checkbox-radios">
                                <?php echo inputFormRadio(array('checked' => ($post['post_status'] == '0' ? TRUE : false), 'name' => 'post_status', 'value' => '0', 'label' => $lang['post_status_0']));?>
                                <?php echo inputFormRadio(array('checked' => ($post['post_status'] == '1' ? TRUE : false), 'name' => 'post_status', 'value' => '1', 'label' => $lang['post_status_1']));?>
                                <?php echo inputFormRadio(array('checked' => ($post['post_status'] == '2' ? TRUE : false), 'name' => 'post_status', 'value' => '2', 'label' => $lang['post_status_2']));?>
                            </div>
                            <input type="submit" name="submit" class="btn btn-fill btn-rose" value="<?php echo $lang['post_update'];?>">
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header"><h4 class="card-title"><?php echo $lang['label_option'];?></h4> </div>
                        <div class="card-body">
                            <fieldset class="form-group">
                                <p>Chuyên mục</p>
                                <select name="post_category" class="form-control">
                                    <?php
                                    $data = getGlobalAll('dong_category', array('category_type' => 'post'));
                                    showCategories(array('data' => $data, 'type' => 'select', 'selected' => $post['post_category']))
                                    ?>
                                </select>
                            </fieldset>
                            <?php echo inputFormText(array('name' => 'post_url', 'value' => $post['post_url'], 'label' => $lang['label_url'], 'error' => $error['post_url']));?>
                            <legend><?php echo $lang['post_type'];?></legend>
                            <div class="checkbox-radios">
                                <?php echo inputFormRadio(array('checked' => ($post['post_type'] == 'news' ? TRUE : false), 'name' => 'post_type', 'value' => 'news', 'label' => $lang['post_type_news']));?>
                                <?php echo inputFormRadio(array('checked' => ($post['post_type'] == 'docs' ? TRUE : false), 'name' => 'post_type', 'value' => 'docs', 'label' => $lang['post_type_docs']));?>
                                <?php echo inputFormRadio(array('checked' => ($post['post_type'] == 'report' ? TRUE : false), 'name' => 'post_type', 'value' => 'report', 'label' => $lang['post_type_report']));?>
                            </div>
                            <?php if($error['post_url']){echo $error['post_url'];}?>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header"><h4 class="card-title">Ảnh bài viết</h4> </div>
                        <div class="card-body">
                            <fieldset class="form-group"><div class="col"><input type="file" class="form-control-file" name="post_images"></div></fieldset>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <?php
        break;
    default:
        $admin_title = $lang['post_list'];
        require_once 'header.php';
        $post_category = isset($_GET['post_category']) && !empty($_GET['post_category']) ? $_GET['post_category'] : false;
        ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"><h4 class="card-title"><?php echo $lang['post_list'];?> <a href="<?php echo _URL_ADMIN.'/post.php?act=add'?>" class="btn round btn-outline-cyan">Đăng bài mới</a> </h4></div>
                    <div class="card-body">
                        <form action="" method="get">
                            <div class="row">
                                <div class="col-md-3">
                                    <fieldset class="form-group">
                                        <select class="form-control" name="post_category">
                                            <option value="">Tất cả chuyên mục</option>
                                            <?php
                                            $data = getGlobalAll(_TABLE_CATEGORY, array('category_type' => $type ? $type : 'post'));
                                            showCategories(array('data' => $data, 'type' => 'select', 'form_value' => 'id', 'form_text' => 'category_name', 'selected' => $post_category))
                                            ?>
                                        </select>
                                    </fieldset>
                                </div>
                                <div class="col-md-3">
                                    <fieldset class="form-group">
                                        <select class="form-control" name="post_user">
                                            <option value="">Tất cả thành viên</option>
                                            <?php
                                            $data = getGlobalAll(_TABLE_USERS, '');
                                            showCategories(array('data' => $data, 'type' => 'select', 'form_value' => 'users_id', 'form_text' => 'users_name', 'selected' => $post_category))
                                            ?>
                                        </select>
                                    </fieldset>
                                </div>
                                <div class="col-md-3">
                                    <input type="submit" class="btn btn-outline-blue round" value="Lọc dữ liệu">
                                </div>
                            </div>
                        </form>
                        <?php
                        $para = array('post_status','post_category','post_user');
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
                        $config_pagenavi['page_num']    = ceil(checkGlobal(_TABLE_POST, $parameters)/$config_pagenavi['page_row']);
                        $config_pagenavi['url']         = _URL_ADMIN.'/post.php?'.$para_list.'&';
                        $page_start                     = ($page-1) * $config_pagenavi['page_row'];
                        $data   = getGlobalAll(_TABLE_POST, $parameters,array(
                            'order_by_row'  => 'id',
                            'order_by_value'=> 'DESC',
                            'limit_start'   => $page_start,
                            'limit_number'  => $config_pagenavi['page_row']
                        ));
                        echo '<div class="table-responsive">';
                        echo '<table class="table">';
                        echo '<thread>';
                        echo '<tr>';
                        echo '<th width="45%">'. $lang['post_title'] .'</th>';
                        echo '<th>'. $lang['label_category'] .'</th>';
                        echo '<th>'. $lang['post_user'] .'</th>';
                        echo '<th>'. $lang['post_hl'] .'</th>';
                        echo '<th>'. $lang['post_time'] .'</th>';
                        echo '</tr>';
                        echo '</thread>';
                        echo '<tbody>';
                        foreach ($data AS $datas){
                            echo '<tr>';
                            echo '<td width="45%"><a href="'. _URL_ADMIN .'/post.php?act=update&id='. $datas['id'] .'">'. $datas['post_title'] .'</a> - <a href="'. _URL_ADMIN .'/post.php?act=del&id='. $datas['id'] .'" class="danger"><small>Xóa</small></a></td>';
                            echo '<td><a href="'. _URL_ADMIN .'/category.php?act=update&id='. $datas['post_category'] .'&type='. getGlobalAll(_TABLE_CATEGORY, array('id' => $datas['post_category']), array('onecolum' => 'category_type')) .'">'. getGlobalAll(_TABLE_CATEGORY, array('id' => $datas['post_category']), array('onecolum' => 'category_name')) .'</a></td>';
                            echo '<td><a href="'. _URL_ADMIN .'/users.php?act=update&id='. $datas['post_user'] .'">'. getGlobalAll(_TABLE_USERS, array('users_id' => $datas['post_user']), array('onecolum' => 'users_name'))  .'</a></td>';
                            echo '<td>'. ($datas['post_headlines'] == 1 ? 'Có' : 'Không') .'</td>';
                            echo '<td>'. getViewTime($datas['post_time']) .'</td>';
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
        break;
}
require_once 'footer.php';


