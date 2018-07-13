<?php
/**
 * Created by PhpStorm.
 * User: nguye
 * Date: 21/03/2018
 * Time: 21:40
 */
require_once '../includes/core.php';
$post       = getGlobal('dong_post', array('post_url' => $url));
$user_post  = getGlobal('dong_users', array('users_id' => $post['post_user']));
$header_title = $post['post_title'];
require_once '../includes/header.php';
?>
<div class="row">
    <div class="left-side col-md-8">
        <div class="post-content ">
            <div class="main-post-content margin-b30">
                <div class="post-item border-bottom">
                    <div class="post-info">
                        <h3 class="margin-b20"><?php echo $post['post_title'];?></h3>
                        <ul class="post-list-info">
                            <li><i class="ion-android-calendar"></i><span> <?php echo getViewTime($post['post_time'])?></span></li>
                            <li><?php echo $user_post['users_name'];?></li>
                        </ul>
                    </div>
                </div>
                <div class="post-detail margin-b30">
                    <p><?php echo $post['post_content']?></p>
                    <?php
                    if($post['post_source']){
                        echo '<p>Nguồn '. $post['post_source'] .'</p>';
                    }
                    ?>
                </div>
            </div>
        </div>
        <div class="single-share border-bottom">
            <a href="#" class="hvr-bob" target="blank"><i class="fa fa-facebook"></i><span></span></a>
            <a href="#" class="hvr-bob" target="blank"><i class="fa fa-twitter"></i><span></span></a>
            <a href="" class="hvr-bob" target="blank"><i class="fa fa-google"></i><span></span></a>
        </div>
        <div class="post-meta">
            <div class="article-avatar"><a href="#" class="hvr-rotate"><img alt='' src='<?php echo _URL_HOME.'/images/system/avatar.png';?>' /></a></div>
            <div class="article-info">
                <p>Viết bởi</p>
                <div class="social-article">
                    <h3><a href="#"><?php echo $user_post['users_name'];?></a></h3>
                    <ul class="list-inline-block">
                        <li><a class="dark" href="#"><i class="fa fa-facebook"></i></a></li>
                        <li><a class="dark" href="#"><i class="fa fa-twitter"></i></a></li>
                        <li><a class="dark" href="#"><i class="fa fa-pinterest"></i></a></li>
                    </ul>
                </div>
                <p>Quản trị viên</p>
            </div>
        </div>
        <!--<div class="control-text margin-b30 border-bottom border-top">
            <div class="row">
                <div class="col-xs-6"><div class="text-left"><i class="ion-chevron-left"></i><h3><a href="#" rel="prev">Ngành y tế Hà Nội hợp tác với Partners HealthCare.</a></h3></div></div>
                <div class="col-xs-6"><div class="text-right"><h3><a href="#" rel="next">Tổ chức các hoạt động kỷ niệm 63 năm Ngày thầy thuốc Việt Nam</a></h3><i class="ion-chevron-right"></i></div></div>
            </div>
        </div>-->
        <div class="leave-comment margin-b50">
            <h3>Viết bình luận</h3>
            <p>Địa chỉ email của bạn sẽ không hiển thị. Các trường bắt buộc được đánh dấu *</p>
            <form>
                <input name="name" value="Tên *" onfocus="if (this.value==this.defaultValue) this.value = ''" onblur="if (this.value=='') this.value = this.defaultValue" type="text">
                <input name="email" value="Đại chỉ Email *" onfocus="if (this.value==this.defaultValue) this.value = ''" onblur="if (this.value=='') this.value = this.defaultValue" type="email">
                <textarea name="message" cols="30" rows="8" onfocus="if (this.value==this.defaultValue) this.value = ''" onblur="if (this.value=='') this.value = this.defaultValue">Bình luận của bạn *</textarea>
                <input class="" value="Bình luận" type="submit">
            </form>
        </div>
    </div>
    <div class="col-md-4">
        <?php echo getPost(array('title' => 'Bài biết mới nhất', 'limit' => 5, 'post_type' => 'news'));?>
        <?php echo $table_config['block_6'];?>
        <?php echo $table_config['block_1'];?>
    </div>
</div>
<?php
require_once '../includes/footer.php';