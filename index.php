<?php
/**
 * Created by PhpStorm.
 * User: nguye
 * Date: 05/01/2018
 * Time: 22:58
 */
require_once 'includes/core.php';
$header_title = $table_config['index_title'];
require_once 'includes/header.php';
?>
    <div class="row"><?php getNewsTop(6);?></div>
    <?php
        echo getSlideIndex(array('title' => 'Bài biết mới nhất', 'limit' => 6, 'post_type' => 'news'));
        echo getPostByCategoryIndex($table_config['category_index_1'],'block_1');
        echo getPostByCategoryIndex($table_config['category_index_2'],'block_2');
        echo getPostByCategoryIndex($table_config['category_index_3'],'block_3');
        echo getPostByCategoryIndex($table_config['category_index_4'],'block_4');
        echo getPostByCategoryIndex($table_config['category_index_5'],'block_5');
    ?>
    <!-- End Content -->
<?php
require_once 'includes/footer.php';