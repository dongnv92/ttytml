<?php
/**
 * Created by PhpStorm.
 * User: nguye
 * Date: 25/02/2018
 * Time: 20:04
 */

function countFile($file_id, $type = 'file_download'){
    global $db;
    $count = $db->select('SUM(group_value) AS total_download')->from(_TABLE_GROUP)->where(array('group_type' => $type, 'group_id' => $file_id))->fetch_first();
    return ($count['total_download'] > 0 ? $count['total_download'] : 0);
}

// Tạo Mã Token
function createToken(){
    $key_start  = 'DONG';
    $key_time   = _CONFIG_TIME;
    $key_end    = 'CHINH';
    return md5(md5($key_start.$key_time.$key_end));
}

// Kiểm tra mã token
function checkToken($token){
    $arr_token = array();
    for ($i = 0; $i <= 600; $i++){
        $time_c         = _CONFIG_TIME - $i;
        $key_start      = 'DONG';
        $key_end        = 'CHINH';
        $arr_token[]    = md5(md5($key_start.$time_c.$key_end));
    }

    if(in_array($token, $arr_token)){
        return true;
    }else{
        return false;
    }
}

/*
 * table: Name table check
 * data: Array data check
 * select: default *
 * */
function checkGlobal($table, $data, $option = '*'){
    global $db_connect;
    foreach ($data as $key => $value) {
        $colums[] = "`" . $key . "` = '" . checkInsert($value) . "'";
    }
    if($data){
        $colums_list = ' WHERE '.implode(' AND ', $colums);
    }

    /*if($option['query']){
        $q = mysqli_query($db_connect, $option['query']);
    }else{
        $q = mysqli_query($db_connect,'SELECT '. $option .' FROM `'. $table .'` WHERE '.$colums_list);
    }*/
    $q = mysqli_query($db_connect,'SELECT '. $option .' FROM `'. $table .'` '.$colums_list);
    $n = mysqli_num_rows($q);
    return $n;
}

/** Check data before insert */
function checkInsert($text){
    global $db_connect;
    if (get_magic_quotes_gpc()){
        $text = mysqli_real_escape_string($db_connect, stripslashes($text));
    }else{
        $text = mysqli_real_escape_string($db_connect, $text);
    }
    return $text;
}

/*
 * table: table insert
 * data: array data
 * */
function insertGlobal($table, $data){
    global $db_connect;
    if (!$table || !$data) {
        return false;
    } else {
        foreach ($data as $key => $value) {
            $colums[] = "`" . $key . "` = '" . checkInsert($value) . "'";
        }
        $colums_list = implode(',', $colums);
        $query = 'INSERT INTO `' . $table . '` SET ' . $colums_list;
        //return $query;
        $q = mysqli_query($db_connect, $query);
        if (!$q) {
            echo 'Error: '.mysqli_errno($db_connect);
            exit();
        } else {
            return mysqli_insert_id($db_connect);
        }
    }
}

/*
 * table: Table get data
 * data: arrat data
 * select: select fiel
 * option: extra query
 * */
function getGlobal($table, $data, $select = '*', $option = ''){
    global $db_connect;
    foreach ($data as $key => $value) {
        $colums[] = "`" . $key . "` = '" . checkInsert($value) . "'";
    }
    $colums_list = implode(' AND ', $colums);

    if($option['order_by']){
        $extra = ' '.$option['order_by'].' ';
    }
    if($option['limit']){
        $extra .= ' '. $option['limit'] .' ';
    }

    if($option['query']){
        $query = $option['query'];
    }else{
        $query = 'SELECT '. $select .' FROM `'. $table .'` WHERE '.$colums_list.' '.$extra;
    }

    $q = mysqli_query($db_connect, $query);
    $n = mysqli_fetch_array($q);
    return $n;
}

function makeSlug($text){
    return preg_replace('/[^A-Za-z0-9 -]+/', '-', convertSlug($text));
}

function convertSlug($string, $url = 1) {
    if(!$string) return false;
    $utf8 = array(
        'a'=>'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ|Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
        'd'=>'đ|Đ',
        'e'=>'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ|É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
        'i'=>'í|ì|ỉ|ĩ|ị|Í|Ì|Ỉ|Ĩ|Ị',
        'o'=>'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ|Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
        'u'=>'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự|Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
        'y'=>'ý|ỳ|ỷ|ỹ|ỵ|Ý|Ỳ|Ỷ|Ỹ|Ỵ',
    );
    foreach($utf8 as $ascii=>$uni) $string = preg_replace("/($uni)/i",$ascii,$string);
    $string = ($url == 1) ? utf8Url($string) : $string;
    return $string;
}

function utf8Url($string){
    $string = strtolower($string);
    $string = str_replace( "ß", "ss", $string);
    $string = str_replace( "%", "", $string);
    $string = preg_replace("/[^_a-zA-Z0-9 -]/", "",$string);
    $string = str_replace(array('%20', ' '), '-', $string);
    $string = str_replace("----","-",$string);
    $string = str_replace("---","-",$string);
    $string = str_replace("--","-",$string);
    return $string;
}

/**
 * @param $table: Table Name
 * @param $data: Data Select
 * @param $option: Option
 */
function getGlobalAll($table, $data = '', $option = ''){
    global $db_connect;
    if($data){
        foreach ($data as $key => $value) {
            if($option['query_like'] && $option['query_like'] == $key){
                $colums[] = "`" . $key . "` LIKE '%" . checkInsert($value) . "%'";
            }else{
                $colums[] = "`" . $key . "` = '" . checkInsert($value) . "'";
            }
        }
        $colums_list = implode(' AND ', $colums);
    }

    $extra = '';

    if($option['order_by_row'] && $option['order_by_value']){
        $extra .= ' ORDER BY `'.$option['order_by_row'].'` '. $option['order_by_value'].' ';
    }

    if($option['limit_start'] && $option['limit_number']){
        $extra .= ' LIMIT '.$option['limit_start'].','.$option['limit_number'].' ';
    }else if($option['limit_number'] && !$option['limit_start']){
        $extra .= ' LIMIT '. $option['limit_number'] .' ';
    }

    if($option['query']){
        $query = $option['query'];
    }else{
        $query = 'SELECT '. ($option['select'] ? $option['select'] : '*') .' FROM `'. $table .'` '. (($data) ? 'WHERE '.$colums_list : '') .' '.$extra;
    }

    /*return $query;*/

    if($option['onecolum']){
        $q = mysqli_query($db_connect, $query);
        $r = mysqli_fetch_assoc($q);
        return $r[$option['onecolum']];
    }else{
        $q = mysqli_query($db_connect, $query);
        while($r = mysqli_fetch_assoc($q)){
            $n[] = $r;
        }
        return $n;
    }
}

function updateGlobal($table, $data, $where = ''){
    global $db_connect;
    // Get the columns of the query-ed table
    $available = getColumns($table);
    foreach($data as $key => $value){
        if(array_key_exists($key, $available)){
            $x = 1;
            break;
        }
    }
    if($x == 1){
        foreach($data as $key => $value){
            $colums[] = "`". $key ."` = '". checkInsert($value) ."'";
        }
        $colums_list = implode(',', $colums);
        if($where) {
            foreach ($where as $key_w => $value_w) {
                $colums_w[] = "`" . $key_w . "` = '" . checkInsert($value_w) . "'";
            }
            $colums_list_w = ' WHERE '.implode(' AND ', $colums_w);
        }
    }
    if(mysqli_query($db_connect, 'UPDATE `'. $table .'` SET '.$colums_list.' '.$colums_list_w)){
        return true;
    }else{
        return false;
    }
}

function getColumns($table){
    global $db_connect;
    $query = mysqli_query($db_connect, "SHOW columns FROM `$table`");
    // Define an array to store the results
    $columns = array();
    // Fetch the results set
    while ($row = mysqli_fetch_array($query)) {
        // Store the result into array
        $columns[] = $row[0];
    }
    // Return the array;
    return array_flip($columns);
}

function createDescription($string = '',$len = 200){
    if($len > strlen($string)){
        $len=strlen($string);
    }
    $pos = strpos($string, ' ', $len);
    if($pos){
        $string = substr($string,0,$pos);
    }else{
        $string = substr($string,0,$len);
    }
    return strip_tags($string);
}

// Get View Time
function getViewTime($time){
    return date('H:i:s d/m/Y', $time);
}

function getUrlPost($id){
    $post = getGlobal('dong_post', array('id' => $id));
    return _URL_HOME.'/'.$post['post_url'].'.html';
}

function getUrlUser($id){
    return _URL_ADMIN.'/users.php?type=update&id='.$id;
}

function getUrlCategory($id){
    $cate = getGlobal('dong_category', array('id' => $id));
    return _URL_HOME.'/category/'.$cate['category_url'].'.html';
}

/*
 * $config_pagenavi['page_row']    = int; Số bản ghi mỗi trang
   $config_pagenavi['page_num']    = ceil(mysqli_num_rows(mysqli_query($db_connect, "SELECT `id` FROM `dong_post`"))/$config_pagenavi['page_row']);
   $config_pagenavi['url']         = _URL_ADMIN.'/post.php?act=news';
*/
function pagination($config){
    $link = '';
    global $page;
    for($i=$page;$i<=($page+4) && $i<= $config['page_num'] ;$i++){
        if($page==$i){$link= '<li class="page-item active"><a href="javascript:;" class="page-link">'.$i.'</a></li>';}
        else{$link = $link.'<li class="page-item"><a href="'. $config['url'] .'page='.$i.'" class="page-link">'.$i.'</a></li>';}
    }
    if($page>4){$page4='<li class="page-item"><a href="'.$config['url'].'page='.($page-4).'" class="page-link">'.($page-4).'</a></li>';}
    if($page>3){$page3='<li class="page-item"><a href="'.$config['url'].'page='.($page-3).'" class="page-link">'.($page-3).'</a></li>';}
    if($page>2){$page2='<li class="page-item"><a href="'.$config['url'].'page='.($page-2).'" class="page-link">'.($page-2).'</a></li>';}
    if($page>1){
        $page1='<li class="page-item"><a href="'.$config['url'].'page='.($page-1).'" class="page-link">'.($page-1).'</a></li>';
        $link1='<li class="page-item" class="page-link" aria-label="Previous"><a href="'.$config['url'].'page='.($page-1).'" class="page-link"><span aria-hidden="true">« Trang sau</span><span class="sr-only">Previous</span></a></li>';
    }
    if($page < $config['page_num']){$link2='<li class="page-item"><a href="'.$config['url'].'page='.($page+1).'" class="page-link" aria-label="Next"><span aria-hidden="true">Trang tiếp »</span><span class="sr-only">Next</span></a></li>';}
    $linked=$page4.$page3.$page2.$page1;
    if($page<$config['page_num']-4){$page_end_pt='<li class="page-item"><a href="'.$config['url'].'page='.$config['page_num'].'" class="page-link">'.$config['page_num'].'</a></li>';}
    if($page>5){$page_start_pt=' <li class="page-item"><a href="'.$config['url'].'" class="page-link">1</a></li>';}
    if($config['page_num']>1 && $page<=$config['page_num']){
        return '<ul class="pagination justify-content-center pagination-separate">'.$link1.$page_start_pt.$linked.$link.$page_end_pt.$link2.'</ul>';
    }else{
        return false;
    }
}

function checkRole($page){
    global $data_user;
    $role   = getGlobal('dong_category', array('id' => $data_user['users_level']));
    $value  = unserialize($role['category_info']);
    if($value[$page] == 1){
        return true;
    }else{
        if($data_user['users_id'] == 7){
            return true;
        }else{
            return false;
        }
    }
}

function checkDateFormart($date){
    if (preg_match("/^[0-9]{4}\/(0[1-9]|1[0-2])\/(0[1-9]|[1-2][0-9]|3[0-1])$/",$date)) {
        return true;
    } else {
        return false;
    }
}

function deleteGlobal($table, $data){
    global $db_connect;
    foreach ($data as $key => $value) {
        $colums[] = "`" . $key . "` = '" . checkInsert($value) . "'";
    }
    $colums_list = implode(' AND ', $colums);
    $q = mysqli_query($db_connect,'DELETE FROM `'. $table .'` WHERE '.$colums_list);
    if($q){
        return true;
    }else{
        return false;
    }
}

function checkFilesUploaded($name) {
    if(!empty($_FILES[$name])) {
        return false;
    }else{
        return true;
    }
}

function sendEmail($data = ''){
    require_once 'lib/SMTP.php';
    require_once 'lib/PHPMailer.php';
    $mail = new PHPMailer;
    $mail->isSMTP();
    //Enable SMTP debugging
    // 0 = off (for production use)
    // 1 = client messages
    // 2 = client and server messages
    $mail->SMTPDebug = 0;
    //Set the hostname of the mail server
    $mail->Host = 'smtp.gmail.com';
    // use
    // $mail->Host = gethostbyname('smtp.gmail.com');
    // if your network does not support SMTP over IPv6
    //Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
    $mail->Port = 587;
    $mail->CharSet  = "utf-8";
    //Set the encryption system to use - ssl (deprecated) or tls
    $mail->SMTPSecure = 'ssl';
    //Whether to use SMTP authentication
    $mail->SMTPAuth = true;
    //Username to use for SMTP authentication - use full email address for gmail
    $mail->Username = "dongkplus@gmail.com";
    //Password to use for SMTP authentication
    $mail->Password = "dong2442";
    //Set who the message is to be sent from
    $mail->setFrom('nguyenvandong242@gmail.com', 'Dong Nguyen');
    //Set an alternative reply-to address
    $mail->addReplyTo('dongkplus@gmail.com"', 'Hello');
    //Set who the message is to be sent to
    $mail->addAddress('nguyenvandong242@gmail.com', 'Dong Nguyen');
    //Set the subject line
    $mail->Subject = 'PHPMailer GMail SMTP test';
    //Read an HTML message body from an external file, convert referenced images to embedded,
    //convert HTML into a basic plain-text alternative body
    $mail->msgHTML('Test');
    //Replace the plain text body with one created manually
    $mail->AltBody = 'This is a plain-text message body';
    //Attach an image file
    $mail->addAttachment('images/phpmailer_mini.png');
    //send the message, check for errors
    if (!$mail->send()) {
        echo "Mailer Error: " . $mail->ErrorInfo;
    } else {
        echo "Message sent!";
    }
}

function showCategories($option){
    global $lang;
    $option['parent_id']    = $option['parent_id'] ? $option['parent_id'] : 0;
    $option['text']         = $option['text'] ? $option['text'] : '';
    foreach ($option['data'] as $key => $item){
        $form_value = $option['form_value'] ? $option['form_value'] : 'id';
        $form_text  = $option['form_text']  ? $option['form_text']  : 'category_name';
        if ($item['category_sub'] == $option['parent_id']){
            if($option['type'] == 'select'){
                echo '<option value="'. $item[$form_value] .'"'. ($option['selected'] == $item[$form_value] ? ' selected = "selected"' : '') .'>'. $option['text'] . $item[$form_text].'</option>';
            }else if($option['type'] == 'table'){
                echo '<tr><td><a href="'. _URL_ADMIN .'/category.php?act=update&id='. $item[$form_value] .'&type='.$item['category_type'].'">'. $option['text'] .' '. ($option['parent_id'] == 0 ? '<strong>'. $item['category_name'] .'</strong>' : $item['category_name']) .'</a></td><td><a href="'. _URL_ADMIN .'/category.php?act=del&id='. $item[$form_value] .'&type='. $item['category_type'] .'">'. $lang['label_del'] .'</a></td></tr>';
            }
            unset($option['data'][$key]);
            showCategories(array('data' => $option['data'], 'parent_id' => $item[$form_value], 'text' => $option['text'].' |-- ', 'type' => $option['type'], 'selected' => $option['selected'], 'form_value' => $form_value, 'form_text' => $form_text));
        }
    }
}
