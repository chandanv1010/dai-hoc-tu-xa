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

class CreateNewsOutstanding extends Command
{
    protected $signature = 'post:create-news-outstanding';
    protected $description = 'Tạo chuyên mục và bài viết "TIN TỨC NỔI BẬT"';

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
        $this->info('Bắt đầu tạo chuyên mục và bài viết "TIN TỨC NỔI BẬT"...');

        $languageId = 1; // Language ID mặc định

        DB::beginTransaction();
        try {
            // Kiểm tra xem đã tồn tại chưa
            $existingCatalogue = DB::table('post_catalogues')
                ->join('post_catalogue_language', 'post_catalogues.id', '=', 'post_catalogue_language.post_catalogue_id')
                ->where('post_catalogue_language.language_id', $languageId)
                ->where('post_catalogue_language.canonical', 'tin-tuc-noi-bat')
                ->select('post_catalogues.id')
                ->first();

            if ($existingCatalogue) {
                $this->warn('Chuyên mục "TIN TỨC NỔI BẬT" đã tồn tại với ID: ' . $existingCatalogue->id);
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
                // Tạo PostCatalogue Language
                $catalogueLanguageData = [
                    'name' => 'TIN TỨC NỔI BẬT',
                    'canonical' => 'tin-tuc-noi-bat',
                    'description' => 'Các tin tức nổi bật về đào tạo từ xa, giáo dục và các chương trình học tập',
                    'content' => '',
                    'meta_title' => 'Tin tức nổi bật',
                    'meta_keyword' => 'tin tức, đào tạo từ xa, giáo dục',
                    'meta_description' => 'Các tin tức nổi bật về đào tạo từ xa và giáo dục'
                ];

                $postCatalogue->languages()->attach($languageId, $catalogueLanguageData);

                // Tạo Router
                $routerData = [
                    'canonical' => 'tin-tuc-noi-bat',
                    'module' => 'post_catalogue',
                    'module_id' => $postCatalogue->id,
                    'language_id' => $languageId
                ];

                $existingRouter = $this->routerRepository->findByCondition([
                    ['canonical', '=', 'tin-tuc-noi-bat'],
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

                // Tạo các Posts demo
                $posts = [
                    [
                        'name' => 'Chương trình đào tạo từ xa mở rộng thêm 10 ngành mới năm 2024',
                        'canonical' => 'chuong-trinh-dao-tao-tu-xa-mo-rong-them-10-nganh-moi-nam-2024',
                        'description' => 'Năm 2024, hệ thống đào tạo từ xa tiếp tục mở rộng với 10 ngành học mới, đáp ứng nhu cầu học tập của người đi làm.',
                        'content' => '<p>Năm 2024 đánh dấu một bước tiến quan trọng trong việc mở rộng hệ thống đào tạo từ xa. Với việc bổ sung 10 ngành học mới, chương trình đào tạo từ xa ngày càng đa dạng và phù hợp với nhu cầu thực tế của người học.</p><p>Các ngành mới được bổ sung bao gồm các lĩnh vực công nghệ, kinh tế, và xã hội, mang đến nhiều cơ hội học tập cho người đi làm.</p>',
                        'image' => ''
                    ],
                    [
                        'name' => 'Bộ Giáo dục công nhận giá trị bằng đại học từ xa',
                        'canonical' => 'bo-giao-duc-cong-nhan-gia-tri-bang-dai-hoc-tu-xa',
                        'description' => 'Bộ Giáo dục và Đào tạo chính thức công nhận giá trị pháp lý của bằng đại học từ xa, tương đương với bằng chính quy.',
                        'content' => '<p>Bộ Giáo dục và Đào tạo đã chính thức công nhận giá trị pháp lý của bằng đại học từ xa. Theo quy định mới, bằng đại học từ xa có giá trị tương đương với bằng chính quy, được công nhận trên toàn quốc.</p><p>Đây là tin vui lớn cho hàng nghìn học viên đang theo học chương trình đào tạo từ xa, mở ra nhiều cơ hội nghề nghiệp mới.</p>',
                        'image' => ''
                    ],
                    [
                        'name' => 'Học phí đào tạo từ xa giảm 20% cho học viên mới',
                        'canonical' => 'hoc-phi-dao-tao-tu-xa-giam-20-cho-hoc-vien-moi',
                        'description' => 'Chương trình ưu đãi học phí đặc biệt dành cho học viên đăng ký mới trong quý đầu năm 2024.',
                        'content' => '<p>Nhằm khuyến khích và hỗ trợ người đi làm tiếp cận với chương trình đào tạo từ xa, hệ thống triển khai chương trình ưu đãi học phí giảm 20% cho tất cả học viên đăng ký mới trong quý đầu năm 2024.</p><p>Chương trình áp dụng cho tất cả các ngành học và không giới hạn số lượng học viên.</p>',
                        'image' => ''
                    ],
                    [
                        'name' => 'Hơn 50.000 học viên tốt nghiệp đại học từ xa năm 2023',
                        'canonical' => 'hon-50000-hoc-vien-tot-nghiep-dai-hoc-tu-xa-nam-2023',
                        'description' => 'Năm 2023 ghi nhận số lượng học viên tốt nghiệp đại học từ xa kỷ lục với hơn 50.000 người.',
                        'content' => '<p>Năm 2023 là một năm thành công của hệ thống đào tạo từ xa với hơn 50.000 học viên tốt nghiệp. Con số này cho thấy sự phát triển mạnh mẽ và sự tin tưởng của người học đối với chương trình đào tạo từ xa.</p><p>Đa số học viên tốt nghiệp đều có việc làm ổn định và được đánh giá cao về năng lực chuyên môn.</p>',
                        'image' => ''
                    ],
                    [
                        'name' => 'Công nghệ AI hỗ trợ học tập trong đào tạo từ xa',
                        'canonical' => 'cong-nghe-ai-ho-tro-hoc-tap-trong-dao-tao-tu-xa',
                        'description' => 'Ứng dụng công nghệ AI vào hệ thống đào tạo từ xa, mang đến trải nghiệm học tập cá nhân hóa và hiệu quả hơn.',
                        'content' => '<p>Công nghệ trí tuệ nhân tạo (AI) đang được tích hợp vào hệ thống đào tạo từ xa, mang đến nhiều tiện ích cho người học. AI giúp cá nhân hóa lộ trình học tập, đưa ra gợi ý phù hợp với từng học viên.</p><p>Hệ thống cũng sử dụng AI để đánh giá và phản hồi tự động, giúp học viên cải thiện kết quả học tập một cách nhanh chóng.</p>',
                        'image' => ''
                    ]
                ];

                $this->info('Đang tạo các bài viết demo...');
                foreach ($posts as $index => $postData) {
                    $post = $this->postRepository->create([
                        'publish' => 2,
                        'follow' => 1,
                        'image' => $postData['image'],
                        'album' => '',
                        'post_catalogue_id' => $postCatalogue->id,
                        'video' => '',
                        'template' => '',
                        'status_menu' => 0,
                        'short_name' => '',
                        'order' => $index + 1,
                        'user_id' => Auth::id() ?: 1
                    ]);

                    if ($post->id > 0) {
                        // Tạo Post Language
                        $post->languages()->attach($languageId, [
                            'name' => $postData['name'],
                            'canonical' => $postData['canonical'],
                            'description' => $postData['description'],
                            'content' => $postData['content'],
                            'meta_title' => $postData['name'],
                            'meta_keyword' => 'tin tức, đào tạo từ xa',
                            'meta_description' => $postData['description']
                        ]);

                        // Tạo Router cho Post
                        $postRouterData = [
                            'canonical' => $postData['canonical'],
                            'module' => 'post',
                            'module_id' => $post->id,
                            'language_id' => $languageId
                        ];

                        $existingPostRouter = $this->routerRepository->findByCondition([
                            ['canonical', '=', $postData['canonical']],
                            ['language_id', '=', $languageId]
                        ], true);
                        if (!$existingPostRouter || $existingPostRouter->isEmpty()) {
                            $this->routerRepository->create($postRouterData);
                        }

                        // Gắn Post vào PostCatalogue
                        $post->post_catalogues()->sync([$postCatalogue->id]);

                        $this->info('  ✓ Đã tạo Post: ' . $postData['name']);
                    }
                }

                DB::commit();
                $this->info('');
                $this->info('✓ Hoàn thành! Đã tạo PostCatalogue và ' . count($posts) . ' bài viết demo.');
                return 0;
            }

            DB::rollBack();
            $this->error('Không thể tạo PostCatalogue');
            return 1;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Lỗi: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }
    }
}
