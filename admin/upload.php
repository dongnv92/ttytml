<?php
/**
 * Created by PhpStorm.
 * User: nguye
 * Date: 06/06/2018
 * Time: 22:27
 */
require_once "../includes/core.php";
if(!$user_id){
    header('location:'._URL_LOGIN);
}
$active_menu = 'upload';

switch ($act){
    case 'detail':
        $files = getGlobal('dong_files', array('id' => $id));
        // Check Post
        if(!$files){
            $admin_title    = 'Chi tiết tập tin';
            require_once 'header.php';
            echo getAdminPanelError(array('header' => $lang['label_notice'], 'message' => 'Tập tin không tồn tại'));
            require_once 'footer.php';
            exit();
        }
        $admin_title = 'Chi tiết tập tin';
        require_once 'header.php';
        ?>
        <div class="row">
            <div class="col">
                <a href="upload.php" class="btn btn-outline-cyan round">Quay lại</a><hr />
                <div class="card">
                    <div class="card-header"><h4 class="card-title"><?php echo $admin_title;?></h4> </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                <tr>
                                    <td width="30%">Tên File</td>
                                    <td width="70%"><?php echo $files['files_name'];?></td>
                                </tr>
                                <tr>
                                    <td width="30%">Đường đẫn tải về</td>
                                    <td width="70%"><?php echo _URL_HOME .'/dl/'. $files['id'];?></td>
                                </tr>
                                <tr>
                                    <td width="30%">Nhúng ảnh vài bài viết</td>
                                    <td width="70%">
                                        <?php
                                        $file_name = explode('.', $files['files_name']);
                                        $file_name = $file_name[(count($file_name) - 1)];
                                        if(in_array($file_name, array('jpg', 'JPG' , 'png', 'PNG' , 'JPEG', 'jpeg'))){
                                            echo '<textarea cols="80" rows="6"><div class="text-center"><img style="width: 80%" src="'. _URL_HOME .'/'. $files['files_url'] .'" /></div></textarea>';
                                        }else{
                                            echo 'Chỉ hỗ trợ File ảnh';
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Ngày tải lên</td>
                                    <td><?php echo getViewTime($files['files_time']);?></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        break;
    default:
        $admin_title = 'Danh sách tập tin';
        require_once 'header.php';
        ?>
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header"><h4 class="card-title">Upload File</h4> </div>
                    <div class="card-body">
                        <input type="file" name="files[]" id="input2" multiple="multiple" />
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"><h4 class="card-title">Danh sách tập tin</h4> </div>
                    <div class="card-content">
                        <?php

                        foreach ($para AS $paras){
                            if(isset($_REQUEST[$paras]) && !empty($_REQUEST[$paras])){
                                $parameters[$paras] = $_REQUEST[$paras];
                            }
                        }
                        $parameters['files_users'] = $user_id;
                        if($parameters){
                            foreach ($parameters as $key => $value) {
                                $colums[] = '`'.$key .'` = "'. checkInsert($value) .'"';
                            }
                            $parameters_list = ' WHERE '.implode(' AND ', $colums);
                        }

                        // Tạo Url Parameter động
                        foreach ($parameters as $key => $value) {
                            $para_url[] = $key .'='. $value;
                        }
                        $para_list                      = implode('&', $para_url);
                        // Tạo Url Parameter động
                        $config_pagenavi['page_row']    = _CONFIG_PAGINATION;
                        $config_pagenavi['page_num']    = ceil(checkGlobal(_TABLE_POST, $parameters)/$config_pagenavi['page_row']);
                        $config_pagenavi['url']         = _URL_ADMIN.'/upload.php?'.$para_list.'&';
                        $page_start                     = ($page-1) * $config_pagenavi['page_row'];
                        $data   = getGlobalAll('dong_files', $parameters,array(
                            'order_by_row'  => 'id',
                            'order_by_value'=> 'DESC',
                            'limit_start'   => $page_start,
                            'limit_number'  => $config_pagenavi['page_row']
                        ));
                        echo '<div class="table-responsive">';
                        echo '<table class="table">';
                        echo '<thread>';
                        echo '<tr>';
                        echo '<th width="45%">Tên Files</th>';
                        echo '<th>Đường dẫn tải về</th>';
                        echo '<th>Thời gian tải lên</th>';
                        echo '<th>Ghi chú</th>';
                        echo '</tr>';
                        echo '</thread>';
                        echo '<tbody>';
                        foreach ($data AS $datas){
                            echo '<tr>';
                                echo '<td><a href="'. _URL_ADMIN .'/upload.php?act=detail&id='. $datas['id'] .'">'. $datas['files_name'] .'</a></td>';
                                echo '<td>'. _URL_HOME .'/dl/'. $datas['id'] .'</td>';
                                echo '<td>'. getViewTime($datas['files_time']) .'</td>';
                                echo '<td>...</td>';
                            echo '</tr>';
                        }
                        echo '</tbody>';
                        echo '</table>';
                        echo '</div>';
                        echo '<nav aria-label="Page navigation">'.pagination($config_pagenavi).'</nav>';
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
        break;
}
?>
    <!--JavaScript-->
    <script language="JavaScript">
        $(document).ready(function() {
            $('#input1').filer();

            $('.file_input').filer({
                showThumbs: true,
                templates: {
                    box: '<ul class="jFiler-item-list"></ul>',
                    item: '<li class="jFiler-item">\
                            <div class="jFiler-item-container">\
                                <div class="jFiler-item-inner">\
                                    <div class="jFiler-item-thumb">\
                                        <div class="jFiler-item-status"></div>\
                                        <div class="jFiler-item-info">\
                                            <span class="jFiler-item-title"><b title="{{fi-name}}">{{fi-name | limitTo: 25}}</b></span>\
                                        </div>\
                                        {{fi-image}}\
                                    </div>\
                                    <div class="jFiler-item-assets jFiler-row">\
                                        <ul class="list-inline pull-left">\
                                            <li><span class="jFiler-item-others">{{fi-icon}} {{fi-size2}}</span></li>\
                                        </ul>\
                                        <ul class="list-inline pull-right">\
                                            <li><a class="icon-jfi-trash jFiler-item-trash-action"></a></li>\
                                        </ul>\
                                    </div>\
                                </div>\
                            </div>\
                        </li>',
                    itemAppend: '<li class="jFiler-item">\
                            <div class="jFiler-item-container">\
                                <div class="jFiler-item-inner">\
                                    <div class="jFiler-item-thumb">\
                                        <div class="jFiler-item-status"></div>\
                                        <div class="jFiler-item-info">\
                                            <span class="jFiler-item-title"><b title="{{fi-name}}">{{fi-name | limitTo: 25}}</b></span>\
                                        </div>\
                                        {{fi-image}}\
                                    </div>\
                                    <div class="jFiler-item-assets jFiler-row">\
                                        <ul class="list-inline pull-left">\
                                            <span class="jFiler-item-others">{{fi-icon}} {{fi-size2}}</span>\
                                        </ul>\
                                        <ul class="list-inline pull-right">\
                                            <li><a class="icon-jfi-trash jFiler-item-trash-action"></a></li>\
                                        </ul>\
                                    </div>\
                                </div>\
                            </div>\
                        </li>',
                    progressBar: '<div class="bar"></div>',
                    itemAppendToEnd: true,
                    removeConfirmation: true,
                    _selectors: {
                        list: '.jFiler-item-list',
                        item: '.jFiler-item',
                        progressBar: '.bar',
                        remove: '.jFiler-item-trash-action',
                    }
                },
                addMore: true,
                files: [{
                    name: "appended_file.jpg",
                    size: 5453,
                    type: "image/jpg",
                    file: "http://dummyimage.com/158x113/f9f9f9/191a1a.jpg",
                },{
                    name: "appended_file_2.png",
                    size: 9503,
                    type: "image/png",
                    file: "http://dummyimage.com/158x113/f9f9f9/191a1a.png",
                }]
            });

            $('#input2').filer({
                limit: null,
                maxSize: null,
                extensions: null,
                changeInput: '<div class="jFiler-input-dragDrop"><div class="jFiler-input-inner"><div class="jFiler-input-icon"><i class="icon-jfi-cloud-up-o"></i></div><div class="jFiler-input-text"><h3>Kéo File và thả tại đây</h3> <span style="display:inline-block; margin: 15px 0">HOẶC</span></div><a class="jFiler-input-choose-btn blue">Chọn tập tin</a></div></div>',
                showThumbs: true,
                appendTo: null,
                theme: "dragdropbox",
                templates: {
                    box: '<ul class="jFiler-item-list"></ul>',
                    item: '<li class="jFiler-item">\
                            <div class="jFiler-item-container">\
                                <div class="jFiler-item-inner">\
                                    <div class="jFiler-item-thumb">\
                                        <div class="jFiler-item-status"></div>\
                                        <div class="jFiler-item-info">\
                                            <span class="jFiler-item-title"><b title="{{fi-name}}">{{fi-name | limitTo: 25}}</b></span>\
                                        </div>\
                                        {{fi-image}}\
                                    </div>\
                                    <div class="jFiler-item-assets jFiler-row">\
                                        <ul class="list-inline pull-left">\
                                            <li>{{fi-progressBar}}</li>\
                                        </ul>\
                                        <ul class="list-inline pull-right">\
                                            <li><a class="icon-jfi-trash jFiler-item-trash-action">Xóa</a></li>\
                                        </ul>\
                                    </div>\
                                </div>\
                            </div>\
                        </li>',
                    itemAppend: '<li class="jFiler-item">\
                            <div class="jFiler-item-container">\
                                <div class="jFiler-item-inner">\
                                    <div class="jFiler-item-thumb">\
                                        <div class="jFiler-item-status"></div>\
                                        <div class="jFiler-item-info">\
                                            <span class="jFiler-item-title"><b title="{{fi-name}}">{{fi-name | limitTo: 25}}</b></span>\
                                        </div>\
                                        {{fi-image}}\
                                    </div>\
                                    <div class="jFiler-item-assets jFiler-row">\
                                        <ul class="list-inline pull-left">\
                                            <span class="jFiler-item-others">{{fi-icon}} {{fi-size2}}</span>\
                                        </ul>\
                                        <ul class="list-inline pull-right">\
                                            <li><a class="icon-jfi-trash jFiler-item-trash-action">Xóa</a></li>\
                                        </ul>\
                                    </div>\
                                </div>\
                            </div>\
                        </li>',
                    progressBar: '<div class="bar"></div>',
                    itemAppendToEnd: false,
                    removeConfirmation: true,
                    _selectors: {
                        list: '.jFiler-item-list',
                        item: '.jFiler-item',
                        progressBar: '.bar',
                        remove: '.jFiler-item-trash-action',
                    }
                },
                uploadFile: {
                    url: "upload_process.php",
                    data: {},
                    type: 'POST',
                    enctype: 'multipart/form-data',
                    beforeSend: function(){},
                    success: function(data, el){
                        var parent = el.find(".jFiler-jProgressBar").parent();
                        el.find(".jFiler-jProgressBar").fadeOut("slow", function(){
                            $("<div class=\"jFiler-item-others text-success\"><i class=\"icon-jfi-check-circle\"></i> Thành công</div>").hide().appendTo(parent).fadeIn("slow");
                        });
                    },
                    error: function(el){
                        var parent = el.find(".jFiler-jProgressBar").parent();
                        el.find(".jFiler-jProgressBar").fadeOut("slow", function(){
                            $("<div class=\"jFiler-item-others text-error\"><i class=\"icon-jfi-minus-circle\"></i> Lỗi rồi</div>").hide().appendTo(parent).fadeIn("slow");
                        });
                    },
                    statusCode: {},
                    onProgress: function(){},
                },
                dragDrop: {
                    dragEnter: function(){},
                    dragLeave: function(){},
                    drop: function(){},
                },
                addMore: true,
                clipBoardPaste: true,
                excludeName: null,
                beforeShow: function(){return true},
                onSelect: function(){},
                afterShow: function(){},
                onRemove: function(itemEl, file, id, listEl, boxEl, newInputEl, inputEl){
                    var filerKit = inputEl.prop("jFiler"),
                        file_name = filerKit.files_list[id].name;
                    $.post('upload_process_remove.php', {file: file_name});
                },
                onEmpty: function(){},
                captions: {
                    button: "Choose Files",
                    feedback: "Choose files To Upload",
                    feedback2: "files were chosen",
                    drop: "Drop file here to Upload",
                    removeConfirmation: "Are you sure you want to remove this file?",
                    errors: {
                        filesLimit: "Only {{fi-limit}} files are allowed to be uploaded.",
                        filesType: "Only Images are allowed to be uploaded.",
                        filesSize: "{{fi-name}} is too large! Please upload file up to {{fi-maxSize}} MB.",
                        filesSizeAll: "Files you've choosed are too large! Please upload files up to {{fi-maxSize}} MB."
                    }
                }
            });
        });
    </script>
<?php
require_once 'footer.php';
