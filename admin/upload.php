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

// Cài đặt URL
$parameter                  = array();
$parameter['page']          = $page > 1 ? $page : false;
// Cài đặt URL

// File Where
$files_where                = array();
$files_where['files_users'] = $user_id;
// File Where

// Config Pagination
$db->select('id')->from(_TABLE_FILES)->where($files_where)->fetch();
$files_count                = $db->affected_rows;
$pagination['page_row']     = _CONFIG_PAGINATION;
$pagination['page_num']     = ceil($files_count/$pagination['page_row']);
$pagination['url']          = _URL_ADMIN.'/upload.php'.$function->createParameter(array('page' => '{page}'));
$page_start                 = ($page-1) * $pagination['page_row'];
// Config Pagination

// Data
$db->from(_TABLE_FILES)->where($files_where);
$db->order_by('id', 'DESC');
$db->limit(_CONFIG_PAGINATION, $page_start);
$data = $db->fetch();
// Data


$admin_title = 'Danh sách tập tin';
require_once 'header.php';
?>
<!-- Form Upload -->
<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header"><h4 class="card-title">Upload File</h4></div>
            <div class="card-body">
                <input type="file" name="files[]" id="input2" multiple="multiple" />
            </div>
        </div>
    </div>
</div>
<!-- Form Upload -->

<!-- Data -->
    <div class="row">
        <div id="recent-transactions" class="col-12">
            <div class="card">
                <div class="card-header"><h4 class="card-title">Danh sách tập tin (<?=$files_count?>)</h4></div>
                <div class="card-content">
                    <?php
                    if(count($data) == 0){
                        echo '<div class="text-center text-danger">Không có dữ liệu để hiển thị</div><br>';
                    }else{
                        ?>
                        <div class="table-responsive">
                            <table id="recent-orders" class="table table-hover table-xl mb-0">
                                <thead>
                                <tr>
                                    <th class="border-top-0" width="50%">Tên File</th>
                                    <th class="border-top-0" width="20%">Nơi Upload</th>
                                    <th class="border-top-0" width="10%">Lượt tải</th>
                                    <th class="border-top-0" width="10%">Lượt xem</th>
                                    <th class="border-top-0" width="10%">Thời gian tải lên</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                foreach ($data AS $row){
                                    ?>
                                    <tr>
                                        <td class="text-truncate"><i class="la la-cloud-upload success font-medium-1 mr-1"></i> <a href="download.php?id=<?=$row['id']?>"><?=$row['files_name']?></a></td>
                                        <td class="text-truncate"><?=$function->getStatus('files_type', $row['files_type'])?></td>
                                        <td class="text-truncate"><?=countFile($row['id'], 'file_download')?></td>
                                        <td class="text-truncate"><?=countFile($row['id'], 'file_view')?></td>
                                        <td class="text-truncate"><?=$function->getTimeDisplay($row['files_time'])?></td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                </tbody>
                            </table>
                            <?php echo '<nav aria-label="Page navigation">'. $function->pagination($pagination) .'</nav>';?>
                        </div>
                    <?php }?>
                </div>
            </div>
        </div>
    </div>
<!-- Data -->

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
