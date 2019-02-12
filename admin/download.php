<?php
/**
 * Created by PhpStorm.
 * User: DONG
 * Date: 2019-02-07
 * Time: 16:48
 */
require_once "../includes/core.php";
if(!$user_id){
    header('location:'._URL_LOGIN);
}
$active_menu = 'download';
$files = getGlobal('dong_files', array('id' => $id));
// Check File
if(!$files){
    $admin_title    = 'Chi tiết tập tin';
    require_once 'header.php';
    echo getAdminPanelError(array('header' => $lang['label_notice'], 'message' => 'Tập tin không tồn tại'));
    require_once 'footer.php';
    exit();
}
$user           = $db->from(_TABLE_USERS)->where('users_id', $files['files_users'])->fetch_first();
$admin_title    = 'Tải file '.$files['files_name'];
$check_view     = $db->from(_TABLE_GROUP)->where(array('group_type' => 'file_view', 'group_id' => $files['id'], 'group_users' => $user_id))->fetch_first();
if($check_view){
    if(!$_SESSION['view_file_'.$files['id']]){
        $db->where('id', $check_view['id'])->update(_TABLE_GROUP, array('group_value' => ($check_view['group_value'] + 1)));
        $_SESSION['view_file_'.$files['id']] = $user_id;
    }
}else{
    if(!$_SESSION['view_file_'.$files['id']]){
        $data_download = array(
            'group_type'    => 'file_view',
            'group_id'      => $files['id'],
            'group_value'   => 1,
            'group_users'   => $user_id,
            'group_time'    => _CONFIG_TIME
        );
        $db->insert(_TABLE_GROUP, $data_download);
        $_SESSION['view_file_'.$files['id']] = $user_id;
    }
}
require_once 'header.php';
?>
    <div class="text-center">
        <button type="button" onclick="location='<?=_URL_HOME.'/dl/'.$files['id'].'?token='.createToken()?>'" class="btn btn-float btn-float-lg btn-outline-info">
            <i class="la la-cloud-download"></i><span>TẢI XUỐNG</span>
        </button>
    </div><br />
    <div class="row">
        <div id="recent-transactions" class="col-12">
            <div class="card">
                <div class="card-header"><h4 class="card-title">Thông tin tập tin</h4> </div>
                <div class="card-content">
                    <div class="table-responsive">
                        <table id="recent-orders" class="table table-hover table-xl mb-0">
                            <tbody>
                            <tr>
                                <td width="30%">Người Upload</td>
                                <td width="70%"><a href="users.php?act=detail&id=<?=$user['users_id']?>"><?=$user['users_name']?></a></td>
                            </tr>
                            <tr>
                                <td width="50%">Tên File</td>
                                <td width="50%"><?php echo $files['files_name'];?></td>
                            </tr>
                            <?php
                                $file_name = explode('.', $files['files_name']);
                                $file_name = $file_name[(count($file_name) - 1)];
                                if(in_array($file_name, array('jpg', 'JPG' , 'png', 'PNG' , 'JPEG', 'jpeg'))){
                                    echo '<tr><td>Nhúng ảnh vài bài viết</td><td>';
                                    echo '<textarea style="width: 100%" rows="6"><div class="text-center"><img style="width: 80%" src="'. _URL_HOME .'/'. $files['files_url'] .'" /></div></textarea>';
                                    echo '</td></tr>';
                                }
                            ?>
                            <tr>
                                <td>Ngày tải lên</td>
                                <td><?php echo getViewTime($files['files_time']);?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php if($files['files_users'] == $user_id){?>
    <div class="row">
        <div class="col-md-6 order-md-2 mb-4">
            <div class="card">
                <div class="card-header mb-3">
                    <h4 class="card-title">Lượt tải xuống (<?=countFile($files['id'], 'file_download')?>)</h4>
                </div>
                <div class="card-content">
                    <ul class="list-group mb-3">
                        <?php
                        $db->select('*')->from(_TABLE_GROUP);
                        $db->join(_TABLE_USERS, 'group_users = users_id');
                        $db->where(array('group_type' => 'file_download', 'group_id' => $files['id']));
                        foreach ($db->fetch() AS $person_download){
                            echo '<li class="list-group-item d-flex justify-content-between lh-condensed"><div><a href="users.php?act=detail&id='. $person_download['users_id'] .'">'. $person_download['users_name'] .'</a></div>';
                            echo '<span class="text-muted">tải '. $person_download['group_value'] .' lần</span></li>';
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6 order-md-2 mb-4">
            <div class="card">
                <div class="card-header mb-3">
                    <h4 class="card-title">Lượt xem (<?=countFile($files['id'], 'file_view')?>)</h4>
                </div>
                <div class="card-content">
                    <ul class="list-group mb-3">
                        <?php
                        $db->select('*')->from(_TABLE_GROUP);
                        $db->join(_TABLE_USERS, 'group_users = users_id');
                        $db->where(array('group_type' => 'file_view', 'group_id' => $files['id']));
                        foreach ($db->fetch() AS $person_download){
                            echo '<li class="list-group-item d-flex justify-content-between lh-condensed"><div><a href="users.php?act=detail&id='. $person_download['users_id'] .'">'. $person_download['users_name'] .'</a></div>';
                            echo '<span class="text-muted">xem '. $person_download['group_value'] .' lần</span></li>';
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
<?php
    }
require_once 'footer.php';
