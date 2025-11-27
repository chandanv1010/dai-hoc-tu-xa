<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Major;
use Illuminate\Support\Facades\DB;

class SetMajorHome extends Command
{
    protected $signature = 'major:set-home {--limit=6 : Số lượng majors cần set is_home=2}';
    protected $description = 'Set is_home = 2 cho một số majors để hiển thị ở trang chủ';

    public function handle()
    {
        $limit = (int)$this->option('limit');
        
        $this->info("Đang set is_home = 2 cho {$limit} majors...");
        
        // Lấy majors có publish = 2 và chưa có is_home = 2
        $majors = Major::where('publish', 2)
            ->where(function($query) {
                $query->where('is_home', '!=', 2)
                      ->orWhereNull('is_home');
            })
            ->whereNull('deleted_at')
            ->orderBy('id', 'asc')
            ->limit($limit)
            ->get();
        
        if ($majors->isEmpty()) {
            $this->warn('Không tìm thấy majors nào để set is_home = 2');
            $this->info('Có thể tất cả majors đã có is_home = 2 hoặc không có majors nào publish = 2');
            return 0;
        }
        
        $count = 0;
        foreach ($majors as $major) {
            $major->is_home = 2;
            $major->save();
            $count++;
            $this->info("  ✓ Đã set is_home = 2 cho Major ID: {$major->id}");
        }
        
        $this->info("");
        $this->info("✓ Hoàn thành! Đã set is_home = 2 cho {$count} majors");
        
        return 0;
    }
}

