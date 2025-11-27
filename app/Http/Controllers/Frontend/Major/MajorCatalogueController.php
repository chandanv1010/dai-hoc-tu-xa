<?php

namespace App\Http\Controllers\Frontend\Major;

use App\Http\Controllers\FrontendController;
use Illuminate\Http\Request;
use App\Repositories\MajorRepository;
use App\Repositories\SystemRepository;

class MajorCatalogueController extends FrontendController
{
    protected $language;
    protected $system;
    protected $majorRepository;
    protected $systemRepository;

    public function __construct(
        MajorRepository $majorRepository,
        SystemRepository $systemRepository,
    ) {
        $this->majorRepository = $majorRepository;
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

        // Lấy danh sách majors với phân trang
        $majors = $this->majorRepository->paginate($request, $this->language, 12, 'cac-nganh-dao-tao-tu-xa.html');
        
        // Lấy SEO từ system
        $seo = $this->getSeo($page);
        
        $config = $this->config();
        $system = $this->system;
        
        $template = 'frontend.major.catalogue.index';
        
        return view($template, compact(
            'config',
            'seo',
            'system',
            'majors',
            'page'
        ));
    }

    private function getSeo($page = 1)
    {
        $canonical = ($page > 1) 
            ? write_url('cac-nganh-dao-tao-tu-xa', true, false) . '/trang-' . $page . config('apps.general.suffix')
            : write_url('cac-nganh-dao-tao-tu-xa', true, true);
        
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
        ];
    }

    private function config()
    {
        return [
            'language' => $this->language,
        ];
    }
}

