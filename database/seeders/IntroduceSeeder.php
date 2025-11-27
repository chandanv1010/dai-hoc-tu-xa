<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Language;
use App\Models\User;

class IntroduceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Xóa tất cả dữ liệu trong bảng introduces
        DB::table('introduces')->truncate();

        // Lấy language_id mặc định (tiếng Việt)
        $language = Language::where('canonical', 'vn')->first();
        if (!$language) {
            $language = Language::first();
        }
        
        if (!$language) {
            $this->command->error('Không tìm thấy language. Vui lòng tạo language trước.');
            return;
        }

        // Lấy user_id đầu tiên hoặc tạo user mặc định
        $user = User::first();
        if (!$user) {
            $this->command->error('Không tìm thấy user. Vui lòng tạo user trước.');
            return;
        }

        $languageId = $language->id;
        $userId = $user->id;

        // Dữ liệu demo cho 6 khối
        $data = [
            // Khối 1: Giới thiệu
            [
                'language_id' => $languageId,
                'user_id' => $userId,
                'keyword' => 'block_1_title',
                'content' => 'Về Chúng Tôi',
            ],
            [
                'language_id' => $languageId,
                'user_id' => $userId,
                'keyword' => 'block_1_description',
                'content' => '<p>Chúng tôi là đơn vị tiên phong trong lĩnh vực đào tạo từ xa tại Việt Nam, với hơn 10 năm kinh nghiệm trong việc cung cấp các chương trình đào tạo chất lượng cao, linh hoạt và phù hợp với nhu cầu của người đi làm.</p><p>Chúng tôi cam kết mang đến cho học viên những trải nghiệm học tập tốt nhất, với đội ngũ giảng viên giàu kinh nghiệm và hệ thống học tập trực tuyến hiện đại.</p>',
            ],
            [
                'language_id' => $languageId,
                'user_id' => $userId,
                'keyword' => 'block_1_image',
                'content' => '',
            ],

            // Khối 2: Tầm nhìn
            [
                'language_id' => $languageId,
                'user_id' => $userId,
                'keyword' => 'block_2_title',
                'content' => 'Tầm Nhìn',
            ],
            [
                'language_id' => $languageId,
                'user_id' => $userId,
                'keyword' => 'block_2_description',
                'content' => '<p>Trở thành đơn vị đào tạo từ xa hàng đầu tại Việt Nam, được công nhận về chất lượng đào tạo và dịch vụ chăm sóc học viên.</p><p>Chúng tôi hướng tới việc xây dựng một cộng đồng học tập sôi động, nơi mọi người có thể phát triển kỹ năng và kiến thức một cách linh hoạt, không bị giới hạn bởi thời gian và không gian.</p>',
            ],

            // Khối 3: Sứ Mệnh
            [
                'language_id' => $languageId,
                'user_id' => $userId,
                'keyword' => 'block_3_title',
                'content' => 'Sứ Mệnh',
            ],
            [
                'language_id' => $languageId,
                'user_id' => $userId,
                'keyword' => 'block_3_description',
                'content' => '<p>Mang đến cơ hội học tập bình đẳng cho mọi người, đặc biệt là những người đi làm, người có hoàn cảnh khó khăn về thời gian và địa lý.</p><p>Chúng tôi cam kết cung cấp các chương trình đào tạo chất lượng cao, được công nhận bởi Bộ Giáo dục và Đào tạo, giúp học viên nâng cao trình độ và phát triển sự nghiệp.</p>',
            ],

            // Khối 4: Lịch sử hình thành
            [
                'language_id' => $languageId,
                'user_id' => $userId,
                'keyword' => 'block_4_title',
                'content' => 'Lịch Sử Hình Thành',
            ],
            [
                'language_id' => $languageId,
                'user_id' => $userId,
                'keyword' => 'block_4_description',
                'content' => '<p><strong>2015:</strong> Thành lập với mục tiêu mang giáo dục đến mọi người</p><p><strong>2017:</strong> Mở rộng chương trình đào tạo, hợp tác với các trường đại học hàng đầu</p><p><strong>2020:</strong> Phát triển hệ thống học tập trực tuyến hiện đại, phục vụ hàng nghìn học viên</p><p><strong>2023:</strong> Đạt được nhiều giải thưởng về chất lượng đào tạo và dịch vụ</p><p><strong>2025:</strong> Tiếp tục mở rộng và phát triển, hướng tới tương lai giáo dục số</p>',
            ],

            // Khối 5: Thông tin liên hệ
            [
                'language_id' => $languageId,
                'user_id' => $userId,
                'keyword' => 'block_5_title',
                'content' => 'Thông Tin Liên Hệ',
            ],
            [
                'language_id' => $languageId,
                'user_id' => $userId,
                'keyword' => 'block_5_description',
                'content' => '<p><strong>Địa chỉ:</strong> Số 123, Đường ABC, Quận XYZ, TP. Hà Nội</p><p><strong>Hotline:</strong> 1900 1234</p><p><strong>Email:</strong> info@daotaotuxa.vn</p><p><strong>Giờ làm việc:</strong> Thứ 2 - Thứ 6: 8:00 - 17:30 | Thứ 7: 8:00 - 12:00</p>',
            ],
        ];

        // Insert dữ liệu
        DB::table('introduces')->insert($data);

        $this->command->info('Đã tạo dữ liệu demo cho bảng introduces thành công!');
    }
}
