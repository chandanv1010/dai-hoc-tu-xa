<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use Illuminate\Http\Request;
use App\Services\WidgetService;
use App\Repositories\SchoolRepository;
use Jenssegers\Agent\Facades\Agent;
use Illuminate\Support\Facades\DB;
use App\Models\Contact;

class ContactController extends FrontendController
{
    protected $language;
    protected $system;
    protected $widgetService;
    protected $schoolRepository;

    public function __construct(
        WidgetService $widgetService,
        SchoolRepository $schoolRepository,
    ){
        $this->widgetService = $widgetService;
        $this->schoolRepository = $schoolRepository;
        parent::__construct(); 
    }


    public function index(Request $request){
        $widgets = $this->widgetService->getWidget([
            ['keyword' => 'showroom-system','object' => true],
            ['keyword' => 'news-outstanding','object' => true],
        ], $this->language);
        $config = $this->config();
        $system = $this->system;
        $seo = [
            'meta_title' => 'Trang Thông tin liên hệ',
            'meta_description' => 'Thông tin liên hệ của '.$system['homepage_company'],
            'meta_keyword' => '',
            'meta_image' => '',
            'canonical' => write_url('lien-he')
        ];
        $template = 'frontend.contact.index';
        return view($template, compact(
            'widgets',
            'config',
            'seo',
            'system',
        ));
    }

    public function save(Request $request){
        try {
            DB::beginTransaction();
            $payload = $request->only(['email', 'name', 'phone', 'address', 'message']);
            Contact::create($payload);
            DB::commit();
            return response()->json([
                'message' => 'success',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
        
    }

    public function saveContact(Request $request){
        // Validation: chỉ bắt buộc name và phone
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'content' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'address' => 'nullable|string|max:255',
            'message' => 'nullable|string',
            'type' => 'nullable|string',
        ], [
            'name.required' => 'Vui lòng nhập họ tên.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'email.email' => 'Email không hợp lệ.',
        ]);
        
        try {
            DB::beginTransaction();
            $payload = $request->only(['email', 'name', 'phone', 'address', 'message', 'content', 'description', 'type']);
            Contact::create($payload);
            DB::commit();
            
            // Nếu là AJAX request, trả về JSON với redirect URL
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'message' => 'success',
                    'redirect' => route('contact.thankyou')
                ]);
            }
            
            // Nếu không phải AJAX, redirect về trang cảm ơn
            return redirect()->route('contact.thankyou')->with('success', 'Gửi đăng ký thành công. Chúng tôi sẽ liên hệ lại trong thời gian sớm nhất');
        } catch (\Throwable $th) {
            DB::rollBack();
            
            // Nếu là AJAX request, trả về JSON error
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'message' => 'error',
                    'error' => $th->getMessage()
                ], 500);
            }
            
            throw $th;
        }
        
    }

    public function thankYou(Request $request){
        $config = $this->config();
        $system = $this->system;
        
        // Lấy danh sách schools để hiển thị logo
        $schools = $this->schoolRepository->getAllSchools($this->language, 0);
        
        $seo = [
            'meta_title' => 'Cảm ơn bạn đã đăng ký',
            'meta_description' => 'Cảm ơn bạn đã để lại thông tin liên hệ',
            'meta_keyword' => '',
            'meta_image' => '',
            'canonical' => write_url('cam-on')
        ];
        
        $template = 'frontend.contact.thankyou';
        return view($template, compact(
            'config',
            'seo',
            'system',
            'schools'
        ));
    }

    /**
     * Lưu dữ liệu từ Form.io (download roadmap form)
     * Được gọi từ JavaScript khi form submit
     */
    public function saveRoadmapContact(Request $request){
        try {
            DB::beginTransaction();
            
            // Lấy tất cả dữ liệu từ request
            $payload = [
                'name' => $request->input('name') ?? $request->input('fullname') ?? $request->input('ho_ten') ?? null,
                'phone' => $request->input('phone') ?? $request->input('sdt') ?? $request->input('so_dien_thoai') ?? null,
                'email' => $request->input('email') ?? null,
                'address' => $request->input('address') ?? $request->input('dia_chi') ?? null,
                'message' => $request->input('message') ?? $request->input('mo_ta') ?? $request->input('description') ?? null,
                'type' => 'download_roadmap', // Đánh dấu loại form
            ];
            
            // Lấy major_id nếu có
            if ($request->has('major_id')) {
                $payload['major_id'] = $request->input('major_id');
            }
            
            Contact::create($payload);
            DB::commit();
            return response()->json([
                'message' => 'success',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            \Log::error('Error saving roadmap contact: ' . $th->getMessage());
            return response()->json([
                'message' => 'error',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Lưu dữ liệu từ Form.io (consultation form)
     * Được gọi từ JavaScript khi form submit
     */
    public function saveConsultationContact(Request $request){
        try {
            DB::beginTransaction();
            
            // Lấy tất cả dữ liệu từ request
            $payload = [
                'name' => $request->input('name') ?? $request->input('fullname') ?? $request->input('ho_ten') ?? null,
                'phone' => $request->input('phone') ?? $request->input('sdt') ?? $request->input('so_dien_thoai') ?? null,
                'email' => $request->input('email') ?? null,
                'address' => $request->input('address') ?? $request->input('dia_chi') ?? null,
                'message' => $request->input('message') ?? $request->input('mo_ta') ?? $request->input('description') ?? null,
                'type' => 'consultation', // Đánh dấu loại form
            ];
            
            // Lấy major_id nếu có
            if ($request->has('major_id')) {
                $payload['major_id'] = $request->input('major_id');
            }
            
            Contact::create($payload);
            DB::commit();
            return response()->json([
                'message' => 'success',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            \Log::error('Error saving consultation contact: ' . $th->getMessage());
            return response()->json([
                'message' => 'error',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    private function config(){
        return [
            'language' => $this->language,
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'backend/library/location.js',
                'frontend/core/library/cart.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
            ]
        ];
    }

}
