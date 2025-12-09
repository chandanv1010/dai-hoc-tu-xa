<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Major;
use App\Repositories\SystemRepository;
use Illuminate\Support\Facades\DB;

class UpdateMajorFormDataSeeder extends Seeder
{
    protected $systemRepository;

    public function __construct(SystemRepository $systemRepository)
    {
        $this->systemRepository = $systemRepository;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy dữ liệu từ systems (language_id = 1 cho tiếng Việt)
        $systems = DB::table('systems')
            ->where('language_id', 1)
            ->get()
            ->keyBy('keyword');
        
        // Lấy dữ liệu form từ systems
        $formTaiLoTrinhSystem = $systems->get('form_tai_lo_trinh') ?? null;
        $formTuVanSystem = $systems->get('form_tu_van_mien_phi') ?? null;
        $formHocThuSystem = $systems->get('form_hoc_thu') ?? null;
        
        // Parse JSON từ systems
        $formTaiLoTrinhData = $formTaiLoTrinhSystem ? json_decode($formTaiLoTrinhSystem->content, true) : null;
        $formTuVanData = $formTuVanSystem ? json_decode($formTuVanSystem->content, true) : null;
        $formHocThuData = $formHocThuSystem ? json_decode($formHocThuSystem->content, true) : null;
        
        // Nếu không có dữ liệu từ systems, dùng dữ liệu demo
        $formTaiLoTrinhJson = $formTaiLoTrinhData ?: [
            'script' => '<script>/* Form script demo - Tải lộ trình học */</script>',
            'title' => 'Tải Lộ Trình Học',
            'description' => 'Nhận ngay lộ trình học chi tiết cho ngành học này',
            'footer' => 'Còn <span class="cl">10</span> chỉ tiêu tuyển sinh năm 2025'
        ];
        
        $formTuVanJson = $formTuVanData ?: [
            'script' => '<script>/* Form script demo - Tư vấn miễn phí */</script>',
            'title' => 'Nhận Tư Vấn Miễn Phí',
            'description' => 'Để lại thông tin để được tư vấn miễn phí về ngành học',
            'footer' => 'Còn <span class="cl">10</span> chỉ tiêu tuyển sinh năm 2025'
        ];
        
        $formHocThuJson = $formHocThuData ?: [
            'script' => '<script>/* Form script demo - Học thử miễn phí */</script>',
            'title' => 'Học Thử Miễn Phí',
            'description' => 'Đăng ký học thử miễn phí để trải nghiệm chương trình học',
            'footer' => 'Còn <span class="cl">10</span> suất học thử miễn phí'
        ];
        
        // Lấy tất cả các majors
        $majors = Major::all();
        
        echo "Updating form data for {$majors->count()} majors...\n";
        
        $updated = 0;
        foreach ($majors as $index => $major) {
            // Update major với dữ liệu form
            $major->form_tai_lo_trinh_json = $formTaiLoTrinhJson;
            $major->form_tu_van_mien_phi_json = $formTuVanJson;
            $major->form_hoc_thu_json = $formHocThuJson;
            $major->save();
            
            $updated++;
            
            if (($index + 1) % 10 == 0) {
                echo "Updated " . ($index + 1) . " majors...\n";
            }
        }
        
        echo "Completed! Updated {$updated} majors with form data.\n";
        echo "Summary:\n";
        echo "- Form Tải Lộ Trình Học: " . ($formTaiLoTrinhData ? 'From systems' : 'Demo data') . "\n";
        echo "- Form Tư Vấn Miễn Phí: " . ($formTuVanData ? 'From systems' : 'Demo data') . "\n";
        echo "- Form Học Thử Miễn Phí: " . ($formHocThuData ? 'From systems' : 'Demo data') . "\n";
    }
}
