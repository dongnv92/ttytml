<?php
/**
 * Created by PhpStorm.
 * User: nguye
 * Date: 25/03/2018
 * Time: 20:55
 */
require_once '../includes/core.php';
$category       = getGlobal('dong_category', array('category_url' => $url));
$header_title   = $category['category_name'];
if(in_array($category['category_sub'], array(8, 27)) && !$user_id){
    header('location:'._URL_LOGIN);
}
require_once '../includes/header.php';
?>
<div class="row">
    <div class="bottom-listing2">
        <div class="col-md-8">
            <div class="listing-content">
                <div class="listing-title color-border-top"><h2 class="color-title pull-left"><?php echo $category['category_name']?></h2></div>
                <div class="post-box-listing listing1 margin-b20 border-bottom">
                <?php
                    $config_pagenavi['page_row']    = 10;
                    $config_pagenavi['page_num']    = ceil(mysqli_num_rows(mysqli_query($db_connect, "SELECT `id` FROM `dong_post` WHERE `post_category` = '". $category['id'] ."'"))/$config_pagenavi['page_row']);
                    $page_start                     = ($page-1) * $config_pagenavi['page_row'];
                    $config_pagenavi['url']         = _URL_HOME.'/'._SLUG_CATEGORY.'/'.$category['category_url'].'-trang';
                    $config_pagenavi['page']        = 'category';
                    $data   = getGlobalAll('dong_post', array('post_status' => 0, 'post_category' => $category['id']),array(
                        'order_by_row'  => 'id',
                        'order_by_value'=> 'DESC',
                        'limit_start'   => $page_start,
                        'limit_number'  => $config_pagenavi['page_row']
                    ));
                    $table_header   = array($lang['post_title'], $lang['label_category'], $lang['post_user'], $lang['post_status'], $lang['post_type'], $lang['post_hl'], $lang['post_time'],$lang['label_control']);
                    foreach ($data AS $datas){
                        ?>
                        <div class="post-item">
                            <div class="post-thumb">
                                <div class="list-cat-btn">
                                    <ul>
                                        <li><a href="<?php echo getUrlPost($datas['id'])?>" class="business-bg"><?php echo getInfoPostType($datas['post_type'])?></a></li>
                                    </ul>
                                </div>
                                <a href="<?php echo getUrlPost($datas['id'])?>"><img src="<?php echo _URL_HOME.'/'.$datas['post_images']?>" alt=""></a>
                            </div>
                            <div class="post-info">
                                <a href="<?php echo getUrlPost($datas['id'])?>"><b><?php echo $datas['post_title']?></b></a>
                                <ul class="post-list-info">
                                    <li><i class="ion-android-calendar"></i> <span><?php echo getViewTime($datas['post_time'])?></span></li>
                                </ul>
                                <p><?php echo createDescription($datas['post_content'], 200)?> ....</p>
                                <a href="<?php echo getUrlPost($datas['id'])?>" class="read-more">Đọc tiếp</a>
                            </div>
                        </div>
                    <?php
                    }
                ?>
                </div>
                <div class="page-nav-bar margin-b20 text-center">
                    <?php echo pagenaviGlobal($config_pagenavi)?>
                </div>
            </div>
        </div>
        <div class="col-md-4"><?php echo getPost(array('title' => 'Bài biết mới nhất', 'limit' => 5, 'post_type' => 'news'));?></div>
    </div>
</div>
<?php
require_once '../includes/footer.php';