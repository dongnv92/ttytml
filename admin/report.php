<?php/** * Created by PhpStorm. * User: nguye * Date: 28/04/2018 * Time: 21:25 */require_once "../includes/core.php";if(!$user_id){    header('location:'._URL_LOGIN);}$active_menu = 'report';switch ($act){    case 'export':        require_once '../includes/lib/PHPExcel.php';        $start          = isset($_GET['start'])         ? trim($_GET['start'])      : '';        $end            = isset($_GET['end'])           ? trim($_GET['end'])        : '';        $tasks_users    = isset($_GET['tasks_users'])   ? trim($_GET['tasks_users']): '';        switch ($type){            case 'me':                $query          = 'SELECT * FROM `dong_task` WHERE `task_to` = "'. $user_id .'" AND `task_status` = "2" AND (`task_end` BETWEEN "'. $start .'" AND "'. $end .'")';                break;            case 'users_manager':                // Check Users Manager                if(checkGlobal('dong_users', array('users_id' => $id, 'users_manager' => $user_id)) == 0){                    header('location:'._URL_ADMIN);                    exit();                }                $query          = 'SELECT * FROM `dong_task` WHERE `task_to` = "'. $id .'" AND `task_status` = "2" AND (`task_end` BETWEEN "'. $start .'" AND "'. $end .'")';                break;            case 'users_manager_all':                $task_user      = getGlobalAll('dong_users', array('users_manager' => $user_id));                foreach ($task_user as $data_users) {                    $colums[] = "`task_to` = '" . checkInsert($data_users['users_id']) . "'";                }                $colums_list = implode(' OR ', $colums);                $query          = 'SELECT * FROM `dong_task` WHERE ('. $colums_list .') AND `task_status` = "2" AND (`task_end` BETWEEN "'. $start .'" AND "'. $end .'")';                break;            case 'admins':                $task_user      = getGlobalAll('dong_users', array('users_level' => $tasks_users));                foreach ($task_user as $data_users) {                    $colums[] = "`task_to` = '" . checkInsert($data_users['users_id']) . "'";                }                $colums_list = implode(' OR ', $colums);                $query          = 'SELECT * FROM `dong_task` WHERE ('. $colums_list .') AND `task_status` = "2" AND (`task_end` BETWEEN "'. $start .'" AND "'. $end .'")';                break;        }/*        if($data_user['users_level'] == 9){            $task_user      = getGlobalAll('dong_users', array());        }else{            $task_user      = getGlobalAll('dong_users', array('users_manager' => $user_id));        }        foreach ($task_user as $data_users) {            $colums[] = "`task_to` = '" . checkInsert($data_users['users_id']) . "'";        }        $colums_list = implode(' OR ', $colums);        $query          = 'SELECT * FROM `dong_task` WHERE ('. $colums_list .') AND `task_status` = "2" AND (`task_end` BETWEEN "'. $start .'" AND "'. $end .'")';*/        $data_tasks     = getGlobalAll('dong_task', array(), array('query' => $query));        $excel = new PHPExcel();        $excel->setActiveSheetIndex(0);        $excel->getActiveSheet()->setTitle($lang['report_title']);        $excel->getActiveSheet()->getStyle('A1:I1')->getFont()->setBold(true);        $excel->getActiveSheet()->setCellValue('A1', $lang['label_tasks']);        $excel->getActiveSheet()->setCellValue('B1', $lang['tasks_users_send']);        $excel->getActiveSheet()->setCellValue('C1', $lang['tasks_users_receive']);        $excel->getActiveSheet()->setCellValue('D1', $lang['tasks_date_start']);        $excel->getActiveSheet()->setCellValue('E1', $lang['tasks_date_finish']);        $excel->getActiveSheet()->setCellValue('F1', $lang['tasks_time_finish']);        $excel->getActiveSheet()->setCellValue('G1', $lang['tasks_from_star']);        $excel->getActiveSheet()->setCellValue('H1', $lang['tasks_to_star']);        $excel->getActiveSheet()->setCellValue('I1', $lang['post_status']);        $excel->getActiveSheet()->getColumnDimension('A')->setWidth(40);        $excel->getActiveSheet()->getColumnDimension('B')->setWidth(22);        $excel->getActiveSheet()->getColumnDimension('C')->setWidth(22);        $excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);        $excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);        $excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);        $excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);        $excel->getActiveSheet()->getColumnDimension('H')->setWidth(20);        $excel->getActiveSheet()->getColumnDimension('I')->setWidth(25);        $numRow = 2;        foreach ($data_tasks AS $excel_data){            $tasks_users_send   = getGlobal('dong_users', array('users_id' => $excel_data['task_from']));            $tasks_users_receive= getGlobal('dong_users', array('users_id' => $excel_data['task_to']));            $day = (strtotime($excel_data['task_end']) - strtotime(date('Y-m-d', $excel_data['task_time_rep']))) / (60 * 60 * 24);            if($day < 0){                $text_day = $lang['tasks_progess_excel_1'];                $text_day = str_replace('{day}', ($day * -1), $text_day);            }else if($day == 0){                $text_day = $lang['tasks_progess_2'];            }else if($day > 0){                $text_day = $lang['tasks_progess_excel_3'];                $text_day = str_replace('{day}', $day, $text_day);            }            $excel->getActiveSheet()->setCellValue('A'.$numRow, $excel_data['task_name']);            $excel->getActiveSheet()->setCellValue('B'.$numRow, $tasks_users_send['users_name']);            $excel->getActiveSheet()->setCellValue('C'.$numRow, $tasks_users_receive['users_name']);            $excel->getActiveSheet()->setCellValue('D'.$numRow, date('d-m-Y', strtotime($excel_data['task_start'])));            $excel->getActiveSheet()->setCellValue('E'.$numRow, date('d-m-Y', strtotime($excel_data['task_end'])));            $excel->getActiveSheet()->setCellValue('F'.$numRow, date('d-m-Y', $excel_data['task_time_rep']));            $excel->getActiveSheet()->setCellValue('G'.$numRow, $lang['tasks_star_'.$excel_data['task_from_star']]);            $excel->getActiveSheet()->setCellValue('H'.$numRow, $lang['tasks_star_'.$excel_data['task_to_star']]);            $excel->getActiveSheet()->setCellValue('I'.$numRow, $text_day);            $numRow++;        }        //PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('data.xlsx');        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');        header('Content-Disposition: attachment;filename="Export To '. $start .' to '. $end .'.xlsx"');        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('php://output');        break;    case 'admins':        if($submit){            $tasks_users    = isset($_POST['tasks_users'])  ? trim($_POST['tasks_users'])   : '';            $tasks_timer 	= isset($_POST['tasks_timer'])  ? trim($_POST['tasks_timer'])   : '';            switch ($tasks_timer){                case 'this_week':                    $time_start = date('Y-m-d', strtotime('last Monday', _CONFIG_TIME));                    $time_end   = date('Y-m-d', strtotime('next Sunday', _CONFIG_TIME));                    break;                case 'this_month':                    $time_start = date('Y-m-d', strtotime('first day of this month', _CONFIG_TIME));                    $time_end   = date('Y-m-d', strtotime('last day of this month', _CONFIG_TIME));                    break;                case 'this_year':                    $time_start = date('Y-m-d', strtotime('first day of January', _CONFIG_TIME));                    $time_end   = date('Y-m-d', strtotime('last day of December', _CONFIG_TIME));                    break;            }            header('location:'._URL_ADMIN.'/report.php?act=export&type=admins&tasks_users='.$tasks_users.'&start='.$time_start.'&end='.$time_end);        }        $admin_title = $lang['report_title'];        require_once 'header.php';        ?>        <div class="row">            <form action="" method="post">                <div class="card">                    <div class="card-header"><h4 class="card-title"><?php echo $admin_title;?></h4> </div>                    <div class="card-content">                        <div class="row">                            <div class="col-md-4">                                <select class="selectpicker" name="tasks_users" data-style="select-with-transition" data-size="7">                                    <option>Chọn 1 phòng</option>                                    <?php                                    foreach (getGlobalAll('dong_category', array(), array('query' => 'SELECT * FROM `dong_category` WHERE `category_type` = "role" AND `category_sub` > 0')) AS $data_role){                                        echo '<option value="'. $data_role['id'] .'">'. $data_role['category_name'] .'</option>';                                    }                                    ?>                                </select>                            </div>                            <div class="col-md-4">                                <select class="selectpicker" name="tasks_timer" data-style="select-with-transition" data-size="7">                                    <option>Chọn khoảng thời gian</option>                                    <option value="this_week">Tuần này</option>                                    <option value="this_month">Tháng Này</option>                                    <option value="this_year">Năm nay</option>                                </select>                            </div>                            <div class="col-md-4">                                <input type="submit" name="submit" class="btn btn-success" value="Tải Xuống">                            </div>                        </div>                    </div>                </div>            </form>        </div>        <?php        break;    default:        if($submit){            // Lấy danh sách các thành viên            $time_start 	    = isset($_POST['time_start'])           ? $_POST['time_start']          : '';            $time_end 	        = isset($_POST['time_end'])             ? $_POST['time_end']            : '';            $report_users 	    = isset($_POST['report_users'])         ? $_POST['report_users']        : array();            $report_users_group = isset($_POST['report_users_group'])   ? $_POST['report_users_group']  : '';            $report_users_role 	= isset($_POST['report_users_role'])    ? $_POST['report_users_role']   : '';            $report_users_room 	= isset($_POST['report_users_room'])    ? $_POST['report_users_room']   : '';            $users_lists        = array();            if(!checkRole('report') || checkGlobal(_TABLE_GROUP, array('group_type' => 'users_manager_room', 'group_id' => $user_id)) == 0){                $report_users = array($user_id);            }            // Thành viên được chọn            $users_lists         = $report_users;            // Nhóm thành viên            foreach($report_users_group AS $list_group){                $for_group = getGlobalAll(_TABLE_GROUP, array('group_type' => 'users_group', 'group_id' => $list_group));                foreach($for_group AS $for_group_list){                    $users_lists[] = $for_group_list['group_value'];                }            }            // Chức vụ            foreach($report_users_role AS $list_role){                foreach(getGlobalAll(_TABLE_USERS, array('users_level' => $list_role)) AS $for_role_list){                    $users_lists[] = $for_role_list['users_id'];                }            }            // Phòng Ban            foreach($report_users_room AS $list_room){                foreach(getGlobalAll(_TABLE_USERS, array('users_room' => $list_room)) AS $for_room_list){                    $users_lists[] = $for_room_list['users_id'];                }            }            $users_lists = array_unique($users_lists);            $error = array();            if(!$time_start){                $error['time_start']    = getViewError('Cần nhập thời gian');            }else{                $time_start             = date('Y-m-d', strtotime($time_start));            }            if(!$time_end){                $error['time_end']      = getViewError('Cần nhập thời gian');            }else{                $time_end               = date('Y-m-d', strtotime($time_end));            }        }        $css_plus       = array(            'app-assets/vendors/css/extensions/datedropper.min.css',            'app-assets/vendors/css/extensions/timedropper.min.css',            'app-assets/vendors/css/forms/icheck/icheck.css',            'app-assets/css/plugins/forms/checkboxes-radios.css',            'app-assets/css/chosen.css');        $js_plus        = array(            'app-assets/js/scripts/tinymce/js/tinymce/tinymce.min.js',            'app-assets/vendors/js/extensions/datedropper.min.js',            'app-assets/vendors/js/extensions/timedropper.min.js',            'app-assets/js/chosen.jquery.js',            'app-assets/js/prism.js',            'app-assets/js/init.js');        $admin_title = 'Xem báo cáo';        require_once 'header.php';        ?>        <div class="row">            <div class="col-md-12">                <div class="card">                    <div class="card-header"><h4 class="card-title">Xuất báo cáo công việc</h4></div>                    <div class="card-body">                        <form action="" method="post">                            <!-- Date -->                            <div class="row">                                <div class="col-md-6">                                    <div class="card">                                        <div class="card-header">                                            <h4 class="card-title">Xuất từ ngày <small>(Ngày-Tháng-Năm)</small></h4>                                        </div>                                        <div class="card-body">                                            <input value="<?php echo $time_start ? date('d-m-Y', strtotime($time_start)) : '';?>" type="text" name="time_start" class="form-control input-lg" id="animate" placeholder="Xem từ ngày">                                            <?php if($error['time_start']){ echo $error['time_start']; }?>                                        </div>                                    </div>                                </div>                                <div class="col-md-6">                                    <div class="card">                                        <div class="card-header">                                            <h4 class="card-title">Đến ngày <small>(Ngày-Tháng-Năm)</small></h4>                                        </div>                                        <div class="card-body">                                            <input type="text" name="time_end" class="form-control input-lg" id="animate_1" value="<?php echo $time_end ? date('d-m-Y', strtotime($time_end)) : '';?>" placeholder="Xem đến ngày">                                            <?php if($error['time_end']){ echo $error['time_end']; }?>                                            <script language="JavaScript">                                                $(document).ready(function(){                                                    $('#animate').dateDropper({                                                        dropWidth: 200,                                                        lang: 'vi',                                                        format: 'd-m-Y'                                                    });                                                    $('#animate_1').dateDropper({                                                        dropWidth: 200,                                                        lang: 'vi',                                                        format: 'd-m-Y'                                                    });                                                });                                            </script>                                        </div>                                    </div>                                </div>                            </div>                            <!-- Date -->                            <div class="row">                                <!-- Danh sách thành viên -->                                <?php if(checkRole('report')){?>                                <div class="col-md-6">                                    <fieldset class="form-group">                                        <select name="report_users[]" data-placeholder="Nhập tên" multiple class="chosen-select-width round form-control">                                            <option value=""></option>                                            <?php                                            $users_list = getGlobalAll(_TABLE_USERS, '', array('order_by_row' => 'users_name', 'order_by_value' => 'ASC'));                                            foreach ($users_list AS $users){                                                echo '<option value="'. $users['users_id'] .'" '. ((in_array($users['users_id'], $report_users)) ? 'selected' : '') .'>'. $users['users_name'] .'</option>';                                            }                                            ?>                                        </select>                                    <fieldset>                                </div>                                <!-- Danh sách phòng ban -->                                <div class="col-md-6">                                    <fieldset class="form-group">                                        <select name="report_users_role[]" data-placeholder="Nhập các chức vụ" multiple class="chosen-select-width form-control">                                            <option value=""></option>                                            <?php                                            $role_list = getGlobalAll(_TABLE_CATEGORY, array('category_type' => 'role'));                                            foreach ($role_list AS $role_group){                                                echo '<option value="'. $role_group['id'] .'" '. ((in_array($role_group['id'], $report_users_role)) ? 'selected' : '') .'>'. $role_group['category_name'] .'</option>';                                            }                                            ?>                                        </select>                                    </fieldset>                                </div>                                <!-- Danh sách chức vụ -->                                <div class="col-md-6">                                    <select name="report_users_room[]" data-placeholder="Nhập các phòng ban" multiple class="chosen-select-width form-control">                                        <option value=""></option>                                        <?php                                        $room_list = getGlobalAll(_TABLE_CATEGORY, array('category_type' => 'room'));                                        foreach ($room_list AS $room_group){                                            echo '<option value="'. $room_group['id'] .'" '. ((in_array($room_group['id'], $report_users_room)) ? 'selected' : '') .'>'. $room_group['category_name'] .'</option>';                                        }                                        ?>                                    </select>                                </div>                                <!-- Danh sách nhóm -->                                <div class="col-md-6">                                    <fieldset class="form-group">                                        <select name="report_users_group[]" data-placeholder="Nhập Group" multiple class="chosen-select-width form-control">                                            <option value=""></option>                                            <?php                                            $group_list = getGlobalAll(_TABLE_CATEGORY, array('category_type' => 'users_group'));                                            foreach ($group_list AS $users_group){                                                echo '<option value="'. $users_group['id'] .'" '. ((in_array($users_group['id'], $report_users_group)) ? 'selected' : '') .'>'. $users_group['category_name'] .'</option>';                                            }                                            ?>                                        </select>                                    </fieldset>                                </div>                                <?php }else if(checkGlobal(_TABLE_GROUP, array('group_type' => 'users_manager_room', 'group_id' => $user_id)) > 0){?>                                    <!-- Danh sách thành viên -->                                    <div class="col-md-6">                                        <fieldset class="form-group">                                            <select name="report_users[]" data-placeholder="Nhập tên" multiple class="chosen-select-width round form-control">                                                <option value=""></option>                                                <?php                                                // Phòng Ban                                                foreach(getGlobalAll(_TABLE_GROUP, array('group_type' => 'users_manager_room', 'group_id' => $user_id)) AS $list_room){                                                    foreach(getGlobalAll(_TABLE_USERS, array('users_room' => $list_room['group_value'])) AS $for_room_list){                                                        echo '<option value="'. $for_room_list['users_id'] .'">'. $for_room_list['users_name'] .'</option>';                                                    }                                                }                                                ?>                                            </select>                                        </fieldset>                                    </div>                                    <!-- Danh sách phòng ban -->                                    <div class="col-md-6">                                        <fieldset class="form-group">                                            <select name="report_users_room[]" data-placeholder="Nhập các chức vụ" multiple class="chosen-select-width form-control">                                                <option value=""></option>                                                <?php                                                foreach (getGlobalAll(_TABLE_GROUP, array('group_type' => 'users_manager_room', 'group_id' => $user_id)) AS $list_disp_users_manager_room){                                                    $cate = getGlobal(_TABLE_CATEGORY, array('id' => $list_disp_users_manager_room['group_value']));                                                    echo '<option value="'. $list_disp_users_manager_room['group_value'] .'">'. $cate['category_name'] .'</option>';                                                }                                                ?>                                            </select>                                        </fieldset>                                    </div>                                <?php }?>                                <div class="col-md-6 text-left">                                    <script language="JavaScript">                                        var tableToExcel = (function() {                                            var uri = 'data:application/vnd.ms-excel;base64,'                                                , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'                                                , base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }                                                , format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }                                            return function(table, name) {                                                if (!table.nodeType) table = document.getElementById(table)                                                var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML}                                                window.location.href = uri + base64(format(template, ctx))                                            }                                        })()                                    </script>                                    <input type="button" class="btn btn-outline-cyan round" onclick="tableToExcel('testTable', 'W3C Example Table')" value="Export to Excel">                                </div>                                <div class="col-md-6 text-right">                                    <input type="submit" name="submit" class="btn btn-outline-cyan round" value="Xem kết quả">                                </div>                            </div>                        </form>                        <hr>                        <?php                        echo '<div class="table-responsive">';                        echo '<table class="table table-bordered mb-0" id="testTable">';                        echo '<thread>';                        echo '<tr>';                        echo '<th align="center" rowspan="2" valign="middle">Họ Tên</th>';                        echo '<th align="center" rowspan="2" valign="middle">Đơn Vị</th>';                        echo '<th align="center" rowspan="2" valign="middle">Số việc được giao</th>';                        echo '<th class="text-center" colspan="3"> Kết quả tự đánh giá</th>';                        echo '<th class="text-center" colspan="3">Kết quả cấp trên đánh giá</th>';                        echo '<th align="center" rowspan="2" valign="middle">Từ ngày</th>';                        echo '<th align="center" rowspan="2" valign="middle">Đến ngày</th>';                        echo '</tr>';                        echo '<tr>';                        echo '<th align="center">Tốt n(%)</th>';                        echo '<th align="center">Hoàn thành n(%)</th>';                        echo '<th align="center">Không hoàn thành n(%)</th>';                        echo '<th align="center">Tốt n(%)</th>';                        echo '<th align="center">Hoàn thành n(%)</th>';                        echo '<th align="center">Không hoàn thành n(%)</th>';                        echo '</tr>';                        echo '</thread>';                        echo '<tbody>';                        foreach ($users_lists AS $datas){                            $data_user  = $db->select()->from(_TABLE_USERS)->where('users_id', $datas)->fetch_first();                            $data_room  = $db->select()->from(_TABLE_CATEGORY)->where('id', $data_user['users_room'])->fetch_first();                            $db->from(_TABLE_GROUP);                            $db->join(_TABLE_TASKS, _TABLE_GROUP.'.group_id = '._TABLE_TASKS.'.id');                            $db->where(array('group_type' => 'tasks', 'group_value' => $datas))->between(_TABLE_TASKS.'.task_start', $time_start, $time_end);                            $db->execute();                            $data_totalTasks = $db->affected_rows;                            $db->from(_TABLE_GROUP);                            $db->join(_TABLE_TASKS, _TABLE_GROUP.'.group_id = '._TABLE_TASKS.'.id');                            $db->where(array('group_type' => 'tasks', 'group_value' => $datas, 'task_to_star' => 3))->between(_TABLE_TASKS.'.task_start', $time_start, $time_end);                            $db->execute();                            $me_star_3 = $db->affected_rows;                            $db->from(_TABLE_GROUP);                            $db->join(_TABLE_TASKS, _TABLE_GROUP.'.group_id = '._TABLE_TASKS.'.id');                            $db->where(array('group_type' => 'tasks', 'group_value' => $datas, 'task_to_star' => 2))->between(_TABLE_TASKS.'.task_start', $time_start, $time_end);                            $db->execute();                            $me_star_2 = $db->affected_rows;                            $db->from(_TABLE_GROUP);                            $db->join(_TABLE_TASKS, _TABLE_GROUP.'.group_id = '._TABLE_TASKS.'.id');                            $db->where(array('group_type' => 'tasks', 'group_value' => $datas, 'task_to_star' => 1))->between(_TABLE_TASKS.'.task_start', $time_start, $time_end);                            $db->execute();                            $me_star_1 = $db->affected_rows;                            $db->from(_TABLE_GROUP);                            $db->join(_TABLE_TASKS, _TABLE_GROUP.'.group_id = '._TABLE_TASKS.'.id');                            $db->where(array('group_type' => 'tasks', 'group_value' => $datas, 'task_from_star' => 3))->between(_TABLE_TASKS.'.task_start', $time_start, $time_end);                            $db->execute();                            $from_star_3 = $db->affected_rows;                            $db->from(_TABLE_GROUP);                            $db->join(_TABLE_TASKS, _TABLE_GROUP.'.group_id = '._TABLE_TASKS.'.id');                            $db->where(array('group_type' => 'tasks', 'group_value' => $datas, 'task_from_star' => 2))->between(_TABLE_TASKS.'.task_start', $time_start, $time_end);                            $db->execute();                            $from_star_2 = $db->affected_rows;                            $db->from(_TABLE_GROUP);                            $db->join(_TABLE_TASKS, _TABLE_GROUP.'.group_id = '._TABLE_TASKS.'.id');                            $db->where(array('group_type' => 'tasks', 'group_value' => $datas, 'task_from_star' => 1))->between(_TABLE_TASKS.'.task_start', $time_start, $time_end);                            $db->execute();                            $from_star_1 = $db->affected_rows;                            echo '<tr>';                            echo '<td>'. $data_user['users_name'] .'</td>';                            echo '<td>'. $data_room['category_name'] .'</td>';                            echo '<td>'. $data_totalTasks .'</td>';                            echo '<td>'. $me_star_3 .' ('. ($data_totalTasks > 0 ? round((($me_star_3 / $data_totalTasks)*100)) : '0') .'%)</td>';                            echo '<td>'. $me_star_2 .' ('. ($data_totalTasks > 0 ? round((($me_star_2 / $data_totalTasks)*100)) : '0') .'%)</td>';                            echo '<td>'. $me_star_1 .' ('. ($data_totalTasks > 0 ? round((($me_star_1 / $data_totalTasks)*100)) : '0') .'%)</td>';                            echo '<td>'. $from_star_3 .' ('. ($data_totalTasks > 0 ? round((($from_star_3 / $data_totalTasks)*100)) : '0') .'%)</td>';                            echo '<td>'. $from_star_2 .' ('. ($data_totalTasks > 0 ? round((($from_star_2 / $data_totalTasks)*100)) : '0') .'%)</td>';                            echo '<td>'. $from_star_1 .' ('. ( $data_totalTasks > 0 ? round((($from_star_1 / $data_totalTasks)*100)) : '0' ) .'%)</td>';                            echo '<td>'. date('d/m/Y', strtotime($time_start)) .'</td>';                            echo '<td>'. date('d/m/Y', strtotime($time_end)) .'</td>';                            echo '</tr>';                        }                        echo '</tbody>';                        echo '</table>';                        echo '</div>';                        ?>                    </div>                </div>            </div>        </div>        <?php        break;}require_once 'footer.php';