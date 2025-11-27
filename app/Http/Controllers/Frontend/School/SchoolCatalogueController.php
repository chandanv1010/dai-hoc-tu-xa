<?php

namespace App\Http\Controllers\Frontend\School;

use App\Http\Controllers\FrontendController;
use Illuminate\Http\Request;
use App\Repositories\SchoolRepository;
use App\Repositories\SystemRepository;

class SchoolCatalogueController extends FrontendController
{
    protected $language;
    protected $system;
    protected $schoolRepository;
    protected $systemRepository;

    public function __construct(
        SchoolRepository $schoolRepository,
        SystemRepository $systemRepository,
    ) {
        $this->schoolRepository = $schoolRepository;
        $this->systemRepository = $systemRepository;
        parent::__construct();
    }

    public function index(Request $request, $page = null)
    {
        // Lấy page từ route parameter hoặc request
        if ($page === null) {
            if ($request->has('page')) {
                $page = (int) $request->get('page');
            } else {
                $page = 1;
            }
        } else {
            $page = (int) $page;
        }

        // Set current page resolver cho pagination
        \Illuminate\Pagination\Paginator::currentPageResolver(function () use ($page) {
            return $page;
        });

        // Lấy danh sách schools với phân trang
        $schools = $this->schoolRepository->paginate($request, $this->language, 12, 'cac-truong-dao-tao-tu-xa.html');
        
        // Lấy SEO từ system
        $seo = $this->getSeo($page);
        
        $config = $this->config();
        $system = $this->system;
        
        $template = 'frontend.school.catalogue.index';
        
        return view($template, compact(
            'config',
            'seo',
            'system',
            'schools',
            'page'
        ));
    }

    private function getSeo($page = 1)
    {
        $canonical = ($page > 1) 
            ? write_url('cac-truong-dao-tao-tu-xa', true, false) . '/trang-' . $page . config('apps.general.suffix')
            : write_url('cac-truong-dao-tao-tu-xa', true, true);
        
        // Lấy SEO từ system
        $metaTitle = $this->system['schools_catalogue_meta_title'] ?? null;
        $metaDescription = $this->system['schools_catalogue_meta_description'] ?? null;
        $metaKeyword = $this->system['schools_catalogue_meta_keyword'] ?? null;
        $metaImage = $this->system['schools_catalogue_meta_image'] ?? null;
        
        // Nếu không có trong system, lấy từ homepage description
        if (empty($metaTitle)) {
            $metaTitle = $this->system['homepage_meta_title'] ?? 'Danh sách các trường đào tạo từ xa';
        }
        
        if (empty($metaDescription)) {
            $metaDescription = $this->system['homepage_meta_description'] ?? $this->system['homepage_description'] ?? 'Danh sách đầy đủ các trường đào tạo từ xa uy tín, chất lượng. Tìm hiểu thông tin chi tiết về các chương trình đào tạo từ xa tại các trường đại học hàng đầu.';
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
        ];
    }

    private function config()
    {
        return [
            'language' => $this->language,
        ];
    }
}

