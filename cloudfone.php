<?php/** * Created by PhpStorm. * User: DONG * Date: 2019-04-02 * Time: 16:14 */error_reporting(0);function historyCall($data){    $data = json_encode($data);    $ch = curl_init('https://api.cloudfone.vn/api/CloudFone/GetCallHistory');    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    curl_setopt($ch, CURLOPT_HTTPHEADER, array(            'Content-Type: application/json',            'Content-Length: ' . strlen($data))    );    $result = curl_exec($ch);    curl_close($ch);    $result = json_decode($result, true);    return $result;}$act            = isset($_REQUEST['act'])           && !empty($_REQUEST['act'])         ? trim($_REQUEST['act'])        : '';$date_start     = isset($_REQUEST['date_start'])    && !empty($_REQUEST['date_start'])  ? trim($_REQUEST['date_start']) : '';$date_end       = isset($_REQUEST['date_end'])      && !empty($_REQUEST['date_end'])    ? trim($_REQUEST['date_end'])   : '';$call_num       = isset($_REQUEST['call_num'])      && !empty($_REQUEST['call_num'])    ? trim($_REQUEST['call_num'])   : '';$receive_num    = isset($_REQUEST['receive_num'])   && !empty($_REQUEST['receive_num']) ? trim($_REQUEST['receive_num']): '';$data = array(    'ServiceName'   => 'CF-P-25243',    'AuthUser'      => 'ODS5481',    'AuthKey'       => 'da6068e-ef0a-4647-9163-d99b7d2f69f5',    'TypeGet'       => 0, // 0: tất cả, 1: gọi đến, 2: gọi đi, 3: gọi nội bộ, 4: gợi nhở, 5: gọi nhóm    'PageIndex'     => 1, // Số trang    'PageSize'      => 200 // Số dòng trên 1 trang, max 200);if($date_start){    $data['DateStart'] = $date_start;}if($date_end){    $data['DateEnd'] = $date_end.' 23:59:59';}if($call_num){    $data['CallNum'] = $call_num;}if($receive_num){    $data['ReceiveNums'] = $receive_num;}switch ($act){    case 'detail':        $data['ReceiveNum'] = $receive_num;        $result = historyCall($data);        $datas  = $result['data'];        foreach ($datas as $data){            $tr .= '<tr><td>'. $data['soGoiDen'] .'</td><td>'. $data['soNhan'] .'</td><td>'. $data['tongThoiGianGoi'] .'</td><td>'. $data['thoiGianThucGoi'] .'</td><td><a href="'. $data['linkFile'] .'" target="_blank">Tải File</a> </td></tr>';        }        echo $tr;        break;    default:        $ReceiveNum = explode("\n", $data['ReceiveNums']);        foreach ($ReceiveNum as $list_receive){            $list_receive = trim($list_receive);            if(is_numeric($list_receive)){                $data['ReceiveNum'] = $list_receive;                $result = historyCall($data);                $total_call = $result['total'];                if($total_call == 0){                    $status = '<i class="la la-dot-circle-o danger font-medium-1 mr-1"></i> <span class="text-danger">Không có cuộc gọi nào đến số này</span>';                }else{                    $status = '<i class="la la-dot-circle-o success font-medium-1 mr-1"></i> <span class="success">Có cuộc gọi đến số này</span>';                }                $tr .= '<tr><td class="'. ($total_call == 0 ? 'danger' : 'success') .'">'. $list_receive .'</td><td>'. $status .'</td><td class="'. ($total_call == 0 ? 'danger' : 'success') .'">'. $total_call .'</td><td>'. ($total_call > 0 ? '<a href="index.php?act=detail&date_start='. $date_start .'&date_end='. $date_end .''. ($data['CallNum'] ? '&call_num='.$data['CallNum'] : '') .''. ($data['ReceiveNums'] ? '&receive_num='.$list_receive : '') .'">Xem chi tiết</a>' : '') .'</td></tr>';            }        }        ?>        <!-- Result -->        <div class="col-lg-12">            <div class="card product_item_list">                <div class="body table-responsive">                    <table class="table table-hover m-b-0">                        <thead>                        <tr>                            <th width="25%">Số điện thoại</th>                            <th width="25%">Trạng thái</th>                            <th width="25%">Số cuộc gọi đến</th>                            <th width="25%"></th>                        </tr>                        </thead>                        <tbody>                        <?=$tr?>                        </tbody>                    </table>                </div>            </div>        </div>        <!-- Result -->        <?php        break;}