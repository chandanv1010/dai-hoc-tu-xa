<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateSchoolMajorData extends Command
{
    protected $signature = 'school:sync-majors';
    protected $description = 'Đọc dữ liệu từ school_language.majors (JSON) và tạo records trong school_major';

    public function handle()
    {
        $this->info('Bắt đầu migrate dữ liệu từ school_language.majors sang school_major...');
        
        DB::beginTransaction();
        try {
            // Lấy tất cả records từ school_language có majors JSON
            $schoolLanguages = DB::table('school_language')
                ->whereNotNull('majors')
                ->get();
            
            $this->info("Tìm thấy {$schoolLanguages->count()} school_language records có majors");
            
            $totalInserted = 0;
            $totalSkipped = 0;
            
            foreach ($schoolLanguages as $schoolLanguage) {
                $schoolId = $schoolLanguage->school_id;
                $majorsJson = $schoolLanguage->majors;
                
                // Decode JSON
                $majors = json_decode($majorsJson, true);
                
                if (!is_array($majors) || empty($majors)) {
                    continue;
                }
                
                // Extract major_id từ mỗi item trong majors array
                foreach ($majors as $major) {
                    if (!isset($major['major_id']) || empty($major['major_id'])) {
                        continue;
                    }
                    
                    $majorId = (int)$major['major_id'];
                    
                    // Kiểm tra xem record đã tồn tại chưa
                    $exists = DB::table('school_major')
                        ->where('school_id', $schoolId)
                        ->where('major_id', $majorId)
                        ->exists();
                    
                    if (!$exists) {
                        // Insert vào school_major
                        DB::table('school_major')->insert([
                            'school_id' => $schoolId,
                            'major_id' => $majorId,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        $totalInserted++;
                        $this->info("  ✓ Đã thêm: School {$schoolId} <-> Major {$majorId}");
                    } else {
                        $totalSkipped++;
                    }
                }
            }
            
            DB::commit();
            
            $this->info('');
            $this->info("✓ Hoàn thành!");
            $this->info("  - Đã thêm: {$totalInserted} records");
            $this->info("  - Đã bỏ qua (đã tồn tại): {$totalSkipped} records");
            
            return 0;
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Lỗi: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }
    }
}
