<?php
namespace App\Classes;

class Introduce{

    public function config(){
        $data['block_1'] = [
            'label' => 'Khối 1: Giới thiệu',
            'description' => 'Text trái, ảnh phải (nếu không có ảnh sẽ hiển thị khung vuông demo)',
            'value' => [
                'title' => ['type' => 'text', 'label' => 'Tiêu đề khối'],
                'description' => ['type' => 'editor', 'label' => 'Nội dung giới thiệu'],
                'image' => ['type' => 'images', 'label' => 'Hình ảnh'],
            ]
        ];
        $data['block_2'] = [
            'label' => 'Khối 2: Tầm nhìn',
            'description' => 'Cài đặt đầy đủ thông tin khối dưới đây',
            'value' => [
                'title' => ['type' => 'text', 'label' => 'Tiêu đề khối'],
                'description' => ['type' => 'editor', 'label' => 'Nội dung tầm nhìn'],
            ]
        ];
        $data['block_3'] = [
            'label' => 'Khối 3: Sứ Mệnh',
            'description' => 'Cài đặt đầy đủ thông tin khối dưới đây',
            'value' => [
                'title' => ['type' => 'text', 'label' => 'Tiêu đề khối'],
                'description' => ['type' => 'editor', 'label' => 'Nội dung sứ mệnh'],
            ]
        ];
        $data['block_4'] = [
            'label' => 'Khối 4: Lịch sử hình thành',
            'description' => 'Cài đặt đầy đủ thông tin khối dưới đây',
            'value' => [
                'title' => ['type' => 'text', 'label' => 'Tiêu đề khối'],
                'description' => ['type' => 'editor', 'label' => 'Nội dung lịch sử hình thành'],
            ]
        ];
        $data['block_5'] = [
            'label' => 'Khối 5: Thông tin liên hệ',
            'description' => 'Cài đặt đầy đủ thông tin khối dưới đây',
            'value' => [
                'title' => ['type' => 'text', 'label' => 'Tiêu đề khối'],
                'description' => ['type' => 'editor', 'label' => 'Nội dung thông tin liên hệ'],
            ]
        ];
        return $data;
    }
	
}
