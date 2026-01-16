<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\SchoolRepository;
use App\Models\Language;
use Illuminate\Support\Facades\View;

class SchoolController extends Controller
{
    protected $schoolRepository;
    protected $language;

    public function __construct(SchoolRepository $schoolRepository)
    {
        $this->schoolRepository = $schoolRepository;
        $this->middleware(function($request, $next){
            $locale = app()->getLocale();
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language ? $language->id : 1;
            return $next($request);
        });
    }

    public function filter(Request $request)
    {
        // Lấy danh sách schools với filter
        $schools = $this->schoolRepository->paginate($request, $this->language, 12, 'cac-truong-dao-tao-tu-xa.html');
        
        // Render HTML cho schools
        $html = $this->renderFilterSchools($schools);
        
        // Render pagination HTML
        $paginationHtml = $this->renderPagination($schools);
        
        return response()->json([
            'data' => $html,
            'countSchool' => $schools->total(),
            'pagination' => $paginationHtml
        ]);
    }

    private function renderFilterSchools($schools)
    {
        $html = '';
        
        if ($schools && $schools->count() > 0) {
            $html .= '<div class="uk-grid uk-grid-medium" data-uk-grid-match>';
            
            foreach ($schools as $school) {
                // Lấy thông tin từ languages relationship
                $schoolLanguage = $school->languages->first() ?? null;
                $schoolName = '';
                $schoolCanonical = '';
                $majorsCount = 0;
                
                if ($schoolLanguage) {
                    $pivot = $schoolLanguage->pivot ?? null;
                    if ($pivot) {
                        $schoolName = $pivot->name ?? '';
                        $schoolCanonical = $pivot->canonical ?? '';
                        
                        // Đếm số ngành từ majors JSON
                        if (isset($pivot->majors) && is_array($pivot->majors)) {
                            $majorsCount = count($pivot->majors);
                        } elseif (isset($pivot->majors) && is_string($pivot->majors)) {
                            $majorsData = json_decode($pivot->majors, true);
                            if (is_array($majorsData)) {
                                $majorsCount = count($majorsData);
                            }
                        }
                    }
                }
                
                // Lấy ảnh
                $schoolImage = $school->image ?? '';
                $schoolImageUrl = $schoolImage ? asset($schoolImage) : asset('frontend/resources/img/school-default.png');
                
                // Tạo URL
                $schoolUrl = $schoolCanonical ? write_url($schoolCanonical) : '#';
                
                // Icon mặc định
                $schoolIcon = $schoolImageUrl;
                
                $html .= '<div class="uk-width-medium-1-2 uk-width-large-1-3">';
                $html .= '<div class="school-card">';
                $html .= '<div class="school-card-icon">';
                $html .= '<img src="' . $schoolIcon . '" alt="' . htmlspecialchars($schoolName, ENT_QUOTES, 'UTF-8') . '">';
                $html .= '</div>';
                $html .= '<div class="school-card-content">';
                $html .= '<h3 class="school-card-name">' . htmlspecialchars($schoolName, ENT_QUOTES, 'UTF-8') . '</h3>';
                $html .= '<div class="school-card-info">';
                $html .= '<div class="school-card-info-item">';
                $html .= '<span class="info-label">Hệ Đào Tạo Từ Xa</span>';
                $html .= '</div>';
                $html .= '<div class="school-card-info-item">';
                $html .= '<span class="info-label">Số ngành đào tạo: <strong>' . $majorsCount . '</strong> ngành</span>';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '<a href="' . $schoolUrl . '" class="school-card-button">Xem chi tiết chương trình</a>';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</div>';
            }
            
            $html .= '</div>';
        } else {
            $html .= '<div class="schools-empty">';
            $html .= '<p>Không tìm thấy trường đào tạo từ xa nào phù hợp với bộ lọc.</p>';
            $html .= '</div>';
        }
        
        return $html;
    }

    private function renderPagination($schools)
    {
        if (!$schools->hasPages()) {
            return '';
        }
        
        $html = '<div class="schools-pagination">';
        $html .= '<ul class="pagination">';
        
        // Previous Page Link
        $prevPageUrl = null;
        if ($schools->currentPage() > 1) {
            $prevUrl = $schools->previousPageUrl();
            if ($prevUrl) {
                $parsed = parse_url($prevUrl);
                $query = [];
                if (isset($parsed['query'])) {
                    parse_str($parsed['query'], $query);
                }
                $pageNum = $query['page'] ?? ($schools->currentPage() - 1);
                $prevPageUrl = formatPaginationUrl($prevUrl, $pageNum);
            }
        }
        
        if ($prevPageUrl) {
            $html .= '<li class="page-item"><a class="page-link" href="' . $prevPageUrl . '">‹ Trước</a></li>';
        } else {
            $html .= '<li class="page-item disabled"><span class="page-link">‹ Trước</span></li>';
        }
        
        // Pagination Links
        foreach ($schools->getUrlRange(max(1, $schools->currentPage() - 2), min($schools->lastPage(), $schools->currentPage() + 2)) as $page => $url) {
            $paginationUrl = formatPaginationUrl($url, $page);
            $activeClass = ($page == $schools->currentPage()) ? 'active' : '';
            $html .= '<li class="page-item ' . $activeClass . '"><a class="page-link" href="' . $paginationUrl . '">' . $page . '</a></li>';
        }
        
        // Next Page Link
        $nextPageUrl = null;
        if ($schools->hasMorePages()) {
            $nextUrl = $schools->nextPageUrl();
            if ($nextUrl) {
                $parsed = parse_url($nextUrl);
                $query = [];
                if (isset($parsed['query'])) {
                    parse_str($parsed['query'], $query);
                }
                $pageNum = $query['page'] ?? ($schools->currentPage() + 1);
                $nextPageUrl = formatPaginationUrl($nextUrl, $pageNum);
            }
        }
        if ($nextPageUrl) {
            $html .= '<li class="page-item"><a class="page-link" href="' . $nextPageUrl . '">Sau ›</a></li>';
        } else {
            $html .= '<li class="page-item disabled"><span class="page-link">Sau ›</span></li>';
        }
        
        $html .= '</ul>';
        $html .= '</div>';
        
        return $html;
    }
}

