<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Language;
use App\Models\User;

class InsertPostSystemKeys extends Command
{
    protected $signature = 'system:insert-post-keys';
    protected $description = 'Chèn các key mới cho phần post detail vào bảng systems nếu chưa có';

    public function handle()
    {
        $this->info('Bắt đầu chèn các key mới vào bảng systems...');

        // Lấy user_id đầu tiên hoặc user_id = 1
        $userId = User::first()->id ?? 1;
        
        // Lấy tất cả các language
        $languages = Language::all();
        
        if ($languages->isEmpty()) {
            $this->error('Không tìm thấy language nào trong database');
            return 1;
        }

        // Định nghĩa các key cần chèn với giá trị mặc định
        $keys = [
            'post_contact_title' => 'LIÊN HỆ NGAY ĐỂ NHẬN TƯ VẤN NHANH NHẤT:',
            'post_contact_website' => '',
            'post_contact_fanpage' => '',
            'post_center_title' => 'Trung Tâm luyện thi eVSTEP bậc 4/6',
            'post_center_description' => '<p>eVSTEP là trung tâm luyện thi tiếng Anh với mục tiêu đào tạo <strong>HỌC THẬT – THI THẬT - KIẾN THỨC THẬT</strong>. Cam kết chất lượng đầu ra, bám sát theo khung năng lực ngoại ngữ 6 bậc dành cho Việt Nam (VSTEP).</p>',
        ];

        DB::beginTransaction();
        try {
            $insertedCount = 0;
            $skippedCount = 0;

            foreach ($languages as $language) {
                $languageId = $language->id;
                $this->info("Xử lý language_id: {$languageId} ({$language->name})");

                foreach ($keys as $keyword => $defaultValue) {
                    // Kiểm tra xem key đã tồn tại chưa
                    $existing = DB::table('systems')
                        ->where('keyword', $keyword)
                        ->where('language_id', $languageId)
                        ->first();

                    if ($existing) {
                        $this->line("  - Key '{$keyword}' đã tồn tại, bỏ qua");
                        $skippedCount++;
                        continue;
                    }

                    // Chèn key mới
                    DB::table('systems')->insert([
                        'language_id' => $languageId,
                        'user_id' => $userId,
                        'keyword' => $keyword,
                        'content' => $defaultValue,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $this->info("  ✓ Đã chèn key '{$keyword}'");
                    $insertedCount++;
                }
            }

            DB::commit();
            
            $this->info('');
            $this->info("✓ Hoàn thành!");
            $this->info("  - Đã chèn: {$insertedCount} key(s)");
            $this->info("  - Đã bỏ qua: {$skippedCount} key(s) (đã tồn tại)");
            
            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Lỗi: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }
    }
}

