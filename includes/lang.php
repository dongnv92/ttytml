<?php
/**
 * Created by PhpStorm.
 * User: nguye
 * Date: 25/02/2018
 * Time: 20:24
 */

$lang = array();

// Language Global
$lang['label_login']            = 'Đăng Nhập';
$lang['label_register']         = 'Đăng ký';
$lang['label_logout']           = 'Đăng xuất';
$lang['label_users']            = 'Thành viên';
$lang['label_password']         = 'Mật khẩu';
$lang['label_setting']          = 'Cài đặt';
$lang['label_home']             = 'Trang chủ';
$lang['label_post']             = 'Bài viết';
$lang['label_category']         = 'Chuyên mục';
$lang['label_input_url']        = 'Đường dẫn';
$lang['label_description']      = 'Mô tả';
$lang['label_control']          = 'Điều khiển';
$lang['label_edit']             = 'Chỉnh sửa';
$lang['label_del']              = 'Xóa';
$lang['label_option']           = 'Tùy chọn';
$lang['label_url']              = 'Đường dẫn';
$lang['label_notice']           = 'Thông báo';
$lang['label_post_new']         = 'Bài viết mới nhất';
$lang['label_back']             = 'Bài viết';
$lang['label_update']           = 'Cập nhập';
$lang['label_tasks']            = 'Công việc';
$lang['label_reply']            = 'Trả lời';
$lang['label_info']             = 'Thông tin';
$lang['label_report']           = 'Báo cáo';

// Login.php
$lang['login_remember_me']      = 'Ghi nhớ đăng nhập';
$lang['login_forrgot_password'] = 'Quên mật khẩu';
$lang['login_register']         = 'Đăng ký';
$lang['login_error']            = 'Đăng nhập không thành công';

// Admin/index.php
$lang['admin_title']            = 'Trang quản trị';
$lang['admin_view_profile']     = 'Trang cá nhân';

// Admin/post.php
$lang['post_list']              = 'Danh sách bài viết';
$lang['post_add']               = 'Thêm bài viết';
$lang['post_del']               = 'Xóa bài viết';
$lang['post_title']             = 'Tiêu đề';
$lang['post_select_category']   = 'Chọn chuyên mục';
$lang['post_headlines']         = 'Hiển thị trên tin chính';
$lang['post_des']               = 'Mô tả';
$lang['post_key']               = 'Từ khóa';
$lang['post_type']              = 'Kiểu bài viết';
$lang['post_type_news']         = 'Tin tức';
$lang['post_type_docs']         = 'Văn bản';
$lang['post_type_report']       = 'Báo cáo';
$lang['post_images']            = 'Ảnh bài viết';
$lang['post_images_choose']     = 'Chọn Ảnh';
$lang['post_images_change']     = 'Đổi ảnh khác';
$lang['post_user']              = 'Người đăng';
$lang['post_status']            = 'Trạng thái';
$lang['post_type']              = 'Định dạng';
$lang['post_hl']                = 'Nổi bật';
$lang['post_time']              = 'Đăng lúc';
$lang['post_status_0']          = 'Đã được đăng';
$lang['post_status_1']          = 'Đang chờ xét duyệt';
$lang['post_status_2']          = 'Không được đăng';
$lang['post_type_news']         = 'Tin tức';
$lang['post_type_docs']         = 'Văn bản';
$lang['post_type_report']       = 'Báo cáo';
$lang['post_update']            = 'Sửa bài viết';
$lang['post_update_success']    = 'Sửa bài viết thành công';
$lang['post_list_approval']     = 'Đang chờ duyệt';
$lang['post_approval']          = 'Đã duyệt';

// Admin/category.php
$lang['category_manager']       = 'Quản lý chuyên mục';
$lang['category_add']           = 'Thêm chuyên mục';
$lang['category_name']          = 'Tên chuyên mục';
$lang['category_parent']        = 'Chuyên mục cha';
$lang['category_option_parent'] = 'Chọn chuyên mục cha';
$lang['category_parent_empty']  = 'Chuyên mục cha không đúng';
$lang['category_list']          = 'Danh sách chuyên mục';
$lang['category_add_success']   = 'Đã thêm chuyên mục';
$lang['category_update']        = 'Cập nhập chuyên mục';
$lang['category_del']           = 'Xóa chuyên mục';
$lang['category_update_success']= 'Đã cập nhập chuyên mục';

// Admin/users.php
$lang['users_list']             = 'Danh sách thành viên';
$lang['users_add']              = 'Thêm thành viên';
$lang['users_edit_role']        = 'Sửa chức vụ';
$lang['users_manager']          = 'Quản lý thành viên';
$lang['users_manager_users']    = 'Người quản lý';
$lang['users_manager_list']     = 'Danh sách những người {{name}} quản lý';
$lang['users_name_login']       = 'Tên đăng nhập';
$lang['users_repass']           = 'Nhập lại mật khẩu';
$lang['users_email']            = 'Email';
$lang['users_name']             = 'Tên hiển thị';
$lang['users_phone']            = 'Số điện thoại';
$lang['users_select_role']      = 'Chọn vai trò';
$lang['users_select_manager']   = 'Chọn người quản lý';
$lang['users_select_manager']   = 'Chọn người quản lý';
$lang['users_role']             = 'Vai trò';
$lang['users_role_9']           = 'Ban quản trị';
$lang['users_role_8']           = 'Kiểm duyệt';
$lang['users_role_7']           = 'Biên tập viên';
$lang['users_role_6']           = 'Cộng tác viên';
$lang['users_pass_nosame']      = '2 mật khẩu không giống nhau';
$lang['users_user_exits']       = 'Tên đăng nhập này đã tồn tại';
$lang['users_email_exits']      = 'Email đã tồn tại';
$lang['users_add_success']      = 'Thêm thành viên thành công';
$lang['users_update']           = 'Sửa thành viên';
$lang['users_update_node']      = 'Chú ý: Nếu đổi mật khẩu thì nhập mật khẩu mới, không thì không cần nhập vào ô mật khẩu';

// Admin/settings.php
$lang['settings_general']       = 'Cài đặt chung';
$lang['settings_role']          = 'Sửa quyền truy cập';
$lang['settings_url_home']      = 'URL trang chủ';
$lang['settings_title_home']    = 'Tiêu đề trang chủ';
$lang['settings_role_choose']   = 'Chọn 1 quản trị viên';
$lang['settings_role_name']     = 'Tên quản trị viên';
$lang['settings_role_no_id']    = 'Bạn cần chọn 1 quản trị viên để chỉnh sửa quyền';

// Admin/tasks.php
$lang['tasks_add']              = 'Thêm công việc';
$lang['tasks_manager']          = 'Quản lý công việc';
$lang['tasks_date_start']       = 'Ngày giao việc';
$lang['tasks_date_end']         = 'Ngày hoàn thành';
$lang['tasks_date_finish']      = 'Ngày phải hoàn thành';
$lang['tasks_input_title']      = 'Nhập tên công việc';
$lang['tasks_input_files']      = 'Chọn tệp đính kèm';
$lang['tasks_input_users']      = 'Người nhận công việc';
$lang['tasks_input_guide']      = 'Hướng dẫn công việc';
$lang['tasks_users_send']       = 'Ngưởi giao';
$lang['tasks_users_receive']    = 'Ngưởi nhận';
$lang['tasks_status_0']         = 'Chưa nhận việc';
$lang['tasks_status_1']         = 'Đã nhận việc';
$lang['tasks_status_2']         = 'Đã Hoàn thành';
$lang['tasks_from_star']        = 'Quản lý đánh giá';
$lang['tasks_to_star']          = 'Tự đánh Giá';
$lang['tasks_file']             = 'Tập tin đính kèm';
$lang['tasks_detail']           = 'Chi tiết công việc';
$lang['tasks_empty_content']    = 'Bạn không có quyền truy cập công việc này, hoặc công việc này không tồn tại';
$lang['tasks_star_1']           = 'Không hoàn thành';
$lang['tasks_star_2']           = 'Hoàn thành';
$lang['tasks_star_3']           = 'Hoàn thành tốt';
$lang['tasks_progess']          = 'Tiến trình';
$lang['tasks_progess_1']        = 'Hoàn thành chậm <strong>{day}</strong> ngày';
$lang['tasks_progess_2']        = 'Hoàn thành đúng tiến độ';
$lang['tasks_progess_3']        = 'Hoàn thành trước <strong>{day}</strong> ngày';
$lang['tasks_progess_excel_1']  = 'Hoàn thành chậm {day} ngày';
$lang['tasks_progess_excel_3']  = 'Hoàn thành trước {day} ngày';
$lang['tasks_result']           = 'Báo cáo kết quả làm việc';
$lang['tasks_result_manager']   = 'Báo cáo kết quả làm việc những người bạn quản lý';
$lang['tasks_result_header']    = 'Báo cáo kết quả làm việc trong 4 tuần gần nhất';
$lang['tasks_time_finish']      = 'Ngày thực tế hoàn thành';

// Admin/report.php
$lang['report_title']           = 'Báo cáo kết quả';
$lang['report_detail']          = 'Chi tiết kết quả làm việc';

// Notification
$lang['notification_add_task']          = '{users_send} đã gửi cho bạn 1 công việc mới';
$lang['notification_add_comment']       = '{users_send} đã bình luận trong công việc của bạn';
$lang['notification_task_close']        = '{users_send} vừa kết thúc và tự đánh giá 1 công việc';
$lang['notification_task_send_close']   = '{users_send} Vừa đánh giá công việc của bạn';
$lang['notification_task_seen']         = 'Việc bạn gửi đã có người nhận và bắt đầu làm việc.';

// Error
$lang['error_empty_input']      = 'Bạn cần nhập đủ các trường';
$lang['error_empty_this_fiel']  = 'Bạn cần nhập ô này';
$lang['error_url_exits']        = 'URL này đã tồn tại';
$lang['error_no_accep']         = 'Bạn không có quyền truy cập tính năng này';
$lang['error_access']           = 'Lỗi truy cập';
$lang['error_mysql']            = 'Có lỗi đến từ cú pháp MYSQL SERVER. Hãy liên hệ đến Ban Quản trị';
