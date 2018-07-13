<?php
/**
 * Created by PhpStorm.
 * User: nguye
 * Date: 28/04/2018
 * Time: 21:25
 */

require_once "../includes/core.php";

if(!$user_id){
    header('location:'._URL_LOGIN);
}
$active_menu = 'report';

switch ($act){
    case 'export':
        require_once '../includes/lib/PHPExcel.php';
        $start          = isset($_GET['start'])         ? trim($_GET['start'])      : '';
        $end            = isset($_GET['end'])           ? trim($_GET['end'])        : '';
        $tasks_users    = isset($_GET['tasks_users'])   ? trim($_GET['tasks_users']): '';

        switch ($type){
            case 'me':
                $query          = 'SELECT * FROM `dong_task` WHERE `task_to` = "'. $user_id .'" AND `task_status` = "2" AND (`task_end` BETWEEN "'. $start .'" AND "'. $end .'")';
                break;
            case 'users_manager':
                // Check Users Manager
                if(checkGlobal('dong_users', array('users_id' => $id, 'users_manager' => $user_id)) == 0){
                    header('location:'._URL_ADMIN);
                    exit();
                }
                $query          = 'SELECT * FROM `dong_task` WHERE `task_to` = "'. $id .'" AND `task_status` = "2" AND (`task_end` BETWEEN "'. $start .'" AND "'. $end .'")';
                break;
            case 'users_manager_all':
                $task_user      = getGlobalAll('dong_users', array('users_manager' => $user_id));
                foreach ($task_user as $data_users) {
                    $colums[] = "`task_to` = '" . checkInsert($data_users['users_id']) . "'";
                }
                $colums_list = implode(' OR ', $colums);
                $query          = 'SELECT * FROM `dong_task` WHERE ('. $colums_list .') AND `task_status` = "2" AND (`task_end` BETWEEN "'. $start .'" AND "'. $end .'")';
                break;
            case 'admins':
                $task_user      = getGlobalAll('dong_users', array('users_level' => $tasks_users));
                foreach ($task_user as $data_users) {
                    $colums[] = "`task_to` = '" . checkInsert($data_users['users_id']) . "'";
                }
                $colums_list = implode(' OR ', $colums);
                $query          = 'SELECT * FROM `dong_task` WHERE ('. $colums_list .') AND `task_status` = "2" AND (`task_end` BETWEEN "'. $start .'" AND "'. $end .'")';
                break;
        }

/*        if($data_user['users_level'] == 9){
            $task_user      = getGlobalAll('dong_users', array());
        }else{
            $task_user      = getGlobalAll('dong_users', array('users_manager' => $user_id));
        }

        foreach ($task_user as $data_users) {
            $colums[] = "`task_to` = '" . checkInsert($data_users['users_id']) . "'";
        }
        $colums_list = implode(' OR ', $colums);
        $query          = 'SELECT * FROM `dong_task` WHERE ('. $colums_list .') AND `task_status` = "2" AND (`task_end` BETWEEN "'. $start .'" AND "'. $end .'")';*/

        $data_tasks     = getGlobalAll('dong_task', array(), array('query' => $query));
        $excel = new PHPExcel();
        $excel->setActiveSheetIndex(0);
        $excel->getActiveSheet()->setTitle($lang['report_title']);
        $excel->getActiveSheet()->getStyle('A1:I1')->getFont()->setBold(true);
        $excel->getActiveSheet()->setCellValue('A1', $lang['label_tasks']);
        $excel->getActiveSheet()->setCellValue('B1', $lang['tasks_users_send']);
        $excel->getActiveSheet()->setCellValue('C1', $lang['tasks_users_receive']);
        $excel->getActiveSheet()->setCellValue('D1', $lang['tasks_date_start']);
        $excel->getActiveSheet()->setCellValue('E1', $lang['tasks_date_finish']);
        $excel->getActiveSheet()->setCellValue('F1', $lang['tasks_time_finish']);
        $excel->getActiveSheet()->setCellValue('G1', $lang['tasks_from_star']);
        $excel->getActiveSheet()->setCellValue('H1', $lang['tasks_to_star']);
        $excel->getActiveSheet()->setCellValue('I1', $lang['post_status']);

        $excel->getActiveSheet()->getColumnDimension('A')->setWidth(40);
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth(22);
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth(22);
        $excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('I')->setWidth(25);

        $numRow = 2;
        foreach ($data_tasks AS $excel_data){
            $tasks_users_send   = getGlobal('dong_users', array('users_id' => $excel_data['task_from']));
            $tasks_users_receive= getGlobal('dong_users', array('users_id' => $excel_data['task_to']));
            $day = (strtotime($excel_data['task_end']) - strtotime(date('Y-m-d', $excel_data['task_time_rep']))) / (60 * 60 * 24);
            if($day < 0){
                $text_day = $lang['tasks_progess_excel_1'];
                $text_day = str_replace('{day}', ($day * -1), $text_day);
            }else if($day == 0){
                $text_day = $lang['tasks_progess_2'];
            }else if($day > 0){
                $text_day = $lang['tasks_progess_excel_3'];
                $text_day = str_replace('{day}', $day, $text_day);
            }
            $excel->getActiveSheet()->setCellValue('A'.$numRow, $excel_data['task_name']);
            $excel->getActiveSheet()->setCellValue('B'.$numRow, $tasks_users_send['users_name']);
            $excel->getActiveSheet()->setCellValue('C'.$numRow, $tasks_users_receive['users_name']);
            $excel->getActiveSheet()->setCellValue('D'.$numRow, date('d-m-Y', strtotime($excel_data['task_start'])));
            $excel->getActiveSheet()->setCellValue('E'.$numRow, date('d-m-Y', strtotime($excel_data['task_end'])));
            $excel->getActiveSheet()->setCellValue('F'.$numRow, date('d-m-Y', $excel_data['task_time_rep']));
            $excel->getActiveSheet()->setCellValue('G'.$numRow, $lang['tasks_star_'.$excel_data['task_from_star']]);
            $excel->getActiveSheet()->setCellValue('H'.$numRow, $lang['tasks_star_'.$excel_data['task_to_star']]);
            $excel->getActiveSheet()->setCellValue('I'.$numRow, $text_day);
            $numRow++;
        }
        //PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('data.xlsx');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Export To '. $start .' to '. $end .'.xlsx"');
        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('php://output');
        break;
    case 'admins':
        if($submit){
            $tasks_users    = isset($_POST['tasks_users'])  ? trim($_POST['tasks_users'])   : '';
            $tasks_timer 	= isset($_POST['tasks_timer'])  ? trim($_POST['tasks_timer'])   : '';
            switch ($tasks_timer){
                case 'this_week':
                    $time_start = date('Y-m-d', strtotime('last Monday', _CONFIG_TIME));
                    $time_end   = date('Y-m-d', strtotime('next Sunday', _CONFIG_TIME));
                    break;
                case 'this_month':
                    $time_start = date('Y-m-d', strtotime('first day of this month', _CONFIG_TIME));
                    $time_end   = date('Y-m-d', strtotime('last day of this month', _CONFIG_TIME));
                    break;
                case 'this_year':
                    $time_start = date('Y-m-d', strtotime('first day of January', _CONFIG_TIME));
                    $time_end   = date('Y-m-d', strtotime('last day of December', _CONFIG_TIME));
                    break;
            }
            header('location:'._URL_ADMIN.'/report.php?act=export&type=admins&tasks_users='.$tasks_users.'&start='.$time_start.'&end='.$time_end);
        }
        $admin_title = $lang['report_title'];
        require_once 'header.php';
        ?>
        <div class="row">
            <form action="" method="post">
                <div class="card">
                    <div class="card-header"><h4 class="card-title"><?php echo $admin_title;?></h4> </div>
                    <div class="card-content">
                        <div class="row">
                            <div class="col-md-4">
                                <select class="selectpicker" name="tasks_users" data-style="select-with-transition" data-size="7">
                                    <option>Chọn 1 phòng</option>
                                    <?php
                                    foreach (getGlobalAll('dong_category', array(), array('query' => 'SELECT * FROM `dong_category` WHERE `category_type` = "role" AND `category_sub` > 0')) AS $data_role){
                                        echo '<option value="'. $data_role['id'] .'">'. $data_role['category_name'] .'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select class="selectpicker" name="tasks_timer" data-style="select-with-transition" data-size="7">
                                    <option>Chọn khoảng thời gian</option>
                                    <option value="this_week">Tuần này</option>
                                    <option value="this_month">Tháng Này</option>
                                    <option value="this_year">Năm nay</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <input type="submit" name="submit" class="btn btn-success" value="Tải Xuống">
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <?php
        break;
    default:
        if($submit){
            $tasks_users    = isset($_POST['tasks_users'])  ? trim($_POST['tasks_users'])   : '';
            $tasks_start    = isset($_POST['tasks_start'])  ? trim($_POST['tasks_start'])   : '';
            $tasks_end      = isset($_POST['tasks_end'])    ? trim($_POST['tasks_end'])     : '';
            $tasks_role     = isset($_POST['tasks_role'])   ? trim($_POST['tasks_role'])    : '';

            if($tasks_users > 0){
                $query          = 'SELECT * FROM `dong_task` WHERE `task_to` = "'. $tasks_users .'" AND `task_status` = "2" AND (`task_end` BETWEEN "'. $tasks_start .'" AND "'. $tasks_end .'")';
            }else{
                $task_user = getGlobalAll('dong_users', array('users_level' => $tasks_role));
                foreach ($task_user as $data_users) {
                    $colums[] = "`task_to` = '" . checkInsert($data_users['users_id']) . "'";
                }
                $colums_list = implode(' OR ', $colums);
                $query          = 'SELECT * FROM `dong_task` WHERE ('. $colums_list .') AND `task_status` = "2" AND (`task_end` BETWEEN "'. $tasks_start .'" AND "'. $tasks_end .'")';
            }

            $data_tasks     = getGlobalAll('dong_task', array(), array('query' => $query));
            $tasks_users_dt = getGlobal('dong_users', array('users_id' => $tasks_users));
        }

        $admin_title = $lang['report_title'];
        require_once 'header.php';

        ?>
        <div class="row">
            <form action="" method="post">
                <!--Select one person-->
                <div class="col-md-2">
                    <select class="selectpicker" name="tasks_users" data-style="select-with-transition" data-size="7">
                        <?php
                        if(checkGlobal('dong_users', array('users_id' => $tasks_users)) > 0){
                            echo '<option value="'. $tasks_users .'">'. (getGlobalAll('dong_users', array('users_id' => $tasks_users), array('onecolum' => 'users_name'))) .'</option>';
                        }else{
                            echo '<option> Chọn thành viên</option>';
                        }
                        foreach (getGlobalAll('dong_users', array()) AS $data_user){
                            echo '<option value="'. $data_user['users_id'] .'">'. $data_user['users_name'] .'</option>';
                        }
                        ?>
                    </select>
                </div>
                <!--Select Room-->
                <div class="col-md-2">
                    <select class="selectpicker" name="tasks_role" data-style="select-with-transition" data-size="7">
                        <?php
                        if(checkGlobal('dong_category', array('id' => $tasks_role, 'category_type' => 'role')) > 0){
                            echo '<option value="'. $tasks_role .'">'. (getGlobalAll('dong_category', array('id' => $tasks_role), array('onecolum' => 'category_name'))) .'</option>';
                        }else{
                            echo '<option> Chọn khoa, phòng</option>';
                        }
                        foreach (getGlobalAll('dong_category', array(), array('query' => 'SELECT * FROM `dong_category` WHERE `category_type` = "role" AND `category_sub` > 0')) AS $data_role){
                            echo '<option value="'. $data_role['id'] .'">'. $data_role['category_name'] .'</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="text" name="tasks_start" class="form-control datepicker" value="<?php echo $tasks_start;?>" placeholder="Chọn ngày bắt đầu" />
                </div>
                <div class="col-md-2">
                    <input type="text" name="tasks_end" class="form-control datepicker" value="<?php echo $tasks_end;?>" placeholder="Chọn ngày kết thúc" />
                </div>
                <div class="col-md-2">
                    <input type="submit" name="submit" value="Xem kết quả" class="btn btn-success btn-google">
                </div>

            </form>
        </div>
    <div class="row">
        <div class="card">
            <div class="card-header"><h4 class="card-title"><?php echo $lang['report_detail'];?> của <strong><?php echo $tasks_users_dt['users_name'];?></strong> từ ngày <?php echo '<strong>'.$tasks_start.'</strong>  đến ngày <strong>'.$tasks_end.'</strong>';?></h4> </div>
            <div class="card-content">
            <?php
                $table_header   = array(
                    $lang['label_tasks'],
                    $lang['tasks_users_send'],
                    $lang['tasks_users_receive'],
                    $lang['tasks_date_finish'],
                    $lang['tasks_time_finish'],
                    $lang['tasks_progess'],
                    $lang['tasks_from_star'],
                    $lang['tasks_to_star'],
                    $lang['post_status']
                );
                $table_data     = array();
                foreach ($data_tasks AS $tasks){
                    $tasks_users_send   = getGlobal('dong_users', array('users_id' => $tasks['task_from']));
                    $tasks_users_receive= getGlobal('dong_users', array('users_id' => $tasks['task_to']));
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
                    array_push($table_data, array(
                        '<a href="'. _URL_ADMIN.'/tasks.php?act=detail&id='.$tasks['id'].'">'.$tasks['task_name'].'</a>',
                        '<a href="'. _URL_ADMIN.'/users.php?type=update&id='.  $tasks_users_send['users_id'] .'">'. $tasks_users_send['users_name'] .'</a>',
                        '<a href="'. _URL_ADMIN.'/users.php?type=update&id='.  $tasks_users_receive['users_id'] .'">'. $tasks_users_receive['users_name'] .'</a>',
                        $tasks['task_end'],
                        getViewTime($tasks['task_time_rep']),
                        $text_day,
                        $lang['tasks_star_'.$tasks['task_from_star']],
                        $lang['tasks_star_'.$tasks['task_to_star']],
                        $lang['tasks_status_'.$tasks['task_status']]
                    ));
                }
                if(mysqli_num_rows(mysqli_query($db_connect, $query)) == 0){
                    echo '<div class="text-center">Chưa có thông tin</div>';
                }else{
                    echo getDataTable($table_header, $table_data);
                }
            ?>
            </div>
        </div>
    </div>
        <?php
        break;
}

require_once 'footer.php';