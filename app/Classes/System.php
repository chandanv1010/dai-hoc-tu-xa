<?php
namespace App\Classes;

class System{

    public function config(){
        $data['homepage'] = [
            'label' => 'Thông tin chung',
            'description' => 'Cài đặt đầy đủ thông tin chung của website. Tên thương hiệu hiệu website, Logo, Favicon, vv...',
            'value' => [
                'company' => ['type' => 'text', 'label' => 'Tên công ty'],
                'brand' => ['type' => 'text', 'label' => 'Tên thương hiệu'],
                'slogan' => ['type' => 'text', 'label' => 'Slogan'],
                'logo' => ['type' => 'images', 'label' => 'Logo Website', 'title' => 'Click vào ô phía dưới để tải logo'],
                'logo_mobile' => ['type' => 'images', 'label' => 'Logo Mobile', 'title' => 'Click vào ô phía dưới để tải logo'],
                'favicon' => ['type' => 'images', 'label' => 'Favicon', 'title' => 'Click vào ô phía dưới để tải logo'],
                'copyright' => ['type' => 'text', 'label' => 'Copyright'],
                'website' => [
                    'type' => 'select', 
                    'label' => 'Tình trạng website',
                    'option' => [
                        'open' => 'Mở cửa website',
                        'close' => 'Website đang bảo trì'
                    ]
                ],
                'homepage_download' => ['type' => 'images', 'label' => 'File tải lộ trình học', 'title' => 'Click vào ô phía dưới để tải file'],
                'enrollment_text' => ['type' => 'text', 'label' => 'Text thông báo tuyển sinh (Header)'],
                'why_distance_learning_footer_title' => ['type' => 'text', 'label' => 'Tại sao học từ xa - Tiêu đề Footer'],
                'why_distance_learning_footer_subtitle' => ['type' => 'text', 'label' => 'Tại sao học từ xa - Mô tả Footer'],
                'value-bring_name' => ['type' => 'text', 'label' => 'Giá trị bằng đại học - Tiêu đề'],
                'value-bring_description' => ['type' => 'editor', 'label' => 'Giá trị bằng đại học - Mô tả'],
                'value-bring_video' => ['type' => 'textarea', 'label' => 'Giá trị bằng đại học - Video (URL hoặc embed code)'],
                'value-bring_image' => ['type' => 'images', 'label' => 'Giá trị bằng đại học - Hình ảnh'],
                'value-bring_image_2' => ['type' => 'images', 'label' => 'Giá trị bằng đại học - Hình ảnh 2'],
                'post_contact_title' => ['type' => 'text', 'label' => 'Tiêu đề khối liên hệ (dưới bài viết)'],
                'post_contact_website' => ['type' => 'text', 'label' => 'Website (dưới bài viết)'],
                'post_contact_fanpage' => ['type' => 'text', 'label' => 'Fanpage (dưới bài viết)'],
                'post_center_title' => ['type' => 'text', 'label' => 'Tiêu đề giới thiệu trung tâm (dưới bài viết)'],
                'post_center_description' => ['type' => 'editor', 'label' => 'Mô tả giới thiệu trung tâm (dưới bài viết)'],
                'schools_catalogue_meta_title' => ['type' => 'text', 'label' => 'SEO - Danh sách trường: Meta Title'],
                'schools_catalogue_meta_description' => ['type' => 'textarea', 'label' => 'SEO - Danh sách trường: Meta Description'],
                'schools_catalogue_meta_keyword' => ['type' => 'text', 'label' => 'SEO - Danh sách trường: Meta Keyword'],
                'schools_catalogue_meta_image' => ['type' => 'images', 'label' => 'SEO - Danh sách trường: Meta Image'],
                'majors_catalogue_meta_title' => ['type' => 'text', 'label' => 'SEO - Danh sách ngành: Meta Title'],
                'majors_catalogue_meta_description' => ['type' => 'textarea', 'label' => 'SEO - Danh sách ngành: Meta Description'],
                'majors_catalogue_meta_keyword' => ['type' => 'text', 'label' => 'SEO - Danh sách ngành: Meta Keyword'],
                'majors_catalogue_meta_image' => ['type' => 'images', 'label' => 'SEO - Danh sách ngành: Meta Image'],
                'homepage_h1' => ['type' => 'text', 'label' => 'Tiêu đề H1 (hiển thị display:none cho SEO)'],
            ]
        ];

        $data['statistics'] = [
            'label' => 'Thống kê trang chủ',
            'description' => 'Cài đặt các thống kê hiển thị trên trang chủ',
            'value' => [
                'stat_1_number' => ['type' => 'text', 'label' => 'Số lượng - Trường Đào Tạo Từ Xa'],
                'stat_1_label' => ['type' => 'text', 'label' => 'Nhãn - Trường Đào Tạo Từ Xa'],
                'stat_2_number' => ['type' => 'text', 'label' => 'Số lượng - Ngành Học'],
                'stat_2_label' => ['type' => 'text', 'label' => 'Nhãn - Ngành Học'],
                'stat_3_number' => ['type' => 'text', 'label' => 'Số lượng - Học Viên Theo Học'],
                'stat_3_label' => ['type' => 'text', 'label' => 'Nhãn - Học Viên Theo Học'],
                'stat_4_number' => ['type' => 'text', 'label' => 'Số lượng - Khóa Khai giảng'],
                'stat_4_label' => ['type' => 'text', 'label' => 'Nhãn - Khóa Khai giảng'],
                'stat_5_number' => ['type' => 'text', 'label' => 'Số lượng - Học Viên Hài Lòng (%)'],
                'stat_5_label' => ['type' => 'text', 'label' => 'Nhãn - Học Viên Hài Lòng'],
                'stat_6_number' => ['type' => 'text', 'label' => 'Số lượng - Có việc sau tốt nghiệp (%)'],
                'stat_6_label' => ['type' => 'text', 'label' => 'Nhãn - Có việc sau tốt nghiệp'],
            ]
        ];

        $data['contact'] = [
            'label' => 'Thông tin liên hệ',
            'description' => 'Cài đặt thông tin liên hệ của website ví dụ: Địa chỉ công ty, Văn phòng giao dịch, Hotline, Bản đồ, vv...',
            'value' => [
                'office' => ['type' => 'text', 'label' => 'Địa chỉ công ty'],
                'office_map' => [
                    'type' => 'textarea', 
                    'label' => 'Bản đồ công ty',
                    'link' => [
                        'text' => 'Hướng dẫn thiết lập bản đồ',
                        'href' => 'https://manhan.vn/hoc-website-nang-cao/huong-dan-nhung-ban-do-vao-website/',
                        'target' => '_blank'
                    ]
                ],
                'address' => ['type' => 'text', 'label' => 'Văn phòng'],
                'hotline' => ['type' => 'text', 'label' => 'Hotline'],
                'phone' => ['type' => 'text', 'label' => 'Số cố định'],
                'fax' => ['type' => 'text', 'label' => 'Fax'],
                'email' => ['type' => 'text', 'label' => 'Email'],
                'website' => ['type' => 'text', 'label' => 'Website'],
                'map' => [
                    'type' => 'textarea', 
                    'label' => 'Bản đồ', 
                    'link' => [
                        'text' => 'Hướng dẫn thiết lập bản đồ',
                        'href' => 'https://manhan.vn/hoc-website-nang-cao/huong-dan-nhung-ban-do-vao-website/',
                        'target' => '_blank'
                    ]
                ],
                'intro' => ['type' => 'textarea', 'label' => 'Giới thiệu'],
            ]
        ];

        
        $data['form_tai_lo_trinh'] = [
            'label' => 'Cấu Hình Form Tải Lộ Trình Học',
            'description' => 'Cài đặt đầy đủ thông tin ',
            'value' => [
                'script' => ['type' => 'textarea', 'label' => 'Mã Nhúng'],
                'title' => ['type' => 'text', 'label' => 'Tiêu đề Form'],
                'description' => ['type' => 'text', 'label' => 'Mô tả'],
                'footer' => ['type' => 'textarea', 'label' => 'Footer (có thể dùng HTML, ví dụ: Còn <span class="cl">10</span> chỉ tiêu tuyển sinh năm 2025)'],
            ]
        ];

        $data['form_tu_van_mien_phi'] = [
            'label' => 'Cấu Hình Form Tư vấn miễn phí',
            'description' => 'Cài đặt đầy đủ thông tin ',
            'value' => [
                'script' => ['type' => 'textarea', 'label' => 'Mã Nhúng'],
                'title' => ['type' => 'text', 'label' => 'Tiêu đề Form'],
                'description' => ['type' => 'text', 'label' => 'Mô tả'],
                'footer' => ['type' => 'textarea', 'label' => 'Footer (có thể dùng HTML, ví dụ: Còn <span class="cl">10</span> chỉ tiêu tuyển sinh năm 2025)'],
            ]
        ];

        $data['form_hoc_thu'] = [
            'label' => 'Cấu Hình Form Học Thử Miễn Phí',
            'description' => 'Cài đặt đầy đủ thông tin ',
            'value' => [
                'script' => ['type' => 'textarea', 'label' => 'Mã Nhúng'],
                'title' => ['type' => 'text', 'label' => 'Tiêu đề Form'],
                'description' => ['type' => 'text', 'label' => 'Mô tả'],
                'footer' => ['type' => 'textarea', 'label' => 'Footer (có thể dùng HTML, ví dụ: Còn <span class="cl">10</span> suất học thử miễn phí)'],
            ]
        ];

        $data['form_san_pham'] = [
            'label' => 'Cấu Hình Form Sản Phẩm',
            'description' => 'Cài đặt đầy đủ thông tin ',
            'value' => [
                'script' => ['type' => 'textarea', 'label' => 'Mã Nhúng'],
                'title' => ['type' => 'text', 'label' => 'Tiêu đề Form'],
                'description' => ['type' => 'text', 'label' => 'Mô tả'],
                'footer' => ['type' => 'textarea', 'label' => 'Footer (có thể dùng HTML, ví dụ: Còn <span class="cl">10</span> chỉ tiêu tuyển sinh năm 2025)'],
            ]
        ];

        $data['product_cta'] = [
            'label' => 'Cấu Hình Sidebar Sản Phẩm',
            'description' => 'Cài đặt thông tin sidebar hiển thị trên trang chi tiết sản phẩm',
            'value' => [
                'cta_button_text' => ['type' => 'text', 'label' => 'Text nút CTA'],
                'cta_button_link' => ['type' => 'text', 'label' => 'Link nút CTA'],
                'overview_title' => ['type' => 'text', 'label' => 'Tiêu đề Tổng quan'],
                'overview_item_1' => ['type' => 'text', 'label' => 'Mục 1 - Đầu ra'],
                'overview_item_2' => ['type' => 'text', 'label' => 'Mục 2 - Học online'],
                'overview_item_3' => ['type' => 'text', 'label' => 'Mục 3 - Nội dung'],
                'overview_item_4' => ['type' => 'textarea', 'label' => 'Mục 4 - Đặc biệt (có thể dùng HTML)'],
            ]
        ];
        
        $data['seo'] = [
            'label' => 'Cấu hình SEO dành cho trang chủ',
            'description' => 'Cài đặt đầy đủ thông tin về SEO của trang chủ website. Bao gồm tiêu đề SEO, Từ Khóa SEO, Mô Tả SEO, Meta images',
            'value' => [
                'meta_title' => ['type' => 'text', 'label' => 'Tiêu đề SEO'],
                'meta_keyword' => ['type' => 'text', 'label' => 'Từ khóa SEO'],
                'meta_description' => ['type' => 'textarea', 'label' => 'Mô tả SEO'],
                'meta_images' => ['type' => 'images', 'label' => 'Ảnh SEO'],
            ]
        ];

        $data['text'] = [
            'label' => 'Cấu hình Trang Liên Hệ',
            'description' => '',
            'value' => [
                '12' => ['type' => 'textarea', 'label' => 'Mô tả Contact'],
            ]
        ];



        $data['social'] = [
            'label' => 'Cấu hình Mạng xã hội dành cho trang chủ',
            'description' => 'Cài đặt đầy đủ thông tin về Mạng xã hội của trang chủ website. Bao gồm tiêu đề Mạng xã hội, Từ Khóa SEO, Mô Tả SEO, Meta images',
            'value' => [
                'facebook' => ['type' => 'text', 'label' => 'Facebook'],
                'google' => ['type' => 'text', 'label' => 'Google'],
                'tiktok' => ['type' => 'text', 'label' => 'Tiktok'],
                'twitter' => ['type' => 'text', 'label' => 'Twitter'],
                'messenger' => ['type' => 'text', 'label' => 'Messenger'],
                'zalo' => ['type' => 'text', 'label' => 'Zalo'],
                'youtube' => ['type' => 'text', 'label' => 'Youtube'],
                'instagram' => ['type' => 'text', 'label' => 'Instagram'],
                'lazada' => ['type' => 'text', 'label' => 'Lazada'],
                'shopee' => ['type' => 'text', 'label' => 'Shopee'],
            ]
        ];

       
        $data['script'] = [
            'label' => 'Cấu hình script',
            'description' => '',
            'value' => [
                '1' => ['type' => 'textarea', 'label' => 'Script Head'],
                '2' => ['type' => 'textarea', 'label' => 'Script Body'],
            ]
        ];


       
       
        return $data;
    }
	
}
