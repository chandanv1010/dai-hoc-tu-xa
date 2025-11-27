<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\PostCatalogueRepository;
use App\Repositories\PostRepository;
use App\Repositories\RouterRepository;
use App\Models\Language;
use App\Classes\Nestedsetbie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CreateStudentFeedback extends Command
{
    protected $signature = 'post:create-student-feedback';
    protected $description = 'Tạo chuyên mục và bài viết "Học viên nói gì về Hệ Từ Xa"';

    protected $postCatalogueRepository;
    protected $postRepository;
    protected $routerRepository;

    public function __construct(
        PostCatalogueRepository $postCatalogueRepository,
        PostRepository $postRepository,
        RouterRepository $routerRepository
    ) {
        parent::__construct();
        $this->postCatalogueRepository = $postCatalogueRepository;
        $this->postRepository = $postRepository;
        $this->routerRepository = $routerRepository;
    }

    public function handle()
    {
        $this->info('Bắt đầu tạo chuyên mục và bài viết feedback...');

        $languageId = 1; // Language ID mặc định

        DB::beginTransaction();
        try {
            // Kiểm tra xem đã tồn tại chưa
            $existingCatalogue = DB::table('post_catalogues')
                ->join('post_catalogue_language', 'post_catalogues.id', '=', 'post_catalogue_language.post_catalogue_id')
                ->where('post_catalogue_language.language_id', $languageId)
                ->where('post_catalogue_language.canonical', 'hoc-vien-noi-gi-ve-he-tu-xa')
                ->select('post_catalogues.id')
                ->first();

            $postCatalogueId = null;
            if ($existingCatalogue) {
                $this->warn('Chuyên mục "Học viên nói gì về Hệ Từ Xa" đã tồn tại với ID: ' . $existingCatalogue->id);
                $postCatalogueId = $existingCatalogue->id;
            } else {
                // Tạo PostCatalogue
                $this->info('Đang tạo PostCatalogue...');
                $catalogueData = [
                    'parent_id' => 0,
                    'publish' => 2,
                    'follow' => 1,
                    'image' => '',
                    'album' => '',
                    'short_name' => '',
                    'user_id' => Auth::id() ?: 1
                ];

                $postCatalogue = $this->postCatalogueRepository->create($catalogueData);

                if ($postCatalogue->id > 0) {
                    $catalogueLanguageData = [
                        'post_catalogue_id' => $postCatalogue->id,
                        'language_id' => $languageId,
                        'name' => 'Học viên nói gì về Hệ Từ Xa',
                        'canonical' => 'hoc-vien-noi-gi-ve-he-tu-xa',
                        'description' => 'Cảm nhận từ những học viên đã chọn Học Đại học Từ Xa Hàng nghìn học viên trên toàn quốc đã và đang theo học chương trình Đại học Từ xa. Cùng lắng nghe những chia sẻ thật từ họ - những người đã chọn học linh hoạt và đạt được tấm bằng mơ ước.',
                        'content' => '',
                        'meta_title' => 'Học viên nói gì về Hệ Từ Xa',
                        'meta_keyword' => 'học viên, feedback, đánh giá, đại học từ xa',
                        'meta_description' => 'Cảm nhận từ những học viên đã chọn Học Đại học Từ Xa'
                    ];

                    $postCatalogue->languages()->attach($languageId, $catalogueLanguageData);

                    // Tạo router cho catalogue
                    $routerData = [
                        'canonical' => 'hoc-vien-noi-gi-ve-he-tu-xa',
                        'module_id' => $postCatalogue->id,
                        'language_id' => $languageId,
                        'controllers' => 'App\Http\Controllers\Frontend\PostCatalogueController',
                    ];
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
                    $postCatalogueId = $postCatalogue->id;
                }
            }

            // Tạo Posts demo
            $this->info('Đang tạo Posts demo...');
            $postsData = [
                [
                    'name' => 'Nguyễn Văn An',
                    'major' => 'Quản trị kinh doanh',
                    'class' => 'K2023',
                    'rating' => 5,
                    'content' => 'Tôi vừa đi làm vừa học được nhờ hệ từ xa. Giảng viên nhiệt tình, học liệu đầy đủ, thời gian linh hoạt. Giờ tôi đã có bằng đại học và được thăng chức trong công ty. Cảm ơn chương trình đã tạo cơ hội cho tôi!',
                    'canonical' => 'nguyen-van-an-quan-tri-kinh-doanh-k2023'
                ],
                [
                    'name' => 'Trần Thị Hương',
                    'major' => 'Kế toán',
                    'class' => 'K2022',
                    'rating' => 5,
                    'content' => 'Ban đầu tôi còn lo lắng về chất lượng học online, nhưng thực tế vượt mong đợi. Bài giảng rõ ràng, có thể học lại nhiều lần. Tôi học buổi tối sau khi con ngủ, rất phù hợp với mẹ bỉm sữa như tôi.',
                    'canonical' => 'tran-thi-huong-ke-toan-k2022'
                ],
                [
                    'name' => 'Phạm Minh Tuấn',
                    'major' => 'Công nghệ thông tin',
                    'class' => 'K2023',
                    'rating' => 5,
                    'content' => 'Chương trình học cập nhật, thực tế. Tôi có thể áp dụng ngay kiến thức vào công việc. Chi phí thấp hơn nhiều so với hệ chính quy nhưng chất lượng không hề thua kém. Đây là lựa chọn tốt nhất cho người đi làm!',
                    'canonical' => 'pham-minh-tuan-cong-nghe-thong-tin-k2023'
                ],
                [
                    'name' => 'Lê Thị Mai',
                    'major' => 'Luật',
                    'class' => 'K2022',
                    'rating' => 5,
                    'content' => 'Học từ xa giúp tôi cân bằng được công việc và học tập. Tài liệu học tập phong phú, giảng viên hỗ trợ nhiệt tình. Tôi đã hoàn thành chương trình và đang làm việc tại một công ty luật lớn.',
                    'canonical' => 'le-thi-mai-luat-k2022'
                ],
                [
                    'name' => 'Hoàng Văn Đức',
                    'major' => 'Ngôn ngữ Anh',
                    'class' => 'K2023',
                    'rating' => 5,
                    'content' => 'Chất lượng đào tạo rất tốt, tương đương với hệ chính quy. Tôi có thể học mọi lúc mọi nơi, rất tiện lợi. Bằng cấp được công nhận, tôi đã sử dụng để xin việc thành công.',
                    'canonical' => 'hoang-van-duc-ngon-ngu-anh-k2023'
                ]
            ];

            $createdPosts = 0;
            foreach ($postsData as $index => $postData) {
                // Kiểm tra post đã tồn tại chưa
                $existingPost = DB::table('posts')
                    ->join('post_language', 'posts.id', '=', 'post_language.post_id')
                    ->where('post_language.language_id', $languageId)
                    ->where('post_language.canonical', $postData['canonical'])
                    ->select('posts.id')
                    ->first();

                if ($existingPost) {
                    $this->warn("  Post '{$postData['name']}' đã tồn tại, bỏ qua...");
                    continue;
                }

                // Tạo Post
                $post = $this->postRepository->create([
                    'publish' => 2,
                    'follow' => 1,
                    'image' => '',
                    'album' => '',
                    'post_catalogue_id' => $postCatalogueId,
                    'video' => '',
                    'template' => '',
                    'status_menu' => 0,
                    'short_name' => '',
                    'user_id' => Auth::id() ?: 1
                ]);

                if ($post->id > 0) {
                    // Tạo language pivot cho post
                    $postLanguageData = [
                        'post_id' => $post->id,
                        'language_id' => $languageId,
                        'name' => $postData['name'],
                        'canonical' => $postData['canonical'],
                        'description' => $postData['content'],
                        'content' => '<div class="student-feedback-item">
                            <div class="feedback-major">' . $postData['major'] . ' - ' . $postData['class'] . '</div>
                            <div class="feedback-rating">' . str_repeat('★', $postData['rating']) . '</div>
                            <div class="feedback-content">' . $postData['content'] . '</div>
                        </div>',
                        'meta_title' => $postData['name'] . ' - ' . $postData['major'],
                        'meta_keyword' => 'học viên, feedback, ' . $postData['major'],
                        'meta_description' => $postData['content']
                    ];

                    $post->languages()->attach($languageId, $postLanguageData);

                    // Sync với catalogue
                    $post->post_catalogues()->sync([$postCatalogueId]);

                    // Tạo router cho post
                    $postRouterData = [
                        'canonical' => $postData['canonical'],
                        'module_id' => $post->id,
                        'language_id' => $languageId,
                        'controllers' => 'App\Http\Controllers\Frontend\PostController',
                    ];
                    $existingRouter = $this->routerRepository->findByCondition([
                        ['canonical', '=', $postRouterData['canonical']],
                        ['language_id', '=', $languageId]
                    ], true);
                    if (!$existingRouter || $existingRouter->isEmpty()) {
                        $this->routerRepository->create($postRouterData);
                    }

                    $createdPosts++;
                    $this->info("  ✓ Đã tạo Post: {$postData['name']} (ID: {$post->id})");
                }
            }

            DB::commit();
            $this->info('');
            $this->info("✓ Hoàn thành! Đã tạo {$createdPosts} posts feedback.");
            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Lỗi: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }
    }
}
