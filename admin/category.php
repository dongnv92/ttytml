<?php
require_once "../includes/core.php";
if(!$user_id){
    header('location:'._URL_LOGIN);
}
$active_menu = 'category';
// Check Roles
if(!checkRole('category_'.$type)){
    $admin_title    = $lang['error_access'];
    require_once 'header.php';
    echo getAdminPanelError(array('header' => $lang['label_notice'], 'message' => $lang['error_no_accep']));
    require_once 'footer.php';
    exit();
}

switch ($act){
    case 'del':
        // Check Roles
        if(!checkRole('category_del')){
            $admin_title    = $lang['error_access'];
            require_once 'header.php';
            echo getAdminPanelError(array('header' => $lang['label_notice'], 'message' => $lang['error_no_accep']));
            require_once 'footer.php';
            exit();
        }
        $category = getGlobal(_TABLE_CATEGORY, array('id' => $id));
        if(!$category){
            header('location:'._URL_ADMIN.'/category.php');
        }

        if($submit){
            deleteGlobal(_TABLE_CATEGORY, array('id' => $id));
            header('location:'._URL_ADMIN.'/category.php?type='.$category['category_type']);
        }

        $admin_title = 'Xóa Chuyên mục';
        require_once 'header.php';
        ?>
        <form action="" method="post">
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-header"><h4 class="card-title"><?php echo $admin_title;?></h4></div>
                        <div class="card-body text-center">
                            Bạn có chắc chắn muốn xóa chuyên mục <strong><?php echo $category['category_name'];?></strong> không?<hr/>
                            <?php echo _BUTTON_BACK;?> <input type="submit" name="submit" class="btn btn round btn-outline-success" value="<?php echo $lang['label_del'];?>">
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <?php
        break;
    default:
        switch ($act){
            default:
                if($submit) {
                    $category_name  = isset($_POST['category_name'])? trim($_POST['category_name']) : '';
                    $category_url   = isset($_POST['category_url']) ? trim($_POST['category_url'])  : '';
                    $category_sub   = isset($_POST['category_sub']) ? trim($_POST['category_sub'])  : 0;
                    $category_des   = isset($_POST['category_des']) ? trim($_POST['category_des'])  : '';

                    // Check Error
                    $error = array();
                    if(!$category_name){
                        $error['category_name'] = getViewError($lang['error_empty_this_fiel']);
                    }
                    if(!$category_url){
                        $category_url = makeSlug($category_name);
                    }
                    if($category_url && (checkGlobal(_TABLE_CATEGORY, array('category_url' => $category_url, 'category_type' => $type)) > 0)){
                        $error['category_url'] = getViewError($lang['error_url_exits']);
                    }
                    if($category_sub > 0 && checkGlobal('dong_category', array('id' => $category_sub)) == 0){
                        $error['category_sub'] = getViewError($lang['category_parent_empty']);
                    }

                    if(!$error){
                        $data = array(
                            'category_name' => $category_name,
                            'category_url'  => $category_url,
                            'category_des'  => $category_des,
                            'category_sub'  => $category_sub,
                            'category_type' => $type,
                            'category_info' => '',
                            'category_time' => _CONFIG_TIME,
                            'category_user' => $user_id
                        );
                        $category_result = insertGlobal('dong_category', $data);
                    }
                }
                break;
            case 'update':
                $category       = getGlobal('dong_category', array('id' => $id));
                $cate_parent    = getGlobal('dong_category', array('id' => $category['category_sub']));
                if($submit) {
                    $category_name  = isset($_POST['category_name'])? trim($_POST['category_name']) : '';
                    $category_url   = isset($_POST['category_url']) ? trim($_POST['category_url'])  : '';
                    $category_sub   = isset($_POST['category_sub']) ? trim($_POST['category_sub'])  : 0;
                    $category_des   = isset($_POST['category_des']) ? trim($_POST['category_des'])  : '';

                    // Check Error
                    $error = array();
                    if(!$category_name){
                        $error['category_name'] = getViewError($lang['error_empty_this_fiel']);
                    }
                    if(!$category_url){
                        $category_url = makeSlug($category_name);
                    }
                    if(($category_url != $category['category_url']) && checkGlobal('dong_category', array('category_url' => $category_url, 'category_type' => $type)) > 0){
                        $error['category_url'] = getViewError($lang['category_url']);
                    }
                    if($category_sub > 0 && checkGlobal('dong_category', array('id' => $category_sub)) == 0){
                        $error['category_sub'] = getViewError($lang['category_parent_empty']);
                    }

                    if(!$error){
                        $data = array(
                            'category_name' => $category_name,
                            'category_url'  => $category_url,
                            'category_des'  => $category_des,
                            'category_sub'  => $category_sub
                        );
                        $category_result    = updateGlobal('dong_category', $data, array('id' => $id));
                        $category           = getGlobal('dong_category', array('id' => $id));
                    }
                }

                break;
        }
        $admin_title = $lang['category_manager'];
        require_once 'header.php';
        ?>
        <div class="row">
            <?php
            switch ($act){
                default:
                    ?>
                    <div class="col-md-4">
                        <div class="card"> <!--Content-->
                            <div class="card-header"><h4 class="card-title"><?php echo $lang['category_add'];?></h4></div>
                            <div class="card-body">
                                <?php if($submit && $category_result){echo getAlert('primary', $lang['category_add_success']);} ?>
                                <form action="" class="" method="post">
                                    <?php echo inputFormText(array('type' => 'small', 'label' => $lang['category_name'], 'name' => 'category_name', 'value' => $category_name, 'require' => TRUE)); ?>
                                    <?php echo inputFormText(array('type' => 'small', 'label' => $lang['label_input_url'], 'name' => 'category_url', 'value' => $category_url, 'error' => $error['category_url'])); ?>
                                    <fieldset class="form-group">
                                        <p>Chuyên mục cha</p>
                                        <select name="category_sub" class="form-control">
                                            <option value="0">Trống</option>
                                            <?php
                                            $data = getGlobalAll('dong_category', array('category_type' => $type));
                                            showCategories(array('data' => $data, 'type' => 'select', 'form_value' => 'id', 'form_text' => 'category_name'))
                                            ?>
                                        </select>
                                        <?php echo $error['category_sub']? $error['category_sub'] : '';?>
                                    </fieldset>
                                    <?php echo inputFormText(array('label' => $lang['label_description'], 'name' => 'category_des', 'value' => $category_des)); ?>
                                    <input type="submit" name="submit" class="btn btn-outline-success round btn-min-width mr-1 mb-1" value="<?php echo $lang['category_add'];?>">
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php
                    break;
                case 'update':
                    ?>
                    <div class="col-md-4">
                        <div class="card"> <!--Content-->
                            <div class="card-header"><h4 class="card-title"><?php echo $lang['category_update'];?></h4></div>
                            <div class="card-body">
                                <?php if($submit && !$error){echo getAlert('success', $lang['category_update_success']);} ?>
                                <form action="" class="" method="post">
                                    <?php echo inputFormText(array('label' => $lang['category_name'], 'name' => 'category_name', 'value' => $category['category_name'], 'require' => TRUE, 'error' => $error['category_name'])); ?>
                                    <?php echo inputFormText(array('label' => $lang['label_input_url'], 'name' => 'category_url', 'value' => $category['category_url'], 'error' => $error['category_url'])); ?>
                                    <fieldset class="form-group">
                                        <p>Chuyên mục cha</p>
                                        <select name="category_sub" class="form-control">
                                            <option value="0">Trống</option>
                                            <?php
                                            $data = getGlobalAll('dong_category', array('category_type' => $type));
                                            showCategories(array('data' => $data, 'type' => 'select', 'selected' => $category['category_sub']))
                                            ?>
                                        </select>
                                    </fieldset>
                                    <?php
                                    echo inputFormText(array('label' => $lang['label_description'], 'name' => 'category_des', 'value' => $category['category_des']));
                                    ?>
                                    <input type="submit" name="submit" class="btn btn-fill btn-rose" value="<?php echo $lang['category_update'];?>">
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php
                    break;
            }

            ?>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header"><h4 class="card-title"><?php echo $lang['category_list'];?></h4> </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th><?php echo $lang['category_name'];?></th>
                                        <th><?php echo $lang['label_control'];?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                $data = getGlobalAll('dong_category', array('category_type' => $type));
                                showCategories(array('data' => $data, 'type' => 'table'));
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
}
require_once 'footer.php';