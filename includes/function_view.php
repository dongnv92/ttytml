<?php
/**
 * Created by PhpStorm.
 * User: nguye
 * Date: 01/03/2018
 * Time: 22:01
 */

function getViewError($text){
    return '<span class="help-block-error"><font color="#ed1c16"><em>'. $text .'</em></font></span>';
}

/*
 * array option
 * Label, name, value*/
function inputFormText($option){
    if($option['type'] == 'hozi'){
        return '<div class="form-group row"><label class="col-md-3 label-control">'. $option['label'] .'</label><div class="col-md-9"><input '. (($option['require']) ? 'required' : '') .' type="text" value="'. $option['value'] .'" name="'. $option['name'] .'" class="form-control">'. (($option['error']) ? $option['error'] : '') .'</div></div>';
    }else if($option['type'] == 'small'){
        return '<fieldset><h5>'. $option['label'] .'</h5><div class="form-group"><input type="text" '. (($option['require']) ? 'required' : '') .' class="form-control"  name="'. $option['name'] .'"  value="'. $option['value'] .'" placeholder="'. $option['label'] .'"></div>'. (($option['error']) ? $option['error'] : '') .'</fieldset>';
    }else{
        return '<div class="form-group label-floating"><label class="control-label">'. $option['label'] .'</label><input '. (($option['require']) ? 'required' : '') .' type="text" value="'. $option['value'] .'" name="'. $option['name'] .'" class="form-control">'. (($option['error']) ? $option['error'] : '') .'</div>';
    }
}

/*
 * array option
 * Label, name, value*/
function inputFormPassword($option){
    return '<div class="form-group row"><label class="col-md-3 label-control">'. $option['label'] .'</label><div class="col-md-9"><input '. (($option['require']) ? 'required' : '') .' type="password" value="'. $option['value'] .'" name="'. $option['name'] .'" class="form-control">'. (($option['error']) ? $option['error'] : '') .'</div></div>';
}

function inputFormRadio($option){
    return '<div class="radio"><label><input type="radio" name="'. $option['name'] .'" value="'. $option['value'] .'" '. (($option['checked'] == TRUE) ? 'checked="true"' : '') .'> '.$option['label'].'</label></div>';
}


/*
 * array option
 * Label, name, value*/
function inputFormTextarea($option){
    return '<div class="form-group label-floating"><label class="control-label">'. $option['label'] .'</label><textarea style="width: 100%" rows="20" name="'. $option['name'] .'">'. $option['value'] .'</textarea>'. (($option['error']) ? $option['error'] : '') .' </div>';
}

function getInfoPostStatus($data){
    global $lang;
    switch ($data){
        case 0:
            $text = $lang['post_status_0'];
            break;
        case 1:
            $text = $lang['post_status_1'];
            break;
        case 2:
            $text = $lang['post_status_2'];
            break;
    }
    return $text;
}

function getInfoPostType($data){
    global $lang;
    switch ($data){
        case 'news':
            $text = $lang['post_type_news'];
            break;
        case 'docs':
            $text = $lang['post_type_docs'];
            break;
        case 'report':
            $text = $lang['post_type_report'];
            break;
    }
    return $text;
}

function getInfoRole($level){
    $role = getGlobal('dong_role', array('id' => $level));
    return $role['role_name'];
}

function getDataTable($header, $data){
    $text = '';
    $text .= '<div class="table-responsive"><table class="table"><thead><tr>';
    foreach ($header AS $headers){
        $text .= '<th>'. $headers .'</th>';
    }
    $text .= '</tr></thead><tbody>';
    foreach ($data AS $texts){
        $text .= '<tr>';
        foreach ($texts AS $content){
            $text .= '<td>'. $content .'</td>';
        }
        $text .= '</tr>';
    }
    $text .= '</tbody></table></div>';
    return $text;
}

function getAlert($status = 'primary', $text = ''){
    return '<div class="alert round bg-'. $status .' alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert"><span class="alert-icon"><i class="la la-heart"></i></span><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>'. $text .'</div>';
}

function getNewsTop($limit = 5){
    global $table_config;
    ?>
    <div class="top-content">
        <div class="col-md-5">
            <div class="left-top-content drop-shadow margin-b30">
                <div class="top-slider1 ion-big">
                    <div class="wrap-item" data-pagination="true" data-paginumber="true"  data-navigation="true" data-itemscustom="[[0,1],[980,1]]">
                        <?php
                        $data = getGlobalAll('dong_post', array('post_headlines' => 1, 'post_type' => 'news', 'post_status' => 0), array('limit_number' => $limit, 'order_by_row' => 'id', 'order_by_value' => 'DESC'));
                        foreach ($data AS $datas){
                        ?>
                            <!-- Item -->
                            <div class="post-item">
                                <div class="post-thumb"><a href="<?php echo getUrlPost($datas['id']);?>"><img src="<?php echo $datas['post_images'] ? $datas['post_images'] : _URL_HOME.'/images/post/default.jpg';?>" alt=""></a></div>
                                <div class="slide-post-info">
                                    <a href="<?php echo getUrlPost($datas['id']);?>"><b><?php echo $datas['post_title'];?></b></a>
                                    <ul class="post-list-info"><li><i class="ion-android-calendar"></i><span> <?php echo date('d/m/Y', $datas['post_time']);?></span></li></ul>
                                </div>
                            </div>
                            <!-- End Item -->
                        <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4"> <!--Post Recent-->
            <div class="most-popular"><h3 class="title14 business-bg">Tin Mới Nhất</h3></div>
            <div class="post-box-home4">
                <div class="home4-rlt2">
                    <ul class="list-none">
                        <?php
                        foreach ($data AS $datass){
                            ?>
                            <li><a href="<?php echo getUrlPost($datass['id']);?>"><?php echo $datass['post_title'];?></a></li>
                            <?php
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <?php echo $table_config['block_6'];?>
        </div>
    </div>
    <?php
}

function getPost($option){
    ?>
    <div class="cat-home3 home-title listing-cat color-border-top margin-b30">
        <h2 class="color-title"><?php echo $option['title'];?></h2>
        <div class="post-box">
            <div class="square">
                <?php
                $data = getGlobalAll('dong_post', array('post_status' => 0, 'post_type' => $option['post_type']), array('limit_number' => $option['limit']));
                foreach ($data AS $datas) {
                    ?>
                    <div class="post-item">
                        <div class="post-thumb">
                            <a href="<?php echo getUrlPost($datas['id'])?>"><img width="50" height="50" src="<?php echo _URL_HOME.'/'.$datas['post_images']?>" class="attachment-50x50 size-50x50 wp-post-image" alt=""></a></div>
                        <div class="post-info">
                            <a href=""><b><?php echo $datas['post_title']?></b></a>
                            <ul class="post-list-info">
                                <li><i class="ion-android-calendar"></i><span> <?php echo getViewTime($datas['post_time'])?></span></li>
                            </ul>
                        </div>
                    </div>
                    <?php
                }
                    ?>
            </div>
        </div>
    </div>
    <?php
}

function getSlideIndex($option){
    ?>
    <div class="feature-posts home-title color-border-bottom ion-small drop-shadow margin-b30">
        <h2 class="color-title">Đọc nhiều trong ngày</h2>
        <div class="wrap-item" data-pagination="false" data-navigation="true" data-itemscustom="[[0,1],[480,2],[768,3],[980,5]]">
            <?php
            $data = getGlobalAll('dong_post', array('post_status' => 0, 'post_type' => $option['post_type']), array('limit_number' => $option['limit']));
            foreach ($data AS $datas){
                ?>
                <div class="post-item">
                    <div class="post-thumb">
                        <a href="<?php echo getUrlPost($datas['id'])?>"><img src="<?php echo $datas['post_images']?>" alt="" /></a>
                    </div>
                    <div class="post-info">
                        <a href="#"><b><?php echo $datas['post_title']?> ...</b></a>
                        <ul class="post-list-info">
                            <li>
                                <i class="ion-android-calendar"></i>
                                <span><?php echo getViewTime($datas['post_time'])?></span>
                            </li>
                        </ul>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
    <!-- End Feature Post -->
    <?php
}

function getAdminPanelError($message){
    ?>
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header"><h4 class="card-title"><?php echo $message['header'];?></h4> </div>
                <div class="card-body text-center">
                    <?php echo $message['message'];?>
                    <div class="form-actions text-center">
                        <?php echo _BUTTON_BACK;?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}

function getPostByCategoryIndex($category, $block){
    global $db_connect,$table_config;
    $cate = getGlobal('dong_category', array('id' => $category));
    if(checkGlobal('dong_post', array('post_category' => $cate['id'])) == 0){
        return '';
    }else {
        ?>
        <div class="business home-cat color-border-bottom margin-b30">
            <div class="head-cat"><h2 class="color-title"><?php echo $cate['category_name']; ?></h2></div>
            <div class="item-cat-home">
                <div class="col-md-9 cat-home-left">
                    <div class="home-cat-content">
                        <?php
                        $cate_first = getGlobalAll('dong_post', array('post_status' => 0, 'post_category' => $category), array(
                            'order_by_row' => 'id',
                            'order_by_value' => 'DESC',
                            'limit_number' => 1
                        ));
                        foreach ($cate_first AS $cate_firsts) {
                            $id_first = $cate_firsts['id'];
                            ?>
                            <div class="home-cat-first">
                                <div class="home-cat-thumb">
                                    <a href="<?php echo getUrlPost($cate_firsts['id']) ?>"><img
                                                src="<?php echo $cate_firsts['post_images']; ?>" alt=""></a>
                                </div>
                                <div class="home-cat-info">
                                    <a href="<?php echo getUrlPost($cate_firsts['id']) ?>"><b><?php echo $cate_firsts['post_title']; ?></b></a>
                                    <ul class="post-list-info">
                                        <li>
                                            <i class="ion-android-calendar"></i><span> <?php echo getViewTime($cate_firsts['post_time']); ?></span>
                                        </li>
                                    </ul>
                                    <p><?php echo createDescription($cate_firsts['post_content'], 150) ?> ...</p>
                                    <a href="<?php echo getUrlPost($cate_firsts['id']) ?>" class="more business-bg">Xem
                                        thêm</a>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="home-cat-related">
                            <div class="post-box">
                                <?php
                                $sql_iten = mysqli_query($db_connect, 'SELECT * FROM `dong_post` WHERE `post_status` = 0 AND `post_category` = "' . $category . '" AND `id` != "' . $id_first . '" LIMIT 4');
                                while ($row = mysqli_fetch_assoc($sql_iten)) {
                                    ?>
                                    <div class="post-item">
                                        <div class="post-thumb">
                                            <a href="<?php echo getUrlPost($row['id']) ?>"><img
                                                        src="<?php echo $row['post_images']; ?>" alt=""></a>
                                        </div>
                                        <div class="post-info">
                                            <a href="<?php echo getUrlPost($row['id']) ?>"><b><?php echo $row['post_title']; ?></b></a>
                                            <ul class="post-list-info">
                                                <li>
                                                    <i class="ion-android-calendar"></i><span> <?php echo getViewTime($row['post_time']) ?></span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <?php echo $table_config[$block];?>
                </div>
            </div>
        </div>
        <?php
    }
}