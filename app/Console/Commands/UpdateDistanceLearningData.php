<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\PostCatalogueRepository;
use App\Repositories\PostRepository;
use Illuminate\Support\Facades\DB;

class UpdateDistanceLearningData extends Command
{
    protected $signature = 'post:update-distance-learning';
    protected $description = 'Cập nhật lại dữ liệu cho "Hệ đào tạo từ xa là gì"';

    protected $postCatalogueRepository;
    protected $postRepository;

    public function __construct(
        PostCatalogueRepository $postCatalogueRepository,
        PostRepository $postRepository
    ) {
        parent::__construct();
        $this->postCatalogueRepository = $postCatalogueRepository;
        $this->postRepository = $postRepository;
    }

    public function handle()
    {
        $this->info('Bắt đầu cập nhật dữ liệu...');

        $languageId = 1;

        DB::beginTransaction();
        try {
            // Tìm PostCatalogue
            $postCatalogue = DB::table('post_catalogues')
                ->join('post_catalogue_language as pcl', 'pcl.post_catalogue_id', '=', 'post_catalogues.id')
                ->where('pcl.language_id', $languageId)
                ->where('pcl.canonical', 'he-dao-tao-tu-xa-la-gi')
                ->select('post_catalogues.id')
                ->first();

            if (!$postCatalogue) {
                $this->error('Không tìm thấy PostCatalogue với canonical "he-dao-tao-tu-xa-la-gi"');
                $this->info('Vui lòng chạy lệnh: php artisan post:create-distance-learning trước');
                DB::rollBack();
                return 1;
            }

            $postCatalogueId = $postCatalogue->id;
            $this->info("Tìm thấy PostCatalogue ID: {$postCatalogueId}");

            // Cập nhật description của PostCatalogue (chỉ chứa banner)
            DB::table('post_catalogue_language')
                ->where('post_catalogue_id', $postCatalogueId)
                ->where('language_id', $languageId)
                ->update([
                    'description' => '<div class="distance-learning-banner">
                        <p><strong>Ngồi nhà vẫn có bằng Đại học</strong></p>
                        <p>Giải pháp học linh hoạt – Cơ hội nâng cao trình độ học vấn - sự nghiệp vươn xa, lương thưởng x3</p>
                    </div>'
                ]);

            $this->info('✓ Đã cập nhật description của PostCatalogue (banner)');

            // Tìm Post trong catalogue
            $post = DB::table('posts')
                ->join('post_catalogue_post', 'posts.id', '=', 'post_catalogue_post.post_id')
                ->where('post_catalogue_post.post_catalogue_id', $postCatalogueId)
                ->select('posts.id')
                ->first();

            if ($post) {
                // Cập nhật content của Post (mô tả + 4 features)
                DB::table('post_language')
                    ->where('post_id', $post->id)
                    ->where('language_id', $languageId)
                    ->update([
                        'content' => '<p>Hệ đào tạo từ xa là phương pháp giáo dục hiện đại được Bộ Giáo dục và Đào tạo công nhận, cho phép học viên học tập trực tuyến, thi tập trung và nhận bằng cấp tương đương mà không cần ghi rõ hình thức đào tạo.</p>
                        <p>Học viên có thể tham gia các lớp học trực tuyến, sử dụng công nghệ, internet và các thiết bị điện tử để tương tác, học tập qua các bài giảng số và nhận được sự hướng dẫn, hỗ trợ thông qua hệ thống LMS của từng trường đại học. Hình thức học tập linh hoạt về thời gian và địa điểm này phù hợp với những người bận rộn, đang đi làm hoặc ở xa trường học.</p>
                        <p class="feature-item">Không thi đầu vào</p>
                        <p class="feature-item">Linh hoạt thời gian, địa điểm</p>
                        <p class="feature-item">Bằng được Bộ GD&ĐT công nhận</p>
                        <p class="feature-item">Tiết kiệm tối đa chi phí</p>'
                    ]);

                $this->info('✓ Đã cập nhật content của Post (mô tả + 4 features)');
            } else {
                $this->warn('Không tìm thấy Post trong catalogue');
            }

            DB::commit();
            $this->info('');
            $this->info('✓ Hoàn thành! Đã cập nhật dữ liệu thành công.');
            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Lỗi: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }
    }
}

