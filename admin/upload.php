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
    case 'upload':
        $admin_title = 'Upload File';
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
        <?php
        break;
    default:
        $admin_title = 'Danh sách tập tinh';
        require_once 'header.php';
        ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"><h4 class="card-title">Danh sách tập tin</h4> </div>
                    <div class="card-content">
                        <?php
                        $config_pagenavi['page_row']    = 50;
                        $config_pagenavi['page_num']    = ceil(mysqli_num_rows(mysqli_query($db_connect, "SELECT `id` FROM `dong_files` WHERE `files_users` = '$user_id'"))/$config_pagenavi['page_row']);
                        $page_start                     = ($page-1) * $config_pagenavi['page_row'];
                        $config_pagenavi['url']         = _URL_ADMIN.'/upload.php?act=list';
                        $data   = getGlobalAll('dong_files', array('files_users' => $user_id),array(
                            'order_by_row'  => 'id',
                            'order_by_value'=> 'DESC',
                            'limit_start'   => $page_start,
                            'limit_number'  => $config_pagenavi['page_row']
                        ));
                        $table_header   = array('Tên File', 'Đường dẫn tải về', 'Ngày Upload');
                        $table_data     = array();
                        foreach ($data AS $datas){
                            $post_users     = getGlobal('dong_users', array('users_id' => $datas['files_users']));
                            array_push($table_data, array('<a href="'._URL_HOME.'/dl/'.$datas['id'].'">'.$datas['files_name'].'</a>', _URL_HOME.'/dl/'.$datas['id'], getViewTime($datas['files_time'])));
                        }
                        echo getDataTable($table_header, $table_data);
                        echo '<div class="text-center">'.pagenaviGlobal($config_pagenavi).'</div>';
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
