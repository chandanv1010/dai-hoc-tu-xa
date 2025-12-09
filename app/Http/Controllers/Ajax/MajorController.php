<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Language;
use App\Repositories\MajorRepository;
use App\Repositories\MajorCatalogueRepository;

class MajorController extends Controller
{
    protected $language;
    protected $majorRepository;
    protected $majorCatalogueRepository;

    public function __construct(
        MajorRepository $majorRepository,
        MajorCatalogueRepository $majorCatalogueRepository
    ) {
        $this->middleware(function($request, $next){
            $locale = app()->getLocale(); // vn en cn
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            return $next($request);
        });
        $this->majorRepository = $majorRepository;
        $this->majorCatalogueRepository = $majorCatalogueRepository;
    }

    public function getMajorsByCatalogue(Request $request)
    {
        $catalogueId = $request->input('catalogue_id', null);
        $limit = $request->input('limit', 6);

        // Lấy majors sử dụng method trong repository
        $majors = $this->majorRepository->getMajorsForAjax($catalogueId, $this->language, $limit);

        // Lấy canonical của catalogue nếu có
        $canonical = write_url('cac-nganh-dao-tao-tu-xa');
        if ($catalogueId) {
            $catalogue = $this->majorCatalogueRepository->getMajorCatalogueById($catalogueId, $this->language);
            if ($catalogue && isset($catalogue->canonical)) {
                $canonical = write_url($catalogue->canonical);
            }
        }

        // Render HTML cho majors - chỉ trả về các grid items, không có grid container
        $html = '';
        if ($majors->isNotEmpty()) {
            foreach ($majors as $major) {
                $html .= view('frontend.component.major-item', ['major' => $major])->render();
            }
        }

        return response()->json([
            'success' => true,
            'html' => $html,
            'canonical' => $canonical,
            'count' => $majors->count()
        ]);
    }

    public function filter(Request $request)
    {
        // Lấy danh sách majors với filter
        $majors = $this->majorRepository->paginate($request, $this->language, 12, 'cac-nganh-dao-tao-tu-xa.html');
        
        // Render HTML cho majors
        $html = $this->renderFilterMajors($majors);
        
        // Render pagination HTML
        $paginationHtml = $this->renderPagination($majors);
        
        return response()->json([
            'data' => $html,
            'countMajor' => $majors->total(),
            'pagination' => $paginationHtml
        ]);
    }

    private function renderFilterMajors($majors)
    {
        $html = '';
        
        if ($majors && $majors->count() > 0) {
            $html .= '<div class="uk-grid uk-grid-medium" data-uk-grid-match>';
            
            foreach ($majors as $major) {
                $html .= view('frontend.component.major-item', ['major' => $major])->render();
            }
            
            $html .= '</div>';
        } else {
            $html .= '<div class="no-majors-message" style="text-align: center; padding: 60px 20px;">';
            $html .= '<p style="font-size: 18px; color: #666;">Không tìm thấy ngành học nào phù hợp với bộ lọc.</p>';
            $html .= '</div>';
        }
        
        return $html;
    }

    private function renderPagination($majors)
    {
        if (!$majors->hasPages()) {
            return '';
        }
        
        $paginationView = view('pagination::bootstrap-4', ['paginator' => $majors])->render();
        
        $html = '<div class="major-catalogue-pagination" style="margin-top: 40px; text-align: center;">';
        $html .= $paginationView;
        $html .= '</div>';
        
        return $html;
    }
}

