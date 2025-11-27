<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\PostCatalogueRepository;
use App\Repositories\PostRepository;
use App\Repositories\RouterRepository;
use App\Services\PostCatalogueService;
use App\Services\PostService;
use App\Models\Language;
use App\Classes\Nestedsetbie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class CreateDistanceLearningPost extends Command
{
    protected $signature = 'post:create-distance-learning';
    protected $description = 'Tạo chuyên mục và bài viết "Hệ đào tạo từ xa là gì"';

    protected $postCatalogueRepository;
    protected $postRepository;
    protected $routerRepository;
    protected $postCatalogueService;
    protected $postService;

    public function __construct(
        PostCatalogueRepository $postCatalogueRepository,
        PostRepository $postRepository,
        RouterRepository $routerRepository,
        PostCatalogueService $postCatalogueService,
        PostService $postService
    ) {
        parent::__construct();
        $this->postCatalogueRepository = $postCatalogueRepository;
        $this->postRepository = $postRepository;
        $this->routerRepository = $routerRepository;
        $this->postCatalogueService = $postCatalogueService;
        $this->postService = $postService;
    }

    public function handle()
    {
        $this->info('Bắt đầu tạo chuyên mục và bài viết...');

        $languageId = 1; // Language ID mặc định

        DB::beginTransaction();
        try {
            // Kiểm tra xem đã tồn tại chưa
            $existingCatalogue = DB::table('post_catalogues')
                ->join('post_catalogue_language', 'post_catalogues.id', '=', 'post_catalogue_language.post_catalogue_id')
                ->where('post_catalogue_language.language_id', $languageId)
                ->where('post_catalogue_language.canonical', 'he-dao-tao-tu-xa-la-gi')
                ->select('post_catalogues.id')
                ->first();

            if ($existingCatalogue) {
                $this->warn('Chuyên mục "Hệ đào tạo từ xa là gì" đã tồn tại với ID: ' . $existingCatalogue->id);
                $this->info('Bỏ qua việc tạo mới...');
                DB::rollBack();
                return 0;
            }

            // Tạo PostCatalogue
            $this->info('Đang tạo PostCatalogue...');
            $catalogueData = [
                'parent_id' => 0,
                'publish' => 1,
                'follow' => 1,
                'image' => '',
                'album' => '',
                'short_name' => '',
                'user_id' => Auth::id() ?: 1
            ];

            $postCatalogue = $this->postCatalogueRepository->create($catalogueData);

            if ($postCatalogue->id > 0) {
                // Tạo language pivot cho catalogue
                // Description của PostCatalogue chỉ chứa banner
                $catalogueLanguageData = [
                    'post_catalogue_id' => $postCatalogue->id,
                    'language_id' => $languageId,
                    'name' => 'Hệ đào tạo từ xa là gì',
                    'canonical' => 'he-dao-tao-tu-xa-la-gi',
                    'description' => '<div class="distance-learning-banner">
                        <p><strong>Ngồi nhà vẫn có bằng Đại học</strong></p>
                        <p>Giải pháp học linh hoạt – Cơ hội nâng cao trình độ học vấn - sự nghiệp vươn xa, lương thưởng x3</p>
                    </div>',
                    'content' => '',
                    'meta_title' => 'Hệ đào tạo từ xa là gì',
                    'meta_keyword' => 'đào tạo từ xa, học từ xa, đại học từ xa',
                    'meta_description' => 'Hệ đào tạo từ xa là phương pháp giáo dục hiện đại được Bộ GD&ĐT công nhận'
                ];

                $postCatalogue->languages()->attach($languageId, $catalogueLanguageData);

                // Tạo router cho catalogue
                $routerData = [
                    'canonical' => 'he-dao-tao-tu-xa-la-gi',
                    'module_id' => $postCatalogue->id,
                    'language_id' => $languageId,
                    'controllers' => 'App\Http\Controllers\Frontend\PostCatalogueController',
                ];
                // Kiểm tra router đã tồn tại chưa
                $existingRouter = $this->routerRepository->findByCondition([
                    ['canonical', '=', $routerData['canonical']],
                    ['language_id', '=', $languageId]
                ], true);
                if (!$existingRouter || $existingRouter->isEmpty()) {
                    $this->routerRepository->create($routerData);
                }

                // Chạy nestedset
                $nestedset = new Nestedsetbie([
                    'table' => 'post_catalogues',
                    'foreignkey' => 'post_catalogue_id',
                    'language_id' => $languageId,
                ]);
                $nestedset->Get('level ASC, order ASC');
                $nestedset->Recursive(0, $nestedset->Set());
                $nestedset->Action();

                $this->info('✓ Đã tạo PostCatalogue với ID: ' . $postCatalogue->id);

                // Tạo Post
                $this->info('Đang tạo Post...');
                $postData = [
                    'publish' => 1,
                    'follow' => 1,
                    'image' => '',
                    'album' => '',
                    'post_catalogue_id' => $postCatalogue->id,
                    'video' => '',
                    'template' => '',
                    'status_menu' => 0,
                    'short_name' => '',
                    'user_id' => Auth::id() ?: 1
                ];

                $post = $this->postRepository->create($postData);

                if ($post->id > 0) {
                    // Tạo language pivot cho post
                    // Content của Post chứa mô tả + 4 nút features
                    $postLanguageData = [
                        'post_id' => $post->id,
                        'language_id' => $languageId,
                        'name' => 'Hệ đào tạo từ xa là gì',
                        'canonical' => 'he-dao-tao-tu-xa-la-gi',
                        'description' => 'Hệ đào tạo từ xa là phương pháp giáo dục hiện đại được Bộ Giáo dục và Đào tạo công nhận, cho phép học viên học tập trực tuyến, thi tập trung và nhận bằng cấp tương đương mà không cần ghi rõ hình thức đào tạo.',
                        'content' => '<p>Hệ đào tạo từ xa là phương pháp giáo dục hiện đại được Bộ Giáo dục và Đào tạo công nhận, cho phép học viên học tập trực tuyến, thi tập trung và nhận bằng cấp tương đương mà không cần ghi rõ hình thức đào tạo.</p>
                        <p>Học viên có thể tham gia các lớp học trực tuyến, sử dụng công nghệ, internet và các thiết bị điện tử để tương tác, học tập qua các bài giảng số và nhận được sự hướng dẫn, hỗ trợ thông qua hệ thống LMS của từng trường đại học. Hình thức học tập linh hoạt về thời gian và địa điểm này phù hợp với những người bận rộn, đang đi làm hoặc ở xa trường học.</p>
                        <p class="feature-item">Không thi đầu vào</p>
                        <p class="feature-item">Linh hoạt thời gian, địa điểm</p>
                        <p class="feature-item">Bằng được Bộ GD&ĐT công nhận</p>
                        <p class="feature-item">Tiết kiệm tối đa chi phí</p>',
                        'meta_title' => 'Hệ đào tạo từ xa là gì',
                        'meta_keyword' => 'đào tạo từ xa, học từ xa, đại học từ xa',
                        'meta_description' => 'Hệ đào tạo từ xa là phương pháp giáo dục hiện đại được Bộ GD&ĐT công nhận'
                    ];

                    $post->languages()->attach($languageId, $postLanguageData);

                    // Sync với catalogue
                    $post->post_catalogues()->sync([$postCatalogue->id]);

                    // Tạo router cho post (sử dụng canonical khác để tránh trùng)
                    $postRouterData = [
                        'canonical' => 'he-dao-tao-tu-xa-la-gi-bai-viet',
                        'module_id' => $post->id,
                        'language_id' => $languageId,
                        'controllers' => 'App\Http\Controllers\Frontend\PostController',
                    ];
                    // Kiểm tra router đã tồn tại chưa
                    $existingRouter = $this->routerRepository->findByCondition([
                        ['canonical', '=', $postRouterData['canonical']],
                        ['language_id', '=', $languageId]
                    ], true);
                    if (!$existingRouter || $existingRouter->isEmpty()) {
                        $this->routerRepository->create($postRouterData);
                    }

                    $this->info('✓ Đã tạo Post với ID: ' . $post->id);
                }
            }

            DB::commit();
            $this->info('');
            $this->info('✓ Hoàn thành! Đã tạo chuyên mục và bài viết thành công.');
            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Lỗi: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }
    }
}

