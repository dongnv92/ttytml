<?php
require_once 'includes/core.php';

$current_packet = $arrPacket;

$packet_length = $this->bc_binaryToInteger(substr($arrPacket,2,1));
$protocol = $this->bc_binaryToInteger(substr($arrPacket,3,1));

$data = array(
    'data'	=> array()
);
$current_packet = substr($current_packet,4);
switch ($protocol) {
    case 1:
        $data['data']['type'] = 'login';
        $imei = unpack('H*', substr($current_packet,0,8));
        if(isset($imei[1])){
            $data['imei'] = ltrim($imei[1],'0');
        }else{
            return false;
        }
        $current_packet = substr($current_packet,8);

        $data['data']['type_identification_code'] = $this->bc_binaryToInteger(substr($current_packet, 0,2));

        $current_packet = substr($current_packet,2);
        $time_zone = substr($current_packet,0,2);

        $current_packet = substr($current_packet,2);
        $data['data']['time_zone'] = ($this->bc_binaryToInteger($time_zone) >> 4) / 100;

        $current_packet = substr($current_packet,2);
        $data['data']['u32UTC'] = Zend_Date::now()->toValue();
        $time_string = new Zend_date($data['data']['u32UTC']);

        $data['data']['time_string'] = $time_string->toString('yyyy-MM-dd HH:mm:ss');
        $data['data']['date_string'] = $time_string->toString('dd/MM/yyyy');
        $data['data']['date_value'] = $time_string->setHour(12)->setMinute(0)->setSecond(0)->setMilliSecond(0)->toValue();

        $data['data']['ACCSTATUS'] = 0;
        $data['data']['ACCSTATUS'] = 0;
        $data['data']['GPSSTATUS'] = 0;
        $data['data']['DOORSTATUS'] = 0;

        $responds = pack('H*','7878050100059FF80D0A');
        socket_write($socket,$responds,strlen($responds));
        break;
    default:
        return false;
        ;
        break;
}
return $data;