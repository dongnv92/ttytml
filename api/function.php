<?php
/**
 * Created by PhpStorm.
 * User: DONG
 * Date: 18/09/2018
 * Time: 10:22
 */

class functionChatfuel{

    function cURL($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    function sendText($text){
        return json_encode(array('messages' => array(array('text' => $text))));
    }

    function sendImages($url){
        return json_encode(array('messages' => array(array('attachment' => array('type' => 'image', 'payload' => array('url' => $url))))));
    }

    function sendVideo($url){
        return json_encode(array('messages' => array(array('attachment' => array('type' => 'video', 'payload' => array('url' => $url))))));
    }

    function sendAudio($url){
        return json_encode(array('messages' => array(array('attachment' => array('type' => 'audio', 'payload' => array('url' => $url))))));
    }

    function sendFile($url){
        return json_encode(array('messages' => array(array('attachment' => array('type' => 'file', 'payload' => array('url' => $url))))));
    }

    /*
     * List
     * title        :  Title
     * subtitle     : Sub Title
     * image_url    : Url Images
     * buttons      :   array(
     *                      array('type'  => 'web_url','url'   => 'http://google.com','title' => 'Mua Hàng'))
     *                      array('set_attributes'=> array('product' => 'AO05'),'type'=> 'show_block','block_name' => 'Test Info','title'         => 'Xem Chi Tiết')
     *                  )
     *
     * $list[] = array(
        'title'         => 'Giày Snk mới về',
        'image_url'     => 'https://scontent.fhan3-3.fna.fbcdn.net/v/t1.0-9/41868372_289104705255499_4912183886328365056_n.jpg?_nc_cat=0&oh=fa79ce7326af508536e75b124e4260ea&oe=5C21190B',
        'subtitle'      => 'Size 38-43 M998370 Lẻ 450k',
        'buttons'       => array(array('type'  => 'web_url','url'   => 'https://www.facebook.com/permalink.php?story_fbid=289104775255492&id=100024679161166&__xts__%5B0%5D=68.ARCdd2Btc1pceaBAkgab3U5e6I5Fd6wKRNy8oZQw81mu1njRa5tZ8uZXorv4ah1jkASuQJ0eJwJz_xtAhDdfEKiWdmlYd200mBKyBanHrX1hx1BdudHPr42cZnKQqe4f4dQnvVFw7_tIYRrIgzlRHdo_MhKHB4nOy0Ss-PHYMbaTGAyRnoKU&__tn__=-R','title' => 'Mua Hàng'))
    );
     * */
    function sendGalleries($list){
        return json_encode(
            array('messages' => array(
                array('attachment' =>
                    array('type' => 'template', 'payload' =>
                        array('template_type' => 'generic', 'image_aspect_ratio' => 'square', 'elements' => $list)
                    )
                )
            )
            )
        );
    }
}
