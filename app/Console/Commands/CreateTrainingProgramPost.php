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

class CreateTrainingProgramPost extends Command
{
    protected $signature = 'post:create-training-program';
    protected $description = 'Tạo chuyên mục và 4 bài viết "Chương trình Đào tạo dành cho Ai?"';

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
                ->where('post_catalogue_language.canonical', 'chuong-trinh-dao-tao-danh-cho-ai')
                ->select('post_catalogues.id')
                ->first();

            if ($existingCatalogue) {
                $this->warn('Chuyên mục "Chương trình Đào tạo dành cho Ai?" đã tồn tại với ID: ' . $existingCatalogue->id);
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
                $catalogueLanguageData = [
                    'post_catalogue_id' => $postCatalogue->id,
                    'language_id' => $languageId,
                    'name' => 'Chương trình Đào tạo dành cho Ai?',
                    'canonical' => 'chuong-trinh-dao-tao-danh-cho-ai',
                    'description' => 'Ai có thể học? Chương trình đào tạo từ xa mang đến cơ hội học tập và lấy bằng đại học cho tất cả mọi người một cách dễ dàng nhất. Chỉ cần có bằng THPT là đủ, không cần lo về việc thi tuyển. Hãy lựa chọn cho mình chương trình phù hợp nhất nhé!',
                    'content' => '',
                    'meta_title' => 'Chương trình Đào tạo dành cho Ai?',
                    'meta_keyword' => 'chương trình đào tạo, đào tạo từ xa, học đại học',
                    'meta_description' => 'Chương trình đào tạo từ xa dành cho tất cả mọi người, chỉ cần có bằng THPT là đủ'
                ];

                $postCatalogue->languages()->attach($languageId, $catalogueLanguageData);

                // Tạo router cho catalogue
                $routerData = [
                    'canonical' => 'chuong-trinh-dao-tao-danh-cho-ai',
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

                // Tạo 4 Posts
                $posts = [
                    [
                        'name' => 'Học Đại học Từ xa',
                        'canonical' => 'hoc-dai-hoc-tu-xa',
                        'description' => 'Người tốt nghiệp THPT, công việc và thu nhập chưa ổn định, mong muốn vừa đi làm vừa học đại học để có công việc tốt hơn',
                        'icon' => 'fa-graduation-cap'
                    ],
                    [
                        'name' => 'Văn bằng 2 Online',
                        'canonical' => 'van-bang-2-online',
                        'description' => 'Phù hợp với người đã có 1 bằng đại học, muốn mở rộng kiến thức ở một chuyên ngành khác để có nhiều cơ hội phát triển công việc và sự nghiệp',
                        'icon' => 'fa-star'
                    ],
                    [
                        'name' => 'Đại học trực tuyến cho người đi làm',
                        'canonical' => 'dai-hoc-truc-tuyen-cho-nguoi-di-lam',
                        'description' => 'Phù hợp với Nhân viên văn phòng, công chức, người bận rộn. Có bằng THPT trở lên muốn có tấm bằng Đại Học',
                        'icon' => 'fa-briefcase'
                    ],
                    [
                        'name' => 'Nâng bằng từ Trung cấp/Cao đẳng lên Đại học',
                        'canonical' => 'nang-bang-tu-trung-cap-cao-dang-len-dai-hoc',
                        'description' => 'Dành cho: Người đã có bằng Trung cấp hoặc Cao đẳng. Muốn học lên đại học nâng cao kiến thức hỗ trợ công việc, tăng cơ hội thăng tiến hoặc để nâng bậc/hệ số lương',
                        'icon' => 'fa-line-chart'
                    ]
                ];

                foreach ($posts as $index => $postData) {
                    $this->info('Đang tạo Post: ' . $postData['name']);
                    
                    $post = $this->postRepository->create([
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
                    ]);

                    if ($post->id > 0) {
                        $postLanguageData = [
                            'post_id' => $post->id,
                            'language_id' => $languageId,
                            'name' => $postData['name'],
                            'canonical' => $postData['canonical'],
                            'description' => $postData['description'],
                            'content' => '<p>Icon: ' . $postData['icon'] . '</p>',
                            'meta_title' => $postData['name'],
                            'meta_keyword' => 'đào tạo từ xa, ' . strtolower($postData['name']),
                            'meta_description' => $postData['description']
                        ];

                        $post->languages()->attach($languageId, $postLanguageData);

                        // Sync với catalogue
                        $post->post_catalogues()->sync([$postCatalogue->id]);

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

                        $this->info('✓ Đã tạo Post "' . $postData['name'] . '" với ID: ' . $post->id);
                    }
                }
            }

            DB::commit();
            $this->info('');
            $this->info('✓ Hoàn thành! Đã tạo chuyên mục và 4 bài viết thành công.');
            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Lỗi: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }
    }
}

