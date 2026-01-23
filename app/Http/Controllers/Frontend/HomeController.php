<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use App\Repositories\SlideRepository;
use App\Repositories\SystemRepository;
use App\Repositories\LecturerRepository;
use App\Repositories\SchoolRepository;
use App\Repositories\MajorRepository;
use App\Repositories\MajorCatalogueRepository;
use App\Repositories\PostCatalogueRepository;
use App\Services\WidgetService;
use App\Services\SlideService;
use App\Enums\SlideEnum;
use Illuminate\Http\Request;
use App\Services\PostService;
use App\Models\Post;
use App\Models\Lecturer;

class HomeController extends FrontendController
{
    protected $language;
    protected $slideRepository;
    protected $lecturerRepository;
    protected $schoolRepository;
    protected $majorRepository;
    protected $majorCatalogueRepository;
    protected $postCatalogueRepository;
    protected $systemRepository;
    protected $widgetService;
    protected $slideService;
    protected $system;
    protected $postService;

    public function __construct(
        SlideRepository $slideRepository,
        LecturerRepository $lecturerRepository,
        SchoolRepository $schoolRepository,
        MajorRepository $majorRepository,
        MajorCatalogueRepository $majorCatalogueRepository,
        PostCatalogueRepository $postCatalogueRepository,
        WidgetService $widgetService,
        SlideService $slideService,
        SystemRepository $systemRepository,
        PostService $postService,
    ) {
        $this->slideRepository = $slideRepository;
        $this->lecturerRepository = $lecturerRepository;
        $this->schoolRepository = $schoolRepository;
        $this->majorRepository = $majorRepository;
        $this->majorCatalogueRepository = $majorCatalogueRepository;
        $this->postCatalogueRepository = $postCatalogueRepository;
        $this->widgetService = $widgetService;
        $this->slideService = $slideService;
        $this->systemRepository = $systemRepository;
        $this->postService = $postService;

        parent::__construct(
            $systemRepository,
        );
    }


    public function index()
    {
        $config = $this->config();

        $widgets = $this->widgetService->getWidget([
            ['keyword' => 'product-catalogue', 'object' => true],
            ['keyword' => 'best-selling-course', 'children' => true, 'object' => true, 'promotion' => true],
            ['keyword' => 'new-course-launch', 'object' => true, 'promotion' => true],
            ['keyword' => 'news', 'object' => true],
            ['keyword' => 'videos', 'object' => true],
            ['keyword' => 'vstep-intro', 'object' => true],
            ['keyword' => 'vstep-suitable', 'children' => true, 'object' => true],
            ['keyword' => 'vstep-why', 'children' => true, 'object' => true],
            ['keyword' => 'vstep-advantages', 'children' => true, 'object' => true],
            ['keyword' => 'vstep-cta'],
            ['keyword' => 'vstep-courses', 'children' => true, 'object' => true],
            ['keyword' => 'vstep-service', 'children' => true, 'object' => true],
            ['keyword' => 'distance-learning', 'object' => true],
            ['keyword' => 'training-program', 'children' => true, 'object' => true],
            ['keyword' => 'why-distance-learning', 'children' => true, 'object' => true],
            ['keyword' => 'value-we-bring', 'children' => true, 'object' => true],
            ['keyword' => 'schools-list'],
            ['keyword' => 'majors-list'],
            ['keyword' => 'student-feedback', 'object' => true],
        ], $this->language);

        // Lấy dữ liệu news-outstanding trực tiếp
        $newsOutstanding = $this->getNewsOutstanding();

        // Lấy danh sách schools
        $schools = $this->schoolRepository->getAllSchools($this->language, 0);
        
        // Lấy danh sách majors có is_home = 1
        $majors = $this->majorRepository->getHomeMajors($this->language, 6);

        // Lấy danh sách major_catalogues
        $majorCatalogues = $this->majorCatalogueRepository->getAllMajorCatalogues($this->language);

        $slides = $this->slideService->getSlide(
            [SlideEnum::MAIN, 'mobile-slide', SlideEnum::TECHSTAFF, SlideEnum::PARTNER],
            $this->language
        );

        $lecturers = $this->lecturerRepository->all();

        $system = $this->system;

        $seo = [
            'meta_title' => $this->system['seo_meta_title'],
            'meta_keyword' => $this->system['seo_meta_keyword'],
            'meta_description' => $this->system['seo_meta_description'],
            'meta_image' => $this->system['seo_meta_images'],
            'canonical' => config('app.url'),
            'follow' => 1, // Default là follow cho homepage
        ];

        $language = $this->language;

        $ishome = true;

        $template = 'frontend.homepage.home.index';

        return view($template, compact(
            'majorCatalogues',
            'config',
            'slides',
            'widgets',
            'newsOutstanding',
            'seo',
            'system',
            'language',
            'ishome',
            'lecturers',
            'schools',
            'majors'
        ));
    }

    private function getNewsOutstanding()
    {
        // Lấy thông tin post catalogue với ID = 18 (tin tức nổi bật)
        $postCatalogueId = 18;
        
        $postCatalogue = $this->postCatalogueRepository->getPostCatalogueById($postCatalogueId, $this->language);
        
        if (!$postCatalogue) {
            return null;
        }
        
        // Lấy các bài viết thuộc danh mục này (bao gồm cả danh mục con)
        // Sử dụng paginate để tận dụng logic whereRaw xử lý nested categories
        $request = new \Illuminate\Http\Request();
        $request->merge(['post_catalogue_id' => $postCatalogueId]);
        
        $paginatedPosts = $this->postService->paginate(
            $request,
            $this->language,
            $postCatalogue,
            1,
            ['path' => '']
        );
        
        // Lấy 3 bài viết đầu tiên và format lại dữ liệu
        $posts = collect($paginatedPosts->items())->take(3)->map(function($post) {
            return (object)[
                'id' => $post->id,
                'post_catalogue_id' => $post->post_catalogue_id,
                'image' => $post->image,
                'created_at' => $post->created_at,
                'name' => $post->name,
                'description' => $post->description,
                'canonical' => $post->canonical,
            ];
        });
        
        // Format dữ liệu để trả về
        return [
            'catalogue' => $postCatalogue,
            'posts' => $posts,
        ];
    }

    private function config()
    {
        return [
            'language' => $this->language,
            'css' => [
                'frontend/resources/plugins/OwlCarousel2-2.3.4/dist/assets/owl.carousel.min.css',
                'frontend/resources/plugins/OwlCarousel2-2.3.4/dist/assets/owl.theme.default.min.css',
                'frontend/resources/css/custom.css'
            ],
            'js' => [
                'frontend/resources/plugins/OwlCarousel2-2.3.4/dist/owl.carousel.min.js',
                'frontend/resources/library/js/carousel.js',
                'https://getuikit.com/v2/src/js/components/sticky.js'
            ]
        ];
    }

    public function ajaxProject(Request $request){
        $id = $request->id;
        $posts = Post::where('publish', 2)->with(['languages'])->where('post_catalogue_id', $id)->orderBy('order', 'desc')->get();
        $html = '';
        if($posts && count($posts)){
            $html .= '<div class="uk-grid uk-grid-medium">';

            foreach ($posts as $post) {
                $name = $post->languages->first()->pivot->name ?? '';
                $canonical = write_url($post->languages->first()->pivot->canonical ?? '');
                $image = thumb(image($post->image), 600, 400);

                $html .= '
                    <div class="uk-width-1-2 uk-width-small-1-2 uk-width-medium-1-3 mb20">
                        <div class="post-item">
                            <a href="' . $canonical . '" title="' . e($name) . '" class="image img-cover">
                                <img  src="' . $image . '" alt="' . e($name) . '">
                            </a>
                            <div class="info">
                                <h3 class="title"><a href="' . $canonical . '" title="' . e($name) . '">' . e($name) . '</a></h3>
                            </div>
                        </div>
                    </div>';
            }

            $html .= '</div>';
        }
        return response()->json(['html' => $html]);
    }



}