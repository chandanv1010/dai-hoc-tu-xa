<?php

namespace App\Http\Controllers\Frontend\Major;

use App\Http\Controllers\FrontendController;
use Illuminate\Http\Request;
use App\Repositories\MajorRepository;
use App\Repositories\MajorCatalogueRepository;
use App\Repositories\SchoolRepository;
use App\Repositories\SystemRepository;

class MajorCatalogueController extends FrontendController
{
    protected $language;
    protected $system;
    protected $majorRepository;
    protected $majorCatalogueRepository;
    protected $schoolRepository;
    protected $systemRepository;

    public function __construct(
        MajorRepository $majorRepository,
        MajorCatalogueRepository $majorCatalogueRepository,
        SchoolRepository $schoolRepository,
        SystemRepository $systemRepository,
    ) {
        $this->majorRepository = $majorRepository;
        $this->majorCatalogueRepository = $majorCatalogueRepository;
        $this->schoolRepository = $schoolRepository;
        $this->systemRepository = $systemRepository;
        parent::__construct();
    }

    public function index($id = null, Request $request = null, $page = null)
    {
        // Nếu $request là null, có thể đang được gọi từ RouterController
        if ($request === null) {
            $request = request();
        }
        
        // Lấy danh sách majors TẤT CẢ (không phân trang) với filter
        $majors = $this->majorRepository->getAll($request, $this->language);
        
        // Lấy danh sách major catalogues để hiển thị filter tabs
        $majorCatalogues = $this->majorCatalogueRepository->getAllMajorCatalogues($this->language);
        
        // Lấy danh sách schools cho filter
        $schools = $this->schoolRepository->getAllSchools($this->language, 0);
        
        // Lấy danh sách các giá trị duration duy nhất từ majors
        $durations = $this->majorRepository->getDistinctDurations($this->language);
        
        // Lấy filter options để hiển thị trong sidebar
        $filterOptions = $this->majorRepository->getFilterOptions();
        
        // Lấy các filter đã chọn từ request
        $selectedFilters = [
            'catalogue_id' => $request->input('catalogue_id', []),
            'school_id' => $request->input('school_id', []),
            'admission_subject' => $request->input('admission_subject', []),
            'exam_location' => $request->input('exam_location', []),
            'duration' => $request->input('duration', []),
        ];
        
        // Lấy SEO từ system (không cần page nữa vì không phân trang)
        $seo = $this->getSeo($id);
        
        $config = $this->config();
        $system = $this->system;
        
        $template = 'frontend.major.catalogue.index';
        
        return view($template, compact(
            'config',
            'seo',
            'system',
            'majors',
            'majorCatalogues',
            'schools',
            'durations',
            'filterOptions',
            'selectedFilters'
        ));
    }

    private function getSeo($id = null)
    {
        $canonical = write_url('cac-nganh-dao-tao-tu-xa', true, true);
        
        // Lấy follow từ major catalogue nếu có (khi được gọi từ RouterController với $id)
        $follow = 1; // Default là follow
        if ($id !== null) {
            $majorCatalogue = $this->majorCatalogueRepository->findById($id);
            if ($majorCatalogue && isset($majorCatalogue->follow)) {
                $follow = $majorCatalogue->follow ?? 1;
            }
        }
        
        // Lấy SEO từ system
        $metaTitle = $this->system['majors_catalogue_meta_title'] ?? null;
        $metaDescription = $this->system['majors_catalogue_meta_description'] ?? null;
        $metaKeyword = $this->system['majors_catalogue_meta_keyword'] ?? null;
        $metaImage = $this->system['majors_catalogue_meta_image'] ?? null;
        
        // Nếu không có trong system, lấy từ homepage description
        if (empty($metaTitle)) {
            $metaTitle = $this->system['homepage_meta_title'] ?? 'Danh sách các ngành đào tạo từ xa';
        }
        
        if (empty($metaDescription)) {
            $metaDescription = $this->system['homepage_meta_description'] ?? $this->system['homepage_description'] ?? 'Danh sách đầy đủ các ngành đào tạo từ xa uy tín, chất lượng. Tìm hiểu thông tin chi tiết về các chương trình đào tạo từ xa tại các trường đại học hàng đầu.';
        }
        
        if (empty($metaKeyword)) {
            $metaKeyword = $this->system['homepage_meta_keyword'] ?? '';
        }
        
        if (empty($metaImage)) {
            $metaImage = $this->system['homepage_logo'] ?? '';
        }
        
        return [
            'meta_title' => $metaTitle,
            'meta_keyword' => $metaKeyword,
            'meta_description' => $metaDescription,
            'meta_image' => $metaImage,
            'canonical' => $canonical,
            'follow' => $follow,
        ];
    }

    private function config()
    {
        return [
            'language' => $this->language,
        ];
    }
}

