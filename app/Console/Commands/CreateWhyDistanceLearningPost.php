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

class CreateWhyDistanceLearningPost extends Command
{
    protected $signature = 'post:create-why-distance-learning';
    protected $description = 'Tạo chuyên mục và 5 bài viết "Vì Sao Nên Học Hệ Từ Xa?"';

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
                ->where('post_catalogue_language.canonical', 'vi-sao-nen-hoc-he-tu-xa')
                ->select('post_catalogues.id')
                ->first();

            if ($existingCatalogue) {
                $this->warn('Chuyên mục "Vì Sao Nên Học Hệ Từ Xa?" đã tồn tại với ID: ' . $existingCatalogue->id);
                $this->info('Bỏ qua việc tạo mới...');
                DB::rollBack();
                return 0;
            }

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
                    'name' => 'Vì Sao Nên Học Hệ Từ Xa?',
                    'canonical' => 'vi-sao-nen-hoc-he-tu-xa',
                    'description' => 'Đừng để thiếu tấm bằng Đại Học làm ảnh hưởng đến những dự định ước mơ của bạn.',
                    'content' => '',
                    'meta_title' => 'Vì Sao Nên Học Hệ Từ Xa?',
                    'meta_keyword' => 'học từ xa, đào tạo từ xa, đại học từ xa',
                    'meta_description' => 'Đừng để thiếu tấm bằng Đại Học làm ảnh hưởng đến những dự định ước mơ của bạn.'
                ];

                $postCatalogue->languages()->attach($languageId, $catalogueLanguageData);

                // Tạo router cho catalogue
                $routerData = [
                    'canonical' => 'vi-sao-nen-hoc-he-tu-xa',
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

                // Tạo 5 Posts
                $posts = [
                    [
                        'name' => 'Bạn chỉ có bằng Cao đẳng và không thể xin được công việc yêu thích?',
                        'canonical' => 'ban-chi-co-bang-cao-dang-va-khong-the-xin-duoc-cong-viec-yeu-thich',
                        'description' => 'Bạn chỉ có bằng Cao đẳng và không thể xin được công việc yêu thích?',
                        'icon' => 'icon-house.png'
                    ],
                    [
                        'name' => 'Bạn khao khát tìm việc tốt hơn nhưng cảm thấy bế tắc, không biết bắt đầu từ đâu?',
                        'canonical' => 'ban-khao-khat-tim-viec-tot-hon-nhung-cam-thay-be-tac-khong-biet-bat-dau-tu-dau',
                        'description' => 'Bạn khao khát tìm việc tốt hơn nhưng cảm thấy bế tắc, không biết bắt đầu từ đâu?',
                        'icon' => 'icon-person.png'
                    ],
                    [
                        'name' => 'Bạn mong muốn làm việc tại các doanh nghiệp lớn với cơ hội thăng tiến, mức đãi ngộ cao?',
                        'canonical' => 'ban-mong-muon-lam-viec-tai-cac-doanh-nghiep-lon-voi-co-hoi-thang-tien-muc-dai-ngo-cao',
                        'description' => 'Bạn mong muốn làm việc tại các doanh nghiệp lớn với cơ hội thăng tiến, mức đãi ngộ cao?',
                        'icon' => 'icon-group.png'
                    ],
                    [
                        'name' => 'Bạn muốn nâng cao chuyên môn và phát triển bản thân trong môi trường đào tạo uy tín, chất lượng?',
                        'canonical' => 'ban-muon-nang-cao-chuyen-mon-va-phat-trien-ban-than-trong-moi-truong-dao-tao-uy-tin-chat-luong',
                        'description' => 'Bạn muốn nâng cao chuyên môn và phát triển bản thân trong môi trường đào tạo uy tín, chất lượng?',
                        'icon' => 'icon-checklist.png'
                    ],
                    [
                        'name' => 'Bạn muốn học Đại học nhưng công việc bận rộn khiến bạn không thể sắp xếp thời gian đến trường?',
                        'canonical' => 'ban-muon-hoc-dai-hoc-nhung-cong-viec-ban-ron-khien-ban-khong-the-sap-xep-thoi-gian-den-truong',
                        'description' => 'Bạn muốn học Đại học nhưng công việc bận rộn khiến bạn không thể sắp xếp thời gian đến trường?',
                        'icon' => 'icon-play.png'
                    ]
                ];

                foreach ($posts as $index => $postData) {
                    $this->info('Đang tạo Post: ' . $postData['name']);
                    
                    $post = $this->postRepository->create([
                        'publish' => 2,
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
                            'meta_keyword' => 'học từ xa, ' . strtolower($postData['name']),
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
            $this->info('✓ Hoàn thành! Đã tạo chuyên mục và 5 bài viết thành công.');
            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Lỗi: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }
    }
}

