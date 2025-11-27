<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\School;
use Illuminate\Support\Facades\DB;

class SetSchoolShortName extends Command
{
    protected $signature = 'school:set-short-name';
    protected $description = 'Set short_name cho các schools (demo data)';

    public function handle()
    {
        $this->info('Đang set short_name cho schools...');
        
        // Mapping các school với short_name
        // Có thể cập nhật dựa trên dữ liệu thực tế
        $schoolShortNames = [
            // ID => short_name
            // Ví dụ: NEU, HOU, TNU, AOF
        ];
        
        // Lấy tất cả schools
        $schools = School::whereNull('deleted_at')->get();
        
        if ($schools->isEmpty()) {
            $this->warn('Không tìm thấy schools nào');
            return 0;
        }
        
        $count = 0;
        foreach ($schools as $school) {
            // Nếu đã có short_name thì bỏ qua
            if (!empty($school->short_name)) {
                continue;
            }
            
            // Lấy name từ school_language để tạo short_name
            $schoolLanguage = DB::table('school_language')
                ->where('school_id', $school->id)
                ->where('language_id', 1)
                ->first();
            
            if ($schoolLanguage && !empty($schoolLanguage->name)) {
                // Tạo short_name từ name (có thể extract từ tên hoặc set mặc định)
                // Ví dụ: "Đại học Kinh tế Quốc dân" -> "NEU"
                // Tạm thời set theo pattern hoặc để admin tự nhập
                // Ở đây chỉ demo, có thể cập nhật logic sau
            }
            
            // Nếu có mapping trong $schoolShortNames thì dùng
            if (isset($schoolShortNames[$school->id])) {
                $school->short_name = $schoolShortNames[$school->id];
                $school->save();
                $count++;
                $this->info("  ✓ Đã set short_name = '{$school->short_name}' cho School ID: {$school->id}");
            }
        }
        
        $this->info("");
        $this->info("✓ Hoàn thành! Đã set short_name cho {$count} schools");
        $this->info("Lưu ý: Có thể cần cập nhật short_name thủ công trong admin cho các schools khác");
        
        return 0;
    }
}
